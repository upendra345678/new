<?php

namespace DoctorlyDashboard\Includes;

if (! defined('ABSPATH')) {
    exit;
}

class Endpoints
{
    public const ENDPOINTS = [
        'doctorly-dashboard' => 'Dashboard',
        'doctorly-packages'  => 'Doctorly Packages',
        'doctorly-invoices'  => 'My Invoices',
    ];

    public function init(): void
    {
        add_action('init', [$this, 'register']);
    }

    public function register(): void
    {
        foreach (array_keys(self::ENDPOINTS) as $endpoint) {
            add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);
        }
    }
}
