<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checkout_Flow {
	private $plan_manager;
	private $invoice_manager;

	public function __construct( Plan_Manager $plan_manager, Invoice_Manager $invoice_manager ) {
		$this->plan_manager    = $plan_manager;
		$this->invoice_manager = $invoice_manager;

		add_action( 'woocommerce_payment_complete', array( $this, 'process_plan_order' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'process_plan_order' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'maybe_redirect_after_payment' ) );
	}

	public function process_plan_order( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$user_id = (int) $order->get_user_id();
		if ( ! $user_id ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$plan = $this->plan_manager->get_plan_by_product( $item->get_product_id() );
			if ( ! $plan ) {
				continue;
			}

			$purchase_date = current_time( 'mysql' );
			$expiry_date   = gmdate( 'Y-m-d', strtotime( '+' . absint( $plan['duration'] ) . ' days' ) );

			update_user_meta( $user_id, 'doctorly_plan_name', $plan['label'] );
			update_user_meta( $user_id, 'doctorly_plan_slug', $plan['slug'] );
			update_user_meta( $user_id, 'doctorly_purchase_date', $purchase_date );
			update_user_meta( $user_id, 'doctorly_expiry_date', $expiry_date );

			if ( ! $order->get_meta( '_doctorly_invoice_id' ) ) {
				$invoice_id = $this->invoice_manager->create_invoice( $order_id, $user_id, $plan['label'] );
				if ( $invoice_id ) {
					$order->update_meta_data( '_doctorly_invoice_id', $invoice_id );
					$order->save();
				}
			}
		}
	}

	public function maybe_redirect_after_payment( $order_id ) {
		if ( ! is_user_logged_in() || ! $order_id ) {
			return;
		}

		$url = wc_get_account_endpoint_url( 'doctorly-dashboard' );
		echo '<script>setTimeout(function(){window.location.href=' . wp_json_encode( $url ) . ';},2000);</script>';
	}
}
