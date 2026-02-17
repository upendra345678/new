<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register() {
		add_action( 'wp_ajax_doctorly_start_checkout', array( $this, 'start_checkout' ) );
	}

	public function start_checkout() {
		check_ajax_referer( 'doctorly_dashboard_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Please log in first.', 'doctorly-dashboard' ) ), 403 );
		}

		$slug  = isset( $_POST['plan_slug'] ) ? sanitize_key( wp_unslash( $_POST['plan_slug'] ) ) : '';
		$plans = PlanRepository::instance()->get_plans();
		if ( empty( $plans[ $slug ]['product_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid package selected.', 'doctorly-dashboard' ) ) );
		}

		WC()->cart->empty_cart();
		WC()->cart->add_to_cart( absint( $plans[ $slug ]['product_id'] ) );

		$return_url = wc_get_account_endpoint_url( 'doctorly-dashboard' );
		$checkout   = add_query_arg( 'doctorly_return', rawurlencode( $return_url ), wc_get_checkout_url() );
		wp_send_json_success( array( 'redirect' => $checkout ) );
	}
}
