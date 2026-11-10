<?php
/**
 * Lightweight wishlist.
 *
 * Guests: product IDs in the "alph_wishlist" cookie (managed by main.js).
 * Logged-in customers: also saved to user meta so the list follows them.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

const ALPH_WISHLIST_COOKIE = 'alph_wishlist';
const ALPH_WISHLIST_META   = '_alph_wishlist';
const ALPH_WISHLIST_MAX    = 100;

function alph_wishlist_clean( $ids ) {
	$ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $ids ) ) ) );
	return array_slice( $ids, 0, ALPH_WISHLIST_MAX );
}

/**
 * Current visitor's wishlist product IDs.
 */
function alph_wishlist_ids() {
	static $ids = null;
	if ( null !== $ids ) {
		return $ids;
	}
	$cookie = isset( $_COOKIE[ ALPH_WISHLIST_COOKIE ] ) ? explode( ',', sanitize_text_field( wp_unslash( $_COOKIE[ ALPH_WISHLIST_COOKIE ] ) ) ) : array();
	$ids    = $cookie;
	if ( is_user_logged_in() ) {
		$ids = array_merge( (array) get_user_meta( get_current_user_id(), ALPH_WISHLIST_META, true ), $cookie );
	}
	$ids = alph_wishlist_clean( $ids );
	if ( $ids ) {
		// Drop products that were deleted or unpublished since they were saved.
		$live = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'post__in'       => $ids,
				'posts_per_page' => ALPH_WISHLIST_MAX,
				'fields'         => 'ids',
				'orderby'        => 'post__in',
			)
		);
		$ids  = array_map( 'intval', $live );
	}
	return $ids;
}

function alph_wishlist_page_url() {
	$page = get_page_by_path( 'wishlist' );
	return $page ? get_permalink( $page ) : home_url( '/wishlist/' );
}

/**
 * Heart toggle button.
 */
function alph_wishlist_button( $product_id, $with_label = false ) {
	$on    = in_array( (int) $product_id, alph_wishlist_ids(), true );
	$label = $on ? __( 'Remove from wishlist', 'alphonsus' ) : __( 'Add to wishlist', 'alphonsus' );
	return sprintf(
		'<button type="button" class="wish-btn%1$s" data-wishlist="%2$d" aria-pressed="%3$s" aria-label="%4$s" title="%4$s">%5$s%6$s</button>',
		$with_label ? ' wish-btn--label' : '',
		(int) $product_id,
		$on ? 'true' : 'false',
		esc_attr( $label ),
		alph_icon( 'heart', 18 ),
		$with_label ? '<span>' . esc_html__( 'Wishlist', 'alphonsus' ) . '</span>' : ''
	);
}

/**
 * Pass the list + REST nonce to main.js.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_add_inline_script(
			'alphonsus',
			'window.alphWishlist = ' . wp_json_encode(
				array(
					'ids'      => alph_wishlist_ids(),
					'loggedIn' => is_user_logged_in(),
					'nonce'    => is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '',
					'cookie'   => ALPH_WISHLIST_COOKIE,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
				)
			) . ';',
			'before'
		);
	},
	20
);

/**
 * Logged-in customers save their list to their account.
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'alphonsus/v1',
			'/wishlist',
			array(
				'methods'             => 'POST',
				'permission_callback' => 'is_user_logged_in',
				'args'                => array(
					'ids' => array(
						'type'  => 'array',
						'items' => array( 'type' => 'integer' ),
					),
				),
				'callback'            => function ( WP_REST_Request $request ) {
					$ids = alph_wishlist_clean( $request->get_param( 'ids' ) );
					update_user_meta( get_current_user_id(), ALPH_WISHLIST_META, $ids );
					return array( 'ids' => $ids );
				},
			)
		);
	}
);

/**
 * Merge a guest's list into their account when they log in.
 */
add_action(
	'wp_login',
	function ( $login, $user ) {
		if ( empty( $_COOKIE[ ALPH_WISHLIST_COOKIE ] ) ) {
			return;
		}
		$cookie = explode( ',', sanitize_text_field( wp_unslash( $_COOKIE[ ALPH_WISHLIST_COOKIE ] ) ) );
		$merged = alph_wishlist_clean( array_merge( (array) get_user_meta( $user->ID, ALPH_WISHLIST_META, true ), $cookie ) );
		update_user_meta( $user->ID, ALPH_WISHLIST_META, $merged );
	},
	10,
	2
);

/**
 * [alphonsus_wishlist] — the wishlist page grid.
 */
add_shortcode(
	'alphonsus_wishlist',
	function () {
		$products = array();
		foreach ( alph_wishlist_ids() as $id ) {
			$product = wc_get_product( $id );
			if ( $product && 'publish' === $product->get_status() ) {
				$products[] = $product;
			}
		}

		ob_start();
		echo '<div class="wishlist" data-wishlist-page>';
		echo '<div class="empty-state"' . ( $products ? ' hidden' : '' ) . '>';
		echo alph_icon( 'heart', 40 ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<h2>' . esc_html__( 'Your wishlist is empty', 'alphonsus' ) . '</h2>';
		echo '<p>' . esc_html__( 'Tap the heart on any book to save it here for later.', 'alphonsus' ) . '</p>';
		echo '<a class="btn btn--primary" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Browse the shop', 'alphonsus' ) . '</a>';
		echo '</div>';
		if ( $products ) {
			echo '<div class="product-grid">';
			foreach ( $products as $product ) {
				alph_product_card( $product, 'h2' );
			}
			echo '</div>';
		}
		echo '</div>';
		return ob_get_clean();
	}
);
