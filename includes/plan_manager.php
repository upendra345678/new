<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plan_Manager {
	const OPTION_KEY = 'doctorly_package_plans';

	public function get_plans() {
		$defaults = array(
			'starter-plan'     => array( 'label' => 'Starter Plan', 'duration' => 30, 'features' => "Basic analytics\nEmail support", 'product_id' => 0, 'visible' => 1 ),
			'growth-plan'      => array( 'label' => 'Growth Plan', 'duration' => 90, 'features' => "Growth analytics\nPriority support", 'product_id' => 0, 'visible' => 1 ),
			'advanced-plan'    => array( 'label' => 'Advanced Plan', 'duration' => 180, 'features' => "Marketing automations\nDedicated manager", 'product_id' => 0, 'visible' => 1 ),
			'engage-plan'      => array( 'label' => 'Engage Plan', 'duration' => 180, 'features' => "Patient engagement suite\nCustom campaigns", 'product_id' => 0, 'visible' => 1 ),
			'acquisition-plan' => array( 'label' => 'Acquisition Plan', 'duration' => 365, 'features' => "Lead generation suite\nAdvanced targeting", 'product_id' => 0, 'visible' => 1 ),
			'enterprise-plan'  => array( 'label' => 'Enterprise Plan', 'duration' => 365, 'features' => "Full stack features\nEnterprise SLA", 'product_id' => 0, 'visible' => 1 ),
		);

		return wp_parse_args( get_option( self::OPTION_KEY, array() ), $defaults );
	}

	public function save_plans( $plans ) {
		update_option( self::OPTION_KEY, $plans );
	}

	public function get_plan_by_product( $product_id ) {
		foreach ( $this->get_plans() as $slug => $plan ) {
			if ( (int) $plan['product_id'] === (int) $product_id ) {
				$plan['slug'] = $slug;
				return $plan;
			}
		}

		return null;
	}

	public function create_sample_products() {
		foreach ( $this->get_plans() as $slug => $plan ) {
			if ( ! empty( $plan['product_id'] ) ) {
				continue;
			}
			$product = new \WC_Product_Simple();
			$product->set_name( $plan['label'] );
			$product->set_regular_price( '99' );
			$product->set_catalog_visibility( 'hidden' );
			$product->set_status( 'publish' );
			$product->set_virtual( true );
			$product_id = $product->save();

			$plans                    = $this->get_plans();
			$plans[ $slug ]['product_id'] = $product_id;
			$this->save_plans( $plans );
		}
	}
}
