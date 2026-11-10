<?php
/**
 * Foundation: gift books or time.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<?php alph_form_notice( 'volunteer' ); ?>
	<?php alph_form_hidden( 'volunteer' ); ?>
	<div class="form__row">
		<?php alph_field( 'volunteer', 'name', array( 'placeholder' => __( 'e.g. Folake Solarin', 'alphonsus' ), 'autocomplete' => 'name' ) ); ?>
		<?php alph_field( 'volunteer', 'email', array( 'placeholder' => 'name@example.com', 'autocomplete' => 'email' ) ); ?>
	</div>
	<div class="form__row">
		<?php alph_field( 'volunteer', 'type', array( 'placeholder' => __( 'Choose one', 'alphonsus' ) ) ); ?>
		<?php alph_field( 'volunteer', 'location', array( 'placeholder' => __( 'Lagos, Ibadan, Accra, Stockholm…', 'alphonsus' ), 'autocomplete' => 'address-level2' ) ); ?>
	</div>
	<?php alph_field( 'volunteer', 'message', array( 'rows' => 4, 'placeholder' => __( 'Tell us about the books you’d like to donate or your availability for reading sessions.', 'alphonsus' ) ) ); ?>
	<div class="form__foot">
		<button type="submit" class="btn btn--primary"><span><?php esc_html_e( 'Submit', 'alphonsus' ); ?></span><?php echo alph_icon( 'send', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	</div>
</form>
