<?php

namespace Doctorly\Dashboard\Frontend;

use Doctorly\Dashboard\Invoices;
use Doctorly\Dashboard\PlanRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'menu_items' ), 40 );
		add_action( 'woocommerce_account_doctorly-dashboard_endpoint', array( $this, 'render_dashboard' ) );
		add_action( 'woocommerce_account_doctorly-packages_endpoint', array( $this, 'render_packages' ) );
		add_action( 'woocommerce_account_doctorly-invoices_endpoint', array( $this, 'render_invoices' ) );
		add_action( 'template_redirect', array( $this, 'redirect_after_payment' ) );
	}

	public function add_endpoints() {
		add_rewrite_endpoint( 'doctorly-dashboard', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'doctorly-packages', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'doctorly-invoices', EP_ROOT | EP_PAGES );
	}

	public function menu_items( $items ) {
		unset( $items['orders'] );
		$items['doctorly-dashboard'] = __( 'Dashboard', 'doctorly-dashboard' );
		$items['doctorly-packages']  = __( 'Doctorly Packages', 'doctorly-dashboard' );
		$items['doctorly-invoices']  = __( 'My Invoices', 'doctorly-dashboard' );
		return $items;
	}

	public function render_dashboard() {
		$user_id = get_current_user_id();
		include DOCTORLY_DASHBOARD_PATH . 'templates/frontend-dashboard.php';
	}

	public function render_packages() {
		$plans = PlanRepository::instance()->get_plans();
		include DOCTORLY_DASHBOARD_PATH . 'templates/frontend-packages.php';
	}

	public function render_invoices() {
		$invoices = Invoices::instance()->get_user_invoices( get_current_user_id() );
		include DOCTORLY_DASHBOARD_PATH . 'templates/frontend-invoices.php';
	}

	public function redirect_after_payment() {
		if ( is_order_received_page() ) {
			wp_safe_redirect( wc_get_account_endpoint_url( 'doctorly-dashboard' ) );
			exit;
		}
	}
}
