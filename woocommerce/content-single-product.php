<?php
/**
 * Single product layout (overrides WooCommerce's content-single-product.php).
 *
 * @package Alphonsus
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

$alph_short   = wp_strip_all_tags( $product->get_short_description() );
$alph_tagline = ( $alph_short && mb_strlen( $alph_short ) <= 120 ) ? $alph_short : '';
$alph_badge   = alph_product_badge( $product );
$alph_cats    = get_the_terms( $product->get_id(), 'product_cat' );
$alph_regular = (float) $product->get_regular_price();
$alph_save    = $product->is_on_sale() && $alph_regular > 0 ? round( ( $alph_regular - (float) $product->get_price() ) / $alph_regular * 100 ) : 0;
$alph_lang    = $product->get_attribute( 'pa_language' );
$alph_refund  = get_page_by_path( 'no-refund-cancellation-policy' );
$alph_attrs   = array_filter( $product->get_attributes(), fn( $a ) => $a->get_visible() );
$alph_specs   = $alph_attrs || $product->has_weight() || $product->has_dimensions() || $product->get_sku();
$alph_reviews = comments_open() || $product->get_review_count();
$alph_n       = 0;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'ps', $product ); ?>>
	<div class="ps__grid">
		<div class="ps__gallery">
			<div class="ps__gallery-card">
				<?php if ( $alph_badge || $alph_save ) : ?>
					<div class="ps__badges">
						<?php if ( $alph_badge ) : ?>
							<span class="badge badge--<?php echo esc_attr( alph_badge_tone( $alph_badge ) ); ?> badge--static"><?php echo esc_html( $alph_badge ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php woocommerce_show_product_images(); ?>
				<?php if ( $alph_lang ) : ?>
					<p class="ps__lang"><?php echo alph_icon( 'languages', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $alph_lang ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="ps__summary summary entry-summary">
			<?php if ( $alph_cats && ! is_wp_error( $alph_cats ) ) : ?>
				<div class="ps__chips">
					<?php foreach ( $alph_cats as $alph_i => $alph_cat ) : ?>
						<a class="chip <?php echo 0 === $alph_i ? 'chip--lilac' : 'chip--paper'; ?>" href="<?php echo esc_url( get_term_link( $alph_cat ) ); ?>"><?php echo esc_html( $alph_cat->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<h1 class="ps__title product_title entry-title"><?php the_title(); ?></h1>
			<?php if ( $alph_tagline ) : ?>
				<p class="ps__tagline"><?php echo esc_html( $alph_tagline ); ?></p>
			<?php endif; ?>
			<p class="ps__author"><?php echo esc_html( alph_product_author( $product ) ); ?></p>

			<?php if ( $product->get_rating_count() ) : ?>
				<a class="ps__rating" href="#ps-reviews">
					<?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<strong><?php echo esc_html( number_format_i18n( (float) $product->get_average_rating(), 2 ) ); ?></strong>
					<?php /* translators: %d: review count */ ?>
					<span><?php echo esc_html( sprintf( _n( '(%d review)', '(%d reviews)', $product->get_rating_count(), 'alphonsus' ), $product->get_rating_count() ) ); ?></span>
				</a>
			<?php endif; ?>

			<div class="pricecard">
				<div>
					<p class="pricecard__label"><?php echo esc_html( $product->is_downloadable() ? __( 'Digital edition', 'alphonsus' ) : __( 'Price', 'alphonsus' ) ); ?></p>
					<div class="pricecard__price">
						<span class="price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php if ( $alph_save ) : ?>
							<?php /* translators: %d: percent saved */ ?>
							<span class="badge badge--red badge--static"><?php echo esc_html( sprintf( __( 'Save %d%%', 'alphonsus' ), $alph_save ) ); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<div class="pricecard__stock pricecard__stock--<?php echo esc_attr( $product->is_in_stock() ? 'in' : 'out' ); ?>">
					<span class="dot" aria-hidden="true"></span>
					<?php
					if ( ! $product->is_in_stock() ) {
						esc_html_e( 'Out of stock', 'alphonsus' );
					} elseif ( $product->managing_stock() && $product->get_stock_quantity() ) {
						/* translators: %d: units in stock */
						echo esc_html( sprintf( _n( '%d copy ready to ship', '%d copies ready to ship', $product->get_stock_quantity(), 'alphonsus' ), $product->get_stock_quantity() ) );
					} else {
						esc_html_e( 'In stock', 'alphonsus' );
					}
					?>
				</div>
			</div>

			<div class="ps__buy"><?php woocommerce_template_single_add_to_cart(); ?></div>

			<ul class="ps__tiles">
				<li><span class="ps__tile-icon"><?php echo alph_icon( 'truck', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><strong><?php esc_html_e( 'Worldwide delivery', 'alphonsus' ); ?></strong><small><?php esc_html_e( 'Options shown at checkout', 'alphonsus' ); ?></small></span></li>
				<li><span class="ps__tile-icon"><?php echo alph_icon( 'shield', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><strong><?php esc_html_e( 'Secure checkout', 'alphonsus' ); ?></strong><small><?php esc_html_e( 'Pay safely with Paystack', 'alphonsus' ); ?></small></span></li>
				<li><span class="ps__tile-icon"><?php echo alph_icon( 'heart', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><strong><?php esc_html_e( 'Save for later', 'alphonsus' ); ?></strong><small><?php esc_html_e( 'Add it to your wishlist', 'alphonsus' ); ?></small></span></li>
			</ul>

			<?php if ( $alph_short && ! $alph_tagline ) : ?>
				<div class="ps__short"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
			<?php endif; ?>

			<?php woocommerce_template_single_meta(); ?>
		</div>
	</div>

	<section class="ps__notes" aria-labelledby="ps-notes-title">
		<p class="eyebrow"><?php esc_html_e( 'In-depth exploration', 'alphonsus' ); ?></p>
		<h2 id="ps-notes-title" class="shead__title shead__title--ink"><?php esc_html_e( 'Book Notes & Details', 'alphonsus' ); ?></h2>
		<div class="faq">
			<?php if ( trim( get_the_content() ) ) : ?>
				<details class="faq__item" open>
					<summary><span class="faq__num"><?php echo esc_html( sprintf( '%02d', ++$alph_n ) ); ?></span><span class="faq__q"><?php esc_html_e( 'About this book', 'alphonsus' ); ?></span><?php echo alph_icon( 'chevron', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
					<div class="faq__a entry-content"><?php the_content(); ?></div>
				</details>
			<?php endif; ?>
			<?php if ( $alph_specs ) : ?>
				<details class="faq__item"<?php echo 0 === $alph_n ? ' open' : ''; ?>>
					<summary><span class="faq__num"><?php echo esc_html( sprintf( '%02d', ++$alph_n ) ); ?></span><span class="faq__q"><?php esc_html_e( 'Specifications', 'alphonsus' ); ?></span><?php echo alph_icon( 'chevron', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
					<div class="faq__a"><?php wc_display_product_attributes( $product ); ?></div>
				</details>
			<?php endif; ?>
			<?php if ( $alph_reviews ) : ?>
				<details class="faq__item" id="ps-reviews"<?php echo 0 === $alph_n ? ' open' : ''; ?>>
					<?php /* translators: %d: review count */ ?>
					<summary><span class="faq__num"><?php echo esc_html( sprintf( '%02d', ++$alph_n ) ); ?></span><span class="faq__q"><?php echo esc_html( sprintf( __( 'Reader reviews (%d)', 'alphonsus' ), $product->get_review_count() ) ); ?></span><?php echo alph_icon( 'chevron', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
					<div class="faq__a"><?php comments_template(); ?></div>
				</details>
			<?php endif; ?>
			<details class="faq__item">
				<summary><span class="faq__num"><?php echo esc_html( sprintf( '%02d', ++$alph_n ) ); ?></span><span class="faq__q"><?php esc_html_e( 'Delivery & returns', 'alphonsus' ); ?></span><?php echo alph_icon( 'chevron', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
				<div class="faq__a">
					<p><?php esc_html_e( 'Worldwide delivery is available — delivery options and costs are shown at checkout. Digital books are emailed to you after payment.', 'alphonsus' ); ?></p>
					<p><?php esc_html_e( 'All sales are final once an order is confirmed. If an item arrives damaged or materially different from your order, contact us promptly and we will find a suitable remedy.', 'alphonsus' ); ?></p>
					<?php if ( $alph_refund ) : ?>
						<p><a class="link-arrow" href="<?php echo esc_url( get_permalink( $alph_refund ) ); ?>"><?php esc_html_e( 'Read the full policy', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
					<?php endif; ?>
				</div>
			</details>
		</div>
	</section>

	<div class="ps__related">
		<?php
		woocommerce_upsell_display();
		woocommerce_output_related_products();
		?>
	</div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
