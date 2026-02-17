<?php
/**
 * Sample package creation script for wp shell / wp eval-file.
 * Usage: wp eval-file wp-content/plugins/doctorly-dashboard-for-woocommerce/includes/sample-package-seeder.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( '\\Doctorly\\Dashboard\\PlanRepository' ) ) {
	\Doctorly\Dashboard\PlanRepository::instance()->maybe_seed_products();
	echo "Doctorly sample package products ensured.\n";
}
