<?php
/**
 * Template Name: About
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alph_contact = get_page_by_path( 'contact' );
$alph_contact = $alph_contact ? get_permalink( $alph_contact ) : home_url( '/contact/' );
$alph_shop    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<main id="main" class="site-main page-about">
	<?php
	alph_split_hero(
		array(
			'eyebrow' => alph_mod( 'ab_badge' ),
			'title'   => alph_mod( 'ab_title' ),
			'text'    => alph_mod( 'ab_text' ),
			'buttons' => array(
				array( __( 'Meet our founder', 'alphonsus' ), '#founder', 'primary', 'arrow' ),
				array( __( 'Explore our pillars', 'alphonsus' ), '#pillars', 'ghost' ),
			),
			'stats'   => alph_lines( 'ab_stats', true ),
			'image'   => (int) alph_mod( 'ab_image' ),
		)
	);
	?>

	<section class="section container story">
		<?php $alph_story_imgs = alph_image_ids( 'ab_story_images' ); ?>
		<?php if ( $alph_story_imgs ) : ?>
			<div class="story__media story__media--<?php echo esc_attr( min( 2, count( $alph_story_imgs ) ) ); ?>">
				<?php foreach ( array_slice( $alph_story_imgs, 0, 2 ) as $alph_img ) : ?>
					<figure><?php echo wp_get_attachment_image( $alph_img, 'large', false, array( 'loading' => 'lazy' ) ); ?></figure>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="story__copy<?php echo $alph_story_imgs ? '' : ' story__copy--solo'; ?>">
			<p class="eyebrow eyebrow--amber"><?php echo esc_html( alph_mod( 'ab_story_eyebrow' ) ); ?></p>
			<h2 class="shead__title"><?php echo esc_html( alph_mod( 'ab_story_title' ) ); ?></h2>
			<div class="prose"><?php echo alph_paragraphs( 'ab_story_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<a class="note-row" href="<?php echo esc_url( home_url( '/swedish-school/' ) ); ?>">
				<span class="note-row__icon"><?php echo alph_icon( 'languages', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span><strong><?php esc_html_e( 'Swedish School', 'alphonsus' ); ?></strong><small><?php esc_html_e( 'Structured A1–B1 learning with books written for African and international learners.', 'alphonsus' ); ?></small></span>
				<?php echo alph_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</div>
	</section>

	<?php $alph_pillars = alph_items_from_lines( 'ab_pillars' ); ?>
	<?php if ( $alph_pillars ) : ?>
		<section class="band band--blue" id="pillars">
			<div class="container">
				<?php alph_section_head( array( 'eyebrow' => __( 'The foundations', 'alphonsus' ), 'title' => __( 'Our Core Pillars of Learning', 'alphonsus' ), 'text' => __( 'Every book catalogued, lesson delivered and community corner built is guided by three enduring convictions.', 'alphonsus' ), 'align' => 'center' ) ); ?>
				<?php
				foreach ( $alph_pillars as $alph_i => &$alph_p ) {
					/* translators: %d: pillar number */
					$alph_p['kicker'] = sprintf( __( 'Pillar %02d', 'alphonsus' ), $alph_i + 1 );
					$alph_p['icon']   = array( 'users', 'languages', 'book-open' )[ $alph_i % 3 ];
				}
				unset( $alph_p );
				alph_icon_cards( $alph_pillars, 3 );
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_impact = alph_lines( 'ab_impact' ); ?>
	<?php if ( $alph_impact ) : ?>
		<section class="section container">
			<div class="panel panel--lift impact">
				<div class="impact__intro">
					<p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Verified metrics', 'alphonsus' ); ?></p>
					<h2 class="shead__title"><?php esc_html_e( 'Our Impact in Numbers', 'alphonsus' ); ?></h2>
				</div>
				<div class="impact__grid">
					<?php foreach ( $alph_impact as $alph_i => $alph_line ) : ?>
						<?php $alph_n = array_map( 'trim', explode( '|', $alph_line ) ); ?>
						<div class="ncard<?php echo 3 === $alph_i ? ' ncard--gold' : ''; ?>">
							<strong><?php echo esc_html( $alph_n[0] ); ?></strong>
							<span><?php echo esc_html( $alph_n[1] ?? '' ); ?></span>
							<?php if ( ! empty( $alph_n[2] ) ) : ?>
								<p><?php echo esc_html( $alph_n[2] ); ?></p>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="band band--cream" id="founder">
		<div class="container founder">
			<?php $alph_photo = (int) alph_mod( 'ab_founder_image' ); ?>
			<?php if ( $alph_photo && wp_attachment_is_image( $alph_photo ) ) : ?>
				<figure class="founder__photo">
					<?php echo wp_get_attachment_image( $alph_photo, 'large', false, array( 'loading' => 'lazy', 'alt' => alph_mod( 'ab_founder_name' ) ) ); ?>
					<figcaption><strong><?php echo esc_html( alph_mod( 'ab_founder_name' ) ); ?></strong><span><?php echo esc_html( alph_mod( 'ab_founder_role' ) ); ?></span></figcaption>
				</figure>
			<?php endif; ?>
			<div class="founder__copy">
				<span class="chip chip--lilac"><?php echo alph_icon( 'pen', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Founder’s philosophy', 'alphonsus' ); ?></span>
				<?php if ( alph_mod( 'ab_founder_quote' ) ) : ?>
					<blockquote class="founder__quote">“<?php echo esc_html( alph_mod( 'ab_founder_quote' ) ); ?>”</blockquote>
					<p class="founder__by"><?php echo esc_html( alph_mod( 'ab_founder_name' ) . ' • ' . alph_mod( 'ab_founder_role' ) ); ?></p>
				<?php else : ?>
					<h2 class="shead__title"><?php echo esc_html( alph_mod( 'ab_founder_name' ) ); ?></h2>
					<p class="founder__by"><?php echo esc_html( alph_mod( 'ab_founder_role' ) ); ?></p>
				<?php endif; ?>
				<div class="prose"><?php echo alph_paragraphs( 'ab_founder_bio' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<div class="founder__actions">
					<?php echo alph_button( __( 'Explore her books', 'alphonsus' ), $alph_shop, 'primary', 'book-open' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo alph_button( __( 'Contact Chika', 'alphonsus' ), $alph_contact, 'ghost' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</section>

	<?php $alph_timeline = alph_lines( 'ab_timeline' ); ?>
	<?php if ( $alph_timeline ) : ?>
		<section class="section container">
			<?php alph_section_head( array( 'eyebrow' => __( 'The tradition', 'alphonsus' ), 'title' => __( 'Our Journey & Milestones', 'alphonsus' ), 'align' => 'center' ) ); ?>
			<?php alph_timeline( $alph_timeline ); ?>
		</section>
	<?php endif; ?>

	<?php $alph_gallery = alph_image_ids( 'ab_gallery' ); ?>
	<?php if ( $alph_gallery ) : ?>
		<section class="section container">
			<?php alph_section_head( array( 'eyebrow' => __( 'Moments of wonder', 'alphonsus' ), 'title' => __( 'Life Inside Alphonsus', 'alphonsus' ) ) ); ?>
			<?php alph_gallery( $alph_gallery ); ?>
		</section>
	<?php endif; ?>

	<?php
	alph_cta_band(
		__( 'Join our educational future', 'alphonsus' ),
		alph_mod( 'ab_cta_title' ),
		alph_mod( 'ab_cta_text' ),
		array(
			array( __( 'Start a partnership', 'alphonsus' ), $alph_contact, 'amber', 'arrow' ),
			array( __( 'Sponsor a reading corner', 'alphonsus' ), alph_url( 'donate_url' ), 'light' ),
		)
	);
	?>
</main>
<?php
get_footer();
