<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="doctorly-wrap doctorly-front">
	<h2><?php esc_html_e( 'My Invoices', 'doctorly-dashboard' ); ?></h2>
	<table class="doctorly-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Invoice #', 'doctorly-dashboard' ); ?></th>
				<th><?php esc_html_e( 'Order', 'doctorly-dashboard' ); ?></th>
				<th><?php esc_html_e( 'Status', 'doctorly-dashboard' ); ?></th>
				<th><?php esc_html_e( 'Date', 'doctorly-dashboard' ); ?></th>
				<th><?php esc_html_e( 'Download', 'doctorly-dashboard' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $invoices as $invoice ) : ?>
				<?php $order_id = (int) get_post_meta( $invoice->ID, '_doctorly_order_id', true ); ?>
				<?php $order = wc_get_order( $order_id ); ?>
				<tr>
					<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_invoice_number', true ) ); ?></td>
					<td>#<?php echo esc_html( $order_id ); ?></td>
					<td><?php echo esc_html( $order ? wc_get_order_status_name( $order->get_status() ) : '-' ); ?></td>
					<td><?php echo esc_html( get_the_date( '', $invoice ) ); ?></td>
					<td><a class="button" href="<?php echo esc_url( add_query_arg( 'doctorly_invoice_download', $invoice->ID, wc_get_account_endpoint_url( 'doctorly-invoices' ) ) ); ?>" target="_blank"><?php esc_html_e( 'Print / PDF', 'doctorly-dashboard' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
