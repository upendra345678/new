<?php if (! defined('ABSPATH')) { exit; } ?>
<div class="doctorly-shell">
    <h2>Doctorly Packages</h2>
    <div class="doctorly-grid cols-3">
        <?php foreach ($plans as $slug => $plan) : if (empty($plan['visibility'])) { continue; }
            $product = wc_get_product((int) ($plan['product_id'] ?? 0));
            if (! $product) { continue; }
            ?>
            <article class="doctorly-panel package-card">
                <h3><?php echo esc_html($plan['label'] ?? 'Plan'); ?></h3>
                <div class="price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                <ul>
                    <?php foreach (array_filter(array_map('trim', explode("\n", (string) ($plan['features'] ?? '')))) as $feature) : ?>
                        <li><?php echo esc_html($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button class="button doctorly-buy" data-plan="<?php echo esc_attr($slug); ?>">Buy / Upgrade / Renew</button>
            </article>
        <?php endforeach; ?>
    </div>
</div>
