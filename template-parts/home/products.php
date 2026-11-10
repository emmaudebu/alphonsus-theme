<?php
/**
 * Homepage product row.
 *
 * @package Alphonsus
 *
 * @var array $args id, title, sub, cat, ids, link (link text) or note (status text).
 */

defined( 'ABSPATH' ) || exit;

$alph_products = alph_section_products( $args['cat'] ?? '', $args['ids'] ?? '' );
if ( ! $alph_products ) {
	return;
}
$alph_id = 'section-' . sanitize_html_class( $args['id'] );
?>
<section class="section product-section container" id="<?php echo esc_attr( $args['id'] ); ?>" aria-labelledby="<?php echo esc_attr( $alph_id ); ?>">
	<div class="section-head">
		<div>
			<h2 class="section-title" id="<?php echo esc_attr( $alph_id ); ?>"><?php echo alph_split_title( $args['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php if ( ! empty( $args['sub'] ) ) : ?>
				<p class="section-sub"><?php echo esc_html( $args['sub'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $args['link'] ) ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( alph_term_url( $args['cat'] ) ); ?>"><?php echo esc_html( $args['link'] ); ?><?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<?php elseif ( ! empty( $args['note'] ) ) : ?>
			<a class="status-note" href="<?php echo esc_url( alph_term_url( $args['cat'] ) ); ?>"><span class="dot" aria-hidden="true"></span><?php echo esc_html( $args['note'] ); ?></a>
		<?php endif; ?>
	</div>
	<div class="product-grid">
		<?php
		foreach ( $alph_products as $alph_product ) {
			alph_product_card( $alph_product );
		}
		?>
	</div>
</section>
