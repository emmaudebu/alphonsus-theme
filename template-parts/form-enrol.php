<?php
/**
 * Swedish School enrolment request form.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<?php alph_form_notice( 'enrol' ); ?>
	<?php alph_form_hidden( 'enrol' ); ?>
	<div class="form__row">
		<?php alph_field( 'enrol', 'first_name', array( 'autocomplete' => 'given-name' ) ); ?>
		<?php alph_field( 'enrol', 'last_name', array( 'autocomplete' => 'family-name' ) ); ?>
	</div>
	<div class="form__row">
		<?php alph_field( 'enrol', 'email', array( 'placeholder' => 'name@example.com', 'autocomplete' => 'email' ) ); ?>
		<?php alph_field( 'enrol', 'phone', array( 'placeholder' => '+234 800 000 0000', 'autocomplete' => 'tel', 'input' => 'tel' ) ); ?>
	</div>
	<div class="form__row">
		<?php alph_field( 'enrol', 'track', array( 'placeholder' => __( 'Choose a level', 'alphonsus' ) ) ); ?>
		<?php alph_field( 'enrol', 'schedule', array( 'placeholder' => __( 'Any time works', 'alphonsus' ) ) ); ?>
	</div>
	<?php alph_field( 'enrol', 'message', array( 'rows' => 3, 'placeholder' => __( 'e.g. Relocating to Stockholm next year, need B1 for work', 'alphonsus' ) ) ); ?>
	<div class="form__foot">
		<button type="submit" class="btn btn--amber"><span><?php esc_html_e( 'Request enrolment', 'alphonsus' ); ?></span><?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		<span class="form__secure"><?php echo alph_icon( 'shield', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'No payment now — we confirm your seat and fees first.', 'alphonsus' ); ?></span>
	</div>
</form>
