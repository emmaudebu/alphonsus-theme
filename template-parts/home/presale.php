<?php
/**
 * Hardcover pre-sales block with pre-order form.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

$alph_percent = min( 100, max( 0, (int) alph_mod( 'presale_percent' ) ) );
$alph_icons   = array( 'pen', 'truck', 'bookmark', 'award' );
?>
<section class="section container" id="presale" aria-labelledby="presale-title">
	<div class="presale">
		<div class="presale__copy">
			<?php if ( alph_mod( 'presale_badge' ) ) : ?>
				<span class="chip chip--peach"><?php echo alph_icon( 'bookmark', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'presale_badge' ) ); ?></span>
			<?php endif; ?>
			<h2 id="presale-title" class="presale__title"><?php echo esc_html( alph_mod( 'presale_title' ) ); ?></h2>
			<p><?php echo esc_html( alph_mod( 'presale_text' ) ); ?></p>

			<?php if ( alph_lines( 'presale_points' ) ) : ?>
				<ul class="perks">
					<?php foreach ( alph_lines( 'presale_points' ) as $alph_i => $alph_point ) : ?>
						<li><span class="perks__icon"><?php echo alph_icon( $alph_icons[ $alph_i % 4 ], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $alph_point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $alph_percent ) : ?>
				<div class="progress">
					<div class="progress__labels">
						<span><?php esc_html_e( 'Pre-order allocation', 'alphonsus' ); ?></span>
						<?php /* translators: %d: percentage. */ ?>
						<strong><?php echo esc_html( sprintf( __( '%d%% Reserved', 'alphonsus' ), $alph_percent ) ); ?></strong>
					</div>
					<div class="progress__bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $alph_percent ); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php esc_attr_e( 'Pre-order allocation reserved', 'alphonsus' ); ?>">
						<span style="width:<?php echo esc_attr( $alph_percent ); ?>%"></span>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="presale__form">
			<?php get_template_part( 'template-parts/form', 'preorder' ); ?>
		</div>
	</div>
</section>
