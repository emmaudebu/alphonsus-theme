<?php
/**
 * Choose a new password (overrides WooCommerce's myaccount/form-reset-password.php).
 *
 * @package Alphonsus
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );

alph_auth_open( array( 'tabs' => false ) );
?>
<div class="auth__head">
	<span class="auth__badge"><?php echo alph_icon( 'shield', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	<h2 class="auth__title"><?php esc_html_e( 'Choose a new password', 'alphonsus' ); ?></h2>
	<p class="auth__sub"><?php echo esc_html( apply_filters( 'woocommerce_reset_password_message', __( 'Pick something you haven’t used before — at least 8 characters.', 'alphonsus' ) ) ); ?></p>
</div>

<form method="post" class="form auth-form woocommerce-ResetPassword lost_reset_password">
	<?php alph_password_field( 'password_1', 'password_1', __( 'New password', 'alphonsus' ), 'new-password' ); ?>
	<?php alph_password_field( 'password_2', 'password_2', __( 'Repeat new password', 'alphonsus' ), 'new-password' ); ?>

	<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>">
	<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>">

	<?php do_action( 'woocommerce_resetpassword_form' ); ?>

	<input type="hidden" name="wc_reset_password" value="true">
	<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
	<button type="submit" class="btn btn--primary btn--block auth-form__submit" value="<?php esc_attr_e( 'Save', 'alphonsus' ); ?>"><span><?php esc_html_e( 'Save new password', 'alphonsus' ); ?></span><?php echo alph_icon( 'check', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</form>
<?php
alph_auth_close();

do_action( 'woocommerce_after_reset_password_form' );
