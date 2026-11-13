<?php
/**
 * Demo mode: every visitor gets a timed preview, then the site locks for them
 * for a set number of hours. Settings live in Customizer → Demo mode.
 *
 * The start time is kept in a signed cookie and, optionally, against a hash of
 * the visitor's network address so a private window doesn't restart the clock.
 * Logged-in staff (anyone who can edit posts) are never timed or locked.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

const ALPH_DEMO_COOKIE = 'alph_demo';

function alph_demo_enabled() {
	return (bool) alph_mod( 'demo_on' ) && ! current_user_can( 'edit_posts' ) && ! is_customize_preview();
}

/**
 * Demo and lock lengths in seconds.
 */
function alph_demo_lengths() {
	return array(
		max( 1, (int) alph_mod( 'demo_minutes' ) ) * MINUTE_IN_SECONDS,
		max( 1, (int) alph_mod( 'demo_lock_hours' ) ) * HOUR_IN_SECONDS,
	);
}

/**
 * Changes whenever demo mode is switched on, which restarts every timer.
 */
function alph_demo_salt() {
	$salt = get_option( 'alph_demo_salt' );
	if ( ! $salt ) {
		$salt = wp_generate_password( 12, false );
		update_option( 'alph_demo_salt', $salt, false );
	}
	return $salt;
}

function alph_demo_sign( $start ) {
	return substr( wp_hash( $start . '|' . alph_demo_salt(), 'nonce' ), 0, 20 );
}

function alph_demo_ip_key() {
	foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $header ) {
		if ( ! empty( $_SERVER[ $header ] ) ) {
			$ip = trim( explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) ) )[0] );
			return 'alph_demo_' . substr( md5( $ip . alph_demo_salt() ), 0, 20 );
		}
	}
	return '';
}

/**
 * Current visitor's state: [ 'locked' => bool, 'left' => seconds until the demo ends or the lock lifts ].
 */
function alph_demo_state() {
	static $state = null;
	if ( null !== $state ) {
		return $state;
	}

	list( $demo, $lock ) = alph_demo_lengths();
	$now          = time();
	$starts       = array();
	$cookie_start = 0;

	if ( ! empty( $_COOKIE[ ALPH_DEMO_COOKIE ] ) ) {
		$parts = explode( '.', sanitize_text_field( wp_unslash( $_COOKIE[ ALPH_DEMO_COOKIE ] ) ) );
		if ( 2 === count( $parts ) && hash_equals( alph_demo_sign( $parts[0] ), $parts[1] ) ) {
			$cookie_start = (int) $parts[0];
			$starts[]     = $cookie_start;
		}
	}

	$ip_key = alph_mod( 'demo_by_ip' ) ? alph_demo_ip_key() : '';
	if ( $ip_key && get_transient( $ip_key ) ) {
		$starts[] = (int) get_transient( $ip_key );
	}

	// Keep only starts whose demo + lock cycle hasn't finished yet.
	$starts = array_filter( $starts, fn( $s ) => $s <= $now && $now < $s + $demo + $lock );
	$start  = $starts ? min( $starts ) : $now;

	if ( $cookie_start !== $start ) {
		$expires = $start + $demo + $lock;
		setcookie( ALPH_DEMO_COOKIE, $start . '.' . alph_demo_sign( $start ), array( 'expires' => $expires, 'path' => COOKIEPATH ? COOKIEPATH : '/', 'domain' => COOKIE_DOMAIN, 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ) );
		if ( $ip_key ) {
			set_transient( $ip_key, $start, $expires - $now );
		}
	}

	$state = $now < $start + $demo
		? array( 'locked' => false, 'left' => $start + $demo - $now )
		: array( 'locked' => true, 'left' => $start + $demo + $lock - $now );

	return $state;
}

/**
 * Restart everyone's timer when demo mode is switched on.
 */
add_action(
	'customize_save',
	function () {
		$GLOBALS['alph_demo_was_on'] = (bool) alph_mod( 'demo_on' );
	}
);
add_action(
	'customize_save_after',
	function () {
		if ( empty( $GLOBALS['alph_demo_was_on'] ) && alph_mod( 'demo_on' ) ) {
			update_option( 'alph_demo_salt', wp_generate_password( 12, false ), false );
		}
	}
);

/**
 * Lock screen for visitors whose demo time is up.
 */
add_action(
	'template_redirect',
	function () {
		if ( ! alph_demo_enabled() ) {
			return;
		}
		nocache_headers();

		$state = alph_demo_state();
		if ( ! $state['locked'] ) {
			return;
		}

		status_header( 503 );
		header( 'Retry-After: ' . (int) $state['left'] );
		get_template_part( 'template-parts/demo-locked', null, array( 'left' => $state['left'] ) );
		exit;
	},
	1
);

/**
 * "This is a demo" bar with the time left.
 */
add_action(
	'wp_footer',
	function () {
		if ( ! alph_demo_enabled() ) {
			return;
		}
		$state = alph_demo_state();
		if ( $state['locked'] ) {
			return;
		}
		?>
		<div class="demo-bar" role="status" data-demo-left="<?php echo esc_attr( $state['left'] ); ?>">
			<span class="demo-bar__tag"><?php esc_html_e( 'Demo', 'alphonsus' ); ?></span>
			<span class="demo-bar__msg"><?php echo esc_html( alph_mod( 'demo_banner' ) ); ?></span>
			<span class="demo-bar__time"><?php esc_html_e( 'Time left', 'alphonsus' ); ?> <strong data-demo-clock><?php echo esc_html( gmdate( $state['left'] >= HOUR_IN_SECONDS ? 'G:i:s' : 'i:s', $state['left'] ) ); ?></strong></span>
		</div>
		<?php
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( alph_demo_enabled() ) {
			$classes[] = 'has-demo-bar';
		}
		return $classes;
	}
);
