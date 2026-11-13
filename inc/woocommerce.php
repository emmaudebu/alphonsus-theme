<?php
/**
 * WooCommerce integration.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 480,
				'single_image_width'    => 720,
				'product_grid'          => array(
					'default_columns' => 4,
					'default_rows'    => 4,
					'min_columns'     => 2,
					'max_columns'     => 4,
				),
			)
		);
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
			wp_enqueue_script( 'wc-add-to-cart' );
		}
	}
);

// Only the layout-neutral WooCommerce stylesheet; the theme styles the rest.
add_filter(
	'woocommerce_enqueue_styles',
	function ( $styles ) {
		unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
		return $styles;
	}
);

/*
 * Page wrappers.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action(
	'woocommerce_before_main_content',
	function () {
		echo '<main id="main" class="site-main shop-main"><div class="container">';
	},
	10
);
add_action(
	'woocommerce_after_main_content',
	function () {
		echo '</div></main>';
	},
	10
);

add_filter(
	'woocommerce_breadcrumb_defaults',
	function ( $args ) {
		$args['delimiter']   = '<span class="sep" aria-hidden="true">/</span>';
		$args['wrap_before'] = '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'alphonsus' ) . '">';
		$args['wrap_after']  = '</nav>';
		return $args;
	}
);

add_filter(
	'woocommerce_output_related_products_args',
	function ( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}
);

add_filter(
	'woocommerce_upsell_display_args',
	function ( $args ) {
		$args['columns'] = 4;
		return $args;
	}
);

/*
 * Product card fields: author line and corner badge.
 */
add_action(
	'woocommerce_product_options_general_product_data',
	function () {
		echo '<div class="options_group">';
		woocommerce_wp_text_input(
			array(
				'id'          => '_alph_author',
				'label'       => __( 'Author line', 'alphonsus' ),
				'description' => __( 'Shown above the title on product cards, e.g. "A story by Chika Alphonsus". Leave blank to use the default author.', 'alphonsus' ),
				'desc_tip'    => true,
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'          => '_alph_badge',
				'label'       => __( 'Card badge', 'alphonsus' ),
				'description' => __( 'Small label on the product image, e.g. "Ages 4–8", "Beginner A1", "Staff Pick". Sale items show "Sale" automatically.', 'alphonsus' ),
				'desc_tip'    => true,
			)
		);
		echo '</div>';
	}
);

add_action(
	'woocommerce_admin_process_product_object',
	function ( WC_Product $product ) {
		foreach ( array( '_alph_author', '_alph_badge' ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce verifies the product form nonce.
				$product->update_meta_data( $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
			}
		}
	}
);

/**
 * Star rating, only when the product has real reviews.
 */
function alph_stars( WC_Product $product ) {
	$count = $product->get_rating_count();
	if ( ! $count ) {
		return '';
	}
	$avg = (float) $product->get_average_rating();
	/* translators: 1: average rating, 2: number of reviews. */
	$label = sprintf( _n( 'Rated %1$s out of 5 from %2$d review', 'Rated %1$s out of 5 from %2$d reviews', $count, 'alphonsus' ), number_format_i18n( $avg, 1 ), $count );
	return sprintf(
		'<div class="stars" role="img" aria-label="%1$s"><span class="stars__track"><span class="stars__fill" style="width:%2$s%%"></span></span><span class="stars__count">(%3$d)</span></div>',
		esc_attr( $label ),
		esc_attr( round( $avg / 5 * 100 ) ),
		(int) $count
	);
}

/**
 * Badge text for a product card.
 */
function alph_product_badge( WC_Product $product ) {
	$badge = (string) $product->get_meta( '_alph_badge' );
	if ( '' === $badge && $product->is_on_sale() ) {
		$badge = __( 'Sale', 'alphonsus' );
	}
	if ( ! $product->is_in_stock() ) {
		$badge = __( 'Sold out', 'alphonsus' );
	}
	return $badge;
}

/**
 * Add-to-cart (AJAX for simple products) or "View" button for a card.
 */
function alph_card_button( WC_Product $product, $large = false, $labelled = false ) {
	if ( $large ) {
		$cls   = 'btn btn--amber';
		$label = __( 'Add bundle to cart', 'alphonsus' );
	} elseif ( $labelled ) {
		$cls   = 'btn btn--primary btn--sm';
		$label = __( 'Add to cart', 'alphonsus' );
	} else {
		$cls   = 'btn-add';
		$label = __( 'Add', 'alphonsus' );
	}
	if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
		return sprintf(
			'<a href="%1$s" data-quantity="1" data-product_id="%2$d" data-product_sku="%3$s" class="%7$s add_to_cart_button ajax_add_to_cart product_type_simple" aria-label="%4$s" rel="nofollow">%5$s<span>%6$s</span></a>',
			esc_url( $product->add_to_cart_url() ),
			$product->get_id(),
			esc_attr( $product->get_sku() ),
			esc_attr( $product->add_to_cart_description() ),
			alph_icon( 'bag', 15 ),
			esc_html( $label ),
			esc_attr( $cls )
		);
	}
	return sprintf(
		'<a href="%1$s" class="%5$s" aria-label="%2$s">%3$s<span>%4$s</span></a>',
		esc_url( $product->get_permalink() ),
		/* translators: %s: product name. */
		esc_attr( sprintf( __( 'View options for %s', 'alphonsus' ), $product->get_name() ) ),
		alph_icon( 'arrow', 15 ),
		$product->is_in_stock() ? esc_html__( 'View', 'alphonsus' ) : esc_html__( 'Details', 'alphonsus' ),
		esc_attr( $cls )
	);
}

