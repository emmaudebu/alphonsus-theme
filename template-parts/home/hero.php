<?php
/**
 * Homepage hero, stats and feature strip.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

$alph_end      = trim( (string) alph_mod( 'hero_offer_end' ) );
$alph_end_ts   = $alph_end ? strtotime( get_gmt_from_date( $alph_end ) . ' UTC' ) : 0;
$alph_image_id = (int) alph_mod( 'hero_image' );
$alph_tones    = array( 'navy', 'brown', 'green' );
$alph_f_icons  = array( 'users', 'award', 'cap', 'tag' );
?>
<section class="hero container" aria-labelledby="hero-title">
	<div class="hero__card">
		<div class="hero__top">
			<?php if ( alph_mod( 'hero_badge' ) ) : ?>
				<span class="chip chip--sand"><?php echo alph_icon( 'sparkles', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'hero_badge' ) ); ?></span>
			<?php endif; ?>
			<?php if ( $alph_end_ts && $alph_end_ts > time() ) : ?>
				<span class="chip chip--red" data-countdown="<?php echo esc_attr( $alph_end_ts ); ?>">
					<?php echo alph_icon( 'clock', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo esc_html( alph_mod( 'hero_offer' ) ); ?> <strong data-countdown-text></strong>
				</span>
			<?php endif; ?>
		</div>

		<div class="hero__grid">
			<div class="hero__copy">
				<?php if ( alph_mod( 'hero_tagline' ) ) : ?>
					<p class="hero__tagline"><?php echo esc_html( alph_mod( 'hero_tagline' ) ); ?></p>
				<?php endif; ?>
				<h1 id="hero-title" class="hero__title"><?php echo esc_html( alph_mod( 'hero_title' ) ); ?></h1>
				<p class="hero__text"><?php echo esc_html( alph_mod( 'hero_text' ) ); ?></p>

				<div class="hero__actions">
					<?php if ( alph_mod( 'hero_btn1' ) ) : ?>
						<a class="btn btn--primary" href="<?php echo esc_url( alph_url( 'hero_btn1_url' ) ); ?>">
							<?php echo esc_html( alph_mod( 'hero_btn1' ) ); ?>
							<?php if ( alph_mod( 'hero_btn1_price' ) ) : ?>
								<span class="btn__chip"><?php echo esc_html( alph_mod( 'hero_btn1_price' ) ); ?></span>
							<?php endif; ?>
							<?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</a>
					<?php endif; ?>
					<?php if ( alph_mod( 'hero_btn2' ) ) : ?>
						<a class="btn btn--ghost" href="<?php echo esc_url( alph_url( 'hero_btn2_url' ) ); ?>">
							<?php echo alph_icon( 'book', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'hero_btn2' ) ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php $alph_stats = alph_lines( 'stats', true ); ?>
				<?php if ( $alph_stats ) : ?>
					<dl class="stats">
						<?php foreach ( $alph_stats as $alph_i => $alph_stat ) : ?>
							<div class="stat stat--<?php echo esc_attr( $alph_tones[ $alph_i % 3 ] ); ?>">
								<dt><?php echo esc_html( $alph_stat[1] ); ?></dt>
								<dd><?php echo esc_html( $alph_stat[0] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<figure class="hero__media">
				<div class="hero__frame">
					<?php
					if ( $alph_image_id ) {
						echo wp_get_attachment_image(
							$alph_image_id,
							'large',
							false,
							array(
								'loading'       => 'eager',
								'fetchpriority' => 'high',
								'sizes'         => '(max-width: 900px) 92vw, 460px',
							)
						);
					} else {
						echo '<div class="hero__placeholder">' . alph_icon( 'book', 64 ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</div>
				<?php if ( alph_mod( 'hero_card_title' ) ) : ?>
					<figcaption class="hero__caption">
						<span class="hero__caption-icon"><?php echo alph_icon( 'gift', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span><strong><?php echo esc_html( alph_mod( 'hero_card_title' ) ); ?></strong><?php echo esc_html( alph_mod( 'hero_card_text' ) ); ?></span>
					</figcaption>
				<?php endif; ?>
			</figure>
		</div>

		<?php $alph_features = alph_lines( 'features', true ); ?>
		<?php if ( $alph_features ) : ?>
			<ul class="features">
				<?php foreach ( $alph_features as $alph_i => $alph_feature ) : ?>
					<li class="feature">
						<span class="feature__icon feature__icon--<?php echo esc_attr( $alph_i % 4 ); ?>"><?php echo alph_icon( $alph_f_icons[ $alph_i % 4 ], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span><strong><?php echo esc_html( $alph_feature[0] ); ?></strong><small><?php echo esc_html( $alph_feature[1] ); ?></small></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
