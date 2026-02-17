<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PlanRepository {
	const OPTION_KEY = 'doctorly_package_map';

	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function register() {
		add_action( 'admin_init', array( $this, 'register_option' ) );
	}

	public function register_option() {
		register_setting( 'doctorly_package_manager', self::OPTION_KEY, array( $this, 'sanitize' ) );
	}

	public function get_default_plans() {
		return array(
			'starter-plan'     => array( 'label' => 'Starter Plan', 'duration_days' => 30, 'price' => 29 ),
			'growth-plan'      => array( 'label' => 'Growth Plan', 'duration_days' => 30, 'price' => 79 ),
			'advanced-plan'    => array( 'label' => 'Advanced Plan', 'duration_days' => 30, 'price' => 149 ),
			'engage-plan'      => array( 'label' => 'Engage Plan', 'duration_days' => 90, 'price' => 299 ),
			'acquisition-plan' => array( 'label' => 'Acquisition Plan', 'duration_days' => 180, 'price' => 499 ),
			'enterprise-plan'  => array( 'label' => 'Enterprise Plan', 'duration_days' => 365, 'price' => 999 ),
		);
	}

	public function get_plans() {
		return wp_parse_args( get_option( self::OPTION_KEY, array() ), $this->get_default_plans() );
	}

	public function sanitize( $input ) {
		$plans = $this->get_default_plans();
		$data  = array();

		foreach ( $plans as $slug => $plan ) {
			$submitted = $input[ $slug ] ?? array();
			$data[ $slug ] = array(
				'label'         => sanitize_text_field( $submitted['label'] ?? $plan['label'] ),
				'product_id'    => absint( $submitted['product_id'] ?? 0 ),
				'duration_days' => absint( $submitted['duration_days'] ?? $plan['duration_days'] ),
				'price'         => wc_format_decimal( $submitted['price'] ?? $plan['price'] ),
				'features'      => array_filter( array_map( 'sanitize_text_field', preg_split( '/\r\n|\r|\n/', $submitted['features'] ?? '' ) ) ),
				'visible'       => empty( $submitted['visible'] ) ? 0 : 1,
			);
		}

		return $data;
	}

	public function maybe_seed_products() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		$plans   = get_option( self::OPTION_KEY, array() );
		$default = $this->get_default_plans();

		foreach ( $default as $slug => $meta ) {
			if ( ! empty( $plans[ $slug ]['product_id'] ) ) {
				continue;
			}

			$product_id = wp_insert_post(
				array(
					'post_title'  => $meta['label'],
					'post_type'   => 'product',
					'post_status' => 'publish',
				)
			);

			if ( is_wp_error( $product_id ) || ! $product_id ) {
				continue;
			}

			update_post_meta( $product_id, '_price', $meta['price'] );
			update_post_meta( $product_id, '_regular_price', $meta['price'] );
			update_post_meta( $product_id, '_virtual', 'yes' );
			update_post_meta( $product_id, '_downloadable', 'no' );
			update_post_meta( $product_id, '_visibility', 'hidden' );
			wp_set_object_terms( $product_id, 'simple', 'product_type' );

			$plans[ $slug ] = array(
				'label'         => $meta['label'],
				'product_id'    => $product_id,
				'duration_days' => $meta['duration_days'],
				'price'         => $meta['price'],
				'features'      => array(),
				'visible'       => 1,
			);
		}

		update_option( self::OPTION_KEY, wp_parse_args( $plans, $default ) );
	}

	public function find_plan_by_product( $product_id ) {
		foreach ( $this->get_plans() as $slug => $plan ) {
			if ( absint( $plan['product_id'] ) === absint( $product_id ) ) {
				$plan['slug'] = $slug;
				return $plan;
			}
		}

		return null;
	}
}
