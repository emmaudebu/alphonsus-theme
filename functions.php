<?php
/**
 * Alphonsus theme bootstrap.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

define( 'ALPH_VERSION', '2.2.0' );
define( 'ALPH_DIR', get_template_directory() );
define( 'ALPH_URI', get_template_directory_uri() );

require ALPH_DIR . '/inc/defaults.php';
require ALPH_DIR . '/inc/defaults-pages.php';
require ALPH_DIR . '/inc/setup.php';
require ALPH_DIR . '/inc/icons.php';
require ALPH_DIR . '/inc/template-tags.php';
require ALPH_DIR . '/inc/customizer.php';
require ALPH_DIR . '/inc/forms.php';
require ALPH_DIR . '/inc/sections.php';
require ALPH_DIR . '/inc/install.php';
require ALPH_DIR . '/inc/demo.php';

if ( class_exists( 'WooCommerce' ) ) {
	require ALPH_DIR . '/inc/woocommerce.php';
	require ALPH_DIR . '/inc/wishlist.php';
	require ALPH_DIR . '/inc/search.php';
	require ALPH_DIR . '/inc/shop.php';
	require ALPH_DIR . '/inc/currency.php';
	require ALPH_DIR . '/inc/donations.php';
	require ALPH_DIR . '/inc/reading.php';
	require ALPH_DIR . '/inc/account.php';
	require ALPH_DIR . '/inc/auth.php';
}
