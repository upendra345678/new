<?php

namespace Doctorly\Dashboard\Frontend;

use Doctorly\Dashboard\Invoice_Manager;
use Doctorly\Dashboard\Plan_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Customer_Dashboard {
	private $plan_manager;
	private $invoice_manager;

	public function __construct( Plan_Manager $plan_manager, Invoice_Manager $invoice_manager ) {
		$this->plan_manager    = $plan_manager;
		$this->invoice_manager = $invoice_manager;

		add_action( 'init', array( $this, 'register_endpoints' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_menu_items' ) );
		add_action( 'woocommerce_account_doctorly-dashboard_endpoint', array( $this, 'render_dashboard' ) );
		add_action( 'woocommerce_account_doctorly-packages_endpoint', array( $this, 'render_packages' ) );
		add_action( 'woocommerce_account_doctorly-invoices_endpoint', array( $this, 'render_invoices' ) );
		add_action( 'wp_ajax_doctorly_start_checkout', array( $this, 'ajax_start_checkout' ) );
		add_action( 'template_redirect', array( $this, 'handle_invoice_download' ) );
	}

	public function register_endpoints() {
		add_rewrite_endpoint( 'doctorly-dashboard', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'doctorly-packages', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'doctorly-invoices', EP_ROOT | EP_PAGES );
	}

	public function add_menu_items( $items ) {
		unset( $items['orders'] );
		$items['doctorly-dashboard'] = __( 'Dashboard', 'doctorly-dashboard' );
		$items['doctorly-packages']  = __( 'Doctorly Packages', 'doctorly-dashboard' );
		$items['doctorly-invoices']  = __( 'My Invoices', 'doctorly-dashboard' );
		return $items;
	}

	public function render_dashboard() {
		$current_user = wp_get_current_user();
		$plan_name    = get_user_meta( $current_user->ID, 'doctorly_plan_name', true );
		$expiry       = get_user_meta( $current_user->ID, 'doctorly_expiry_date', true );
		require DOCTORLY_DASHBOARD_PATH . 'templates/frontend/dashboard.php';
	}

	public function render_packages() {
		$plans = $this->plan_manager->get_plans();
		require DOCTORLY_DASHBOARD_PATH . 'templates/frontend/packages.php';
	}

	public function render_invoices() {
		$invoices = $this->invoice_manager->get_user_invoices( get_current_user_id() );
		require DOCTORLY_DASHBOARD_PATH . 'templates/frontend/invoices.php';
	}

	public function ajax_start_checkout() {
		check_ajax_referer( 'doctorly_dashboard_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => 'Not logged in' ), 401 );
		}

		$plan_slug = sanitize_key( wp_unslash( $_POST['plan'] ?? '' ) );
		$plans     = $this->plan_manager->get_plans();
		if ( empty( $plans[ $plan_slug ]['product_id'] ) ) {
			wp_send_json_error( array( 'message' => 'Invalid package mapping.' ), 400 );
		}

		WC()->cart->empty_cart();
		WC()->cart->add_to_cart( (int) $plans[ $plan_slug ]['product_id'] );
		wp_send_json_success( array( 'checkout_url' => wc_get_checkout_url() ) );
	}

	public function handle_invoice_download() {
		$invoice_id = isset( $_GET['doctorly_invoice'] ) ? absint( $_GET['doctorly_invoice'] ) : 0;
		if ( ! $invoice_id || ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( (int) get_post_meta( $invoice_id, '_doctorly_user_id', true ) !== $user_id && ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		require DOCTORLY_DASHBOARD_PATH . 'templates/frontend/invoice-print.php';
		exit;
	}
}
