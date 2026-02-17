<?php

namespace Doctorly\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activator {
	public static function activate() {
		Settings::instance()->maybe_seed_defaults();
		PlanRepository::instance()->maybe_seed_products();
		Frontend\Dashboard::instance()->add_endpoints();
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}
}
