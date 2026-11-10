<?php
/**
 * Reading Journey: a personal reading-habit tracker for logged-in customers.
 *
 * Everything is stored in one user-meta array (ALPH_RJ_META):
 *   log      list of [d => Y-m-d, m => minutes, p => product ID, t => timestamp]
 *   goal     daily target in minutes
 *   quests   [Y-m-d => [quest keys done that day]]
 *   progress [product ID => percent read]
 *   finished [product ID => Y-m-d]
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

const ALPH_RJ_META     = '_alph_reading';
const ALPH_RJ_MAX_LOG  = 3000;
const ALPH_RJ_ENDPOINT = 'reading-journey';

/*
 * ---------------------------------------------------------------------------
 * Data.
 * ---------------------------------------------------------------------------
 */

function alph_rj_defaults() {
	return array(
		'log'      => array(),
		'goal'     => 20,
		'quests'   => array(),
		'progress' => array(),
		'finished' => array(),
	);
}

function alph_rj_get( $user_id = 0 ) {
	$user_id = $user_id ? $user_id : get_current_user_id();
	$data    = get_user_meta( $user_id, ALPH_RJ_META, true );
	return wp_parse_args( is_array( $data ) ? $data : array(), alph_rj_defaults() );
}

function alph_rj_save( array $data, $user_id = 0 ) {
	$user_id     = $user_id ? $user_id : get_current_user_id();
	$data['log'] = array_slice( $data['log'], -ALPH_RJ_MAX_LOG );
	// Quest check-offs older than 60 days are no longer shown anywhere.
	$cutoff         = wp_date( 'Y-m-d', strtotime( '-60 days' ) );
	$data['quests'] = array_filter( $data['quests'], fn( $d ) => $d >= $cutoff, ARRAY_FILTER_USE_KEY );
	update_user_meta( $user_id, ALPH_RJ_META, $data );
}

function alph_rj_today() {
	return wp_date( 'Y-m-d' );
}

/**
 * Goal tiers shown as buttons.
 */
function alph_rj_tiers() {
	return array(
		20 => __( 'Casual', 'alphonsus' ),
		45 => __( 'Scholar', 'alphonsus' ),
		75 => __( 'Polyglot', 'alphonsus' ),
	);
}

/**
 * Daily quests the reader can tick off.
 */
function alph_rj_quests() {
	return array(
		'vocab'    => array( __( 'Swedish vocabulary sprint', 'alphonsus' ), __( '15 minutes of Swedish words with audio or your textbook', 'alphonsus' ), 'languages' ),
		'aloud'    => array( __( 'Bedtime storybook aloud', 'alphonsus' ), __( 'Read a story aloud to a child, sibling or friend', 'alphonsus' ), 'book-open' ),
		'notes'    => array( __( 'Bilingual notes', 'alphonsus' ), __( 'Write 5 new words side by side in two languages', 'alphonsus' ), 'pen' ),
		'marathon' => array( __( 'Weekend deep read', 'alphonsus' ), __( 'One unhurried session of 60+ minutes on Saturday or Sunday', 'alphonsus' ), 'clock' ),
	);
}

/**
 * Minutes per day for the last $days days (oldest first), keyed by date.
 */
function alph_rj_days( array $data, $days = 7 ) {
	$out = array();
	for ( $i = $days - 1; $i >= 0; $i-- ) {
		$out[ wp_date( 'Y-m-d', strtotime( "-{$i} days" ) ) ] = 0;
	}
	foreach ( $data['log'] as $e ) {
		if ( isset( $out[ $e['d'] ] ) ) {
			$out[ $e['d'] ] += (int) $e['m'];
		}
	}
	return $out;
}

function alph_rj_minutes_on( array $data, $date ) {
	$sum = 0;
	foreach ( $data['log'] as $e ) {
		if ( $e['d'] === $date ) {
			$sum += (int) $e['m'];
		}
	}
	return $sum;
}

/**
 * Consecutive days with reading, counting back from today (or yesterday if
 * nothing is logged yet today, so the streak isn't "lost" before bedtime).
 */
function alph_rj_streak( array $data ) {
	$days = array();
	foreach ( $data['log'] as $e ) {
		$days[ $e['d'] ] = true;
	}
	$streak = 0;
	$cursor = strtotime( alph_rj_today() . ' 12:00' );
	if ( empty( $days[ wp_date( 'Y-m-d', $cursor ) ] ) ) {
		$cursor -= DAY_IN_SECONDS;
	}
	while ( ! empty( $days[ wp_date( 'Y-m-d', $cursor ) ] ) ) {
		++$streak;
		$cursor -= DAY_IN_SECONDS;
	}
	return $streak;
}

