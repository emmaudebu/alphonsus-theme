<?php
/**
 * Contact form ([alphonsus_contact_form]).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="form form--card" id="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<h2 class="form__title"><?php esc_html_e( 'Send us a message', 'alphonsus' ); ?></h2>
	<p class="form__hint"><?php esc_html_e( 'Questions about an order, bulk school purchases or the Swedish programme? We’re happy to help.', 'alphonsus' ); ?></p>

	<?php alph_form_notice( 'contact' ); ?>
	<?php alph_form_hidden( 'contact' ); ?>

	<div class="form__row">
		<p class="field">
			<label for="ct-name"><?php esc_html_e( 'Your name', 'alphonsus' ); ?> <span class="req">*</span></label>
			<input id="ct-name" name="name" type="text" required autocomplete="name">
		</p>
		<p class="field">
			<label for="ct-email"><?php esc_html_e( 'Email address', 'alphonsus' ); ?> <span class="req">*</span></label>
			<input id="ct-email" name="email" type="email" required autocomplete="email">
		</p>
	</div>
	<div class="form__row">
		<p class="field">
			<label for="ct-phone"><?php esc_html_e( 'Phone (optional)', 'alphonsus' ); ?></label>
			<input id="ct-phone" name="phone" type="tel" autocomplete="tel">
		</p>
		<p class="field">
			<label for="ct-subject"><?php esc_html_e( 'Subject', 'alphonsus' ); ?></label>
			<input id="ct-subject" name="subject" type="text">
		</p>
	</div>
	<p class="field">
		<label for="ct-message"><?php esc_html_e( 'Message', 'alphonsus' ); ?> <span class="req">*</span></label>
		<textarea id="ct-message" name="message" rows="5" required></textarea>
	</p>
	<button type="submit" class="btn btn--primary"><?php echo alph_icon( 'send', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Send message', 'alphonsus' ); ?></button>
</form>
