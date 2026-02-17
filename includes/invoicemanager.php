<?php

namespace DoctorlyDashboard\Includes;

if (! defined('ABSPATH')) {
    exit;
}

class InvoiceManager
{
    public function init(): void
    {
        add_action('init', [$this, 'register_invoice_post_type']);
        add_action('woocommerce_order_status_processing', [$this, 'handle_paid_order']);
        add_action('woocommerce_order_status_completed', [$this, 'handle_paid_order']);
        add_action('wp_ajax_doctorly_download_invoice', [$this, 'download_invoice']);
        add_action('wp_ajax_nopriv_doctorly_download_invoice', [$this, 'download_invoice']);
    }

    public function register_invoice_post_type(): void
    {
        register_post_type('doctorly_invoice', [
            'label' => 'Doctorly Invoices',
            'public' => false,
            'show_ui' => false,
            'supports' => ['title', 'custom-fields'],
        ]);
    }

    public function handle_paid_order(int $order_id): void
    {
        $order = wc_get_order($order_id);
        if (! $order || get_post_meta($order_id, '_doctorly_invoice_id', true)) {
            return;
        }

        $plan_data = $this->extract_plan_from_order($order);
        if (! $plan_data) {
            return;
        }

        $user_id = (int) $order->get_user_id();
        if (! $user_id) {
            return;
        }

        $purchase_date = current_time('mysql');
        $expiry_date = date('Y-m-d', strtotime('+' . absint($plan_data['duration_days']) . ' days', strtotime(current_time('mysql'))));

        update_user_meta($user_id, 'doctorly_plan_name', sanitize_text_field($plan_data['label']));
        update_user_meta($user_id, 'doctorly_plan_purchase_date', $purchase_date);
        update_user_meta($user_id, 'doctorly_plan_expiry_date', $expiry_date);

        $invoice_number = $this->next_invoice_number();

        $invoice_id = wp_insert_post([
            'post_type' => 'doctorly_invoice',
            'post_status' => 'publish',
            'post_title' => 'Invoice #' . $invoice_number,
            'post_author' => $user_id,
        ]);

        if (is_wp_error($invoice_id) || ! $invoice_id) {
            return;
        }
        update_post_meta($invoice_id, '_doctorly_invoice_number', $invoice_number);
        update_post_meta($invoice_id, '_doctorly_order_id', $order_id);
        update_post_meta($invoice_id, '_doctorly_plan', $plan_data['label']);
        update_post_meta($invoice_id, '_doctorly_amount', $order->get_total());
        update_post_meta($invoice_id, '_doctorly_tax', $order->get_total_tax());
        update_post_meta($invoice_id, '_doctorly_payment_method', $order->get_payment_method_title());
        update_post_meta($invoice_id, '_doctorly_transaction_id', $order->get_transaction_id());
        update_post_meta($invoice_id, '_doctorly_invoice_date', current_time('mysql'));
        update_post_meta($order_id, '_doctorly_invoice_id', $invoice_id);
    }

    public function extract_plan_from_order(\WC_Order $order): ?array
    {
        $mapping = get_option('doctorly_plan_mappings', []);
        foreach ($order->get_items() as $item) {
            $product_id = (int) $item->get_product_id();
            foreach ($mapping as $plan) {
                if ((int) ($plan['product_id'] ?? 0) === $product_id) {
                    return $plan;
                }
            }
        }

        return null;
    }

    public function next_invoice_number(): string
    {
        $settings = get_option('doctorly_invoice_settings', []);
        $current = (int) ($settings['current_invoice_number'] ?? 1000);
        $prefix = sanitize_text_field($settings['invoice_prefix'] ?? 'DOC');
        $settings['current_invoice_number'] = $current + 1;
        update_option('doctorly_invoice_settings', $settings);

        return $prefix . '-' . $current;
    }

    public function invoices_for_user(int $user_id): array
    {
        return get_posts([
            'post_type' => 'doctorly_invoice',
            'numberposts' => 100,
            'author' => $user_id,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);
    }

    public function render_invoice_html(int $invoice_id): string
    {
        $settings = get_option('doctorly_invoice_settings', []);
        $order_id = (int) get_post_meta($invoice_id, '_doctorly_order_id', true);
        $order = wc_get_order($order_id);
        $user = $order ? get_userdata((int) $order->get_user_id()) : null;
        ob_start();
        include DOCTORLY_DASHBOARD_PATH . 'templates/frontend/invoice-print.php';
        return (string) ob_get_clean();
    }

    public function download_invoice(): void
    {
        $invoice_id = isset($_GET['invoice_id']) ? absint($_GET['invoice_id']) : 0;
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';

        if (! wp_verify_nonce($nonce, 'doctorly_download_invoice_' . $invoice_id)) {
            wp_die('Invalid nonce');
        }

        $invoice = get_post($invoice_id);
        if (! $invoice || ((int) $invoice->post_author !== get_current_user_id() && ! current_user_can('manage_woocommerce'))) {
            wp_die('Unauthorized');
        }

        $html = wp_strip_all_tags($this->render_invoice_html($invoice_id));
        $pdf = new SimplePdf();
        $content = $pdf->generate('Doctorly Invoice', $html);

        nocache_headers();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoice-' . $invoice_id . '.pdf"');
        echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }
}
