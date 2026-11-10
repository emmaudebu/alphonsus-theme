<?php
/**
 * Reusable page-section components used by the page templates.
 *
 * All text passed in is escaped here; callers pass raw strings.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

/**
 * A button/link. $style: primary | amber | ghost | outline | dark | light.
 */
function alph_button( $label, $url, $style = 'primary', $icon = '' ) {
	if ( '' === trim( (string) $label ) || '' === trim( (string) $url ) ) {
		return '';
	}
	$url    = 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ? home_url( $url ) : $url;
	$target = alph_is_external( $url ) ? ' target="_blank" rel="noopener"' : '';
	return sprintf(
		'<a class="btn btn--%1$s" href="%2$s"%3$s>%4$s<span>%5$s</span></a>',
		esc_attr( $style ),
		esc_url( $url ),
		$target,
		$icon ? alph_icon( $icon, 17 ) : '',
		esc_html( $label )
	);
}

/**
 * Section heading block.
 */
function alph_section_head( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow' => '',
			'title'   => '',
			'text'    => '',
			'align'   => 'split', // split | center | left.
			'aside'   => '',      // Raw, already-escaped HTML for the right side.
			'id'      => '',
			'tag'     => 'h2',
		)
	);
	if ( '' === $args['title'] ) {
		return;
	}
	$tag = in_array( $args['tag'], array( 'h1', 'h2' ), true ) ? $args['tag'] : 'h2';
	echo '<div class="shead shead--' . esc_attr( $args['align'] ) . '"><div class="shead__main">';
	if ( $args['eyebrow'] ) {
		echo '<p class="eyebrow eyebrow--amber">' . esc_html( $args['eyebrow'] ) . '</p>';
	}
	printf( '<%1$s class="shead__title"%2$s>%3$s</%1$s>', esc_html( $tag ), $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '', esc_html( $args['title'] ) );
	if ( $args['text'] ) {
		echo '<p class="shead__text">' . esc_html( $args['text'] ) . '</p>';
	}
	echo '</div>';
	if ( $args['aside'] ) {
		echo '<div class="shead__aside">' . $args['aside'] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- documented as pre-escaped.
	}
	echo '</div>';
}

/**
 * Stat row from "value | label" lines.
 */
function alph_stats_row( array $stats, $class = '' ) {
	if ( ! $stats ) {
		return;
	}
	$tones = array( 'navy', 'amber', 'navy', 'green' );
	echo '<dl class="statrow ' . esc_attr( $class ) . '">';
	foreach ( $stats as $i => $stat ) {
		printf(
			'<div class="statrow__item statrow__item--%1$s"><dt>%2$s</dt><dd>%3$s</dd></div>',
			esc_attr( $tones[ $i % 4 ] ),
			esc_html( $stat[1] ?? '' ),
			esc_html( $stat[0] ?? '' )
		);
	}
	echo '</dl>';
}

/**
 * Split hero used on inner pages.
 */
