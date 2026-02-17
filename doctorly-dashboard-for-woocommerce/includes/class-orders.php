<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Orders {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register() {
		add_action( 'woocommerce_payment_complete', array( $this, 'handle_order_paid' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'handle_order_paid' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'handle_order_paid' ) );
	}

	public function handle_order_paid( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$user_id = $order->get_user_id();
		if ( ! $user_id ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$plan = PlanRepository::instance()->find_plan_by_product( $item->get_product_id() );
			if ( ! $plan ) {
				continue;
			}

			$purchase = current_time( 'mysql' );
			$expiry   = gmdate( 'Y-m-d H:i:s', strtotime( '+' . max( 1, absint( $plan['duration_days'] ) ) . ' days', current_time( 'timestamp', true ) ) );

			update_user_meta( $user_id, 'doctorly_plan_name', sanitize_text_field( $plan['label'] ) );
			update_user_meta( $user_id, 'doctorly_plan_slug', sanitize_key( $plan['slug'] ) );
			update_user_meta( $user_id, 'doctorly_plan_purchase_date', $purchase );
			update_user_meta( $user_id, 'doctorly_plan_expiry_date', $expiry );
			update_post_meta( $order_id, '_doctorly_plan_slug', sanitize_key( $plan['slug'] ) );
		}

		Invoices::instance()->maybe_generate_from_order( $order );
	}
}
