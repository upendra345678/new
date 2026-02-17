<div class="doctorly-wrap doctorly-full">
	<h2><?php esc_html_e( 'Doctorly Packages', 'doctorly-dashboard' ); ?></h2>
	<div class="doctorly-pricing-grid">
		<?php foreach ( $plans as $slug => $plan ) : ?>
			<?php if ( empty( $plan['visible'] ) ) { continue; } ?>
			<div class="doctorly-card">
				<h3><?php echo esc_html( $plan['label'] ); ?></h3>
				<?php if ( ! empty( $plan['product_id'] ) ) : ?>
					<?php $product = wc_get_product( $plan['product_id'] ); ?>
					<p class="price"><?php echo wp_kses_post( $product ? $product->get_price_html() : '' ); ?></p>
				<?php endif; ?>
				<ul>
					<?php foreach ( array_filter( array_map( 'trim', explode( "\n", $plan['features'] ) ) ) as $feature ) : ?>
						<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>
				<button class="button doctorly-buy" data-plan="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Buy / Upgrade / Renew', 'doctorly-dashboard' ); ?></button>
			</div>
		<?php endforeach; ?>
	</div>
</div>
