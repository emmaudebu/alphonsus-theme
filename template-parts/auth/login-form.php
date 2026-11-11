<?php
/**
 * Sign-in form (posts to WooCommerce's login handler).
 *
 * @package Alphonsus
 *
 * @var array $args heading: h1|h2.
 */

defined( 'ABSPATH' ) || exit;

$alph_tag = ( $args['heading'] ?? 'h1' ) === 'h2' ? 'h2' : 'h1';
// phpcs:ignore WordPress.Security.NonceVerification -- repopulating the field after a failed attempt.
$alph_user = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
?>
<div class="auth__head">
	<<?php echo esc_html( $alph_tag ); ?> class="auth__title"><?php esc_html_e( 'Welcome back', 'alphonsus' ); ?></<?php echo esc_html( $alph_tag ); ?>>
	<p class="auth__sub"><?php esc_html_e( 'Sign in to track orders, continue your reading journey and see your wishlist.', 'alphonsus' ); ?></p>
</div>

<form class="form auth-form woocommerce-form woocommerce-form-login login" method="post">
	<?php do_action( 'woocommerce_login_form_start' ); ?>

	<p class="field">
		<label for="username"><?php esc_html_e( 'Email address or username', 'alphonsus' ); ?> <span class="req">*</span></label>
		<span class="input-icon"><?php echo alph_icon( 'mail', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><input type="text" name="username" id="username" autocomplete="username" value="<?php echo esc_attr( $alph_user ); ?>" placeholder="name@example.com" required></span>
	</p>

	<?php alph_password_field( 'password', 'password', __( 'Password', 'alphonsus' ), 'current-password' ); ?>

	<?php do_action( 'woocommerce_login_form' ); ?>

	<div class="auth-form__row">
		<label class="checkline"><input name="rememberme" type="checkbox" id="rememberme" value="forever"> <span><?php esc_html_e( 'Keep me signed in', 'alphonsus' ); ?></span></label>
		<a class="auth-form__link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot password?', 'alphonsus' ); ?></a>
	</div>

	<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
	<input type="hidden" name="redirect" value="<?php echo esc_url( alph_auth_redirect() ); ?>">
	<button type="submit" class="btn btn--primary btn--block auth-form__submit" name="login" value="<?php esc_attr_e( 'Log in', 'alphonsus' ); ?>"><span><?php esc_html_e( 'Sign in', 'alphonsus' ); ?></span><?php echo alph_icon( 'arrow', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>

	<?php do_action( 'woocommerce_login_form_end' ); ?>
</form>

<?php if ( alph_registration_open() ) : ?>
	<p class="auth__switch"><?php esc_html_e( 'New to Alphonsus Library?', 'alphonsus' ); ?> <a href="<?php echo esc_url( alph_auth_url( 'register' ) ); ?>"><?php esc_html_e( 'Create a free account', 'alphonsus' ); ?></a></p>
<?php endif; ?>
