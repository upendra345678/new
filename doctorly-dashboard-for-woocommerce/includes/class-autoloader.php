<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Autoloader {
	public static function register() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	public static function autoload( $class ) {
		$prefix = 'Doctorly\\Dashboard\\';
		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative = strtolower( str_replace( '\\', '-', substr( $class, strlen( $prefix ) ) ) );
		$file     = DOCTORLY_DASHBOARD_PATH . 'includes/class-' . $relative . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
