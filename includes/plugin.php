<?php

namespace Doctorly\Dashboard;

use Doctorly\Dashboard\Admin\Admin_Dashboard;
use Doctorly\Dashboard\Frontend\Customer_Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	private static $instance;

	/** @var Plan_Manager */
	private $plan_manager;

	/** @var Invoice_Manager */
	private $invoice_manager;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->plan_manager    = new Plan_Manager();
		$this->invoice_manager = new Invoice_Manager();

		new Assets();
		new Customer_Dashboard( $this->plan_manager, $this->invoice_manager );
		new Admin_Dashboard( $this->plan_manager, $this->invoice_manager );
		new Checkout_Flow( $this->plan_manager, $this->invoice_manager );
	}
}