function alph_rj_total( array $data ) {
	return array_sum( array_map( fn( $e ) => (int) $e['m'], $data['log'] ) );
}

/**
 * XP: 1 per minute, 25 per completed quest, 100 per finished book.
 */
function alph_rj_xp( array $data ) {
	$quests = array_sum( array_map( 'count', $data['quests'] ) );
	return alph_rj_total( $data ) + 25 * $quests + 100 * count( $data['finished'] );
}

/**
 * Level from XP: [name, current threshold, next threshold or null, percent to next].
 */
function alph_rj_level( $xp ) {
	$levels = array(
		0    => __( 'Curious Reader', 'alphonsus' ),
		300  => __( 'Page Turner', 'alphonsus' ),
		800  => __( 'Story Explorer', 'alphonsus' ),
		1500 => __( 'Bookworm', 'alphonsus' ),
		2500 => __( 'Scholar', 'alphonsus' ),
		4000 => __( 'Laureate', 'alphonsus' ),
		6000 => __( 'Polyglot Laureate', 'alphonsus' ),
	);
	$name   = reset( $levels );
	$floor  = 0;
	$next   = null;
	foreach ( $levels as $min => $label ) {
		if ( $xp >= $min ) {
			$name  = $label;
			$floor = $min;
		} elseif ( null === $next ) {
			$next = $min;
		}
	}
	$pct = $next ? (int) floor( ( $xp - $floor ) / ( $next - $floor ) * 100 ) : 100;
	return array( $name, $floor, $next, $pct );
}

/**
 * Subject group for a product, from its categories.
 */
function alph_rj_subject( $product_id ) {
	if ( ! $product_id ) {
		return __( 'Other reading', 'alphonsus' );
	}
	$slugs = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'slugs' ) );
	if ( is_wp_error( $slugs ) ) {
		return __( 'Other reading', 'alphonsus' );
	}
	if ( in_array( 'sweden-book', $slugs, true ) ) {
		return __( 'Swedish studies', 'alphonsus' );
	}
	if ( in_array( 'wellness', $slugs, true ) ) {
		return __( 'Wellness', 'alphonsus' );
	}
	if ( array_intersect( array( 'story-book', 'count-colour', 'english-version', 'german-version', 'italian-version', 'spanish-version', 'bundles' ), $slugs ) ) {
		return __( 'Children’s books', 'alphonsus' );
	}
	return __( 'Other reading', 'alphonsus' );
}

/**
 * Minutes by subject over the last $days days: [[label, minutes], …].
 */
function alph_rj_mix( array $data, $days = 7 ) {
	$from = wp_date( 'Y-m-d', strtotime( '-' . ( $days - 1 ) . ' days' ) );
	$mix  = array();
	foreach ( $data['log'] as $e ) {
		if ( $e['d'] >= $from ) {
			$label         = alph_rj_subject( (int) $e['p'] );
			$mix[ $label ] = ( $mix[ $label ] ?? 0 ) + (int) $e['m'];
		}
	}
	arsort( $mix );
	$out = array();
	foreach ( $mix as $label => $m ) {
		$out[] = array( $label, $m );
	}
	return $out;
}

/**
 * Books the customer owns (from orders), most recent first.
 *
 * @return WC_Product[]
 */
function alph_customer_products( $user_id = 0, $limit = 24 ) {
	$user_id  = $user_id ? $user_id : get_current_user_id();
	$donation = (int) get_option( 'alph_donation_product' );
	$seen     = array();
	$orders   = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'status'      => array( 'wc-processing', 'wc-completed', 'wc-on-hold' ),
			'limit'       => 50,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);
	foreach ( $orders as $order ) {
		foreach ( $order->get_items() as $item ) {
			$pid = $item->get_product_id();
			if ( $pid && $pid !== $donation && ! isset( $seen[ $pid ] ) ) {
				$product = wc_get_product( $pid );
				if ( $product ) {
					$seen[ $pid ] = $product;
				}
			}
		}
	}
	return array_slice( $seen, 0, $limit, true );
}

/**
 * Books to show in "Books in progress": owned books plus anything logged,
 * each with minutes spent and percent read.
 */
