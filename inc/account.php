<?php
/**
 * My Account: Reading Journey endpoint, menu, dashboard helpers.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'woocommerce_get_query_vars',
	function ( $vars ) {
		$vars[ ALPH_RJ_ENDPOINT ] = ALPH_RJ_ENDPOINT;
		return $vars;
	}
);

add_filter(
	'woocommerce_account_menu_items',
	function ( $items ) {
		$out = array();
		foreach ( $items as $key => $label ) {
			$out[ $key ] = $label;
			if ( 'dashboard' === $key ) {
				$out[ ALPH_RJ_ENDPOINT ] = __( 'Reading Journey', 'alphonsus' );
			}
		}
		if ( function_exists( 'alph_wishlist_page_url' ) ) {
			// Wishlist lives on its own page; insert before logout.
			$logout = $out['customer-logout'] ?? null;
			unset( $out['customer-logout'] );
			$out['alph-wishlist'] = __( 'Wishlist', 'alphonsus' );
			if ( $logout ) {
				$out['customer-logout'] = $logout;
			}
		}
		return $out;
	}
);

add_filter(
	'woocommerce_get_endpoint_url',
	function ( $url, $endpoint ) {
		return 'alph-wishlist' === $endpoint ? alph_wishlist_page_url() : $url;
	},
	10,
	2
);

add_filter(
	'woocommerce_endpoint_' . ALPH_RJ_ENDPOINT . '_title',
	function () {
		return __( 'Reading Journey', 'alphonsus' );
	}
);

add_action(
	'woocommerce_account_' . ALPH_RJ_ENDPOINT . '_endpoint',
	function () {
		get_template_part( 'template-parts/account/reading-journey' );
	}
);

/**
 * Icons for account menu items.
 */
function alph_account_icon( $endpoint ) {
	$map = array(
		'dashboard'       => 'home',
		ALPH_RJ_ENDPOINT  => 'flame',
		'orders'          => 'receipt',
		'downloads'       => 'download',
		'edit-address'    => 'map-pin',
		'payment-methods' => 'shield',
		'edit-account'    => 'user',
		'alph-wishlist'   => 'heart',
		'customer-logout' => 'logout',
	);
	return alph_icon( $map[ $endpoint ] ?? 'arrow', 18 );
}

/**
 * Tracking steps for an order: [[label, done], …] and whether it was cancelled.
 */
function alph_order_steps( WC_Order $order ) {
	$status = $order->get_status();
	$steps  = array(
		array( __( 'Order placed', 'alphonsus' ), true ),
		array( __( 'Payment confirmed', 'alphonsus' ), in_array( $status, array( 'processing', 'completed' ), true ) ),
		array( __( 'Preparing', 'alphonsus' ), in_array( $status, array( 'processing', 'completed' ), true ) ),
		array( $order->has_downloadable_item() && ! $order->needs_shipping_address() ? __( 'Delivered digitally', 'alphonsus' ) : __( 'Completed', 'alphonsus' ), 'completed' === $status ),
	);
	return array( $steps, in_array( $status, array( 'cancelled', 'failed', 'refunded' ), true ) );
}

/**
 * Swedish School enrolment requests sent with this customer's email.
 */
function alph_customer_enrolments( $email ) {
	if ( ! $email ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'   => 'alph_entry',
			'post_status' => 'private',
			'numberposts' => 5,
			'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'   => '_alph_form',
					'value' => 'enrol',
				),
				array(
					'key'   => '_alph_email',
					'value' => $email,
				),
			),
		)
	);
}

/**
 * Count of books bought (items across paid orders).
 */
function alph_customer_book_count( $user_id ) {
	$donation = (int) get_option( 'alph_donation_product' );
	$count    = 0;
	foreach ( wc_get_orders(
		array(
			'customer_id' => $user_id,
			'status'      => array( 'wc-processing', 'wc-completed' ),
			'limit'       => 100,
		)
	) as $order ) {
		foreach ( $order->get_items() as $item ) {
			if ( $item->get_product_id() !== $donation ) {
				$count += $item->get_quantity();
			}
		}
	}
	return $count;
}
