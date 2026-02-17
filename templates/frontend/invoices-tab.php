<?php if (! defined('ABSPATH')) { exit; } ?>
<div class="doctorly-shell">
    <h2>My Invoices</h2>
    <table class="shop_table shop_table_responsive my_account_orders">
        <thead><tr><th>Invoice</th><th>Date</th><th>Plan</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($invoices as $invoice) :
            $order = wc_get_order((int) get_post_meta($invoice->ID, '_doctorly_order_id', true));
            $download = wp_nonce_url(admin_url('admin-ajax.php?action=doctorly_download_invoice&invoice_id=' . $invoice->ID), 'doctorly_download_invoice_' . $invoice->ID);
            ?>
            <tr>
                <td><?php echo esc_html(get_post_meta($invoice->ID, '_doctorly_invoice_number', true)); ?></td>
                <td><?php echo esc_html(get_the_date('', $invoice)); ?></td>
                <td><?php echo esc_html(get_post_meta($invoice->ID, '_doctorly_plan', true)); ?></td>
                <td><?php echo wp_kses_post(wc_price((float) get_post_meta($invoice->ID, '_doctorly_amount', true))); ?></td>
                <td><?php echo esc_html($order ? wc_get_order_status_name($order->get_status()) : 'Paid'); ?></td>
                <td>
                    <a class="button" href="<?php echo esc_url($download); ?>">Download PDF</a>
                    <a class="button" target="_blank" href="<?php echo esc_url(add_query_arg(['doctorly_print_invoice' => $invoice->ID], wc_get_account_endpoint_url('doctorly-invoices'))); ?>">Print</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
