<?php if (! defined('ABSPATH')) { exit; } ?>
<div class="doctorly-shell">
    <div class="doctorly-panel">
        <h2>Welcome to your Doctorly Dashboard</h2>
        <p>Manage your package, billing, and account in one place.</p>
    </div>
    <div class="doctorly-grid cols-3">
        <div class="doctorly-panel"><h3>Active Plan</h3><p><?php echo esc_html($plan_name ?: 'No active plan'); ?></p></div>
        <div class="doctorly-panel"><h3>Purchased</h3><p><?php echo esc_html($purchase_date ?: '—'); ?></p></div>
        <div class="doctorly-panel"><h3>Plan Expiry</h3><p><?php echo esc_html($expiry_date ?: '—'); ?></p></div>
    </div>
    <div class="doctorly-panel">
        <h3>Quick Actions</h3>
        <a class="button" href="<?php echo esc_url(wc_get_account_endpoint_url('doctorly-packages')); ?>">Browse Packages</a>
        <a class="button" href="<?php echo esc_url(wc_get_account_endpoint_url('doctorly-invoices')); ?>">View Invoices</a>
    </div>
</div>
