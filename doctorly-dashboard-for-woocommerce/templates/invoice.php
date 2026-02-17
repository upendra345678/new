<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$invoice_number = get_post_meta( $invoice_id, '_doctorly_invoice_number', true );
$tax            = (float) get_post_meta( $invoice_id, '_doctorly_tax', true );
$total          = (float) get_post_meta( $invoice_id, '_doctorly_total', true );
$plan_slug      = get_post_meta( $invoice_id, '_doctorly_plan_slug', true );
$logo           = ! empty( $settings['logo_id'] ) ? wp_get_attachment_image_url( $settings['logo_id'], 'medium' ) : '';
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo esc_html( $invoice_number ); ?></title>
	<style>
		body{font-family:Arial,sans-serif;max-width:900px;margin:24px auto;color:#1a1d2d}
		.head{display:flex;justify-content:space-between;border-bottom:3px solid <?php echo esc_attr( $settings['invoice_color'] ); ?>;padding-bottom:16px}
		.badge{background:<?php echo esc_attr( $settings['invoice_color'] ); ?>;color:#fff;padding:8px 12px;border-radius:8px}
		table{width:100%;border-collapse:collapse;margin-top:16px}td,th{border:1px solid #ddd;padding:10px;text-align:left}
	</style>
</head>
<body onload="window.print()">
	<div class="head">
		<div>
			<?php if ( $logo ) : ?><img src="<?php echo esc_url( $logo ); ?>" style="max-width:180px"><?php endif; ?>
			<h2><?php echo esc_html( $settings['company_name'] ); ?></h2>
			<p><?php echo nl2br( esc_html( $settings['company_address'] ) ); ?></p>
			<p><?php esc_html_e( 'GST', 'doctorly-dashboard' ); ?>: <?php echo esc_html( $settings['gst_number'] ); ?></p>
		</div>
		<div>
			<p class="badge"><?php echo esc_html( $invoice_number ); ?></p>
			<p><?php esc_html_e( 'Invoice Date', 'doctorly-dashboard' ); ?>: <?php echo esc_html( wp_date( get_option( 'date_format' ) ) ); ?></p>
			<p><?php esc_html_e( 'Order ID', 'doctorly-dashboard' ); ?>: #<?php echo esc_html( $order->get_id() ); ?></p>
		</div>
	</div>
	<h3><?php echo esc_html( $settings['header_text'] ); ?></h3>
	<p><strong><?php esc_html_e( 'Customer', 'doctorly-dashboard' ); ?>:</strong> <?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?></p>
	<table>
		<tr><th><?php esc_html_e( 'Package', 'doctorly-dashboard' ); ?></th><th><?php esc_html_e( 'Amount', 'doctorly-dashboard' ); ?></th></tr>
		<tr><td><?php echo esc_html( $plan_slug ); ?></td><td><?php echo wp_kses_post( wc_price( $total ) ); ?></td></tr>
		<?php if ( ! empty( $settings['fields']['show_tax'] ) ) : ?>
		<tr><td><?php esc_html_e( 'Tax', 'doctorly-dashboard' ); ?></td><td><?php echo wp_kses_post( wc_price( $tax ) ); ?></td></tr>
		<?php endif; ?>
	</table>
	<p><?php esc_html_e( 'Payment Method', 'doctorly-dashboard' ); ?>: <?php echo esc_html( get_post_meta( $invoice_id, '_doctorly_payment_method', true ) ); ?></p>
	<?php if ( ! empty( $settings['fields']['show_transaction_id'] ) ) : ?>
		<p><?php esc_html_e( 'Transaction ID', 'doctorly-dashboard' ); ?>: <?php echo esc_html( get_post_meta( $invoice_id, '_doctorly_transaction_id', true ) ); ?></p>
	<?php endif; ?>
	<footer><p><?php echo esc_html( $settings['footer_text'] ); ?></p></footer>
</body>
</html>
