<?php
/**
 * Serve "image.png.webp" in place of "image.png" whenever that smaller copy
 * exists in the uploads folder. Nothing in the database changes, and images
 * without a WebP copy keep their original file. Only the public site is
 * affected: wp-admin (including product CSV exports) and WP-CLI see originals.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

/**
 * WebP URL for an uploads image URL, or the URL unchanged.
 */
function alph_webp_url( $url ) {
	static $uploads = null;
	static $cache   = array();

	if ( ! is_string( $url ) || ! preg_match( '/\.(jpe?g|png)$/i', $url ) || is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return $url;
	}
	if ( isset( $cache[ $url ] ) ) {
		return $cache[ $url ];
	}
	if ( null === $uploads ) {
		$uploads = wp_get_upload_dir();
	}

	// Match on the path so http/https differences don't matter.
	$base = preg_replace( '#^https?:#', '', $uploads['baseurl'] );
	$rel  = preg_replace( '#^https?:#', '', $url );
	if ( 0 !== strpos( $rel, $base . '/' ) ) {
		return $cache[ $url ] = $url;
	}

	$file = $uploads['basedir'] . rawurldecode( substr( $rel, strlen( $base ) ) );
	return $cache[ $url ] = is_file( $file . '.webp' ) ? $url . '.webp' : $url;
}

add_filter(
	'wp_get_attachment_image_src',
	function ( $image ) {
		if ( is_array( $image ) && ! empty( $image[0] ) ) {
			$image[0] = alph_webp_url( $image[0] );
		}
		return $image;
	}
);

add_filter(
	'wp_calculate_image_srcset',
	function ( $sources ) {
		foreach ( (array) $sources as $width => $source ) {
			$sources[ $width ]['url'] = alph_webp_url( $source['url'] );
		}
		return $sources;
	}
);

// Images placed in page and post content.
add_filter(
	'wp_content_img_tag',
	function ( $tag ) {
		return preg_replace_callback(
			'/\ssrc="([^"]+)"/',
			fn( $m ) => ' src="' . esc_url( alph_webp_url( html_entity_decode( $m[1] ) ) ) . '"',
			$tag
		);
	}
);
