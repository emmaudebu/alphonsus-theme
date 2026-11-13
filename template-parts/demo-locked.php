<?php
/**
 * Lock screen shown once a visitor's demo time is up (see inc/demo.php).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

$alph_left = (int) $args['left'];
$alph_css  = ALPH_DIR . '/assets/css/main.css';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<title><?php echo esc_html( alph_mod( 'demo_lock_title' ) . ' · ' . get_bloginfo( 'name' ) ); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( ALPH_URI . '/assets/css/main.css?ver=' . ALPH_VERSION . '.' . filemtime( $alph_css ) ); ?>">
</head>
<body class="demo-locked">
	<main class="demo-lock">
		<div class="demo-lock__card">
			<?php alph_logo( 'site-logo demo-lock__logo' ); ?>
			<span class="demo-lock__icon" aria-hidden="true"><?php echo alph_icon( 'clock', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<p class="eyebrow"><?php esc_html_e( 'Demo paused', 'alphonsus' ); ?></p>
			<h1 class="demo-lock__title"><?php echo esc_html( alph_mod( 'demo_lock_title' ) ); ?></h1>
			<p class="demo-lock__text"><?php echo esc_html( alph_mod( 'demo_lock_text' ) ); ?></p>
			<div class="demo-lock__timer">
				<span><?php esc_html_e( 'Available again in', 'alphonsus' ); ?></span>
				<strong id="demo-unlock" data-left="<?php echo esc_attr( $alph_left ); ?>"><?php echo esc_html( gmdate( 'G:i:s', $alph_left ) ); ?></strong>
			</div>
		</div>
	</main>
	<script>
	( function () {
		var el = document.getElementById( 'demo-unlock' );
		var end = Date.now() + Number( el.getAttribute( 'data-left' ) ) * 1000;
		function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }
		( function tick() {
			var s = Math.max( 0, Math.round( ( end - Date.now() ) / 1000 ) );
			el.textContent = Math.floor( s / 3600 ) + ':' + pad( Math.floor( s % 3600 / 60 ) ) + ':' + pad( s % 60 );
			if ( s === 0 ) {
				setTimeout( function () { location.reload(); }, 1500 );
				return;
			}
			setTimeout( tick, 1000 );
		} )();
	} )();
	</script>
</body>
</html>
