<?php
/**
 * Plugin Name: Doctorly Dashboard for WooCommerce
 * Description: Branded Doctorly SaaS-style dashboard for WooCommerce packages, payments, and invoices.
 * Version: 1.0.0
 * Author: Doctorly
 * Text Domain: doctorly-dashboard
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! defined('DOCTORLY_DASHBOARD_FILE')) {
    define('DOCTORLY_DASHBOARD_FILE', __FILE__);
}

if (! defined('DOCTORLY_DASHBOARD_PATH')) {
    define('DOCTORLY_DASHBOARD_PATH', plugin_dir_path(__FILE__));
}

if (! defined('DOCTORLY_DASHBOARD_URL')) {
    define('DOCTORLY_DASHBOARD_URL', plugin_dir_url(__FILE__));
}

require_once DOCTORLY_DASHBOARD_PATH . 'includes/class-autoloader.php';

DoctorlyDashboard\Includes\Autoloader::register();

register_activation_hook(__FILE__, [DoctorlyDashboard\Includes\Activator::class, 'activate']);
register_deactivation_hook(__FILE__, [DoctorlyDashboard\Includes\Activator::class, 'deactivate']);

add_action('plugins_loaded', static function () {
    if (! class_exists('WooCommerce')) {
        return;
    }

    DoctorlyDashboard\Includes\Plugin::instance()->boot();
});
