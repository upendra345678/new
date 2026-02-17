<?php

namespace Doctorly\Dashboard\Admin;

use Doctorly\Dashboard\Invoices;
use Doctorly\Dashboard\PlanRepository;
use Doctorly\Dashboard\Settings;

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
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	public function menu() {
		add_menu_page(
			__( 'Doctorly Dashboard', 'doctorly-dashboard' ),
			__( 'Doctorly Dashboard', 'doctorly-dashboard' ),
			'manage_woocommerce',
			'doctorly-dashboard',
			array( $this, 'render' ),
			'dashicons-heart',
			56
		);
	}

	public function render() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Unauthorized access.', 'doctorly-dashboard' ) );
		}

		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview';

		if ( 'packages' === $tab ) {
			$this->render_packages_tab();
			return;
		}

		if ( 'invoices' === $tab ) {
			$this->render_invoices_tab();
			return;
		}

		$this->render_overview_tab();
	}

	private function tabs( $active ) {
		$tabs = array(
			'overview' => __( 'Overview', 'doctorly-dashboard' ),
			'packages' => __( 'Doctorly Packages Manager', 'doctorly-dashboard' ),
			'invoices' => __( 'Invoice Manager', 'doctorly-dashboard' ),
		);

		echo '<h1 class="doctorly-admin-title">Doctorly Dashboard</h1><nav class="doctorly-admin-tabs">';
		foreach ( $tabs as $key => $label ) {
			$url = esc_url( admin_url( 'admin.php?page=doctorly-dashboard&tab=' . $key ) );
			echo '<a class="tab ' . ( $active === $key ? 'active' : '' ) . '" href="' . $url . '">' . esc_html( $label ) . '</a>';
		}
		echo '</nav>';
	}

	private function render_overview_tab() {
		$this->tabs( 'overview' );
		$orders = wc_get_orders(
			array(
				'limit'      => 10,
				'status'     => array( 'wc-processing', 'wc-completed' ),
				'meta_key'   => '_doctorly_plan_slug',
				'meta_compare' => 'EXISTS',
			)
		);
		$revenue = 0;
		foreach ( $orders as $order ) {
			$revenue += (float) $order->get_total();
		}
		$active_users = count_users();
		include DOCTORLY_DASHBOARD_PATH . 'templates/admin-overview.php';
	}

	private function render_packages_tab() {
		$this->tabs( 'packages' );
		$plans    = PlanRepository::instance()->get_plans();
		$settings = Settings::instance()->get_settings();
		include DOCTORLY_DASHBOARD_PATH . 'templates/admin-packages.php';
	}

	private function render_invoices_tab() {
		$this->tabs( 'invoices' );
		$args = array(
			'post_type'      => 'doctorly_invoice',
			'posts_per_page' => 50,
			'post_status'    => 'publish',
		);

		if ( ! empty( $_GET['customer'] ) ) {
			$args['meta_query'][] = array(
				'key'   => '_doctorly_user_id',
				'value' => absint( $_GET['customer'] ),
			);
		}

		if ( ! empty( $_GET['plan'] ) ) {
			$args['meta_query'][] = array(
				'key'   => '_doctorly_plan_slug',
				'value' => sanitize_key( $_GET['plan'] ),
			);
		}

		$invoices = get_posts( $args );
		include DOCTORLY_DASHBOARD_PATH . 'templates/admin-invoices.php';
	}
}
