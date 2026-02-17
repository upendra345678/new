<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	public function frontend_assets() {
		if ( ! is_account_page() ) {
			return;
		}

		wp_enqueue_style( 'doctorly-dashboard', DOCTORLY_DASHBOARD_URL . 'assets/css/frontend.css', array(), DOCTORLY_DASHBOARD_VERSION );
		wp_enqueue_script( 'doctorly-dashboard', DOCTORLY_DASHBOARD_URL . 'assets/js/frontend.js', array( 'jquery' ), DOCTORLY_DASHBOARD_VERSION, true );

		wp_localize_script(
			'doctorly-dashboard',
			'doctorlyDashboard',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'doctorly_dashboard_nonce' ),
			)
		);
	}

	public function admin_assets( $hook ) {
		if ( false === strpos( $hook, 'doctorly-dashboard' ) ) {
			return;
		}

		wp_enqueue_style( 'doctorly-dashboard-admin', DOCTORLY_DASHBOARD_URL . 'assets/css/admin.css', array(), DOCTORLY_DASHBOARD_VERSION );
	}
}
