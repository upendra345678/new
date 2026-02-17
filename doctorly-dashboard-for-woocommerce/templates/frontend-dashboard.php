<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user      = wp_get_current_user();
$plan_name = get_user_meta( $user_id, 'doctorly_plan_name', true );
$purchase  = get_user_meta( $user_id, 'doctorly_plan_purchase_date', true );
$expiry    = get_user_meta( $user_id, 'doctorly_plan_expiry_date', true );
?>
<div class="doctorly-wrap doctorly-front">
	<h2><?php esc_html_e( 'Welcome to Doctorly Dashboard', 'doctorly-dashboard' ); ?></h2>
	<div class="doctorly-grid two">
		<div class="doctorly-card">
			<h3><?php esc_html_e( 'Welcome', 'doctorly-dashboard' ); ?></h3>
			<p><?php echo esc_html( $user->display_name ); ?></p>
		</div>
		<div class="doctorly-card">
			<h3><?php esc_html_e( 'Active Plan', 'doctorly-dashboard' ); ?></h3>
			<p><?php echo esc_html( $plan_name ? $plan_name : __( 'No active plan yet', 'doctorly-dashboard' ) ); ?></p>
			<?php if ( $expiry ) : ?>
				<p><strong><?php esc_html_e( 'Expiry:', 'doctorly-dashboard' ); ?></strong> <?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $expiry ) ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<div class="doctorly-card">
		<h3><?php esc_html_e( 'Quick Actions', 'doctorly-dashboard' ); ?></h3>
		<a class="button" href="<?php echo esc_url( wc_get_account_endpoint_url( 'doctorly-packages' ) ); ?>"><?php esc_html_e( 'Browse Packages', 'doctorly-dashboard' ); ?></a>
		<a class="button" href="<?php echo esc_url( wc_get_account_endpoint_url( 'doctorly-invoices' ) ); ?>"><?php esc_html_e( 'View Invoices', 'doctorly-dashboard' ); ?></a>
		<a class="button" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>"><?php esc_html_e( 'Account Details', 'doctorly-dashboard' ); ?></a>
	</div>
	<?php if ( $purchase ) : ?>
		<p><?php esc_html_e( 'Purchase Date:', 'doctorly-dashboard' ); ?> <?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $purchase ) ) ); ?></p>
	<?php endif; ?>
</div>
