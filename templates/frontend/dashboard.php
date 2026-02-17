<div class="doctorly-wrap doctorly-full">
	<h2><?php esc_html_e( 'Doctorly Dashboard', 'doctorly-dashboard' ); ?></h2>
	<div class="doctorly-card-grid">
		<div class="doctorly-card">
			<h3><?php printf( esc_html__( 'Welcome, %s', 'doctorly-dashboard' ), esc_html( $current_user->display_name ) ); ?></h3>
			<p><strong><?php esc_html_e( 'Active Plan:', 'doctorly-dashboard' ); ?></strong> <?php echo esc_html( $plan_name ? $plan_name : 'No active plan' ); ?></p>
			<p><strong><?php esc_html_e( 'Plan Expiry:', 'doctorly-dashboard' ); ?></strong> <?php echo esc_html( $expiry ? $expiry : 'N/A' ); ?></p>
		</div>
		<div class="doctorly-card">
			<h3><?php esc_html_e( 'Quick Actions', 'doctorly-dashboard' ); ?></h3>
			<a class="button" href="<?php echo esc_url( wc_get_account_endpoint_url( 'doctorly-packages' ) ); ?>"><?php esc_html_e( 'Upgrade Plan', 'doctorly-dashboard' ); ?></a>
			<a class="button" href="<?php echo esc_url( wc_get_account_endpoint_url( 'doctorly-invoices' ) ); ?>"><?php esc_html_e( 'View Invoices', 'doctorly-dashboard' ); ?></a>
		</div>
	</div>
</div>
