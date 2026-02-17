<?php

namespace Doctorly\Dashboard\Admin;

use Doctorly\Dashboard\Invoice_Manager;
use Doctorly\Dashboard\Plan_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Dashboard {
	private $plan_manager;
	private $invoice_manager;

	public function __construct( Plan_Manager $plan_manager, Invoice_Manager $invoice_manager ) {
		$this->plan_manager    = $plan_manager;
		$this->invoice_manager = $invoice_manager;
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_doctorly_save_settings', array( $this, 'save_settings' ) );
		add_action( 'admin_post_doctorly_generate_sample_products', array( $this, 'generate_sample_products' ) );
	}

	public function register_menu() {
		add_menu_page( 'Doctorly Dashboard', 'Doctorly Dashboard', 'manage_woocommerce', 'doctorly-dashboard', array( $this, 'render_page' ), 'dashicons-heart', 56 );
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$tab = sanitize_key( $_GET['tab'] ?? 'overview' );
		$plans = $this->plan_manager->get_plans();
		$invoices = get_posts( array( 'post_type' => Invoice_Manager::CPT, 'posts_per_page' => 100 ) );
		$revenue = array_sum( array_map( static function( $invoice ) { return (float) get_post_meta( $invoice->ID, '_doctorly_amount', true ); }, $invoices ) );
		require DOCTORLY_DASHBOARD_PATH . 'templates/admin/dashboard.php';
	}

	public function save_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Unauthorized' );
		}
		check_admin_referer( 'doctorly_save_settings' );

		if ( isset( $_POST['plans'] ) && is_array( $_POST['plans'] ) ) {
			$plans = $this->plan_manager->get_plans();
			foreach ( wp_unslash( $_POST['plans'] ) as $slug => $plan_data ) {
				if ( ! isset( $plans[ $slug ] ) ) {
					continue;
				}
				$plans[ $slug ]['product_id'] = absint( $plan_data['product_id'] ?? 0 );
				$plans[ $slug ]['duration']   = absint( $plan_data['duration'] ?? 30 );
				$plans[ $slug ]['features']   = sanitize_textarea_field( $plan_data['features'] ?? '' );
				$plans[ $slug ]['visible']    = ! empty( $plan_data['visible'] ) ? 1 : 0;
			}
			$this->plan_manager->save_plans( $plans );
		}

		$fields = array( 'company_name', 'company_address', 'company_gst', 'invoice_prefix', 'header_text', 'footer_text', 'invoice_color' );
		foreach ( $fields as $field ) {
			update_option( 'doctorly_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ?? '' ) ) );
		}
		update_option( 'doctorly_invoice_start_number', absint( $_POST['invoice_start_number'] ?? 1001 ) );

		wp_safe_redirect( admin_url( 'admin.php?page=doctorly-dashboard&updated=1' ) );
		exit;
	}

	public function generate_sample_products() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Unauthorized' );
		}
		check_admin_referer( 'doctorly_generate_products' );
		$this->plan_manager->create_sample_products();
		wp_safe_redirect( admin_url( 'admin.php?page=doctorly-dashboard&tab=packages&generated=1' ) );
		exit;
	}
}
