<?php
/**
 * Plugin Name: Doctorly Dashboard for WooCommerce
 * Description: Branded Doctorly admin/customer dashboard with package sales and invoice automation for WooCommerce.
 * Version: 1.0.0
 * Author: Doctorly
 * Text Domain: doctorly-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'DOCTORLY_DASHBOARD_FILE' ) ) {
	define( 'DOCTORLY_DASHBOARD_FILE', __FILE__ );
}

if ( ! defined( 'DOCTORLY_DASHBOARD_PATH' ) ) {
	define( 'DOCTORLY_DASHBOARD_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'DOCTORLY_DASHBOARD_URL' ) ) {
	define( 'DOCTORLY_DASHBOARD_URL', plugin_dir_url( __FILE__ ) );
}

require_once DOCTORLY_DASHBOARD_PATH . 'includes/class-autoloader.php';
Doctorly\Dashboard\Autoloader::register();

register_activation_hook( DOCTORLY_DASHBOARD_FILE, array( 'Doctorly\\Dashboard\\Activator', 'activate' ) );
register_deactivation_hook( DOCTORLY_DASHBOARD_FILE, array( 'Doctorly\\Dashboard\\Activator', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		Doctorly\Dashboard\Plugin::instance()->init();
	}
);
