<?php

namespace DoctorlyDashboard\Includes;

if (! defined('ABSPATH')) {
    exit;
}

class Seeder
{
    public static function plans(): array
    {
        return [
            'starter_plan' => 'Starter Plan',
            'growth_plan' => 'Growth Plan',
            'advanced_plan' => 'Advanced Plan',
            'engage_plan' => 'Engage Plan',
            'acquisition_plan' => 'Acquisition Plan',
            'enterprise_plan' => 'Enterprise Plan',
        ];
    }

    public static function seed_packages(): void
    {
        if (! class_exists('WC_Product_Simple')) {
            return;
        }

        $mapping = get_option('doctorly_plan_mappings', []);

        foreach (self::plans() as $slug => $name) {
            if (! empty($mapping[$slug]['product_id']) && get_post($mapping[$slug]['product_id'])) {
                continue;
            }

            $product = new \WC_Product_Simple();
            $product->set_name($name);
            $product->set_status('publish');
            $product->set_catalog_visibility('hidden');
            $product->set_regular_price('99');
            $product->set_virtual(true);
            $product->set_sold_individually(true);
            $product_id = $product->save();

            $mapping[$slug] = [
                'label' => $name,
                'product_id' => $product_id,
                'duration_days' => 30,
                'features' => "Feature 1\nFeature 2\nFeature 3",
                'visibility' => 1,
            ];
        }

        update_option('doctorly_plan_mappings', $mapping);
    }
}
