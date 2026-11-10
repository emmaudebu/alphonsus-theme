<?php
/**
 * Site footer.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;
?>

<?php get_template_part( 'template-parts/newsletter' ); ?>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="footer-brand">
			<?php alph_logo( 'site-logo site-logo--footer' ); ?>
			<p><?php echo esc_html( alph_mod( 'footer_about' ) ); ?></p>
			<?php alph_social_links( 'social' ); ?>
		</div>
		<?php
		alph_footer_menu( 'footer-1' );
		alph_footer_menu( 'footer-2' );
		alph_footer_menu( 'footer-3' );
		?>
	</div>
	<div class="container site-footer__bottom">
		<div class="payments">
			<span><?php esc_html_e( 'Secure Payments:', 'alphonsus' ); ?></span>
			<ul>
				<?php foreach ( alph_lines( 'payments' ) as $alph_pay ) : ?>
					<li><?php echo esc_html( $alph_pay ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="copyright">
			<?php
			/* translators: 1: year, 2: site name. */
			echo esc_html( sprintf( __( '© %1$s %2$s. All rights reserved.', 'alphonsus' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) );
			?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'secondary',
					'container'      => false,
					'menu_class'     => 'legal-links',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</div>
</footer>

<div class="toast" role="status" aria-live="polite" hidden></div>

<?php wp_footer(); ?>
</body>
</html>
