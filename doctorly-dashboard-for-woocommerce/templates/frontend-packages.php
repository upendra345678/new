<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$current_plan = get_user_meta( get_current_user_id(), 'doctorly_plan_slug', true );
?>
<div class="doctorly-wrap doctorly-front">
	<h2><?php esc_html_e( 'Doctorly Packages', 'doctorly-dashboard' ); ?></h2>
	<div class="doctorly-grid three">
		<?php foreach ( $plans as $slug => $plan ) : ?>
			<?php if ( empty( $plan['visible'] ) ) { continue; } ?>
			<div class="doctorly-card doctorly-pricing">
				<h3><?php echo esc_html( $plan['label'] ); ?></h3>
				<p class="price"><?php echo wp_kses_post( wc_price( $plan['price'] ) ); ?></p>
				<p><?php echo esc_html( sprintf( __( '%d day duration', 'doctorly-dashboard' ), absint( $plan['duration_days'] ) ) ); ?></p>
				<ul>
					<?php foreach ( $plan['features'] as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>
				<button class="button doctorly-buy" data-plan="<?php echo esc_attr( $slug ); ?>">
					<?php echo esc_html( $current_plan === $slug ? __( 'Renew', 'doctorly-dashboard' ) : __( 'Buy / Upgrade', 'doctorly-dashboard' ) ); ?>
				</button>
			</div>
		<?php endforeach; ?>
	</div>
</div>