function alph_rj_books( array $data ) {
	$books   = alph_customer_products();
	$minutes = array();
	foreach ( $data['log'] as $e ) {
		if ( $e['p'] ) {
			$minutes[ $e['p'] ] = ( $minutes[ $e['p'] ] ?? 0 ) + (int) $e['m'];
			if ( ! isset( $books[ $e['p'] ] ) ) {
				$product = wc_get_product( $e['p'] );
				if ( $product ) {
					$books[ $e['p'] ] = $product;
				}
			}
		}
	}
	$out = array();
	foreach ( $books as $pid => $product ) {
		$out[] = array(
			'product'  => $product,
			'minutes'  => $minutes[ $pid ] ?? 0,
			'percent'  => (int) ( $data['progress'][ $pid ] ?? 0 ),
			'finished' => $data['finished'][ $pid ] ?? '',
		);
	}
	// Unfinished first, then most-read.
	usort( $out, fn( $a, $b ) => array( (bool) $a['finished'], -$a['minutes'] ) <=> array( (bool) $b['finished'], -$b['minutes'] ) );
	return $out;
}

/**
 * Achievements: [title, text, icon, earned, progress 0-100].
 */
function alph_rj_achievements( array $data ) {
	$total    = alph_rj_total( $data );
	$streak   = alph_rj_streak( $data );
	$finished = count( $data['finished'] );
	$subjects = count( alph_rj_mix( $data, 3650 ) );
	$quests   = array_sum( array_map( 'count', $data['quests'] ) );
	return array(
		array( __( 'First chapter', 'alphonsus' ), __( 'Log your first reading session', 'alphonsus' ), 'book-open', $total > 0, $total > 0 ? 100 : 0 ),
		array( __( 'Week of wonder', 'alphonsus' ), __( 'Read 7 days in a row', 'alphonsus' ), 'flame', $streak >= 7, min( 100, (int) ( $streak / 7 * 100 ) ) ),
		array( __( 'Ten-hour club', 'alphonsus' ), __( 'Read for 10 hours in total', 'alphonsus' ), 'clock', $total >= 600, min( 100, (int) ( $total / 600 * 100 ) ) ),
		array( __( 'Bookworm laureate', 'alphonsus' ), __( 'Finish 5 books', 'alphonsus' ), 'trophy', $finished >= 5, min( 100, $finished * 20 ) ),
		array( __( 'Polyglot palate', 'alphonsus' ), __( 'Read across 3 different subjects', 'alphonsus' ), 'languages', $subjects >= 3, min( 100, (int) ( $subjects / 3 * 100 ) ) ),
		array( __( 'Quest keeper', 'alphonsus' ), __( 'Complete 20 daily quests', 'alphonsus' ), 'target', $quests >= 20, min( 100, $quests * 5 ) ),
	);
}

/*
 * ---------------------------------------------------------------------------
 * REST API (logged-in users only, own data only).
 * ---------------------------------------------------------------------------
 */

