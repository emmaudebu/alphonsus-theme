<?php
/**
 * Contact form ([alphonsus_contact_form] and the Contact page).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="form form--card" id="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Write to us', 'alphonsus' ); ?></p>
	<h2 class="form__title"><?php esc_html_e( 'Send a Message to Our Team', 'alphonsus' ); ?></h2>
	<p class="form__hint"><?php esc_html_e( 'Tell us about your order, the books you’re looking for, your Swedish level or a foundation proposal. We read every message.', 'alphonsus' ); ?></p>

	<?php alph_form_notice( 'contact' ); ?>
	<?php alph_form_hidden( 'contact' ); ?>

	<div class="form__row">
		<?php alph_field( 'contact', 'first_name', array( 'placeholder' => __( 'e.g. Folake', 'alphonsus' ), 'autocomplete' => 'given-name' ) ); ?>
		<?php alph_field( 'contact', 'last_name', array( 'placeholder' => __( 'e.g. Lindqvist', 'alphonsus' ), 'autocomplete' => 'family-name' ) ); ?>
	</div>
	<div class="form__row">
		<?php alph_field( 'contact', 'email', array( 'placeholder' => 'name@example.com', 'autocomplete' => 'email' ) ); ?>
		<?php alph_field( 'contact', 'phone', array( 'placeholder' => '+234 800 000 0000', 'autocomplete' => 'tel', 'input' => 'tel' ) ); ?>
	</div>
	<?php alph_field( 'contact', 'subject', array( 'placeholder' => __( 'Select a topic', 'alphonsus' ) ) ); ?>
	<?php alph_field( 'contact', 'message', array( 'placeholder' => __( 'Book requests, student age group, Swedish level, foundation proposals…', 'alphonsus' ) ) ); ?>
	<?php alph_field( 'contact', 'channel', array( 'radios' => true ) ); ?>

	<div class="form__foot">
		<button type="submit" class="btn btn--primary"><?php echo alph_icon( 'send', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Send message', 'alphonsus' ); ?></span></button>
		<span class="form__secure"><?php echo alph_icon( 'shield', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Your details are only used to reply to you.', 'alphonsus' ); ?></span>
	</div>
</form>
