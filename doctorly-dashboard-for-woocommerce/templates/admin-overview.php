<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="doctorly-wrap doctorly-admin">
	<div class="doctorly-grid three">
		<div class="doctorly-card"><h3><?php esc_html_e( 'Total Package Revenue', 'doctorly-dashboard' ); ?></h3><p><?php echo wp_kses_post( wc_price( $revenue ) ); ?></p></div>
		<div class="doctorly-card"><h3><?php esc_html_e( 'Active Users', 'doctorly-dashboard' ); ?></h3><p><?php echo esc_html( $active_users['total_users'] ); ?></p></div>
		<div class="doctorly-card"><h3><?php esc_html_e( 'Recent Purchases', 'doctorly-dashboard' ); ?></h3><p><?php echo esc_html( count( $orders ) ); ?></p></div>
	</div>
	<div class="doctorly-card">
		<h3><?php esc_html_e( 'Recent Package Orders', 'doctorly-dashboard' ); ?></h3>
		<table class="doctorly-table"><thead><tr><th><?php esc_html_e( 'Order', 'doctorly-dashboard' ); ?></th><th><?php esc_html_e( 'Customer', 'doctorly-dashboard' ); ?></th><th><?php esc_html_e( 'Amount', 'doctorly-dashboard' ); ?></th></tr></thead><tbody>
		<?php foreach ( $orders as $order ) : ?>
			<tr><td>#<?php echo esc_html( $order->get_id() ); ?></td><td><?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?></td><td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td></tr>
		<?php endforeach; ?>
		</tbody></table>
	</div>
</div>
