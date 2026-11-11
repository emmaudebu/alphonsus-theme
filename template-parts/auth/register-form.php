<?php
/**
 * Registration form (posts to WooCommerce's registration handler).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification -- repopulating fields after a failed attempt.
$alph_first = isset( $_POST['alph_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['alph_first_name'] ) ) : '';
$alph_email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
$alph_login = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
$alph_news  = ! empty( $_POST['alph_newsletter'] );
// phpcs:enable
?>
<div class="auth__head">
	<h1 class="auth__title"><?php esc_html_e( 'Create your account', 'alphonsus' ); ?></h1>
	<p class="auth__sub"><?php esc_html_e( 'It’s free and takes less than a minute.', 'alphonsus' ); ?></p>
</div>

<?php if ( ! alph_registration_open() ) : ?>
	<div class="notice notice--error" role="status"><?php esc_html_e( 'New account registration is currently closed. Please check back soon.', 'alphonsus' ); ?></div>
	<p class="auth__switch"><?php esc_html_e( 'Already have an account?', 'alphonsus' ); ?> <a href="<?php echo esc_url( alph_auth_url( 'login' ) ); ?>"><?php esc_html_e( 'Sign in', 'alphonsus' ); ?></a></p>
	<?php
	return;
endif;
?>

<form class="form auth-form woocommerce-form woocommerce-form-register register" method="post" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
	<?php do_action( 'woocommerce_register_form_start' ); ?>

	<p class="field">
		<label for="reg_first_name"><?php esc_html_e( 'First name', 'alphonsus' ); ?> <span class="req">*</span></label>
		<span class="input-icon"><?php echo alph_icon( 'user', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><input type="text" name="alph_first_name" id="reg_first_name" autocomplete="given-name" value="<?php echo esc_attr( $alph_first ); ?>" placeholder="<?php esc_attr_e( 'e.g. Adaeze', 'alphonsus' ); ?>" required></span>
	</p>

	<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
		<p class="field">
			<label for="reg_username"><?php esc_html_e( 'Username', 'alphonsus' ); ?> <span class="req">*</span></label>
			<span class="input-icon"><?php echo alph_icon( 'user', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><input type="text" name="username" id="reg_username" autocomplete="username" value="<?php echo esc_attr( $alph_login ); ?>" required></span>
		</p>
	<?php endif; ?>

	<p class="field">
		<label for="reg_email"><?php esc_html_e( 'Email address', 'alphonsus' ); ?> <span class="req">*</span></label>
		<span class="input-icon"><?php echo alph_icon( 'mail', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><input type="email" name="email" id="reg_email" autocomplete="email" value="<?php echo esc_attr( $alph_email ); ?>" placeholder="name@example.com" required></span>
	</p>

	<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
		<?php alph_password_field( 'reg_password', 'password', __( 'Create a password', 'alphonsus' ), 'new-password', 'minlength="8"' ); ?>
		<p class="field__hint"><?php esc_html_e( 'Use at least 8 characters with a mix of letters, numbers and symbols.', 'alphonsus' ); ?></p>
	<?php else : ?>
		<p class="field__hint field__hint--box"><?php echo alph_icon( 'mail', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'We’ll email you a link to set your password.', 'alphonsus' ); ?></p>
	<?php endif; ?>

	<label class="checkline checkline--box">
		<input type="checkbox" name="alph_newsletter" value="1" <?php checked( $alph_news ); ?>>
		<span><strong><?php esc_html_e( 'Join the young readers & language learners club', 'alphonsus' ); ?></strong><small><?php esc_html_e( 'Book recommendations, Swedish practice sheets and new arrivals. Unsubscribe anytime.', 'alphonsus' ); ?></small></span>
	</label>

	<div class="auth-form__privacy"><?php do_action( 'woocommerce_register_form' ); ?></div>

	<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
	<button type="submit" class="btn btn--primary btn--block auth-form__submit" name="register" value="<?php esc_attr_e( 'Register', 'alphonsus' ); ?>"><span><?php esc_html_e( 'Create account', 'alphonsus' ); ?></span><?php echo alph_icon( 'arrow', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>

	<?php do_action( 'woocommerce_register_form_end' ); ?>
</form>

<p class="auth__switch"><?php esc_html_e( 'Already have an account?', 'alphonsus' ); ?> <a href="<?php echo esc_url( alph_auth_url( 'login' ) ); ?>"><?php esc_html_e( 'Sign in', 'alphonsus' ); ?></a></p>