/**
 * Author line for a product ("By …"), falling back to the default author.
 */
function alph_product_author( WC_Product $product ) {
	$author = trim( (string) $product->get_meta( '_alph_author' ) );
	if ( '' === $author && alph_is_merch( $product ) ) {
		return ''; // Umbrellas and socks don't have an author.
	}
	$author = '' !== $author ? $author : (string) alph_mod( 'default_author' );
	if ( '' === $author ) {
		return '';
	}
	// "A story by Chika…" already reads naturally; plain names get "By".
	return preg_match( '/\bby\b/i', $author ) ? $author : sprintf( /* translators: %s: author name */ __( 'By %s', 'alphonsus' ), $author );
}

/**
 * Is the product in the Merch category (or one of its sub-categories)?
 */
function alph_is_merch( WC_Product $product ) {
	$merch = get_term_by( 'slug', alph_mod( 'mc_cat' ), 'product_cat' );
	if ( ! $merch ) {
		return false;
	}
	$id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	foreach ( wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'ids' ) ) as $term_id ) {
		if ( (int) $term_id === (int) $merch->term_id || term_is_ancestor_of( $merch->term_id, $term_id, 'product_cat' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * The most specific category name to show on a card (skips the generic "Books").
 */
function alph_product_kicker( WC_Product $product ) {
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	// Prefer a sub-category (e.g. "Umbrellas" over "Merch").
	foreach ( $terms as $term ) {
		if ( $term->parent && 'books' !== $term->slug ) {
			return $term->name;
		}
	}
	foreach ( $terms as $term ) {
		if ( 'books' !== $term->slug ) {
			return $term->name;
		}
	}
	return $terms[0]->name;
}

/**
 * Compact rating for cards — only when there are real reviews.
 */
function alph_rating_mini( WC_Product $product ) {
	$count = $product->get_rating_count();
	if ( ! $count ) {
		return '';
	}
	return sprintf(
		'<span class="rating-mini" aria-label="%1$s">%2$s<strong>%3$s</strong><small>(%4$d)</small></span>',
		/* translators: 1: average rating, 2: review count */
		esc_attr( sprintf( __( 'Rated %1$s out of 5 from %2$d reviews', 'alphonsus' ), number_format_i18n( (float) $product->get_average_rating(), 1 ), $count ) ),
		alph_icon( 'star', 13 ),
		esc_html( number_format_i18n( (float) $product->get_average_rating(), 2 ) ),
		(int) $count
	);
}

/**
 * Product card markup (homepage sections, shop loops, page grids, wishlist).
 *
 * @param WC_Product $product Product.
 * @param string     $heading h2 or h3.
 * @param array      $opts    excerpt (bool): show a short description line.
 */
function alph_product_card( WC_Product $product, $heading = 'h3', array $opts = array() ) {
	$badge  = alph_product_badge( $product );
	$author = alph_product_author( $product );
	$kicker = alph_product_kicker( $product );
	$url    = $product->get_permalink();
	$tag    = in_array( $heading, array( 'h2', 'h3' ), true ) ? $heading : 'h3';
	$meta   = wp_get_attachment_metadata( (int) $product->get_image_id() );
	$wide   = ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['width'] > $meta['height'] * 1.15;
	$text   = ! empty( $opts['excerpt'] ) ? wp_trim_words( wp_strip_all_tags( $product->get_short_description() ? $product->get_short_description() : $product->get_description() ), 14 ) : '';
	?>
	<article class="card product-card<?php echo $product->is_on_sale() ? ' is-sale' : ''; ?>">
		<div class="product-card__media<?php echo $wide ? ' product-card__media--wide' : ''; ?>">
			<a href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
				<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<?php if ( $badge ) : ?>
				<span class="badge badge--<?php echo esc_attr( alph_badge_tone( $badge ) ); ?>"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
			<?php echo alph_wishlist_button( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="product-card__body">
			<?php if ( $kicker || $product->get_rating_count() ) : ?>
				<div class="product-card__meta"><span><?php echo esc_html( $kicker ); ?></span><?php echo alph_rating_mini( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<<?php echo esc_html( $tag ); ?> class="product-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></<?php echo esc_html( $tag ); ?>>
			<?php if ( $author ) : ?>
				<p class="product-card__author"><?php echo esc_html( $author ); ?></p>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<p class="product-card__text"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
			<div class="product-card__foot">
				<span class="price"><?php echo alph_card_price_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php echo alph_card_button( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Card price: products with options show "From ₦…" instead of a full range.
 */
function alph_card_price_html( WC_Product $product ) {
	if ( $product->is_type( 'variable' ) ) {
		$min = (float) $product->get_variation_price( 'min', true );
		$max = (float) $product->get_variation_price( 'max', true );
		if ( $min !== $max ) {
			/* translators: %s: lowest price */
			return '<span class="price__from">' . esc_html__( 'From', 'alphonsus' ) . '</span> ' . wc_price( $min );
		}
	}
	return $product->get_price_html();
}

/**
 * Bundle spotlight banner (Wellness and Merch pages).
 */
function alph_bundle_spotlight( WC_Product $product ) {
	$regular = (float) $product->get_regular_price();
	$price   = (float) $product->get_price();
	$save    = $product->is_on_sale() && $regular > 0 ? round( ( $regular - $price ) / $regular * 100 ) : 0;
	?>
	<div class="spotlight">
		<div class="spotlight__copy">
			<span class="chip chip--paper"><?php echo alph_icon( 'gift', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $save ? sprintf( /* translators: %d: percent */ __( 'Bundle offer • Save %d%%', 'alphonsus' ), $save ) : __( 'Bundle offer', 'alphonsus' ) ); ?></span>
			<h2 class="shead__title"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h2>
			<?php if ( $product->get_short_description() ) : ?>
				<p class="shead__text"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 40 ) ); ?></p>
			<?php endif; ?>
			<div class="spotlight__buy">
				<span class="price price--lg"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php echo alph_card_button( $product, true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
		<a class="spotlight__media" href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true"><?php echo $product->get_image( 'large', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
	</div>
	<?php
}

/**
 * Pick a badge colour from its wording so badges vary like the design.
 */
function alph_badge_tone( $badge ) {
	$b = strtolower( $badge );
	if ( preg_match( '/sale|sold|limited|hot/', $b ) ) {
		return 'red';
	}
	if ( preg_match( '/pick|new|self|care|health/', $b ) ) {
		return 'green';
	}
	if ( preg_match( '/age|beginner|vol|best/', $b ) ) {
		return 'brown';
	}
	if ( preg_match( '/travel|guide/', $b ) ) {
		return 'light';
	}
	return 'navy';
}

/**
 * Products for a homepage section: pinned IDs first, then fill from category.
 *
 * @return WC_Product[]
 */
function alph_section_products( $category, $ids = '', $limit = 4 ) {
	$products = array();
	$pinned   = array_filter( array_map( 'absint', explode( ',', (string) $ids ) ) );

	foreach ( $pinned as $id ) {
		$product = wc_get_product( $id );
		if ( $product && 'publish' === $product->get_status() && $product->is_visible() ) {
			$products[ $id ] = $product;
		}
	}

	if ( count( $products ) < $limit && $category ) {
		$more = wc_get_products(
			array(
				'status'     => 'publish',
				'visibility' => 'catalog',
				'category'   => array( $category ),
				'limit'      => $limit,
				'exclude'    => array_keys( $products ),
				'orderby'    => 'date',
				'order'      => 'DESC',
			)
		);
		foreach ( $more as $product ) {
			$products[ $product->get_id() ] = $product;
		}
	}

	return array_slice( $products, 0, $limit );
}

/**
 * URL for a product category slug (or pass through a URL).
 */
function alph_term_url( $slug_or_url ) {
	if ( preg_match( '#^(https?:)?/#', $slug_or_url ) ) {
		return 0 === strpos( $slug_or_url, '/' ) && 0 !== strpos( $slug_or_url, '//' ) ? home_url( $slug_or_url ) : $slug_or_url;
	}
	$term = get_term_by( 'slug', $slug_or_url, 'product_cat' );
	return $term ? get_term_link( $term ) : home_url( '/' );
}

/**
 * Category pill links; the current category is highlighted.
 */
function alph_category_pills() {
	$current = is_product_category() ? get_queried_object()->slug : ( is_shop() ? 'books' : '' );
	echo '<nav class="pills" aria-label="' . esc_attr__( 'Book categories', 'alphonsus' ) . '">';
	foreach ( alph_lines( 'pills', true ) as $pill ) {
		list( $label, $target ) = $pill;
		$active                 = $target === $current || ( is_front_page() && 'books' === $target );
		printf(
			'<a class="pill%1$s" href="%2$s"%3$s>%4$s</a>',
			$active ? ' is-active' : '',
			esc_url( alph_term_url( $target ) ),
			$active ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}
	echo '</nav>';
}

/*
 * Header cart count + total, refreshed by WooCommerce cart fragments.
 */
function alph_cart_count_html() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="count-dot cart-count' . ( $count ? '' : ' is-empty' ) . '">' . esc_html( $count ) . '</span>';
}

function alph_cart_total_html() {
	return '<span class="cart-total">' . ( WC()->cart ? WC()->cart->get_cart_subtotal() : wc_price( 0 ) ) . '</span>';
}

add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		$fragments['span.cart-count'] = alph_cart_count_html();
		$fragments['span.cart-total'] = alph_cart_total_html();
		return $fragments;
	}
);

/*
 * Single product.
 */
add_action(
	'woocommerce_after_add_to_cart_button',
	function () {
		global $product;
		if ( $product ) {
			echo alph_wishlist_button( $product->get_id(), true ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
);

add_filter(
	'woocommerce_product_related_products_heading',
	function () {
		return __( 'Curated Companions', 'alphonsus' );
	}
);

add_filter(
	'loop_shop_columns',
	function () {
		return 3;
	}
);

add_filter(
	'loop_shop_per_page',
	function () {
		return 12;
	},
	20
);

/*
 * WooCommerce product-grid blocks (e.g. "New in store" in the empty cart)
 * render the theme's product cards instead of the plain block grid.
 */
foreach ( array( 'product-new', 'product-best-sellers', 'product-on-sale', 'product-top-rated', 'product-category' ) as $alph_block ) {
	add_filter(
		'render_block_woocommerce/' . $alph_block,
		function ( $html, $block ) use ( $alph_block ) {
			$attrs = $block['attrs'] ?? array();
			$limit = max( 1, (int) ( $attrs['columns'] ?? 4 ) * (int) ( $attrs['rows'] ?? 1 ) );
			$args  = array(
				'status'     => 'publish',
				'visibility' => 'catalog',
				'limit'      => $limit,
				'exclude'    => array( (int) get_option( 'alph_donation_product' ) ),
			);
			if ( ! empty( $attrs['categories'] ) ) {
				$args['category'] = array_filter( array_map( fn( $id ) => ( $t = get_term( (int) $id, 'product_cat' ) ) && ! is_wp_error( $t ) ? $t->slug : '', (array) $attrs['categories'] ) );
			}
			switch ( $alph_block ) {
				case 'product-best-sellers':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
					$args['order']    = 'DESC';
					break;
				case 'product-on-sale':
					$args['include'] = wc_get_product_ids_on_sale();
					if ( ! $args['include'] ) {
						return '';
					}
					break;
				case 'product-top-rated':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery
					$args['order']    = 'DESC';
					break;
				default:
					$args['orderby'] = 'date';
					$args['order']   = 'DESC';
			}
			$products = wc_get_products( $args );
			if ( ! $products ) {
				return $html;
			}
			ob_start();
			echo '<div class="alph-block-grid">';
			alph_product_grid( $products, min( 4, max( 2, (int) ( $attrs['columns'] ?? 4 ) ) ) );
			echo '</div>';
			return ob_get_clean();
		},
		10,
		2
	);
}

/**
 * Empty cart: friendlier message and next steps under the title.
 */
add_filter(
	'render_block_woocommerce/empty-cart-block',
	function ( $html ) {
		$wish  = function_exists( 'alph_wishlist_ids' ) ? count( alph_wishlist_ids() ) : 0;
		$extra = '<p class="empty-cart__text">' . esc_html__( 'Looks like you haven’t added any books yet. Explore story books, Swedish courses and wellness guides — or pick up where you left off.', 'alphonsus' ) . '</p>';
		$extra .= '<div class="empty-cart__actions">';
		$extra .= '<a class="btn btn--primary" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . alph_icon( 'book-open', 17 ) . '<span>' . esc_html__( 'Browse the book shop', 'alphonsus' ) . '</span></a>';
		if ( $wish && function_exists( 'alph_wishlist_page_url' ) ) {
			/* translators: %d: number of saved books */
			$extra .= '<a class="btn btn--ghost" href="' . esc_url( alph_wishlist_page_url() ) . '">' . alph_icon( 'heart', 17 ) . '<span>' . esc_html( sprintf( _n( 'View wishlist (%d)', 'View wishlist (%d)', $wish, 'alphonsus' ), $wish ) ) . '</span></a>';
		}
		$extra .= '</div>';
		// Insert right after the first heading (the "cart is empty" title).
		$pos = strpos( $html, '</h2>' );
		return false === $pos ? $html : substr_replace( $html, '</h2>' . $extra, $pos, 5 );
	}
);
