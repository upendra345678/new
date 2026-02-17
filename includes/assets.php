<?php

namespace DoctorlyDashboard\Includes;

if (! defined('ABSPATH')) {
    exit;
}

class Assets
{
    public function init(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'frontend']);
        add_action('admin_enqueue_scripts', [$this, 'admin']);
    }

    public function frontend(): void
    {
        if (! is_account_page()) {
            return;
        }

        wp_enqueue_style('doctorly-frontend', DOCTORLY_DASHBOARD_URL . 'assets/css/frontend.css', [], '1.0.0');
        wp_enqueue_script('doctorly-frontend', DOCTORLY_DASHBOARD_URL . 'assets/js/frontend.js', ['jquery'], '1.0.0', true);
        wp_localize_script('doctorly-frontend', 'doctorlyFrontend', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('doctorly_package_nonce'),
        ]);
    }

    public function admin(string $hook): void
    {
        if (strpos($hook, 'doctorly-dashboard') === false) {
            return;
        }

        wp_enqueue_style('doctorly-admin', DOCTORLY_DASHBOARD_URL . 'assets/css/admin.css', [], '1.0.0');
        wp_enqueue_script('doctorly-admin', DOCTORLY_DASHBOARD_URL . 'assets/js/admin.js', ['jquery'], '1.0.0', true);
    }
}
