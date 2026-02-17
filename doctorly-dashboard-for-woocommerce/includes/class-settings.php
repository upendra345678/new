<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {
	const OPTION_KEY = 'doctorly_dashboard_settings';

	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_settings() {
		register_setting( 'doctorly_dashboard_settings', self::OPTION_KEY, array( $this, 'sanitize' ) );
	}

	public function maybe_seed_defaults() {
		if ( get_option( self::OPTION_KEY ) ) {
			return;
		}

		update_option(
			self::OPTION_KEY,
			array(
				'company_name'        => 'Doctorly',
				'company_address'     => '',
				'gst_number'          => '',
				'invoice_prefix'      => 'DOC',
				'invoice_start'       => 1000,
				'invoice_current'     => 1000,
				'invoice_color'       => '#2B6EF3',
				'header_text'         => 'Doctorly Invoice',
				'footer_text'         => 'Thank you for your business.',
				'fields'              => array(
					'show_tax'           => 1,
					'show_transaction_id'=> 1,
				),
			)
		);
	}

	public function sanitize( $input ) {
		$fields = isset( $input['fields'] ) && is_array( $input['fields'] ) ? $input['fields'] : array();

		return array(
			'company_name'    => sanitize_text_field( $input['company_name'] ?? 'Doctorly' ),
			'company_address' => sanitize_textarea_field( $input['company_address'] ?? '' ),
			'gst_number'      => sanitize_text_field( $input['gst_number'] ?? '' ),
			'invoice_prefix'  => sanitize_text_field( $input['invoice_prefix'] ?? 'DOC' ),
			'invoice_start'   => absint( $input['invoice_start'] ?? 1000 ),
			'invoice_current' => absint( $input['invoice_current'] ?? 1000 ),
			'invoice_color'   => sanitize_hex_color( $input['invoice_color'] ?? '#2B6EF3' ),
			'header_text'     => sanitize_text_field( $input['header_text'] ?? '' ),
			'footer_text'     => sanitize_text_field( $input['footer_text'] ?? '' ),
			'logo_id'         => absint( $input['logo_id'] ?? 0 ),
			'fields'          => array(
				'show_tax'            => empty( $fields['show_tax'] ) ? 0 : 1,
				'show_transaction_id' => empty( $fields['show_transaction_id'] ) ? 0 : 1,
			),
		);
	}

	public function get_settings() {
		$defaults = array(
			'company_name'    => 'Doctorly',
			'company_address' => '',
			'gst_number'      => '',
			'invoice_prefix'  => 'DOC',
			'invoice_start'   => 1000,
			'invoice_current' => 1000,
			'invoice_color'   => '#2B6EF3',
			'header_text'     => 'Doctorly Invoice',
			'footer_text'     => '',
			'logo_id'         => 0,
			'fields'          => array(
				'show_tax'            => 1,
				'show_transaction_id' => 1,
			),
		);

		return wp_parse_args( get_option( self::OPTION_KEY, array() ), $defaults );
	}
}
