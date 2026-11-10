<?php
/**
 * Alphonsus theme bootstrap.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

define( 'ALPH_VERSION', '1.0.0' );
define( 'ALPH_DIR', get_template_directory() );
define( 'ALPH_URI', get_template_directory_uri() );

require ALPH_DIR . '/inc/defaults.php';
require ALPH_DIR . '/inc/setup.php';
require ALPH_DIR . '/inc/icons.php';
require ALPH_DIR . '/inc/template-tags.php';
require ALPH_DIR . '/inc/customizer.php';
require ALPH_DIR . '/inc/forms.php';

if ( class_exists( 'WooCommerce' ) ) {
	require ALPH_DIR . '/inc/woocommerce.php';
	require ALPH_DIR . '/inc/wishlist.php';
	require ALPH_DIR . '/inc/search.php';
}
