<?php
/**
 * Newsletter band above the footer.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="newsletter" id="newsletter" aria-labelledby="newsletter-title">
	<div class="container newsletter__inner">
		<div>
			<?php if ( alph_mod( 'newsletter_eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( alph_mod( 'newsletter_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h2 id="newsletter-title" class="newsletter__title"><?php echo esc_html( alph_mod( 'newsletter_title' ) ); ?></h2>
			<p class="newsletter__text"><?php echo esc_html( alph_mod( 'newsletter_text' ) ); ?></p>
		</div>
		<form class="newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php alph_form_hidden( 'newsletter' ); ?>
			<div class="newsletter__field">
				<label class="screen-reader-text" for="nl-email"><?php esc_html_e( 'Email address', 'alphonsus' ); ?></label>
				<input id="nl-email" type="email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'Enter your email address…', 'alphonsus' ); ?>">
				<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Subscribe Now', 'alphonsus' ); ?></button>
			</div>
			<?php alph_form_notice( 'newsletter' ); ?>
			<p class="newsletter__fine"><?php esc_html_e( 'We respect your inbox and you can unsubscribe at any time.', 'alphonsus' ); ?></p>
		</form>
	</div>
</section>
