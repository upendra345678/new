<?php
/**
 * Plugin Name: Doctorly Dashboard for WooCommerce
 * Description: Branded Doctorly admin and customer dashboards with package purchasing and automated invoices.
 * Version: 1.0.0
 * Author: Doctorly
 * Text Domain: doctorly-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DOCTORLY_DASHBOARD_VERSION', '1.0.0' );
define( 'DOCTORLY_DASHBOARD_FILE', __FILE__ );
define( 'DOCTORLY_DASHBOARD_PATH', plugin_dir_path( __FILE__ ) );
define( 'DOCTORLY_DASHBOARD_URL', plugin_dir_url( __FILE__ ) );

require_once DOCTORLY_DASHBOARD_PATH . 'includes/class-autoloader.php';
Doctorly\Dashboard\Autoloader::register();

register_activation_hook( __FILE__, array( 'Doctorly\\Dashboard\\Installer', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Doctorly\\Dashboard\\Installer', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		Doctorly\Dashboard\Plugin::instance();
	}
);
