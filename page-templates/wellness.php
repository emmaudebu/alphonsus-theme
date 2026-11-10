<?php
/**
 * Template Name: Wellness
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alph_cat      = get_term_by( 'slug', 'wellness', 'product_cat' );
$alph_products = function_exists( 'alph_section_products' ) ? alph_section_products( 'wellness', '', 12 ) : array();
$alph_feature  = function_exists( 'wc_get_product' ) ? wc_get_product( (int) alph_mod( 'wl_feature' ) ) : null;
$alph_image    = (int) alph_mod( 'wl_image' );
if ( ! $alph_image && $alph_products ) {
	$alph_image = (int) reset( $alph_products )->get_image_id();
}
?>
<main id="main" class="site-main page-wellness">
	<?php
	alph_split_hero(
		array(
			'eyebrow' => alph_mod( 'wl_eyebrow' ),
			'title'   => alph_mod( 'wl_title' ),
			'text'    => alph_mod( 'wl_text' ),
			'buttons' => array( array( __( 'Explore the library', 'alphonsus' ), '#catalog', 'primary', 'arrow' ) ),
			'stats'   => alph_lines( 'wl_stats', true ),
			'image'   => $alph_image,
			'caption' => array(
				'label' => '',
				'title' => __( 'Discreet, respectful service', 'alphonsus' ),
				'text'  => __( 'Questions? Message us privately on WhatsApp or email.', 'alphonsus' ),
			),
		)
	);
	?>

	<?php if ( $alph_products ) : ?>
		<section class="section container" id="catalog">
			<?php
			alph_section_head(
				array(
					'eyebrow' => __( 'Alphonsus publication index', 'alphonsus' ),
					'title'   => __( 'Featured Wellness Books', 'alphonsus' ),
					'aside'   => $alph_cat ? '<a class="link-arrow" href="' . esc_url( get_term_link( $alph_cat ) ) . '">' . esc_html__( 'View all in the shop', 'alphonsus' ) . alph_icon( 'arrow', 15 ) . '</a>' : '',
				)
			);
			alph_product_grid( $alph_products, 4 );
			?>
		</section>
	<?php endif; ?>

	<?php if ( $alph_feature && $alph_feature->is_visible() ) : ?>
		<section class="section container"><?php alph_bundle_spotlight( $alph_feature ); ?></section>
	<?php endif; ?>

	<section class="section container">
		<div class="ethos">
			<div class="ethos__copy">
				<span class="chip chip--glass"><?php echo alph_icon( 'shield', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Our ethos', 'alphonsus' ); ?></span>
				<h2><?php echo esc_html( alph_mod( 'wl_ethos_title' ) ); ?></h2>
				<p><?php echo esc_html( alph_mod( 'wl_ethos_text' ) ); ?></p>
				<?php $alph_points = alph_items_from_lines( 'wl_ethos_points' ); ?>
				<?php if ( $alph_points ) : ?>
					<ul class="ethos__points">
						<?php foreach ( $alph_points as $alph_p ) : ?>
							<li><?php echo alph_icon( 'check', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><strong><?php echo esc_html( $alph_p['title'] ); ?></strong><?php echo esc_html( $alph_p['text'] ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php if ( alph_mod( 'wl_quote' ) ) : ?>
				<figure class="ethos__quote">
					<p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Curator’s personal pledge', 'alphonsus' ); ?></p>
					<blockquote>“<?php echo esc_html( alph_mod( 'wl_quote' ) ); ?>”</blockquote>
					<figcaption><span class="avatar">CA</span><span><strong><?php echo esc_html( alph_mod( 'ab_founder_name' ) ); ?></strong><small><?php echo esc_html( alph_mod( 'ab_founder_role' ) ); ?></small></span></figcaption>
				</figure>
			<?php endif; ?>
		</div>
	</section>

	<?php $alph_print = alph_lines( 'wl_printables' ); ?>
	<?php if ( $alph_print ) : ?>
		<section class="section container">
			<?php alph_section_head( array( 'eyebrow' => __( 'Open educational sanctuary', 'alphonsus' ), 'title' => __( 'Free Wellness Printables', 'alphonsus' ) ) ); ?>
			<div class="cards cards--3">
				<?php foreach ( $alph_print as $alph_i => $alph_line ) : ?>
					<?php $alph_d = array_pad( array_map( 'trim', explode( '|', $alph_line ) ), 3, '' ); ?>
					<article class="icard">
						<span class="icard__icon icard__icon--<?php echo esc_attr( $alph_i % 2 ? 'amber' : 'navy' ); ?>"><?php echo alph_icon( 'download', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<h3 class="icard__title"><?php echo esc_html( $alph_d[0] ); ?></h3>
						<p class="icard__text"><?php echo esc_html( $alph_d[1] ); ?></p>
						<?php if ( $alph_d[2] ) : ?>
							<a class="link-arrow" href="<?php echo esc_url( $alph_d[2] ); ?>" download><?php esc_html_e( 'Download', 'alphonsus' ); ?><?php echo alph_icon( 'download', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_quotes = alph_lines( 'wl_testimonials' ); ?>
	<?php if ( $alph_quotes ) : ?>
		<section class="band band--cream">
			<div class="container">
				<?php alph_section_head( array( 'eyebrow' => __( 'Community reflections', 'alphonsus' ), 'title' => __( 'Reader Words', 'alphonsus' ), 'align' => 'center' ) ); ?>
				<?php alph_testimonials( $alph_quotes ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_trust = alph_lines( 'wl_trust' ); ?>
	<?php if ( $alph_trust ) : ?>
		<section class="section container"><?php alph_trust_bar( $alph_trust ); ?></section>
	<?php endif; ?>
</main>
<?php
get_footer();
