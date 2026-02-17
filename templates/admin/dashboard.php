<?php if (! defined('ABSPATH')) { exit; } ?>
<div class="wrap doctorly-admin-wrap">
    <h1>Doctorly Dashboard</h1>
    <nav class="nav-tab-wrapper">
        <a class="nav-tab <?php echo $tab === 'overview' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=doctorly-dashboard&tab=overview')); ?>">Overview</a>
        <a class="nav-tab <?php echo $tab === 'packages' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=doctorly-dashboard&tab=packages')); ?>">Doctorly Packages Manager</a>
        <a class="nav-tab <?php echo $tab === 'invoices' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=doctorly-dashboard&tab=invoices')); ?>">Invoice Manager & Builder</a>
    </nav>

    <?php if ($tab === 'overview') : ?>
        <div class="doctorly-grid">
            <div class="doctorly-card"><h3>Total Revenue</h3><p><?php echo wp_kses_post(wc_price($revenue)); ?></p></div>
            <div class="doctorly-card"><h3>Active Plans</h3><p><?php echo esc_html($active_plans); ?></p></div>
            <div class="doctorly-card"><h3>Recent Purchases</h3><p><?php echo esc_html(count($recent)); ?></p></div>
        </div>
        <table class="widefat striped"><thead><tr><th>Invoice</th><th>Order</th><th>Plan</th><th>Amount</th><th>Date</th></tr></thead><tbody>
        <?php foreach ($recent as $invoice) : ?>
            <tr>
                <td><?php echo esc_html(get_post_meta($invoice->ID, '_doctorly_invoice_number', true)); ?></td>
                <td>#<?php echo esc_html(get_post_meta($invoice->ID, '_doctorly_order_id', true)); ?></td>
                <td><?php echo esc_html(get_post_meta($invoice->ID, '_doctorly_plan', true)); ?></td>
                <td><?php echo wp_kses_post(wc_price((float) get_post_meta($invoice->ID, '_doctorly_amount', true))); ?></td>
                <td><?php echo esc_html(get_the_date('', $invoice)); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody></table>
    <?php elseif ($tab === 'packages') : ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('doctorly_save_packages'); ?>
            <input type="hidden" name="action" value="doctorly_save_packages">
            <table class="widefat striped"><thead><tr><th>Plan</th><th>Woo Product ID</th><th>Duration Days</th><th>Features (line separated)</th><th>Visible</th></tr></thead><tbody>
                <?php foreach ($plans as $slug => $label) : $row = $mapping[$slug] ?? []; ?>
                    <tr>
                        <td><?php echo esc_html($label); ?></td>
                        <td><input type="number" name="plans[<?php echo esc_attr($slug); ?>][product_id]" value="<?php echo esc_attr($row['product_id'] ?? ''); ?>"></td>
                        <td><input type="number" name="plans[<?php echo esc_attr($slug); ?>][duration_days]" value="<?php echo esc_attr($row['duration_days'] ?? 30); ?>"></td>
                        <td><textarea name="plans[<?php echo esc_attr($slug); ?>][features]" rows="3"><?php echo esc_textarea($row['features'] ?? ''); ?></textarea></td>
                        <td><input type="checkbox" name="plans[<?php echo esc_attr($slug); ?>][visibility]" <?php checked((int) ($row['visibility'] ?? 1), 1); ?>></td>
                    </tr>
                <?php endforeach; ?>
            </tbody></table>
            <p><button class="button button-primary">Save Package Mapping</button></p>
        </form>
    <?php else : ?>
        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="doctorly-builder">
            <?php wp_nonce_field('doctorly_save_invoice_settings'); ?>
            <input type="hidden" name="action" value="doctorly_save_invoice_settings">
            <div class="doctorly-grid cols-2">
                <div class="doctorly-card">
                    <h3>Invoice Design Builder</h3>
                    <p><label>Logo</label><input type="file" name="invoice_logo"></p>
                    <p><label>Company Name</label><input type="text" name="invoice[company_name]" value="<?php echo esc_attr($invoice_settings['company_name'] ?? 'Doctorly'); ?>"></p>
                    <p><label>Company Address</label><textarea name="invoice[company_address]"><?php echo esc_textarea($invoice_settings['company_address'] ?? ''); ?></textarea></p>
                    <p><label>GST Number</label><input type="text" name="invoice[company_gst]" value="<?php echo esc_attr($invoice_settings['company_gst'] ?? ''); ?>"></p>
                    <p><label>Invoice Color</label><input type="color" name="invoice[invoice_color]" value="<?php echo esc_attr($invoice_settings['invoice_color'] ?? '#3b82f6'); ?>"></p>
                    <p><label>Header Text</label><input type="text" name="invoice[header_text]" value="<?php echo esc_attr($invoice_settings['header_text'] ?? ''); ?>"></p>
                    <p><label>Footer Text</label><input type="text" name="invoice[footer_text]" value="<?php echo esc_attr($invoice_settings['footer_text'] ?? ''); ?>"></p>
                    <p><label>Invoice Prefix</label><input type="text" name="invoice[invoice_prefix]" value="<?php echo esc_attr($invoice_settings['invoice_prefix'] ?? 'DOC'); ?>"></p>
                    <p><label>Start Invoice Number From</label><input type="number" name="invoice[invoice_start_number]" value="<?php echo esc_attr($invoice_settings['current_invoice_number'] ?? 1000); ?>"></p>
                    <fieldset><legend>Enable/Disable Fields</legend>
                        <label><input type="checkbox" name="invoice[enabled_fields][gst]" <?php checked((int) ($invoice_settings['enabled_fields']['gst'] ?? 1), 1); ?>> GST</label>
                        <label><input type="checkbox" name="invoice[enabled_fields][tax]" <?php checked((int) ($invoice_settings['enabled_fields']['tax'] ?? 1), 1); ?>> Tax</label>
                        <label><input type="checkbox" name="invoice[enabled_fields][transaction_id]" <?php checked((int) ($invoice_settings['enabled_fields']['transaction_id'] ?? 1), 1); ?>> Transaction ID</label>
                    </fieldset>
                </div>
                <div class="doctorly-card doctorly-preview">
                    <h3>Live Preview</h3>
                    <div class="invoice-preview" style="border-top:4px solid <?php echo esc_attr($invoice_settings['invoice_color'] ?? '#3b82f6'); ?>">
                        <strong><?php echo esc_html($invoice_settings['company_name'] ?? 'Doctorly'); ?></strong>
                        <p><?php echo esc_html($invoice_settings['header_text'] ?? 'Invoice preview'); ?></p>
                        <small><?php echo esc_html($invoice_settings['footer_text'] ?? 'Footer text preview'); ?></small>
                    </div>
                </div>
            </div>
            <p><button class="button button-primary">Save Invoice Settings</button></p>
        </form>

        <h3>All Invoices</h3>
        <table class="widefat striped"><thead><tr><th>Invoice</th><th>Customer</th><th>Plan</th><th>Date</th></tr></thead><tbody>
        <?php foreach (get_posts(['post_type' => 'doctorly_invoice', 'numberposts' => 50]) as $invoice) : ?>
            <tr>
                <td><?php echo esc_html(get_post_meta($invoice->ID, '_doctorly_invoice_number', true)); ?></td>
                <td><?php echo esc_html(get_the_author_meta('display_name', (int) $invoice->post_author)); ?></td>
                <td><?php echo esc_html(get_post_meta($invoice->ID, '_doctorly_plan', true)); ?></td>
                <td><?php echo esc_html(get_the_date('', $invoice)); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody></table>
    <?php endif; ?>
</div>
