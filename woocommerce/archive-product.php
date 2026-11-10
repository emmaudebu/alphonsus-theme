<?php
/**
 * Book Shop and product category archives (overrides WooCommerce's archive-product.php).
 *
 * @package Alphonsus
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$alph_is_shop = is_shop() && ! is_search();
$alph_title   = $alph_is_shop ? alph_mod( 'shop_title' ) : woocommerce_page_title( false );
$alph_text    = $alph_is_shop ? alph_mod( 'shop_text' ) : wp_strip_all_tags( term_description() );
list( $alph_active, $alph_base ) = alph_active_filters();
?>
<main id="main" class="site-main shop-main">
	<header class="shop-hero">
		<div class="container">
			<?php woocommerce_breadcrumb(); ?>
			<div class="shop-hero__grid">
				<div>
					<?php if ( $alph_is_shop && alph_mod( 'shop_eyebrow' ) ) : ?>
						<span class="chip chip--gold"><?php echo alph_icon( 'sparkles', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'shop_eyebrow' ) ); ?></span>
					<?php endif; ?>
					<h1 class="shop-hero__title"><?php echo esc_html( $alph_title ); ?></h1>
					<?php if ( $alph_text ) : ?>
						<p class="shop-hero__text"><?php echo esc_html( $alph_text ); ?></p>
					<?php endif; ?>
				</div>
				<?php $alph_info = alph_lines( 'shop_info', true ); ?>
				<?php if ( $alph_info ) : ?>
					<ul class="shop-hero__info">
						<?php foreach ( $alph_info as $alph_i => $alph_row ) : ?>
							<li>
								<span class="shop-hero__icon shop-hero__icon--<?php echo esc_attr( $alph_i % 2 ? 'gold' : 'blue' ); ?>"><?php echo alph_icon( $alph_i % 2 ? 'book-open' : 'truck', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span><strong><?php echo esc_html( str_replace( '{count}', number_format_i18n( alph_catalog_count() ), $alph_row[0] ) ); ?></strong><small><?php echo esc_html( str_replace( '{count}', number_format_i18n( alph_catalog_count() ), $alph_row[1] ) ); ?></small></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php alph_category_pills(); ?>
		</div>
	</header>

	<div class="container shop-layout">
		<aside class="shop-layout__side">
			<details class="filters-toggle" data-filters-toggle open>
				<summary class="btn btn--ghost btn--sm"><?php echo alph_icon( 'filter', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Filters', 'alphonsus' ); ?></span><?php echo $alph_active ? '<span class="count-pill">' . count( $alph_active ) . '</span>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
				<?php alph_shop_filters(); ?>
			</details>
		</aside>

		<div class="shop-layout__main">
			<?php woocommerce_output_all_notices(); ?>
			<div class="toolbar">
				<div class="toolbar__info">
					<?php woocommerce_result_count(); ?>
					<?php if ( $alph_active ) : ?>
						<ul class="active-filters">
							<?php foreach ( $alph_active as $alph_f ) : ?>
								<li><a href="<?php echo esc_url( $alph_f[1] ); ?>"><?php echo esc_html( $alph_f[0] ); ?><span aria-hidden="true">×</span><span class="screen-reader-text"><?php esc_html_e( '(remove filter)', 'alphonsus' ); ?></span></a></li>
							<?php endforeach; ?>
							<li><a class="active-filters__reset" href="<?php echo esc_url( $alph_base ); ?>"><?php esc_html_e( 'Reset', 'alphonsus' ); ?></a></li>
						</ul>
					<?php endif; ?>
				</div>
				<?php woocommerce_catalog_ordering(); ?>
			</div>

			<?php if ( woocommerce_product_loop() ) : ?>
				<?php
				wc_set_loop_prop( 'columns', 3 );
				woocommerce_product_loop_start();
				while ( have_posts() ) {
					the_post();
					wc_get_template_part( 'content', 'product' );
				}
				woocommerce_product_loop_end();
				woocommerce_pagination();
				?>
			<?php else : ?>
				<div class="empty-state">
					<?php echo alph_icon( 'search', 40 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h2><?php esc_html_e( 'No books match these filters', 'alphonsus' ); ?></h2>
					<p><?php esc_html_e( 'Try removing a filter or searching for a different title.', 'alphonsus' ); ?></p>
					<a class="btn btn--primary" href="<?php echo esc_url( $alph_base ); ?>"><?php esc_html_e( 'Clear all filters', 'alphonsus' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $alph_is_shop && alph_mod( 'shop_ed_title' ) ) : ?>
		<section class="section container">
			<div class="editorial">
				<?php $alph_img = (int) alph_mod( 'shop_ed_image' ); ?>
				<?php if ( $alph_img && wp_attachment_is_image( $alph_img ) ) : ?>
					<figure class="editorial__media"><?php echo wp_get_attachment_image( $alph_img, 'large', false, array( 'loading' => 'lazy' ) ); ?></figure>
				<?php endif; ?>
				<div class="editorial__copy">
					<p class="eyebrow eyebrow--amber"><?php echo esc_html( alph_mod( 'shop_ed_eyebrow' ) ); ?></p>
					<h2 class="shead__title"><?php echo esc_html( alph_mod( 'shop_ed_title' ) ); ?></h2>
					<p class="shead__text"><?php echo esc_html( alph_mod( 'shop_ed_text' ) ); ?></p>
					<?php $alph_points = alph_items_from_lines( 'shop_ed_points' ); ?>
					<?php if ( $alph_points ) : ?>
						<div class="editorial__points">
							<?php foreach ( $alph_points as $alph_i => $alph_p ) : ?>
								<div class="mini mini--card">
									<span class="mini__icon"><?php echo alph_icon( $alph_i ? 'leaf' : 'headphones', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<div><strong><?php echo esc_html( $alph_p['title'] ); ?></strong><p><?php echo esc_html( $alph_p['text'] ); ?></p></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div class="editorial__actions">
						<?php echo alph_button( __( 'Explore Swedish School', 'alphonsus' ), '/swedish-school/', 'primary', 'languages' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo alph_button( __( 'Sponsor a book bundle', 'alphonsus' ), alph_url( 'donate_url' ), 'ghost', 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
</main>
<?php
get_footer( 'shop' );
