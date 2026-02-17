<?php

namespace Doctorly\Dashboard;

use Doctorly\Dashboard\Admin\Dashboard as AdminDashboard;
use Doctorly\Dashboard\Frontend\Dashboard as FrontendDashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init() {
		PlanRepository::instance()->register();
		Settings::instance()->register();
		Invoices::instance()->register();
		Orders::instance()->register();
		Assets::instance()->register();
		Ajax::instance()->register();
		AdminDashboard::instance()->register();
		FrontendDashboard::instance()->register();
	}
}
