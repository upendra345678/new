<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="doctorly-wrap doctorly-admin">
	<form method="post" action="options.php" class="doctorly-card">
		<h3><?php esc_html_e( 'Doctorly Packages Manager', 'doctorly-dashboard' ); ?></h3>
		<?php settings_fields( 'doctorly_package_manager' ); ?>
		<table class="widefat striped">
			<thead><tr><th>Plan</th><th>Product ID</th><th>Price</th><th>Duration (Days)</th><th>Features (line break)</th><th>Visible</th></tr></thead>
			<tbody>
			<?php foreach ( $plans as $slug => $plan ) : ?>
				<tr>
					<td><input type="text" name="doctorly_package_map[<?php echo esc_attr( $slug ); ?>][label]" value="<?php echo esc_attr( $plan['label'] ); ?>"></td>
					<td><input type="number" name="doctorly_package_map[<?php echo esc_attr( $slug ); ?>][product_id]" value="<?php echo esc_attr( $plan['product_id'] ?? '' ); ?>"></td>
					<td><input type="text" name="doctorly_package_map[<?php echo esc_attr( $slug ); ?>][price]" value="<?php echo esc_attr( $plan['price'] ); ?>"></td>
					<td><input type="number" name="doctorly_package_map[<?php echo esc_attr( $slug ); ?>][duration_days]" value="<?php echo esc_attr( $plan['duration_days'] ); ?>"></td>
					<td><textarea name="doctorly_package_map[<?php echo esc_attr( $slug ); ?>][features]" rows="3"><?php echo esc_textarea( implode( PHP_EOL, $plan['features'] ?? array() ) ); ?></textarea></td>
					<td><input type="checkbox" name="doctorly_package_map[<?php echo esc_attr( $slug ); ?>][visible]" value="1" <?php checked( ! empty( $plan['visible'] ) ); ?>></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php submit_button( __( 'Save Packages', 'doctorly-dashboard' ) ); ?>
	</form>

	<form method="post" action="options.php" class="doctorly-card">
		<h3><?php esc_html_e( 'Custom Invoice Design Builder', 'doctorly-dashboard' ); ?></h3>
		<?php settings_fields( 'doctorly_dashboard_settings' ); ?>
		<input type="hidden" id="doctorly_logo_id" name="doctorly_dashboard_settings[logo_id]" value="<?php echo esc_attr( $settings['logo_id'] ); ?>">
		<p><button type="button" class="button" id="doctorly-upload-logo"><?php esc_html_e( 'Upload Logo', 'doctorly-dashboard' ); ?></button></p>
		<p><label>Company Name <input type="text" name="doctorly_dashboard_settings[company_name]" value="<?php echo esc_attr( $settings['company_name'] ); ?>"></label></p>
		<p><label>Company Address <textarea name="doctorly_dashboard_settings[company_address]"><?php echo esc_textarea( $settings['company_address'] ); ?></textarea></label></p>
		<p><label>GST Number <input type="text" name="doctorly_dashboard_settings[gst_number]" value="<?php echo esc_attr( $settings['gst_number'] ); ?>"></label></p>
		<p><label>Invoice Prefix <input type="text" name="doctorly_dashboard_settings[invoice_prefix]" value="<?php echo esc_attr( $settings['invoice_prefix'] ); ?>"></label></p>
		<p><label>Start Invoice Number <input type="number" name="doctorly_dashboard_settings[invoice_start]" value="<?php echo esc_attr( $settings['invoice_start'] ); ?>"></label></p>
		<p><label>Current Invoice Number <input type="number" name="doctorly_dashboard_settings[invoice_current]" value="<?php echo esc_attr( $settings['invoice_current'] ); ?>"></label></p>
		<p><label>Invoice Color <input type="color" name="doctorly_dashboard_settings[invoice_color]" value="<?php echo esc_attr( $settings['invoice_color'] ); ?>"></label></p>
		<p><label>Header Text <input type="text" name="doctorly_dashboard_settings[header_text]" value="<?php echo esc_attr( $settings['header_text'] ); ?>"></label></p>
		<p><label>Footer Text <input type="text" name="doctorly_dashboard_settings[footer_text]" value="<?php echo esc_attr( $settings['footer_text'] ); ?>"></label></p>
		<p><label><input type="checkbox" name="doctorly_dashboard_settings[fields][show_tax]" value="1" <?php checked( ! empty( $settings['fields']['show_tax'] ) ); ?>> Show Tax</label></p>
		<p><label><input type="checkbox" name="doctorly_dashboard_settings[fields][show_transaction_id]" value="1" <?php checked( ! empty( $settings['fields']['show_transaction_id'] ) ); ?>> Show Transaction ID</label></p>
		<div class="doctorly-invoice-preview"><?php esc_html_e( 'Live preview updates when invoice page is opened.', 'doctorly-dashboard' ); ?></div>
		<?php submit_button( __( 'Save Invoice Settings', 'doctorly-dashboard' ) ); ?>
	</form>
</div>
