<div class="wrap doctorly-admin-wrap">
	<h1>Doctorly Dashboard</h1>
	<nav class="nav-tab-wrapper">
		<a class="nav-tab <?php echo 'overview' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=doctorly-dashboard&tab=overview' ) ); ?>">Overview</a>
		<a class="nav-tab <?php echo 'packages' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=doctorly-dashboard&tab=packages' ) ); ?>">Doctorly Packages Manager</a>
		<a class="nav-tab <?php echo 'invoices' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=doctorly-dashboard&tab=invoices' ) ); ?>">Invoice Manager</a>
		<a class="nav-tab <?php echo 'design' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=doctorly-dashboard&tab=design' ) ); ?>">Invoice Design Builder</a>
	</nav>

	<?php if ( 'overview' === $tab ) : ?>
		<div class="doctorly-card-grid">
			<div class="doctorly-card"><h3>Total Revenue</h3><p><?php echo wp_kses_post( wc_price( $revenue ) ); ?></p></div>
			<div class="doctorly-card"><h3>Active Plans</h3><p><?php echo esc_html( (string) count( array_filter( get_users( array( 'fields' => 'ID' ) ), static function( $id ) { return (bool) get_user_meta( $id, 'doctorly_plan_name', true ); } ) ) ); ?></p></div>
			<div class="doctorly-card"><h3>Recent Purchases</h3><p><?php echo esc_html( (string) count( $invoices ) ); ?></p></div>
		</div>
	<?php elseif ( 'packages' === $tab ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'doctorly_save_settings' ); ?>
			<input type="hidden" name="action" value="doctorly_save_settings">
			<table class="widefat striped"><thead><tr><th>Plan</th><th>Product ID</th><th>Duration(days)</th><th>Features</th><th>Visible</th></tr></thead><tbody>
			<?php foreach ( $plans as $slug => $plan ) : ?>
			<tr>
				<td><?php echo esc_html( $plan['label'] ); ?></td>
				<td><input type="number" name="plans[<?php echo esc_attr( $slug ); ?>][product_id]" value="<?php echo esc_attr( (string) $plan['product_id'] ); ?>"></td>
				<td><input type="number" name="plans[<?php echo esc_attr( $slug ); ?>][duration]" value="<?php echo esc_attr( (string) $plan['duration'] ); ?>"></td>
				<td><textarea name="plans[<?php echo esc_attr( $slug ); ?>][features]"><?php echo esc_textarea( $plan['features'] ); ?></textarea></td>
				<td><input type="checkbox" name="plans[<?php echo esc_attr( $slug ); ?>][visible]" value="1" <?php checked( 1, $plan['visible'] ); ?>></td>
			</tr>
			<?php endforeach; ?>
			</tbody></table>
			<p><button class="button button-primary">Save Package Mapping</button></p>
		</form>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'doctorly_generate_products' ); ?>
			<input type="hidden" name="action" value="doctorly_generate_sample_products">
			<button class="button">Create Sample Package Products</button>
		</form>
	<?php elseif ( 'invoices' === $tab ) : ?>
		<table class="widefat striped"><thead><tr><th>Invoice #</th><th>Customer</th><th>Plan</th><th>Status</th><th>Date</th></tr></thead><tbody>
		<?php foreach ( $invoices as $invoice ) : ?>
		<tr>
			<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_invoice_number', true ) ); ?></td>
			<td><?php echo esc_html( (string) get_userdata( (int) get_post_meta( $invoice->ID, '_doctorly_user_id', true ) )->display_name ); ?></td>
			<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_plan_name', true ) ); ?></td>
			<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_status', true ) ); ?></td>
			<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_date', true ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody></table>
	<?php elseif ( 'design' === $tab ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'doctorly_save_settings' ); ?>
			<input type="hidden" name="action" value="doctorly_save_settings">
			<table class="form-table">
			<tr><th>Company Name</th><td><input name="company_name" value="<?php echo esc_attr( get_option( 'doctorly_company_name', 'Doctorly' ) ); ?>"></td></tr>
			<tr><th>Company Address</th><td><input name="company_address" value="<?php echo esc_attr( get_option( 'doctorly_company_address', '' ) ); ?>"></td></tr>
			<tr><th>GST Number</th><td><input name="company_gst" value="<?php echo esc_attr( get_option( 'doctorly_company_gst', '' ) ); ?>"></td></tr>
			<tr><th>Invoice Color</th><td><input type="color" name="invoice_color" value="<?php echo esc_attr( get_option( 'doctorly_invoice_color', '#0A5FFF' ) ); ?>"></td></tr>
			<tr><th>Header Text</th><td><input name="header_text" value="<?php echo esc_attr( get_option( 'doctorly_header_text', '' ) ); ?>"></td></tr>
			<tr><th>Footer Text</th><td><input name="footer_text" value="<?php echo esc_attr( get_option( 'doctorly_footer_text', '' ) ); ?>"></td></tr>
			<tr><th>Invoice Prefix</th><td><input name="invoice_prefix" value="<?php echo esc_attr( get_option( 'doctorly_invoice_prefix', 'DOC-INV-' ) ); ?>"></td></tr>
			<tr><th>Start Invoice Number</th><td><input type="number" name="invoice_start_number" value="<?php echo esc_attr( (string) get_option( 'doctorly_invoice_start_number', 1001 ) ); ?>"></td></tr>
			</table>
			<button class="button button-primary">Save Invoice Builder Settings</button>
		</form>
		<div class="doctorly-live-preview" style="border-top:4px solid <?php echo esc_attr( get_option( 'doctorly_invoice_color', '#0A5FFF' ) ); ?>;padding:20px;margin-top:20px;background:#fff;">
			<h3>Live Preview</h3>
			<p><strong><?php echo esc_html( get_option( 'doctorly_company_name', 'Doctorly' ) ); ?></strong></p>
			<p><?php echo esc_html( get_option( 'doctorly_header_text', '' ) ); ?></p>
			<p><?php echo esc_html( get_option( 'doctorly_footer_text', '' ) ); ?></p>
		</div>
	<?php endif; ?>
</div>
