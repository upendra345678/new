<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Installer {
	public static function activate() {
		self::ensure_options();
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}

	private static function ensure_options() {
		$defaults = array(
			'invoice_prefix'       => 'DOC-INV-',
			'invoice_start_number' => 1001,
			'invoice_color'        => '#0A5FFF',
			'company_name'         => 'Doctorly',
			'header_text'          => 'Thank you for your business.',
			'footer_text'          => 'Doctorly - Healthcare growth platform',
			'company_address'      => '',
			'company_gst'          => '',
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( 'doctorly_' . $key, false ) ) {
				update_option( 'doctorly_' . $key, $value );
			}
		}

		if ( false === get_option( 'doctorly_invoice_last_number', false ) ) {
			update_option( 'doctorly_invoice_last_number', (int) get_option( 'doctorly_invoice_start_number', 1001 ) - 1 );
		}
	}
}
