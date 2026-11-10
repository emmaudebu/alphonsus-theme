<?php
/**
 * Template Name: Contact
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alph_phone = alph_mod( 'phone' );
$alph_wa    = $alph_phone ? 'https://wa.me/' . preg_replace( '/\D+/', '', $alph_phone ) : '';
$alph_icons = array( 'package', 'languages', 'heart', 'map-pin' );
?>
<main id="main" class="site-main page-contact">
	<?php
	alph_split_hero(
		array(
			'eyebrow' => alph_mod( 'ct_eyebrow' ),
			'title'   => alph_mod( 'ct_title' ),
			'text'    => alph_mod( 'ct_text' ),
			'buttons' => array(
				array( __( 'Send an inquiry', 'alphonsus' ), '#contact-form', 'primary', 'arrow' ),
				array( __( 'Chat on WhatsApp', 'alphonsus' ), $alph_wa, 'ghost', 'whatsapp' ),
			),
			'image'   => (int) alph_mod( 'ct_image' ),
		)
	);
	?>

	<?php $alph_depts = alph_lines( 'ct_depts' ); ?>
	<?php if ( $alph_depts ) : ?>
		<section class="section container">
			<div class="cards cards--<?php echo esc_attr( min( 4, count( $alph_depts ) ) ); ?>">
				<?php foreach ( $alph_depts as $alph_i => $alph_line ) : ?>
					<?php $alph_d = array_pad( array_map( 'trim', explode( '|', $alph_line ) ), 5, '' ); ?>
					<article class="icard dept">
						<span class="icard__icon icard__icon--<?php echo esc_attr( $alph_i % 2 ? 'amber' : 'navy' ); ?>"><?php echo alph_icon( $alph_icons[ $alph_i % 4 ], 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<h2 class="icard__title"><?php echo esc_html( $alph_d[0] ); ?></h2>
						<p class="icard__text"><?php echo esc_html( $alph_d[1] ); ?></p>
						<?php if ( $alph_d[2] || $alph_d[3] || $alph_d[4] ) : ?>
							<div class="dept__links">
								<?php if ( $alph_d[2] ) : ?>
									<a href="mailto:<?php echo esc_attr( $alph_d[2] ); ?>"><?php echo alph_icon( 'mail', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $alph_d[2] ); ?></a>
								<?php endif; ?>
								<?php if ( $alph_d[3] ) : ?>
									<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $alph_d[3] ) ); ?>"><?php echo alph_icon( 'phone', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $alph_d[3] ); ?></a>
								<?php endif; ?>
								<?php if ( $alph_d[4] ) : ?>
									<a class="link-arrow" href="<?php echo esc_url( alph_resolve_url( $alph_d[4] ) ); ?>"><?php esc_html_e( 'Learn more', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="section container contact-grid">
		<div><?php get_template_part( 'template-parts/form', 'contact' ); ?></div>
		<aside class="contact-side">
			<div class="card place">
				<?php echo alph_map_embed( alph_mod( 'address' ), alph_mod( 'ct_place' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="place__body">
					<p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Where to find us', 'alphonsus' ); ?></p>
					<h2 class="place__title"><?php echo esc_html( alph_mod( 'ct_place' ) ); ?></h2>
					<p class="place__addr"><?php echo alph_icon( 'map-pin', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'address' ) ); ?></p>
					<?php $alph_hours = alph_lines( 'ct_hours', true ); ?>
					<?php if ( $alph_hours ) : ?>
						<div class="hours">
							<p class="hours__title"><?php echo alph_icon( 'clock', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Opening hours', 'alphonsus' ); ?></p>
							<dl>
								<?php foreach ( $alph_hours as $alph_h ) : ?>
									<div><dt><?php echo esc_html( $alph_h[0] ); ?></dt><dd><?php echo esc_html( $alph_h[1] ); ?></dd></div>
								<?php endforeach; ?>
							</dl>
						</div>
					<?php endif; ?>
					<ul class="place__contacts">
						<?php if ( alph_mod( 'email' ) ) : ?>
							<li><?php echo alph_icon( 'mail', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="mailto:<?php echo esc_attr( alph_mod( 'email' ) ); ?>"><?php echo esc_html( alph_mod( 'email' ) ); ?></a></li>
						<?php endif; ?>
						<?php foreach ( array_merge( $alph_phone ? array( $alph_phone ) : array(), alph_lines( 'ct_phones' ) ) as $alph_num ) : ?>
							<li><?php echo alph_icon( 'phone', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $alph_num ) ); ?>"><?php echo esc_html( $alph_num ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</aside>
	</section>

	<?php if ( $alph_wa ) : ?>
		<section class="section container">
			<div class="wa-band">
				<span class="wa-band__icon"><?php echo alph_icon( 'whatsapp', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<div>
					<h2><?php esc_html_e( 'Quick questions? Chat with us on WhatsApp', 'alphonsus' ); ?></h2>
					<p><?php esc_html_e( 'Ask about book titles, Swedish classes or the status of your delivery — message us directly.', 'alphonsus' ); ?></p>
				</div>
				<a class="btn btn--amber" href="<?php echo esc_url( $alph_wa ); ?>" target="_blank" rel="noopener"><?php echo alph_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Open WhatsApp chat', 'alphonsus' ); ?></span></a>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_faq = alph_lines( 'ct_faq' ); ?>
	<?php if ( $alph_faq ) : ?>
		<section class="section container container--mid">
			<?php alph_section_head( array( 'eyebrow' => __( 'Good to know', 'alphonsus' ), 'title' => __( 'Frequently Asked Questions', 'alphonsus' ), 'text' => __( 'Clear answers about orders, delivery, refunds and digital books.', 'alphonsus' ), 'align' => 'center' ) ); ?>
			<?php alph_faq( $alph_faq ); ?>
		</section>
	<?php endif; ?>
</main>
<?php
get_footer();
