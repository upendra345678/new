<?php

namespace DoctorlyDashboard\Includes;

use DoctorlyDashboard\Admin\AdminDashboard;
use DoctorlyDashboard\Frontend\CustomerDashboard;

if (! defined('ABSPATH')) {
    exit;
}

class Plugin
{
    private static ?Plugin $instance = null;

    public static function instance(): Plugin
    {
        if (! self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void
    {
        (new Endpoints())->init();
        (new Assets())->init();
        (new InvoiceManager())->init();

        if (is_admin()) {
            (new AdminDashboard())->init();
        }

        (new CustomerDashboard())->init();
    }
}
