<?php

namespace DoctorlyDashboard\Frontend;

use DoctorlyDashboard\Includes\Endpoints;
use DoctorlyDashboard\Includes\InvoiceManager;
use DoctorlyDashboard\Includes\Seeder;

if (! defined('ABSPATH')) {
    exit;
}

class CustomerDashboard
{
    public function init(): void
    {
        add_filter('woocommerce_account_menu_items', [$this, 'menu']);
        add_action('woocommerce_account_doctorly-dashboard_endpoint', [$this, 'dashboard_tab']);
        add_action('woocommerce_account_doctorly-packages_endpoint', [$this, 'packages_tab']);
        add_action('woocommerce_account_doctorly-invoices_endpoint', [$this, 'invoices_tab']);
        add_action('wp_ajax_doctorly_package_checkout', [$this, 'package_checkout']);
        add_action('template_redirect', [$this, 'payment_return']);
    }

    public function menu(array $items): array
    {
        unset($items['orders']);

        $new = [];
        $new['doctorly-dashboard'] = Endpoints::ENDPOINTS['doctorly-dashboard'];
        $new['doctorly-packages'] = Endpoints::ENDPOINTS['doctorly-packages'];
        $new['doctorly-invoices'] = Endpoints::ENDPOINTS['doctorly-invoices'];

        foreach ($items as $k => $label) {
            if ($k === 'edit-account') {
                $new[$k] = __('Account Details', 'doctorly-dashboard');
            } elseif (! isset($new[$k])) {
                $new[$k] = $label;
            }
        }

        return $new;
    }

    public function dashboard_tab(): void
    {
        $user_id = get_current_user_id();
        $plan_name = get_user_meta($user_id, 'doctorly_plan_name', true);
        $purchase_date = get_user_meta($user_id, 'doctorly_plan_purchase_date', true);
        $expiry_date = get_user_meta($user_id, 'doctorly_plan_expiry_date', true);
        include DOCTORLY_DASHBOARD_PATH . 'templates/frontend/dashboard-tab.php';
    }

    public function packages_tab(): void
    {
        $plans = get_option('doctorly_plan_mappings', []);
        include DOCTORLY_DASHBOARD_PATH . 'templates/frontend/packages-tab.php';
    }

    public function invoices_tab(): void
    {
        $manager = new InvoiceManager();
        $invoices = $manager->invoices_for_user(get_current_user_id());
        include DOCTORLY_DASHBOARD_PATH . 'templates/frontend/invoices-tab.php';
    }

    public function package_checkout(): void
    {
        check_ajax_referer('doctorly_package_nonce', 'nonce');
        if (! is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please login first']);
        }

        $plan = isset($_POST['plan']) ? sanitize_key(wp_unslash($_POST['plan'])) : '';
        $plans = get_option('doctorly_plan_mappings', []);
        if (empty($plans[$plan]['product_id']) || ! $plans[$plan]['visibility']) {
            wp_send_json_error(['message' => 'Invalid package']);
        }

        WC()->cart->empty_cart();
        WC()->cart->add_to_cart((int) $plans[$plan]['product_id']);
        wp_send_json_success(['redirect' => wc_get_checkout_url()]);
    }

    public function payment_return(): void
    {
        if (! is_account_page()) {
            return;
        }

        if (isset($_GET['doctorly_return'])) {
            wp_safe_redirect(wc_get_account_endpoint_url('doctorly-dashboard'));
            exit;
        }

        if (isset($_GET['doctorly_print_invoice']) && is_user_logged_in()) {
            $invoice_id = absint($_GET['doctorly_print_invoice']);
            $invoice = get_post($invoice_id);
            if ($invoice && (int) $invoice->post_author === get_current_user_id()) {
                $manager = new InvoiceManager();
                echo $manager->render_invoice_html($invoice_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                exit;
            }
        }
    }
}