add_action(
	'rest_api_init',
	function () {
		$auth = function () {
			return is_user_logged_in();
		};

		register_rest_route(
			'alphonsus/v1',
			'/reading/log',
			array(
				'methods'             => 'POST',
				'permission_callback' => $auth,
				'args'                => array(
					'minutes' => array(
						'type'     => 'integer',
						'required' => true,
						'minimum'  => 1,
						'maximum'  => 600,
					),
					'product' => array(
						'type'    => 'integer',
						'default' => 0,
					),
					'date'    => array(
						'type'    => 'string',
						'default' => '',
					),
				),
				'callback'            => function ( WP_REST_Request $r ) {
					$data = alph_rj_get();
					$date = sanitize_text_field( $r['date'] );
					// Back-dating is limited to the past week; never the future.
					$oldest = wp_date( 'Y-m-d', strtotime( '-6 days' ) );
					if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || $date > alph_rj_today() || $date < $oldest ) {
						$date = alph_rj_today();
					}
					$pid           = (int) $r['product'];
					$pid           = $pid && 'product' === get_post_type( $pid ) ? $pid : 0;
					$data['log'][] = array(
						'd' => $date,
						'm' => (int) $r['minutes'],
						'p' => $pid,
						't' => time(),
					);
					alph_rj_save( $data );
					return array(
						'today'  => alph_rj_minutes_on( $data, alph_rj_today() ),
						'streak' => alph_rj_streak( $data ),
					);
				},
			)
		);

		register_rest_route(
			'alphonsus/v1',
			'/reading/undo',
			array(
				'methods'             => 'POST',
				'permission_callback' => $auth,
				'callback'            => function () {
					$data = alph_rj_get();
					array_pop( $data['log'] );
					alph_rj_save( $data );
					return array( 'ok' => true );
				},
			)
		);

		register_rest_route(
			'alphonsus/v1',
			'/reading/goal',
			array(
				'methods'             => 'POST',
				'permission_callback' => $auth,
				'args'                => array(
					'minutes' => array(
						'type'     => 'integer',
						'required' => true,
						'minimum'  => 5,
						'maximum'  => 300,
					),
				),
				'callback'            => function ( WP_REST_Request $r ) {
					$data         = alph_rj_get();
					$data['goal'] = (int) $r['minutes'];
					alph_rj_save( $data );
					return array( 'goal' => $data['goal'] );
				},
			)
		);

		register_rest_route(
			'alphonsus/v1',
			'/reading/quest',
			array(
				'methods'             => 'POST',
				'permission_callback' => $auth,
				'args'                => array(
					'key'  => array(
						'type'     => 'string',
						'required' => true,
						'enum'     => array_keys( alph_rj_quests() ),
					),
					'done' => array(
						'type'     => 'boolean',
						'required' => true,
					),
				),
				'callback'            => function ( WP_REST_Request $r ) {
					$data  = alph_rj_get();
					$today = alph_rj_today();
					$done  = $data['quests'][ $today ] ?? array();
					$done  = $r['done'] ? array_values( array_unique( array_merge( $done, array( $r['key'] ) ) ) ) : array_values( array_diff( $done, array( $r['key'] ) ) );
					$data['quests'][ $today ] = $done;
					alph_rj_save( $data );
					return array( 'done' => $done );
				},
			)
		);

		register_rest_route(
			'alphonsus/v1',
			'/reading/progress',
			array(
				'methods'             => 'POST',
				'permission_callback' => $auth,
				'args'                => array(
					'product' => array(
						'type'     => 'integer',
						'required' => true,
					),
					'percent' => array(
						'type'     => 'integer',
						'required' => true,
						'minimum'  => 0,
						'maximum'  => 100,
					),
				),
				'callback'            => function ( WP_REST_Request $r ) {
					$pid = (int) $r['product'];
					if ( 'product' !== get_post_type( $pid ) ) {
						return new WP_Error( 'alph_bad_product', __( 'Unknown book.', 'alphonsus' ), array( 'status' => 400 ) );
					}
					$data                     = alph_rj_get();
					$data['progress'][ $pid ] = (int) $r['percent'];
					if ( 100 === (int) $r['percent'] ) {
						$data['finished'][ $pid ] = $data['finished'][ $pid ] ?? alph_rj_today();
					} else {
						unset( $data['finished'][ $pid ] );
					}
					alph_rj_save( $data );
					return array( 'percent' => (int) $r['percent'] );
				},
			)
		);
	}
);

/**
 * CSV export of the reader's own log.
 */
