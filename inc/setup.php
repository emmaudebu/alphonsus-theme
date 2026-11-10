<?php
/**
 * Theme supports, menus and assets.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'alphonsus', ALPH_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 120,
				'width'       => 280,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		add_editor_style( 'assets/css/editor.css' );

		add_image_size( 'alph-card', 480, 600, true );
		add_image_size( 'alph-wide', 960, 600, true );

		register_nav_menus(
			array(
				'primary'   => __( 'Main navigation', 'alphonsus' ),
				'footer-1'  => __( 'Footer: Categories', 'alphonsus' ),
				'footer-2'  => __( 'Footer: Community', 'alphonsus' ),
				'footer-3'  => __( 'Footer: Support', 'alphonsus' ),
				'secondary' => __( 'Footer: Bottom links', 'alphonsus' ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$css = ALPH_DIR . '/assets/css/main.css';
		$js  = ALPH_DIR . '/assets/js/main.js';

		wp_enqueue_style( 'alphonsus', ALPH_URI . '/assets/css/main.css', array(), ALPH_VERSION . '.' . filemtime( $css ) );
		wp_enqueue_script( 'alphonsus', ALPH_URI . '/assets/js/main.js', array(), ALPH_VERSION . '.' . filemtime( $js ), array( 'strategy' => 'defer', 'in_footer' => true ) );

		wp_localize_script(
			'alphonsus',
			'alphData',
			array(
				'restUrl'  => esc_url_raw( rest_url( 'alphonsus/v1/' ) ),
				'shopUrl'  => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
				'price'    => function_exists( 'wc_get_price_decimals' ) ? array(
					'symbol'   => html_entity_decode( get_woocommerce_currency_symbol() ),
					'format'   => html_entity_decode( get_woocommerce_price_format() ),
					'decimals' => wc_get_price_decimals(),
					'dec'      => wc_get_price_decimal_separator(),
					'thou'     => wc_get_price_thousand_separator(),
				) : null,
				'i18n'     => array(
					'added'     => __( 'Added to wishlist', 'alphonsus' ),
					'removed'     => __( 'Removed from wishlist', 'alphonsus' ),
					'addLabel'    => __( 'Add to wishlist', 'alphonsus' ),
					'removeLabel' => __( 'Remove from wishlist', 'alphonsus' ),
					'noResults' => __( 'No books found. Press Enter to search the shop.', 'alphonsus' ),
					'viewAll'   => __( 'See all results', 'alphonsus' ),
					'ended'     => __( 'Offer ended', 'alphonsus' ),
				),
			)
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Keeps the header cart count/total in sync after AJAX add-to-cart.
		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_script( 'wc-cart-fragments' );
		}
	}
);

/**
 * Preload the two fonts used above the fold.
 */
add_action(
	'wp_head',
	function () {
		foreach ( array( 'noto-serif.woff2', 'plus-jakarta-sans.woff2' ) as $font ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( ALPH_URI . '/assets/fonts/' . $font ) );
		}
	},
	1
);

// Emoji detection script and styles are not needed.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Block styles used to lay out pages without a page builder.
 */
add_action(
	'init',
	function () {
		register_block_style(
			array( 'core/group', 'core/column' ),
			array(
				'name'  => 'card',
				'label' => __( 'Card', 'alphonsus' ),
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'  => 'checklist',
				'label' => __( 'Checklist', 'alphonsus' ),
			)
		);
		register_block_style(
			'core/button',
			array(
				'name'  => 'orange',
				'label' => __( 'Orange', 'alphonsus' ),
			)
		);
	}
);

/**
 * Content width for embeds.
 */
add_action(
	'after_setup_theme',
	function () {
		$GLOBALS['content_width'] = 760;
	},
	0
);

/**
 * Body classes used by the stylesheet.
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_front_page() ) {
			$classes[] = 'is-home';
		}
		return $classes;
	}
);
