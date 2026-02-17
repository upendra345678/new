<?php
$invoice_number = get_post_meta( $invoice_id, '_doctorly_invoice_number', true );
$order_id       = get_post_meta( $invoice_id, '_doctorly_order_id', true );
$order          = wc_get_order( $order_id );
$color          = get_option( 'doctorly_invoice_color', '#0A5FFF' );
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo esc_html( $invoice_number ); ?></title>
</head>
<body style="font-family:Arial;padding:30px;">
	<h1 style="color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( get_option( 'doctorly_company_name', 'Doctorly' ) ); ?></h1>
	<p><?php echo esc_html( get_option( 'doctorly_company_address', '' ) ); ?></p>
	<p>GST: <?php echo esc_html( get_option( 'doctorly_company_gst', '' ) ); ?></p>
	<hr>
	<h2>Invoice <?php echo esc_html( $invoice_number ); ?></h2>
	<p>Order ID: <?php echo esc_html( $order_id ); ?></p>
	<p>Date: <?php echo esc_html( get_post_meta( $invoice_id, '_doctorly_date', true ) ); ?></p>
	<p>Customer: <?php echo esc_html( $order ? $order->get_formatted_billing_full_name() : '' ); ?></p>
	<p>Package: <?php echo esc_html( get_post_meta( $invoice_id, '_doctorly_plan_name', true ) ); ?></p>
	<p>Amount: <?php echo esc_html( wp_strip_all_tags( wc_price( (float) get_post_meta( $invoice_id, '_doctorly_amount', true ) ) ) ); ?></p>
	<p>Tax: <?php echo esc_html( wp_strip_all_tags( wc_price( (float) get_post_meta( $invoice_id, '_doctorly_tax', true ) ) ) ); ?></p>
	<p>Payment Method: <?php echo esc_html( get_post_meta( $invoice_id, '_doctorly_payment_method', true ) ); ?></p>
	<p>Transaction ID: <?php echo esc_html( get_post_meta( $invoice_id, '_doctorly_transaction_id', true ) ); ?></p>
	<p><?php echo esc_html( get_option( 'doctorly_header_text', '' ) ); ?></p>
	<p><?php echo esc_html( get_option( 'doctorly_footer_text', '' ) ); ?></p>
	<script>window.print();</script>
</body>
</html>