add_action(
	'admin_post_alph_rj_csv',
	function () {
		check_admin_referer( 'alph_rj_csv' );
		$data = alph_rj_get();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=reading-journey-' . alph_rj_today() . '.csv' );
		$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		fputcsv( $out, array( 'date', 'minutes', 'book', 'subject' ) );
		foreach ( $data['log'] as $e ) {
			$title = $e['p'] ? get_the_title( $e['p'] ) : '';
			fputcsv( $out, array( $e['d'], (int) $e['m'], preg_match( '/^[=+\-@]/', $title ) ? "'" . $title : $title, alph_rj_subject( (int) $e['p'] ) ) );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
);

/*
 * ---------------------------------------------------------------------------
 * Charts.
 * ---------------------------------------------------------------------------
 */

/**
 * Progress ring SVG.
 */
function alph_ring_svg( $value, $goal ) {
	$pct = $goal > 0 ? min( 100, $value / $goal * 100 ) : 0;
	$r   = 52;
	$c   = 2 * M_PI * $r;
	return sprintf(
		'<svg class="ring__svg" viewBox="0 0 120 120" aria-hidden="true"><defs><linearGradient id="ring-grad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2b3a8f"/><stop offset="1" stop-color="#c47a2c"/></linearGradient></defs><circle cx="60" cy="60" r="%1$s" fill="none" stroke="#eef1fa" stroke-width="10"/><circle cx="60" cy="60" r="%1$s" fill="none" stroke="url(#ring-grad)" stroke-width="10" stroke-linecap="round" stroke-dasharray="%2$.1f %3$.1f" transform="rotate(-90 60 60)"/></svg>',
		$r,
		$c * $pct / 100,
		$c
	);
}

/**
 * Weekly bar chart with a goal line.
 */
function alph_week_chart_svg( array $days, $goal ) {
	$max   = max( array_merge( array_values( $days ), array( $goal, 30 ) ) );
	$max   = (int) ( ceil( $max * 1.15 / 30 ) * 30 );
	$w     = 560;
	$h     = 240;
	$pad_l = 36;
	$pad_b = 28;
	$plot  = $h - $pad_b - 10;
	$step  = ( $w - $pad_l ) / count( $days );
	$bar   = min( 46, $step * 0.56 );
	$out   = '';

	for ( $k = 0; $k <= 3; $k++ ) {
		$g    = $max * $k / 3;
		$y    = 10 + $plot - $plot * $g / $max;
		$out .= sprintf( '<line x1="%1$d" x2="%2$d" y1="%3$.1f" y2="%3$.1f" class="chart__grid"/><text x="%4$d" y="%5$.1f" class="chart__axis">%6$sm</text>', $pad_l, $w, $y, $pad_l - 8, $y + 4, (int) round( $g ) );
	}

	$i     = 0;
	$today = alph_rj_today();
	foreach ( $days as $date => $m ) {
		$x     = $pad_l + $step * $i + ( $step - $bar ) / 2;
		$bh    = $max ? $plot * $m / $max : 0;
		$y     = 10 + $plot - $bh;
		$class = $date === $today ? 'chart__bar chart__bar--today' : ( $m >= $goal && $m > 0 ? 'chart__bar chart__bar--goal' : 'chart__bar' );
		$label = $date === $today ? __( 'Today', 'alphonsus' ) : wp_date( 'D', strtotime( $date . ' 12:00' ) );
		$out  .= sprintf( '<rect x="%1$.1f" y="10" width="%2$.1f" height="%3$.1f" rx="8" class="chart__track"/>', $x, $bar, $plot );
		if ( $bh > 0 ) {
			$out .= sprintf( '<rect x="%1$.1f" y="%2$.1f" width="%3$.1f" height="%4$.1f" rx="8" class="%5$s"><title>%6$s</title></rect>', $x, $y, $bar, $bh, $class, esc_html( sprintf( /* translators: 1: day, 2: minutes */ __( '%1$s: %2$d minutes', 'alphonsus' ), $label, $m ) ) );
		}
		$out .= sprintf( '<text x="%1$.1f" y="%2$d" class="chart__label%4$s" text-anchor="middle">%3$s</text>', $x + $bar / 2, $h - 6, esc_html( $label ), $date === $today ? ' chart__label--today' : '' );
		++$i;
	}

	$gy   = 10 + $plot - $plot * $goal / $max;
	$out .= sprintf( '<line x1="%1$d" x2="%2$d" y1="%3$.1f" y2="%3$.1f" class="chart__target"/>', $pad_l, $w, $gy );

	return sprintf( '<svg class="chart" viewBox="0 0 %1$d %2$d" role="img" aria-label="%3$s">%4$s</svg>', $w, $h, esc_attr__( 'Minutes read per day this week', 'alphonsus' ), $out );
}

/*
 * ---------------------------------------------------------------------------
 * Front-end data for main.js.
 * ---------------------------------------------------------------------------
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_user_logged_in() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}
		wp_add_inline_script(
			'alphonsus',
			'window.alphReading = ' . wp_json_encode(
				array(
					'url'   => esc_url_raw( rest_url( 'alphonsus/v1/reading/' ) ),
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'today' => alph_rj_today(),
					'i18n'  => array(
						'saved'   => __( 'Saved', 'alphonsus' ),
						'logged'  => __( 'Reading logged — well done!', 'alphonsus' ),
						'error'   => __( 'Something went wrong. Please try again.', 'alphonsus' ),
						'pause'   => __( 'Pause', 'alphonsus' ),
						'resume'  => __( 'Resume', 'alphonsus' ),
						'start'   => __( 'Start reading timer', 'alphonsus' ),
						'stop'    => __( 'Stop & log', 'alphonsus' ),
					),
				)
			) . ';',
			'before'
		);
	},
	25
);
