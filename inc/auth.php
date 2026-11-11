<?php
/**
 * Sign-in and registration pages.
 *
 * The forms post to WooCommerce's own handlers (login, registration, lost and
 * reset password), so security plugins hooked into those forms keep working.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of the Sign In / Create Account pages (falls back to My Account).
 */
function alph_auth_url( $which = 'login' ) {
	$page = get_page_by_path( 'register' === $which ? 'register' : 'login' );
	return $page && 'publish' === $page->post_status ? get_permalink( $page ) : wc_get_page_permalink( 'myaccount' );
}

function alph_registration_open() {
	return 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
}

/**
 * Where to send the customer after signing in or registering.
 */
function alph_auth_redirect() {
	// phpcs:ignore WordPress.Security.NonceVerification -- only read and validated.
	$to = isset( $_GET['redirect_to'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ), '' ) : '';
	return $to ? $to : wc_get_page_permalink( 'myaccount' );
}

/**
 * Signed-in visitors don't need the sign-in pages.
 */
add_action(
	'template_redirect',
	function () {
		if ( is_user_logged_in() && ( is_page_template( 'page-templates/login.php' ) || is_page_template( 'page-templates/register.php' ) ) ) {
			wp_safe_redirect( alph_auth_redirect() );
			exit;
		}
	}
);

/**
 * Password strength meter on the registration and reset forms.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_page_template( 'page-templates/register.php' ) && 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
			wp_enqueue_script( 'wc-password-strength-meter' );
		}
	},
	30
);

add_filter(
	'woocommerce_registration_redirect',
	function ( $redirect ) {
		// phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce verified the registration nonce.
		return ! empty( $_POST['redirect'] ) ? $redirect : wc_get_page_permalink( 'myaccount' );
	}
);

/*
 * Extra registration fields: first name (required) and newsletter opt-in.
 */
add_action(
	'woocommerce_register_post',
	function ( $username, $email, $errors ) {
		// phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce verified the registration nonce.
		if ( isset( $_POST['alph_first_name'] ) && '' === trim( sanitize_text_field( wp_unslash( $_POST['alph_first_name'] ) ) ) ) {
			$errors->add( 'alph_first_name', __( 'Please enter your first name.', 'alphonsus' ) );
		}
	},
	10,
	3
);

add_action(
	'woocommerce_created_customer',
	function ( $customer_id ) {
		// phpcs:disable WordPress.Security.NonceVerification -- WooCommerce verified the registration nonce.
		if ( ! empty( $_POST['alph_first_name'] ) ) {
			$first = sanitize_text_field( wp_unslash( $_POST['alph_first_name'] ) );
			wp_update_user(
				array(
					'ID'           => $customer_id,
					'first_name'   => $first,
					'display_name' => $first,
				)
			);
			update_user_meta( $customer_id, 'billing_first_name', $first );
			update_user_meta( $customer_id, 'shipping_first_name', $first );
		}
		if ( ! empty( $_POST['alph_newsletter'] ) && function_exists( 'alph_save_entry' ) ) {
			$user = get_userdata( $customer_id );
			alph_save_entry( 'newsletter', array( 'email' => $user->user_email ) );
		}
		// phpcs:enable
	}
);

/**
 * Password input with a show/hide toggle.
 */
function alph_password_field( $id, $name, $label, $autocomplete, $extra = '' ) {
	?>
	<p class="field">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?> <span class="req">*</span></label>
		<span class="pw">
			<input type="password" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" autocomplete="<?php echo esc_attr( $autocomplete ); ?>" required <?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput -- static attributes. ?>>
			<button type="button" class="pw__toggle" data-pw-toggle aria-pressed="false" aria-label="<?php esc_attr_e( 'Show password', 'alphonsus' ); ?>">
				<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
			</button>
		</span>
	</p>
	<?php
}

/**
 * Two-panel shell: brand panel + form card.
 *
 * @param array $args tabs (bool), active (login|register), heading tag.
 */
function alph_auth_open( array $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'tabs'   => true,
			'active' => 'login',
		)
	);
	$perks = array(
		array( 'truck', __( 'Track every order', 'alphonsus' ), __( 'Follow deliveries from payment to your doorstep.', 'alphonsus' ) ),
		array( 'flame', __( 'Your Reading Journey', 'alphonsus' ), __( 'Log minutes, keep streaks and reach daily goals.', 'alphonsus' ) ),
		array( 'heart', __( 'Wishlist on every device', 'alphonsus' ), __( 'Save books now, find them again anywhere.', 'alphonsus' ) ),
		array( 'zap', __( 'Faster checkout', 'alphonsus' ), __( 'Saved addresses and order history in one place.', 'alphonsus' ) ),
	);
	?>
	<div class="auth">
		<aside class="auth__brand">
			<div class="auth__brand-top">
				<span class="chip chip--glass"><?php echo alph_icon( 'book-open', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Alphonsus reader account', 'alphonsus' ); ?></span>
				<p class="auth__brand-title"><?php esc_html_e( 'Your reading journey, all in one place.', 'alphonsus' ); ?></p>
				<p class="auth__brand-text"><?php esc_html_e( 'One account for your book orders, Swedish studies and the stories your family loves.', 'alphonsus' ); ?></p>
			</div>
			<ul class="auth__perks">
				<?php foreach ( $perks as $perk ) : ?>
					<li><span class="auth__perk-icon"><?php echo alph_icon( $perk[0], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><strong><?php echo esc_html( $perk[1] ); ?></strong><small><?php echo esc_html( $perk[2] ); ?></small></span></li>
				<?php endforeach; ?>
			</ul>
			<p class="auth__motto">“<?php esc_html_e( 'Empowering minds, building futures.', 'alphonsus' ); ?>”</p>
		</aside>
		<section class="auth__card">
			<?php if ( $args['tabs'] && alph_registration_open() ) : ?>
				<nav class="auth-tabs" aria-label="<?php esc_attr_e( 'Account', 'alphonsus' ); ?>">
					<a class="auth-tabs__tab<?php echo 'login' === $args['active'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( alph_auth_url( 'login' ) ); ?>"<?php echo 'login' === $args['active'] ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Sign in', 'alphonsus' ); ?></a>
					<a class="auth-tabs__tab<?php echo 'register' === $args['active'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( alph_auth_url( 'register' ) ); ?>"<?php echo 'register' === $args['active'] ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Create account', 'alphonsus' ); ?></a>
				</nav>
			<?php endif; ?>
			<?php woocommerce_output_all_notices(); ?>
	<?php
}

function alph_auth_close() {
	echo '</section></div>';
}
