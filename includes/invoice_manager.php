<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Invoice_Manager {
	const CPT = 'doctorly_invoice';

	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
	}

	public function register_cpt() {
		register_post_type(
			self::CPT,
			array(
				'label'       => 'Doctorly Invoices',
				'public'      => false,
				'show_ui'     => false,
				'supports'    => array( 'title' ),
				'has_archive' => false,
			)
		);
	}

	public function create_invoice( $order_id, $user_id, $plan_label ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return 0;
		}

		$number = $this->next_invoice_number();
		$post_id = wp_insert_post(
			array(
				'post_type'   => self::CPT,
				'post_status' => 'publish',
				'post_title'  => sprintf( 'Invoice %s', $number ),
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		update_post_meta( $post_id, '_doctorly_invoice_number', $number );
		update_post_meta( $post_id, '_doctorly_order_id', $order_id );
		update_post_meta( $post_id, '_doctorly_user_id', $user_id );
		update_post_meta( $post_id, '_doctorly_plan_name', sanitize_text_field( $plan_label ) );
		update_post_meta( $post_id, '_doctorly_amount', $order->get_total() );
		update_post_meta( $post_id, '_doctorly_tax', $order->get_total_tax() );
		update_post_meta( $post_id, '_doctorly_payment_method', $order->get_payment_method_title() );
		update_post_meta( $post_id, '_doctorly_transaction_id', $order->get_transaction_id() );
		update_post_meta( $post_id, '_doctorly_status', $order->get_status() );
		update_post_meta( $post_id, '_doctorly_date', current_time( 'mysql' ) );

		return $post_id;
	}

	public function get_user_invoices( $user_id ) {
		return get_posts(
			array(
				'post_type'      => self::CPT,
				'posts_per_page' => 100,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'   => '_doctorly_user_id',
						'value' => (int) $user_id,
					),
				),
			)
		);
	}

	public function next_invoice_number() {
		$last   = (int) get_option( 'doctorly_invoice_last_number', 1000 );
		$prefix = sanitize_text_field( get_option( 'doctorly_invoice_prefix', 'DOC-INV-' ) );
		$next   = $last + 1;
		update_option( 'doctorly_invoice_last_number', $next );
		return $prefix . $next;
	}
}
