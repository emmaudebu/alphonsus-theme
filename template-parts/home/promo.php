<?php
/**
 * Swedish programme promo banner.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

$alph_product = wc_get_product( (int) alph_mod( 'promo_product' ) );
$alph_levels  = array_filter( array_map( 'trim', explode( ',', (string) alph_mod( 'promo_levels' ) ) ) );
$alph_points  = alph_lines( 'promo_points' );
?>
<section class="section container" aria-labelledby="promo-title">
	<div class="promo">
		<?php if ( $alph_product ) : ?>
			<a class="promo__product" href="<?php echo esc_url( $alph_product->get_permalink() ); ?>">
				<span class="promo__cover"><?php echo $alph_product->get_image( 'woocommerce_single', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="promo__meta">
					<span><?php echo esc_html( alph_mod( 'promo_label' ) ); ?></span>
					<span class="price"><?php echo $alph_product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</span>
			</a>
		<?php endif; ?>

		<div class="promo__copy">
			<?php if ( alph_mod( 'promo_badge' ) ) : ?>
				<span class="chip chip--lilac"><?php echo alph_icon( 'languages', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'promo_badge' ) ); ?></span>
			<?php endif; ?>
			<h2 id="promo-title" class="promo__title"><?php echo esc_html( alph_mod( 'promo_title' ) ); ?></h2>
			<p class="promo__text"><?php echo esc_html( alph_mod( 'promo_text' ) ); ?></p>

			<?php if ( $alph_levels ) : ?>
				<div class="levels">
					<span class="levels__label"><?php esc_html_e( 'CEFR levels', 'alphonsus' ); ?></span>
					<ul>
						<?php foreach ( $alph_levels as $alph_level ) : ?>
							<li><?php echo esc_html( $alph_level ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $alph_points ) : ?>
				<ul class="checklist checklist--2col">
					<?php foreach ( $alph_points as $alph_point ) : ?>
						<li><?php echo alph_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $alph_point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( alph_mod( 'promo_btn' ) ) : ?>
				<a class="btn btn--dark" href="<?php echo esc_url( alph_url( 'promo_btn_url' ) ); ?>"><?php echo alph_icon( 'bag', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'promo_btn' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
