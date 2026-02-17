<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Invoices {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'handle_download' ) );
	}

	public function register_post_type() {
		register_post_type(
			'doctorly_invoice',
			array(
				'label'       => 'Doctorly Invoices',
				'public'      => false,
				'show_ui'     => false,
				'supports'    => array( 'title' ),
				'show_in_menu'=> false,
			)
		);
	}

	public function maybe_generate_from_order( $order ) {
		if ( get_post_meta( $order->get_id(), '_doctorly_invoice_id', true ) ) {
			return;
		}

		$invoice_number = $this->next_invoice_number();
		$post_id        = wp_insert_post(
			array(
				'post_type'   => 'doctorly_invoice',
				'post_status' => 'publish',
				'post_title'  => sprintf( 'Invoice %s', $invoice_number ),
			)
		);

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, '_doctorly_invoice_number', $invoice_number );
		update_post_meta( $post_id, '_doctorly_order_id', $order->get_id() );
		update_post_meta( $post_id, '_doctorly_user_id', $order->get_user_id() );
		update_post_meta( $post_id, '_doctorly_plan_slug', get_post_meta( $order->get_id(), '_doctorly_plan_slug', true ) );
		update_post_meta( $post_id, '_doctorly_payment_method', $order->get_payment_method_title() );
		update_post_meta( $post_id, '_doctorly_transaction_id', $order->get_transaction_id() );
		update_post_meta( $post_id, '_doctorly_total', $order->get_total() );
		update_post_meta( $post_id, '_doctorly_tax', $order->get_total_tax() );
		update_post_meta( $post_id, '_doctorly_date', current_time( 'mysql' ) );

		update_post_meta( $order->get_id(), '_doctorly_invoice_id', $post_id );
	}

	private function next_invoice_number() {
		$settings = Settings::instance()->get_settings();
		$current  = max( absint( $settings['invoice_current'] ), absint( $settings['invoice_start'] ) );
		$next     = $current + 1;

		$settings['invoice_current'] = $next;
		update_option( Settings::OPTION_KEY, $settings );

		return sanitize_text_field( $settings['invoice_prefix'] ) . '-' . $next;
	}

	public function get_user_invoices( $user_id ) {
		return get_posts(
			array(
				'post_type'      => 'doctorly_invoice',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'meta_key'       => '_doctorly_user_id',
				'meta_value'     => absint( $user_id ),
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
	}

	public function handle_download() {
		$invoice_id = isset( $_GET['doctorly_invoice_download'] ) ? absint( wp_unslash( $_GET['doctorly_invoice_download'] ) ) : 0;
		if ( ! $invoice_id ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'Unauthorized', 'doctorly-dashboard' ) );
		}

		$invoice_user_id = (int) get_post_meta( $invoice_id, '_doctorly_user_id', true );
		if ( get_current_user_id() !== $invoice_user_id && ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Forbidden', 'doctorly-dashboard' ) );
		}

		header( 'Content-Type: text/html; charset=utf-8' );
		$this->render_invoice_html( $invoice_id );
		exit;
	}

	public function render_invoice_html( $invoice_id ) {
		$settings = Settings::instance()->get_settings();
		$order_id = (int) get_post_meta( $invoice_id, '_doctorly_order_id', true );
		$order    = wc_get_order( $order_id );
		if ( ! $order ) {
			echo esc_html__( 'Invalid invoice.', 'doctorly-dashboard' );
			return;
		}

		include DOCTORLY_DASHBOARD_PATH . 'templates/invoice.php';
	}
}
