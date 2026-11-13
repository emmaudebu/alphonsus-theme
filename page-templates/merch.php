<?php
/**
 * Template Name: Merch
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alph_slug     = alph_mod( 'mc_cat' );
$alph_cat      = get_term_by( 'slug', $alph_slug, 'product_cat' );
$alph_products = function_exists( 'alph_section_products' ) ? alph_section_products( $alph_slug, '', 60 ) : array();
$alph_subcats  = $alph_cat ? get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => $alph_cat->term_id,
		'hide_empty' => true,
	)
) : array();
$alph_feature  = function_exists( 'wc_get_product' ) ? wc_get_product( (int) alph_mod( 'mc_feature' ) ) : null;
?>
<main id="main" class="site-main page-merch">
	<?php
	alph_split_hero(
		array(
			'eyebrow' => alph_mod( 'mc_eyebrow' ),
			'title'   => alph_mod( 'mc_title' ),
			'text'    => alph_mod( 'mc_text' ),
			'buttons' => array( array( __( 'Browse collection', 'alphonsus' ), '#catalog', 'primary', 'arrow' ) ),
			'stats'   => alph_lines( 'mc_stats', true ),
			'image'   => (int) alph_mod( 'mc_image' ),
		)
	);
	?>

	<section class="section container" id="catalog">
		<?php if ( $alph_subcats && ! is_wp_error( $alph_subcats ) ) : ?>
			<nav class="pills pills--filter" aria-label="<?php esc_attr_e( 'Merch categories', 'alphonsus' ); ?>" data-filter-pills>
				<button type="button" class="pill is-active" data-filter="all"><?php esc_html_e( 'All items', 'alphonsus' ); ?></button>
				<?php foreach ( $alph_subcats as $alph_sub ) : ?>
					<button type="button" class="pill" data-filter="<?php echo esc_attr( $alph_sub->slug ); ?>"><?php echo esc_html( $alph_sub->name ); ?></button>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php alph_section_head( array( 'eyebrow' => __( 'Catalog archive', 'alphonsus' ), 'title' => __( 'Reader Lifestyle & Essentials', 'alphonsus' ) ) ); ?>

		<?php if ( $alph_products ) : ?>
			<div class="product-grid product-grid--4" data-filter-grid>
				<?php foreach ( $alph_products as $alph_product ) : ?>
					<div class="product-grid__cell" data-cats="<?php echo esc_attr( implode( ' ', wp_get_post_terms( $alph_product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) ) ) ); ?>">
						<?php alph_product_card( $alph_product, 'h3', array( 'excerpt' => true ) ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="empty-state">
				<?php echo alph_icon( 'gift', 40 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2><?php esc_html_e( 'New merch is on its way', 'alphonsus' ); ?></h2>
				<p><?php esc_html_e( 'Our reader essentials are being restocked. Join the club below to hear first when they land — or browse the books in the meantime.', 'alphonsus' ); ?></p>
				<?php echo alph_button( __( 'Browse the book shop', 'alphonsus' ), wc_get_page_permalink( 'shop' ), 'primary', 'book-open' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( $alph_feature && $alph_feature->is_visible() ) : ?>
		<section class="section container"><?php alph_bundle_spotlight( $alph_feature ); ?></section>
	<?php endif; ?>

	<?php $alph_pledges = alph_items_from_lines( 'mc_pledges' ); ?>
	<?php if ( $alph_pledges ) : ?>
		<section class="band band--cream">
			<div class="container">
				<?php alph_section_head( array( 'eyebrow' => __( 'Our material pledge', 'alphonsus' ), 'title' => __( 'Conscious Craftsmanship for Thoughtful Minds', 'alphonsus' ), 'align' => 'center' ) ); ?>
				<?php
				foreach ( $alph_pledges as $alph_i => &$alph_p ) {
					$alph_p['icon'] = array( 'leaf', 'package', 'users' )[ $alph_i % 3 ];
				}
				unset( $alph_p );
				alph_icon_cards( $alph_pledges, 3 );
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_trust = alph_lines( 'mc_trust' ); ?>
	<?php if ( $alph_trust ) : ?>
		<section class="section container"><?php alph_trust_bar( $alph_trust ); ?></section>
	<?php endif; ?>
</main>
<?php
get_footer();
