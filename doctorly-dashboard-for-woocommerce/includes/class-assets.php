<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
	}

	public function frontend_assets() {
		if ( ! is_account_page() ) {
			return;
		}

		wp_enqueue_style( 'doctorly-dashboard-frontend', DOCTORLY_DASHBOARD_URL . 'assets/css/frontend.css', array(), '1.0.0' );
		wp_enqueue_script( 'doctorly-dashboard-frontend', DOCTORLY_DASHBOARD_URL . 'assets/js/frontend.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script(
			'doctorly-dashboard-frontend',
			'DoctorlyDashboard',
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

		wp_enqueue_style( 'doctorly-dashboard-admin', DOCTORLY_DASHBOARD_URL . 'assets/css/admin.css', array(), '1.0.0' );
		wp_enqueue_media();
		wp_enqueue_script( 'doctorly-dashboard-admin', DOCTORLY_DASHBOARD_URL . 'assets/js/admin.js', array( 'jquery' ), '1.0.0', true );
	}
}
