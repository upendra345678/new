<div class="doctorly-wrap doctorly-full">
	<h2><?php esc_html_e( 'My Invoices', 'doctorly-dashboard' ); ?></h2>
	<table class="shop_table shop_table_responsive">
		<thead><tr><th>Invoice</th><th>Plan</th><th>Status</th><th>Date</th><th>Download</th></tr></thead>
		<tbody>
		<?php foreach ( $invoices as $invoice ) : ?>
			<tr>
				<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_invoice_number', true ) ); ?></td>
				<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_plan_name', true ) ); ?></td>
				<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_status', true ) ); ?></td>
				<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_date', true ) ); ?></td>
				<td><a class="button" target="_blank" href="<?php echo esc_url( add_query_arg( 'doctorly_invoice', $invoice->ID, wc_get_account_endpoint_url( 'doctorly-invoices' ) ) ); ?>">PDF / Print</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
