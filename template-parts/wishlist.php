<?php
/**
 * Wishlist: header with totals, saved-book rows, recommendations.
 *
 * Used by the Wishlist page template and the [alphonsus_wishlist] shortcode.
 *
 * @package Alphonsus
 *
 * @var array $args heading: h1|h2.
 */

defined( 'ABSPATH' ) || exit;

$alph_tag      = ( $args['heading'] ?? 'h2' ) === 'h1' ? 'h1' : 'h2';
$alph_products = array();
foreach ( alph_wishlist_ids() as $alph_id ) {
	$alph_p = wc_get_product( $alph_id );
	if ( $alph_p && 'publish' === $alph_p->get_status() ) {
		$alph_products[] = $alph_p;
	}
}
$alph_total = array_sum( array_map( fn( $p ) => (float) wc_get_price_to_display( $p ), $alph_products ) );
$alph_count = count( $alph_products );
$alph_shop  = wc_get_page_permalink( 'shop' );

// Recommendations: same categories as the saved books, else the newest titles.
$alph_cats = array();
foreach ( $alph_products as $alph_p ) {
	$alph_cats = array_merge( $alph_cats, wp_get_post_terms( $alph_p->get_id(), 'product_cat', array( 'fields' => 'slugs' ) ) );
}
$alph_recs = wc_get_products(
	array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => 4,
		'exclude'    => array_merge( alph_wishlist_ids(), array( (int) get_option( 'alph_donation_product' ) ) ),
		'category'   => array_values( array_unique( array_diff( $alph_cats, array( 'books' ) ) ) ),
		'orderby'    => 'date',
		'order'      => 'DESC',
	)
);
?>
<div class="wl" data-wishlist-page>
	<header class="wl-head">
		<div class="wl-head__copy">
			<p class="eyebrow eyebrow--amber"><?php echo alph_icon( 'heart', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Saved for later', 'alphonsus' ); ?></p>
			<<?php echo esc_html( $alph_tag ); ?> class="wl-head__title"><?php esc_html_e( 'My Wishlist', 'alphonsus' ); ?></<?php echo esc_html( $alph_tag ); ?>>
			<p class="wl-head__meta" data-wl-summary<?php echo $alph_count ? '' : ' hidden'; ?>>
				<?php
				printf(
					/* translators: 1: number of books, 2: total price */
					esc_html__( '%1$s saved · Total %2$s', 'alphonsus' ),
					'<strong><span data-wl-count>' . esc_html( $alph_count ) . '</span> ' . esc_html( _n( 'book', 'books', $alph_count, 'alphonsus' ) ) . '</strong>',
					'<strong data-wl-total>' . wp_kses_post( wc_price( $alph_total ) ) . '</strong>'
				);
				?>
			</p>
		</div>
		<div class="wl-head__actions">
			<?php if ( $alph_count ) : ?>
				<form method="post" action="<?php echo esc_url( wc_get_cart_url() ); ?>" data-wl-addall>
					<?php wp_nonce_field( 'alph_add_all', '_alph_add_all' ); ?>
					<input type="hidden" name="alph_add_all" value="1">
					<button type="submit" class="btn btn--primary"><?php echo alph_icon( 'bag', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Add all to cart', 'alphonsus' ); ?></span></button>
				</form>
			<?php endif; ?>
			<a class="btn btn--ghost" href="<?php echo esc_url( $alph_shop ); ?>"><?php esc_html_e( 'Continue shopping', 'alphonsus' ); ?></a>
		</div>
	</header>

	<?php if ( $alph_count ) : ?>
		<ul class="wl-list" data-wl-list>
			<?php foreach ( $alph_products as $alph_p ) : ?>
				<?php
				$alph_badge = alph_product_badge( $alph_p );
				$alph_url   = $alph_p->get_permalink();
				?>
				<li class="wl-item" data-wl-item data-price="<?php echo esc_attr( wc_get_price_to_display( $alph_p ) ); ?>">
					<a class="wl-item__cover" href="<?php echo esc_url( $alph_url ); ?>" tabindex="-1" aria-hidden="true">
						<?php echo $alph_p->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
					<div class="wl-item__info">
						<p class="wl-item__kicker">
							<span><?php echo esc_html( alph_product_kicker( $alph_p ) ); ?></span>
							<?php if ( $alph_badge ) : ?>
								<span class="badge badge--static badge--<?php echo esc_attr( alph_badge_tone( $alph_badge ) ); ?>"><?php echo esc_html( $alph_badge ); ?></span>
							<?php endif; ?>
						</p>
						<h3 class="wl-item__title"><a href="<?php echo esc_url( $alph_url ); ?>"><?php echo esc_html( $alph_p->get_name() ); ?></a></h3>
						<p class="wl-item__author"><?php echo esc_html( alph_product_author( $alph_p ) ); ?></p>
						<p class="wl-item__stock wl-item__stock--<?php echo $alph_p->is_in_stock() ? 'in' : 'out'; ?>"><span class="dot" aria-hidden="true"></span><?php echo $alph_p->is_in_stock() ? esc_html__( 'In stock', 'alphonsus' ) : esc_html__( 'Out of stock', 'alphonsus' ); ?></p>
					</div>
					<div class="wl-item__price price"><?php echo $alph_p->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="wl-item__actions">
						<?php echo alph_card_button( $alph_p, false, true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<button type="button" class="wl-remove" data-wishlist="<?php echo esc_attr( $alph_p->get_id() ); ?>" aria-pressed="true" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: book */ __( 'Remove %s from wishlist', 'alphonsus' ), $alph_p->get_name() ) ); ?>">
							<?php echo alph_icon( 'trash', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Remove', 'alphonsus' ); ?></span>
						</button>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="wl-empty"<?php echo $alph_count ? ' hidden' : ''; ?> data-wl-empty>
		<span class="wl-empty__icon"><?php echo alph_icon( 'heart', 34 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<h3><?php esc_html_e( 'Your wishlist is empty', 'alphonsus' ); ?></h3>
		<p><?php esc_html_e( 'Tap the heart on any book to save it here — we’ll keep it ready for when you are.', 'alphonsus' ); ?></p>
		<a class="btn btn--primary" href="<?php echo esc_url( $alph_shop ); ?>"><?php echo alph_icon( 'book-open', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Browse the book shop', 'alphonsus' ); ?></span></a>
	</div>

	<?php if ( $alph_recs ) : ?>
		<section class="wl-recs">
			<div class="shead">
				<div class="shead__main">
					<p class="eyebrow eyebrow--amber"><?php echo esc_html( $alph_count ? __( 'Handpicked for you', 'alphonsus' ) : __( 'Popular picks', 'alphonsus' ) ); ?></p>
					<h2 class="shead__title"><?php echo esc_html( $alph_count ? __( 'You Might Also Love', 'alphonsus' ) : __( 'Start With These Favourites', 'alphonsus' ) ); ?></h2>
				</div>
			</div>
			<?php alph_product_grid( $alph_recs, 4 ); ?>
		</section>
	<?php endif; ?>
</div>