function alph_split_hero( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'badge'   => '',
			'eyebrow' => '',
			'title'   => '',
			'text'    => '',
			'buttons' => array(),
			'stats'   => array(),
			'image'   => 0,
			'caption' => array(), // label, title, text.
			'quote'   => '',
			'note'    => '',      // Small trust line beside buttons.
			'tone'    => 'cream',
		)
	);
	$has_image = $args['image'] && wp_attachment_is_image( $args['image'] );
	?>
	<section class="phero phero--<?php echo esc_attr( $args['tone'] ); ?><?php echo $has_image ? '' : ' phero--solo'; ?>">
		<div class="container phero__grid">
			<div class="phero__copy">
				<?php if ( $args['badge'] || $args['eyebrow'] ) : ?>
					<div class="phero__chips">
						<?php if ( $args['badge'] ) : ?>
							<span class="chip chip--navy"><?php echo alph_icon( 'award', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $args['badge'] ); ?></span>
						<?php endif; ?>
						<?php if ( $args['eyebrow'] ) : ?>
							<span class="chip chip--paper"><span class="dot dot--amber" aria-hidden="true"></span><?php echo esc_html( $args['eyebrow'] ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<h1 class="phero__title"><?php echo esc_html( $args['title'] ); ?></h1>
				<?php if ( $args['text'] ) : ?>
					<p class="phero__text"><?php echo esc_html( $args['text'] ); ?></p>
				<?php endif; ?>
				<?php if ( $args['buttons'] || $args['note'] ) : ?>
					<div class="phero__actions">
						<?php
						foreach ( $args['buttons'] as $b ) {
							echo alph_button( $b[0], $b[1], $b[2] ?? 'primary', $b[3] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput
						}
						if ( $args['note'] ) {
							echo '<span class="phero__note">' . alph_icon( 'check', 18 ) . esc_html( $args['note'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
						}
						?>
					</div>
				<?php endif; ?>
				<?php alph_stats_row( $args['stats'], 'phero__stats' ); ?>
			</div>
			<?php if ( $has_image ) : ?>
				<figure class="phero__media">
					<div class="phero__frame">
						<?php echo wp_get_attachment_image( $args['image'], 'large', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 900px) 92vw, 480px' ) ); ?>
						<?php if ( $args['quote'] ) : ?>
							<blockquote class="phero__quote"><?php echo esc_html( $args['quote'] ); ?></blockquote>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $args['caption']['title'] ) ) : ?>
						<figcaption class="phero__caption">
							<?php if ( ! empty( $args['caption']['label'] ) ) : ?>
								<span class="phero__caption-label"><span class="dot" aria-hidden="true"></span><?php echo esc_html( $args['caption']['label'] ); ?></span>
							<?php endif; ?>
							<strong><?php echo esc_html( $args['caption']['title'] ); ?></strong>
							<?php if ( ! empty( $args['caption']['text'] ) ) : ?>
								<span><?php echo esc_html( $args['caption']['text'] ); ?></span>
							<?php endif; ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Icon cards. Each item: title, text, icon, kicker, link [label,url], footer.
 */
function alph_icon_cards( array $items, $columns = 3, $class = '' ) {
	if ( ! $items ) {
		return;
	}
	$icons = array( 'book-open', 'languages', 'users', 'award', 'school', 'leaf', 'headphones', 'video' );
	printf( '<div class="cards cards--%1$d %2$s">', (int) $columns, esc_attr( $class ) );
	foreach ( $items as $i => $item ) {
		$icon = $item['icon'] ?? $icons[ $i % count( $icons ) ];
		echo '<article class="icard">';
		echo '<span class="icard__icon icard__icon--' . esc_attr( $i % 2 ? 'amber' : 'navy' ) . '">' . alph_icon( $icon, 22 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! empty( $item['kicker'] ) ) {
			echo '<p class="icard__kicker">' . esc_html( $item['kicker'] ) . '</p>';
		}
		echo '<h3 class="icard__title">' . esc_html( $item['title'] ) . '</h3>';
		if ( ! empty( $item['text'] ) ) {
			echo '<p class="icard__text">' . esc_html( $item['text'] ) . '</p>';
		}
		if ( ! empty( $item['footer'] ) ) {
			echo '<p class="icard__footer">' . esc_html( $item['footer'] ) . '</p>';
		}
		if ( ! empty( $item['link'][0] ) && ! empty( $item['link'][1] ) ) {
			echo '<a class="link-arrow" href="' . esc_url( alph_resolve_url( $item['link'][1] ) ) . '">' . esc_html( $item['link'][0] ) . alph_icon( 'arrow', 15 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</article>';
	}
	echo '</div>';
}

/**
 * Lines of "title | text" → card items.
 */
function alph_items_from_lines( $key ) {
	$items = array();
	foreach ( alph_lines( $key ) as $line ) {
		$parts   = array_map( 'trim', explode( '|', $line ) );
		$items[] = array(
			'title' => $parts[0] ?? '',
			'text'  => $parts[1] ?? '',
			'parts' => $parts,
		);
	}
	return $items;
}

function alph_resolve_url( $url ) {
	$url = trim( (string) $url );
	return 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ? home_url( $url ) : $url;
}

/**
 * Accordion FAQ from "question | answer" lines.
 */
function alph_faq( array $lines, $open_first = true ) {
	if ( ! $lines ) {
		return;
	}
	echo '<div class="faq">';
	foreach ( $lines as $i => $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		printf(
			'<details class="faq__item"%1$s><summary><span class="faq__num">%2$02d</span><span class="faq__q">%3$s</span>%4$s</summary><div class="faq__a"><p>%5$s</p></div></details>',
			$open_first && 0 === $i ? ' open' : '',
			(int) $i + 1,
			esc_html( $parts[0] ),
			alph_icon( 'chevron', 18 ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $parts[1] ?? '' )
		);
	}
	echo '</div>';
}

/**
 * Testimonials from "quote | name | detail" lines.
 */
function alph_testimonials( array $lines ) {
	if ( ! $lines ) {
		return;
	}
	echo '<div class="cards cards--3">';
	foreach ( $lines as $line ) {
		$p        = array_map( 'trim', explode( '|', $line ) );
		$initials = implode( '', array_map( fn( $w ) => mb_substr( $w, 0, 1 ), array_slice( preg_split( '/\s+/', $p[1] ?? '' ), 0, 2 ) ) );
		echo '<figure class="tcard">';
		echo '<span class="tcard__mark">' . alph_icon( 'quote', 22 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<blockquote>' . esc_html( $p[0] ) . '</blockquote>';
		if ( ! empty( $p[1] ) ) {
			echo '<figcaption><span class="avatar">' . esc_html( $initials ) . '</span><span><strong>' . esc_html( $p[1] ) . '</strong>' . ( ! empty( $p[2] ) ? '<small>' . esc_html( $p[2] ) . '</small>' : '' ) . '</span></figcaption>';
		}
		echo '</figure>';
	}
	echo '</div>';
}

/**
 * Horizontal trust bar from "title | text" lines.
 */
function alph_trust_bar( array $lines ) {
	if ( ! $lines ) {
		return;
	}
	$icons = array( 'package', 'truck', 'shield', 'headphones' );
	echo '<ul class="trustbar">';
	foreach ( $lines as $i => $line ) {
		$p = array_map( 'trim', explode( '|', $line ) );
		echo '<li>' . alph_icon( $icons[ $i % 4 ], 20 ) . '<span><strong>' . esc_html( $p[0] ) . '</strong>' . ( ! empty( $p[1] ) ? '<small>' . esc_html( $p[1] ) . '</small>' : '' ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul>';
}

/**
 * Vertical timeline from "year | title | text" lines.
 */
function alph_timeline( array $lines ) {
	if ( ! $lines ) {
		return;
	}
	echo '<ol class="timeline">';
	foreach ( $lines as $i => $line ) {
		$p = array_map( 'trim', explode( '|', $line ) );
		printf(
			'<li class="timeline__item timeline__item--%1$s"><span class="timeline__dot" aria-hidden="true"></span><div class="timeline__card"><p class="timeline__year">%2$s</p><h3>%3$s</h3><p>%4$s</p></div></li>',
			$i % 2 ? 'right' : 'left',
			esc_html( $p[0] ),
			esc_html( $p[1] ?? '' ),
			esc_html( $p[2] ?? '' )
		);
	}
	echo '</ol>';
}

/**
 * Image gallery mosaic.
 */
function alph_gallery( array $ids ) {
	if ( ! $ids ) {
		return;
	}
	echo '<div class="gallery">';
	foreach ( $ids as $i => $id ) {
		echo '<figure class="gallery__item gallery__item--' . ( 0 === $i ? 'wide' : 'std' ) . '">' . wp_get_attachment_image( $id, 'large', false, array( 'loading' => 'lazy' ) ) . '</figure>';
	}
	echo '</div>';
}

/**
 * Checklist.
 */
function alph_checklist( array $items, $class = '' ) {
	if ( ! $items ) {
		return;
	}
	echo '<ul class="checklist ' . esc_attr( $class ) . '">';
	foreach ( $items as $item ) {
		echo '<li>' . alph_icon( 'check', 17 ) . '<span>' . esc_html( $item ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul>';
}

/**
 * Navy call-to-action band.
 */
function alph_cta_band( $eyebrow, $title, $text, array $buttons ) {
	if ( '' === trim( $title ) ) {
		return;
	}
	echo '<section class="section container"><div class="ctaband">';
	echo '<div class="ctaband__copy">';
	if ( $eyebrow ) {
		echo '<span class="chip chip--glass">' . esc_html( $eyebrow ) . '</span>';
	}
	echo '<h2>' . esc_html( $title ) . '</h2>';
	if ( $text ) {
		echo '<p>' . esc_html( $text ) . '</p>';
	}
	echo '</div><div class="ctaband__actions">';
	foreach ( $buttons as $b ) {
		echo alph_button( $b[0], $b[1], $b[2] ?? 'amber', $b[3] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div></div></section>';
}

/**
 * Grid of product cards.
 */
function alph_product_grid( array $products, $columns = 4 ) {
	if ( ! $products || ! function_exists( 'alph_product_card' ) ) {
		return;
	}
	printf( '<div class="product-grid product-grid--%d">', (int) $columns );
	foreach ( $products as $product ) {
		alph_product_card( $product, 'h3', array( 'excerpt' => true ) );
	}
	echo '</div>';
}

/**
 * Embedded map for an address (no API key needed).
 */
function alph_map_embed( $query, $title ) {
	if ( '' === trim( (string) $query ) ) {
		return '';
	}
	return sprintf(
		'<iframe class="map" title="%1$s" src="https://maps.google.com/maps?q=%2$s&amp;t=m&amp;z=13&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
		esc_attr( $title ),
		rawurlencode( $query )
	);
}

/**
 * Donut chart (inline SVG) with a legend.
 *
 * @param array  $segments [ [label, value], ... ].
 * @param string $center   Big text in the middle.
 * @param string $sub      Small text in the middle.
 */
function alph_donut_svg( array $segments, $center = '', $sub = '' ) {
	$colors = array( '#2b3a8f', '#fda13c', '#2d7a58', '#8c5000', '#9caaff', '#d9483b' );
	$total  = array_sum( array_map( fn( $s ) => max( 0, (float) $s[1] ), $segments ) );
	$r      = 15.915; // Circumference ≈ 100, so values map straight to percentages.
	$offset = 25;     // Start at 12 o'clock.
	$rings  = '';
	$legend = '';
	foreach ( $segments as $i => $seg ) {
		$pct    = $total > 0 ? max( 0, (float) $seg[1] ) / $total * 100 : 0;
		$color  = $colors[ $i % count( $colors ) ];
		$rings .= sprintf( '<circle cx="21" cy="21" r="%1$s" fill="none" stroke="%2$s" stroke-width="5" stroke-dasharray="%3$.2f %4$.2f" stroke-dashoffset="%5$.2f"/>', $r, $color, $pct, 100 - $pct, $offset );
		$offset -= $pct;
		$legend .= sprintf( '<li><span class="swatch" style="background:%1$s"></span><span>%2$s</span><strong>%3$s%%</strong></li>', esc_attr( $color ), esc_html( $seg[0] ), esc_html( round( $pct ) ) );
	}
	if ( $total <= 0 ) {
		$rings = sprintf( '<circle cx="21" cy="21" r="%s" fill="none" stroke="#e8e6df" stroke-width="5"/>', $r );
	}
	return sprintf(
		'<div class="donut"><svg viewBox="0 0 42 42" role="img" aria-label="%1$s"><circle cx="21" cy="21" r="%2$s" fill="none" stroke="#eef1fa" stroke-width="5"/>%3$s</svg><div class="donut__center"><strong>%4$s</strong><small>%5$s</small></div></div><ul class="legend">%6$s</ul>',
		esc_attr( $sub ),
		$r,
		$rings,
		esc_html( $center ),
		esc_html( $sub ),
		$legend
	);
}
