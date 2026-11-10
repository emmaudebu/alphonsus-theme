<?php
/**
 * Template Name: Foundation
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alph_tiers = alph_lines( 'fd_tiers', false );
?>
<main id="main" class="site-main page-foundation">
	<?php
	alph_split_hero(
		array(
			'badge'   => alph_mod( 'fd_badge' ),
			'title'   => alph_mod( 'fd_title' ),
			'text'    => alph_mod( 'fd_text' ),
			'buttons' => array(
				array( alph_mod( 'fd_btn1' ), '#donate', 'amber', 'heart' ),
				alph_lines( 'fd_pillars' ) ? array( alph_mod( 'fd_btn2' ), '#pillars', 'ghost' ) : array( __( 'Gift books or your time', 'alphonsus' ), '#volunteer', 'ghost' ),
			),
			'stats'   => alph_lines( 'fd_stats', true ),
			'image'   => (int) alph_mod( 'fd_image' ),
			'caption' => array(
				'label' => alph_mod( 'fd_caption_label' ),
				'title' => alph_mod( 'fd_caption_title' ),
				'text'  => alph_mod( 'fd_caption_text' ),
			),
		)
	);
	?>

	<?php if ( function_exists( 'alph_donation_form' ) ) : ?>
		<section class="section container" id="donate" aria-labelledby="donate-title">
			<div class="panel panel--lift donate">
				<?php
				alph_section_head(
					array(
						'eyebrow' => alph_mod( 'fd_don_eyebrow' ),
						'title'   => alph_mod( 'fd_don_title' ),
						'text'    => alph_mod( 'fd_don_text' ),
						'id'      => 'donate-title',
						'align'   => 'left',
					)
				);
				alph_donation_form( $alph_tiers );
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_pillars = alph_lines( 'fd_pillars' ); ?>
	<?php if ( $alph_pillars ) : ?>
		<section class="band band--cream" id="pillars">
			<div class="container">
				<?php
				alph_section_head(
					array(
						'eyebrow' => alph_mod( 'fd_pillars_eyebrow' ),
						'title'   => alph_mod( 'fd_pillars_title' ),
						'text'    => alph_mod( 'fd_pillars_text' ),
					)
				);
				?>
				<div class="cards cards--3">
					<?php foreach ( $alph_pillars as $alph_i => $alph_line ) : ?>
						<?php $alph_p = array_map( 'trim', explode( '|', $alph_line ) ); ?>
						<article class="pcard">
							<?php if ( ! empty( $alph_p[2] ) && wp_attachment_is_image( (int) $alph_p[2] ) ) : ?>
								<div class="pcard__media"><?php echo wp_get_attachment_image( (int) $alph_p[2], 'alph-wide', false, array( 'loading' => 'lazy' ) ); ?></div>
							<?php endif; ?>
							<p class="pcard__kicker"><?php echo esc_html( sprintf( /* translators: %d: pillar number */ __( 'Pillar %02d', 'alphonsus' ), $alph_i + 1 ) ); ?></p>
							<h3><?php echo esc_html( $alph_p[0] ); ?></h3>
							<p><?php echo esc_html( $alph_p[1] ?? '' ); ?></p>
							<?php if ( ! empty( $alph_p[3] ) ) : ?>
								<div class="pcard__note pcard__note--<?php echo esc_attr( $alph_i % 2 ? 'gold' : 'blue' ); ?>"><strong><?php esc_html_e( 'Impact', 'alphonsus' ); ?></strong><?php echo esc_html( $alph_p[3] ); ?></div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_hubs = alph_lines( 'fd_hubs', false ); ?>
	<?php if ( $alph_hubs ) : ?>
		<section class="section container">
			<?php alph_section_head( array( 'eyebrow' => __( 'Footprint & deployment', 'alphonsus' ), 'title' => __( 'Our Impact Hubs', 'alphonsus' ), 'align' => 'left' ) ); ?>
			<div class="hubs">
				<ul class="hubs__list">
					<?php foreach ( $alph_hubs as $alph_i => $alph_line ) : ?>
						<?php $alph_h = array_map( 'trim', explode( '|', $alph_line ) ); ?>
						<li class="hub">
							<span class="hub__num"><?php echo esc_html( sprintf( '%02d', $alph_i + 1 ) ); ?></span>
							<div><h3><?php echo esc_html( $alph_h[0] ); ?></h3><p class="hub__loc"><?php echo esc_html( $alph_h[1] ?? '' ); ?></p><p><?php echo esc_html( $alph_h[2] ?? '' ); ?></p></div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="hubs__map card">
					<?php
					$alph_first = array_map( 'trim', explode( '|', $alph_hubs[0] ) );
					echo alph_map_embed( $alph_first[1] ?? $alph_first[0], $alph_first[0] ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$alph_alloc = array();
	foreach ( alph_lines( 'fd_alloc' ) as $alph_line ) {
		$alph_parts   = array_map( 'trim', explode( '|', $alph_line ) );
		$alph_alloc[] = array( $alph_parts[0], min( 100, max( 0, (int) ( $alph_parts[1] ?? 0 ) ) ), $alph_parts[2] ?? '' );
	}
	?>
	<?php if ( $alph_alloc ) : ?>
		<section class="band band--cream">
			<div class="container alloc">
				<div>
					<?php alph_section_head( array( 'eyebrow' => __( 'Transparency', 'alphonsus' ), 'title' => __( 'Where Every Kobo Journeys', 'alphonsus' ), 'align' => 'left' ) ); ?>
					<?php foreach ( $alph_alloc as $alph_i => $alph_a ) : ?>
						<div class="meter meter--<?php echo esc_attr( $alph_i % 3 ); ?>">
							<div class="meter__head"><strong><?php echo esc_html( $alph_a[0] ); ?></strong><span><?php echo esc_html( $alph_a[1] ); ?>%</span></div>
							<div class="meter__bar"><span style="width:<?php echo esc_attr( $alph_a[1] ); ?>%"></span></div>
							<?php if ( $alph_a[2] ) : ?>
								<p><?php echo esc_html( $alph_a[2] ); ?></p>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
					<?php if ( alph_mod( 'fd_alloc_report' ) ) : ?>
						<a class="link-arrow" href="<?php echo esc_url( alph_mod( 'fd_alloc_report' ) ); ?>"><?php echo alph_icon( 'download', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Download the financial statement', 'alphonsus' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="card alloc__chart">
					<h3><?php esc_html_e( 'Fund allocation', 'alphonsus' ); ?></h3>
					<?php echo alph_donut_svg( $alph_alloc, $alph_alloc[0][1] . '%', $alph_alloc[0][0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_partners = alph_lines( 'fd_partners' ); ?>
	<?php if ( $alph_partners ) : ?>
		<section class="section container partners">
			<p class="eyebrow center"><?php esc_html_e( 'Institutional sponsors & literacy alliances', 'alphonsus' ); ?></p>
			<ul class="partners__list">
				<?php foreach ( $alph_partners as $alph_partner ) : ?>
					<li><?php echo alph_icon( 'school', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $alph_partner ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php $alph_champs = alph_lines( 'fd_champions' ); ?>
	<?php if ( $alph_champs ) : ?>
		<section class="band band--cream">
			<div class="container">
				<?php alph_section_head( array( 'eyebrow' => __( 'Pillars of generosity', 'alphonsus' ), 'title' => __( 'Hall of Foundation Champions', 'alphonsus' ), 'text' => __( 'Honouring families, literary patrons and diaspora groups whose generosity builds lifelong readers.', 'alphonsus' ) ) ); ?>
				<div class="cards cards--4">
					<?php foreach ( $alph_champs as $alph_line ) : ?>
						<?php
						$alph_c   = array_map( 'trim', explode( '|', $alph_line ) );
						$alph_ini = implode( '', array_map( fn( $w ) => mb_substr( $w, 0, 1 ), array_slice( preg_split( '/\s+/', $alph_c[0] ), 0, 2 ) ) );
						?>
						<article class="champ">
							<div class="champ__head"><span class="avatar"><?php echo esc_html( $alph_ini ); ?></span><div><h3><?php echo esc_html( $alph_c[0] ); ?></h3><p><?php echo esc_html( $alph_c[1] ?? '' ); ?></p></div></div>
							<?php if ( ! empty( $alph_c[2] ) ) : ?>
								<blockquote><?php echo esc_html( $alph_c[2] ); ?></blockquote>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section container" id="volunteer" aria-labelledby="volunteer-title">
		<div class="panel panel--lift split-form">
			<div class="split-form__copy">
				<p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Hands-on philanthropy', 'alphonsus' ); ?></p>
				<h2 id="volunteer-title" class="shead__title"><?php echo esc_html( alph_mod( 'fd_gift_title' ) ); ?></h2>
				<p class="shead__text"><?php echo esc_html( alph_mod( 'fd_gift_text' ) ); ?></p>
				<?php foreach ( alph_items_from_lines( 'fd_gift_points' ) as $alph_i => $alph_item ) : ?>
					<div class="mini">
						<span class="mini__icon"><?php echo alph_icon( $alph_i ? 'users' : 'truck', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div><strong><?php echo esc_html( $alph_item['title'] ); ?></strong><p><?php echo esc_html( $alph_item['text'] ); ?></p></div>
					</div>
				<?php endforeach; ?>
				<?php if ( alph_mod( 'fd_gift_rules' ) ) : ?>
					<div class="note-box"><strong><?php esc_html_e( 'Acceptance guidelines', 'alphonsus' ); ?></strong><p><?php echo esc_html( alph_mod( 'fd_gift_rules' ) ); ?></p></div>
				<?php endif; ?>
			</div>
			<div class="split-form__form"><?php get_template_part( 'template-parts/form', 'volunteer' ); ?></div>
		</div>
	</section>
</main>
<?php
get_footer();
