<?php

namespace DoctorlyDashboard\Includes;

if (! defined('ABSPATH')) {
    exit;
}

class Activator
{
    public static function activate(): void
    {
        $defaults = [
            'invoice_prefix'        => 'DOC',
            'invoice_start_number'  => 1000,
            'current_invoice_number'=> 1000,
            'company_name'          => 'Doctorly',
            'company_address'       => '',
            'company_gst'           => '',
            'invoice_color'         => '#3b82f6',
            'header_text'           => 'Thank you for choosing Doctorly.',
            'footer_text'           => 'For support contact support@doctorly.com',
            'enabled_fields'        => [
                'gst' => 1,
                'transaction_id' => 1,
                'tax' => 1,
            ],
        ];

        if (! get_option('doctorly_invoice_settings')) {
            add_option('doctorly_invoice_settings', $defaults);
        }

        if (! get_option('doctorly_plan_mappings')) {
            add_option('doctorly_plan_mappings', []);
        }

        (new Endpoints())->register();
        flush_rewrite_rules();

        Seeder::seed_packages();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}
