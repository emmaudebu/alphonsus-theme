<?php
/**
 * One-time setup when the theme is activated (safe to re-run).
 *
 * - Creates the inner pages if missing and assigns their templates.
 * - Registers the product attributes used by the shop filters.
 * - Creates the hidden donation product.
 * - Flushes permalinks (for the Reading Journey account endpoint).
 *
 * Re-run any time with: wp eval 'alph_install();'
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

function alph_install() {
	$pages = array(
		'about'          => array( __( 'About Us', 'alphonsus' ), 'page-templates/about.php' ),
		'foundation'     => array( __( 'Foundation', 'alphonsus' ), 'page-templates/foundation.php' ),
		'contact'        => array( __( 'Contact', 'alphonsus' ), 'page-templates/contact.php' ),
		'swedish-school' => array( __( 'Swedish School', 'alphonsus' ), 'page-templates/swedish-school.php' ),
		'wellness'       => array( __( 'Wellness', 'alphonsus' ), 'page-templates/wellness.php' ),
		'merch'          => array( __( 'Merch', 'alphonsus' ), 'page-templates/merch.php' ),
		'wishlist'       => array( __( 'Wishlist', 'alphonsus' ), 'page-templates/wishlist.php' ),
	);
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		$id       = $existing ? $existing->ID : wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $page[0],
				'post_name'   => $slug,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $page[1] );
		}
	}

	if ( class_exists( 'WooCommerce' ) ) {
		$attributes = array(
			'level'      => __( 'Level', 'alphonsus' ),
			'language'   => __( 'Language', 'alphonsus' ),
			'age-range'  => __( 'Age range', 'alphonsus' ),
			'format'     => __( 'Format', 'alphonsus' ),
		);
		foreach ( $attributes as $slug => $name ) {
			if ( ! wc_attribute_taxonomy_id_by_name( $slug ) ) {
				wc_create_attribute(
					array(
						'name'         => $name,
						'slug'         => $slug,
						'type'         => 'select',
						'order_by'     => 'menu_order',
						'has_archives' => false,
					)
				);
			}
		}
		if ( function_exists( 'alph_donation_product_id' ) ) {
			alph_donation_product_id();
		}
	}

	flush_rewrite_rules();
	update_option( 'alph_installed', ALPH_VERSION, false );
}

add_action( 'after_switch_theme', 'alph_install' );

// Upgrades: re-run when the theme version changes.
add_action(
	'admin_init',
	function () {
		if ( get_option( 'alph_installed' ) !== ALPH_VERSION ) {
			alph_install();
		}
	}
);
