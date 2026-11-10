<?php
/**
 * Small template helpers.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Site logo (custom logo, or the site name as text).
 */
function alph_logo( $class = 'site-logo' ) {
	$logo_id = get_theme_mod( 'custom_logo' );
	echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	if ( $logo_id ) {
		echo wp_get_attachment_image(
			$logo_id,
			'medium',
			false,
			array(
				'alt'           => get_bloginfo( 'name' ),
				'loading'       => 'eager',
				'fetchpriority' => 'high',
			)
		);
	} else {
		echo '<span class="site-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
	}
	echo '</a>';
}

/**
 * Two-tone section title: first word in navy, the rest in the accent colour.
 */
function alph_split_title( $title ) {
	$parts = preg_split( '/\s+/', trim( $title ), 2 );
	if ( count( $parts ) < 2 ) {
		return esc_html( $title );
	}
	return esc_html( $parts[0] ) . ' <span class="accent">' . esc_html( $parts[1] ) . '</span>';
}

/**
 * Social profile links from the Customizer.
 */
function alph_social_links( $class = 'social' ) {
	$networks = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
	);
	$items    = '';
	foreach ( $networks as $key => $label ) {
		$url = alph_mod( $key );
		if ( $url ) {
			$items .= sprintf(
				'<li><a href="%s" target="_blank" rel="noopener" aria-label="%s">%s</a></li>',
				esc_url( $url ),
				/* translators: %s: social network name. */
				esc_attr( sprintf( __( 'Alphonsus Library on %s', 'alphonsus' ), $label ) ),
				alph_icon( $key, 18 )
			);
		}
	}
	if ( $items ) {
		echo '<ul class="' . esc_attr( $class ) . '">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts.
	}
}

/**
 * Footer menu column with a heading. Falls back to nothing if unassigned.
 */
function alph_footer_menu( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return;
	}
	$menu = wp_get_nav_menu_object( $locations[ $location ] );
	if ( ! $menu ) {
		return;
	}
	echo '<nav class="footer-col" aria-label="' . esc_attr( $menu->name ) . '">';
	echo '<h2 class="footer-col__title">' . esc_html( $menu->name ) . '</h2>';
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'depth'          => 1,
			'fallback_cb'    => false,
		)
	);
	echo '</nav>';
}

/**
 * Post date + author meta line.
 */
function alph_post_meta() {
	printf(
		'<p class="post-meta">%1$s<time datetime="%2$s">%3$s</time><span aria-hidden="true">·</span><span>%4$s</span></p>',
		alph_icon( 'calendar', 15 ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		/* translators: %s: author name. */
		esc_html( sprintf( __( 'By %s', 'alphonsus' ), get_the_author() ) )
	);
}

/**
 * Pagination for archives.
 */
function alph_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => __( 'Previous', 'alphonsus' ),
			'next_text' => __( 'Next', 'alphonsus' ),
		)
	);
}

/**
 * Page hero used on inner pages and archives.
 */
function alph_page_header( $title, $subtitle = '', $eyebrow = '' ) {
	echo '<header class="page-hero"><div class="container">';
	if ( $eyebrow ) {
		echo '<p class="eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<h1 class="page-hero__title">' . wp_kses_post( $title ) . '</h1>';
	if ( $subtitle ) {
		echo '<div class="page-hero__sub">' . wp_kses_post( $subtitle ) . '</div>';
	}
	echo '</div></header>';
}

/**
 * Status notice after a theme form submission (?alph_sent=form or ?alph_error=form).
 */
function alph_form_notice( $form ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display flag.
	if ( isset( $_GET['alph_sent'] ) && $form === $_GET['alph_sent'] ) {
		$messages = array(
			'preorder'   => __( 'Thank you! Your pre-order request has been received. We will contact you shortly to confirm.', 'alphonsus' ),
			'contact'    => __( 'Thank you! Your message has been sent. We usually reply within one working day.', 'alphonsus' ),
			'newsletter' => __( 'You’re in! Welcome to the young readers & language learners club.', 'alphonsus' ),
		);
		echo '<div class="notice notice--success" role="status">' . alph_icon( 'check', 18 ) . esc_html( $messages[ $form ] ?? __( 'Thank you!', 'alphonsus' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	} elseif ( isset( $_GET['alph_error'] ) && $form === $_GET['alph_error'] ) {
		echo '<div class="notice notice--error" role="alert">' . esc_html__( 'Please fill in all required fields with valid details and try again.', 'alphonsus' ) . '</div>';
	}
	// phpcs:enable
}
