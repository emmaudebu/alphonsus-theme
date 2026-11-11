<?php
/**
 * Forgot password (overrides WooCommerce's myaccount/form-lost-password.php).
 *
 * @package Alphonsus
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );

alph_auth_open( array( 'tabs' => false ) );
?>
<div class="auth__head">
	<span class="auth__badge"><?php echo alph_icon( 'shield', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	<h2 class="auth__title"><?php esc_html_e( 'Forgot your password?', 'alphonsus' ); ?></h2>
	<p class="auth__sub"><?php echo esc_html( apply_filters( 'woocommerce_lost_password_message', __( 'Enter the email address or username on your account and we’ll send you a link to choose a new password.', 'alphonsus' ) ) ); ?></p>
</div>

<form method="post" class="form auth-form woocommerce-ResetPassword lost_reset_password">
	<p class="field">
		<label for="user_login"><?php esc_html_e( 'Email address or username', 'alphonsus' ); ?> <span class="req">*</span></label>
		<span class="input-icon"><?php echo alph_icon( 'mail', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><input type="text" name="user_login" id="user_login" autocomplete="username" placeholder="name@example.com" required aria-required="true"></span>
	</p>

	<?php do_action( 'woocommerce_lostpassword_form' ); ?>

	<input type="hidden" name="wc_reset_password" value="true">
	<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
	<button type="submit" class="btn btn--primary btn--block auth-form__submit" value="<?php esc_attr_e( 'Reset password', 'alphonsus' ); ?>"><span><?php esc_html_e( 'Send reset link', 'alphonsus' ); ?></span><?php echo alph_icon( 'send', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</form>

<p class="auth__switch"><a href="<?php echo esc_url( alph_auth_url( 'login' ) ); ?>">← <?php esc_html_e( 'Back to sign in', 'alphonsus' ); ?></a></p>
<?php
alph_auth_close();

do_action( 'woocommerce_after_lost_password_form' );
