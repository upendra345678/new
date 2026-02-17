<?php if (! defined('ABSPATH')) { exit; }
$logo = ! empty($settings['logo_id']) ? wp_get_attachment_image_url((int) $settings['logo_id'], 'medium') : '';
$enabled = $settings['enabled_fields'] ?? [];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice <?php echo esc_html(get_post_meta($invoice_id, '_doctorly_invoice_number', true)); ?></title>
    <style>
        body{font-family:Arial,sans-serif;max-width:900px;margin:20px auto;color:#222}
        .head{border-top:5px solid <?php echo esc_attr($settings['invoice_color'] ?? '#3b82f6'); ?>;padding-top:12px;display:flex;justify-content:space-between}
        table{width:100%;border-collapse:collapse;margin-top:15px}td,th{border:1px solid #ddd;padding:8px;text-align:left}
    </style>
</head>
<body>
<div class="head">
    <div>
        <?php if ($logo) : ?><img src="<?php echo esc_url($logo); ?>" style="max-height:50px"><?php endif; ?>
        <h2><?php echo esc_html($settings['company_name'] ?? 'Doctorly'); ?></h2>
        <p><?php echo esc_html($settings['company_address'] ?? ''); ?></p>
        <?php if (! empty($enabled['gst'])) : ?><p>GST: <?php echo esc_html($settings['company_gst'] ?? ''); ?></p><?php endif; ?>
    </div>
    <div>
        <p><strong>Invoice:</strong> <?php echo esc_html(get_post_meta($invoice_id, '_doctorly_invoice_number', true)); ?></p>
        <p><strong>Order:</strong> #<?php echo esc_html((string) $order_id); ?></p>
        <p><strong>Date:</strong> <?php echo esc_html(get_post_meta($invoice_id, '_doctorly_invoice_date', true)); ?></p>
    </div>
</div>
<p><?php echo esc_html($settings['header_text'] ?? ''); ?></p>
<h3>Bill To</h3>
<p><?php echo esc_html($user ? $user->display_name : 'Customer'); ?> (<?php echo esc_html($order ? $order->get_billing_email() : ''); ?>)</p>
<table>
    <thead><tr><th>Package</th><th>Amount</th><th>Tax</th><th>Payment Method</th><th>Transaction ID</th></tr></thead>
    <tbody><tr>
        <td><?php echo esc_html(get_post_meta($invoice_id, '_doctorly_plan', true)); ?></td>
        <td><?php echo wp_kses_post(wc_price((float) get_post_meta($invoice_id, '_doctorly_amount', true))); ?></td>
        <td><?php echo ! empty($enabled['tax']) ? wp_kses_post(wc_price((float) get_post_meta($invoice_id, '_doctorly_tax', true))) : '-'; ?></td>
        <td><?php echo esc_html(get_post_meta($invoice_id, '_doctorly_payment_method', true)); ?></td>
        <td><?php echo ! empty($enabled['transaction_id']) ? esc_html(get_post_meta($invoice_id, '_doctorly_transaction_id', true)) : '-'; ?></td>
    </tr></tbody>
</table>
<p><?php echo esc_html($settings['footer_text'] ?? ''); ?></p>
<script>window.print()</script>
</body>
</html>
