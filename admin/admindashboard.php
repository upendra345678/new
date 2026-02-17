<?php

namespace DoctorlyDashboard\Admin;

use DoctorlyDashboard\Includes\Seeder;

if (! defined('ABSPATH')) {
    exit;
}

class AdminDashboard
{
    public function init(): void
    {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_doctorly_save_packages', [$this, 'save_packages']);
        add_action('admin_post_doctorly_save_invoice_settings', [$this, 'save_invoice_settings']);
    }

    public function menu(): void
    {
        add_menu_page(
            'Doctorly Dashboard',
            'Doctorly Dashboard',
            'manage_woocommerce',
            'doctorly-dashboard',
            [$this, 'render'],
            'dashicons-heart',
            56
        );
    }

    public function render(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            return;
        }

        $tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'overview';
        $mapping = get_option('doctorly_plan_mappings', []);
        $invoice_settings = get_option('doctorly_invoice_settings', []);
        $plans = Seeder::plans();

        global $wpdb;
        $revenue = (float) $wpdb->get_var(
            "SELECT SUM(meta.meta_value) FROM {$wpdb->posts} posts
             INNER JOIN {$wpdb->postmeta} meta ON posts.ID = meta.post_id
             WHERE posts.post_type = 'doctorly_invoice' AND meta.meta_key = '_doctorly_amount'"
        );

        $active_plans = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = 'doctorly_plan_expiry_date' AND meta_value >= CURDATE()"
        );

        $recent = get_posts([
            'post_type' => 'doctorly_invoice',
            'numberposts' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        include DOCTORLY_DASHBOARD_PATH . 'templates/admin/dashboard.php';
    }

    public function save_packages(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('doctorly_save_packages');
        $input = isset($_POST['plans']) ? (array) wp_unslash($_POST['plans']) : [];
        $data = [];

        foreach (Seeder::plans() as $slug => $label) {
            $row = $input[$slug] ?? [];
            $data[$slug] = [
                'label' => $label,
                'product_id' => absint($row['product_id'] ?? 0),
                'duration_days' => absint($row['duration_days'] ?? 30),
                'features' => sanitize_textarea_field($row['features'] ?? ''),
                'visibility' => isset($row['visibility']) ? 1 : 0,
            ];
        }

        update_option('doctorly_plan_mappings', $data);
        wp_safe_redirect(admin_url('admin.php?page=doctorly-dashboard&tab=packages&saved=1'));
        exit;
    }

    public function save_invoice_settings(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('doctorly_save_invoice_settings');

        $in = isset($_POST['invoice']) ? (array) wp_unslash($_POST['invoice']) : [];
        $settings = get_option('doctorly_invoice_settings', []);
        $settings['company_name'] = sanitize_text_field($in['company_name'] ?? 'Doctorly');
        $settings['company_address'] = sanitize_textarea_field($in['company_address'] ?? '');
        $settings['company_gst'] = sanitize_text_field($in['company_gst'] ?? '');
        $settings['invoice_color'] = sanitize_hex_color($in['invoice_color'] ?? '#3b82f6') ?: '#3b82f6';
        $settings['header_text'] = sanitize_text_field($in['header_text'] ?? '');
        $settings['footer_text'] = sanitize_text_field($in['footer_text'] ?? '');
        $settings['invoice_prefix'] = sanitize_text_field($in['invoice_prefix'] ?? 'DOC');
        $settings['current_invoice_number'] = max(absint($in['invoice_start_number'] ?? 1000), absint($settings['current_invoice_number'] ?? 1000));
        $settings['enabled_fields'] = [
            'gst' => isset($in['enabled_fields']['gst']) ? 1 : 0,
            'transaction_id' => isset($in['enabled_fields']['transaction_id']) ? 1 : 0,
            'tax' => isset($in['enabled_fields']['tax']) ? 1 : 0,
        ];

        if (! empty($_FILES['invoice_logo']['name'])) {
            $logo_id = media_handle_upload('invoice_logo', 0);
            if (! is_wp_error($logo_id)) {
                $settings['logo_id'] = $logo_id;
            }
        }

        update_option('doctorly_invoice_settings', $settings);
        wp_safe_redirect(admin_url('admin.php?page=doctorly-dashboard&tab=invoices&saved=1'));
        exit;
    }
}
