<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="doctorly-wrap doctorly-admin">
	<form method="get" class="doctorly-card">
		<input type="hidden" name="page" value="doctorly-dashboard">
		<input type="hidden" name="tab" value="invoices">
		<input type="text" name="customer" placeholder="Customer ID" value="<?php echo isset( $_GET['customer'] ) ? esc_attr( wp_unslash( $_GET['customer'] ) ) : ''; ?>">
		<input type="text" name="plan" placeholder="Plan slug" value="<?php echo isset( $_GET['plan'] ) ? esc_attr( wp_unslash( $_GET['plan'] ) ) : ''; ?>">
		<button class="button button-primary">Filter</button>
	</form>
	<div class="doctorly-card">
		<table class="doctorly-table"><thead><tr><th>Invoice #</th><th>Order</th><th>Customer</th><th>Plan</th><th>Total</th><th>Date</th><th>View</th></tr></thead><tbody>
		<?php foreach ( $invoices as $invoice ) : ?>
			<tr>
				<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_invoice_number', true ) ); ?></td>
				<td>#<?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_order_id', true ) ); ?></td>
				<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_user_id', true ) ); ?></td>
				<td><?php echo esc_html( get_post_meta( $invoice->ID, '_doctorly_plan_slug', true ) ); ?></td>
				<td><?php echo wp_kses_post( wc_price( get_post_meta( $invoice->ID, '_doctorly_total', true ) ) ); ?></td>
				<td><?php echo esc_html( get_the_date( '', $invoice ) ); ?></td>
				<td><a class="button" target="_blank" href="<?php echo esc_url( add_query_arg( 'doctorly_invoice_download', $invoice->ID, admin_url() ) ); ?>">Open</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody></table>
	</div>
</div>
