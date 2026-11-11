<?php
/**
 * Reset link sent (overrides WooCommerce's myaccount/lost-password-confirmation.php).
 *
 * @package Alphonsus
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notice( esc_html__( 'Password reset email has been sent.', 'alphonsus' ) );

do_action( 'woocommerce_before_lost_password_confirmation_message' );

alph_auth_open( array( 'tabs' => false ) );
?>
<div class="auth__head auth__head--center">
	<span class="auth__badge auth__badge--green"><?php echo alph_icon( 'mail', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	<h2 class="auth__title"><?php esc_html_e( 'Check your inbox', 'alphonsus' ); ?></h2>
	<p class="auth__sub"><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.', 'alphonsus' ) ) ); ?></p>
</div>
<a class="btn btn--ghost btn--block" href="<?php echo esc_url( alph_auth_url( 'login' ) ); ?>"><?php esc_html_e( 'Back to sign in', 'alphonsus' ); ?></a>
<?php
alph_auth_close();

do_action( 'woocommerce_after_lost_password_confirmation_message' );
