<?php
/**
 * Book Shop: sidebar filters (categories, attributes, price, stock).
 *
 * Query string: cats[]=slug, level[]=slug, language[]=slug, age[]=slug (attribute
 * terms, see ALPH_FILTER_ATTRS), price=min-max, instock=1, q (keyword).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filterable product attributes: query key => [taxonomy, heading].
 * Groups whose taxonomy has no terms in use are not shown.
 */
function alph_filter_attrs() {
	return array(
		'level'    => array( 'pa_level', __( 'Swedish CEFR level', 'alphonsus' ) ),
		'language' => array( 'pa_language', __( 'Language', 'alphonsus' ) ),
		'age'      => array( 'pa_age-range', __( 'Reader age range', 'alphonsus' ) ),
		'format'   => array( 'pa_format', __( 'Book format', 'alphonsus' ) ),
	);
}

/**
 * Price bands (store currency): key => [min, max, label].
 */
function alph_price_bands() {
	$sym = html_entity_decode( get_woocommerce_currency_symbol( get_option( 'woocommerce_currency' ) ) );
	return array(
		'0-15000'      => array( 0, 15000, sprintf( /* translators: %s: price */ __( 'Up to %s', 'alphonsus' ), $sym . '15k' ) ),
		'15000-25000'  => array( 15000, 25000, $sym . '15k – ' . $sym . '25k' ),
		'25000-999999' => array( 25000, 999999, sprintf( /* translators: %s: price */ __( 'Over %s', 'alphonsus' ), $sym . '25k' ) ),
	);
}

/**
 * Sanitised array of slugs from a query param.
 */
function alph_query_slugs( $key ) {
	// phpcs:ignore WordPress.Security.NonceVerification -- read-only catalogue filters.
	return isset( $_GET[ $key ] ) ? array_values( array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET[ $key ] ) ) ) ) : array();
}

/**
 * Apply the filters to the main shop query.
 */
add_action(
	'woocommerce_product_query',
	function ( WP_Query $q ) {
		$tax = (array) $q->get( 'tax_query' );

		$cats = alph_query_slugs( 'cats' );
		if ( $cats ) {
			$tax[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $cats,
				'operator' => 'IN',
			);
		}
		foreach ( alph_filter_attrs() as $key => $attr ) {
			$terms = alph_query_slugs( $key );
			if ( $terms && taxonomy_exists( $attr[0] ) ) {
				$tax[] = array(
					'taxonomy' => $attr[0],
					'field'    => 'slug',
					'terms'    => $terms,
					'operator' => 'IN',
				);
			}
		}
		if ( count( $tax ) > 1 ) {
			$tax['relation'] = 'AND';
		}
		$q->set( 'tax_query', $tax );

		$meta = (array) $q->get( 'meta_query' );
		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! empty( $_GET['instock'] ) ) {
			$meta[] = array(
				'key'   => '_stock_status',
				'value' => 'instock',
			);
		}
		$band = isset( $_GET['price'] ) ? sanitize_text_field( wp_unslash( $_GET['price'] ) ) : '';
		// phpcs:enable
		$bands = alph_price_bands();
		if ( isset( $bands[ $band ] ) ) {
			$meta[] = array(
				'key'     => '_price',
				'value'   => array( $bands[ $band ][0], $bands[ $band ][1] ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			);
		}
		$q->set( 'meta_query', $meta );

		// phpcs:ignore WordPress.Security.NonceVerification
		$kw = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		if ( '' !== $kw ) {
			$q->set( 's', $kw );
		}
	}
);

/**
 * Is any sidebar filter active?
 */
function alph_active_filters() {
	$active = array();
	$base   = alph_shop_base_url();
	// phpcs:disable WordPress.Security.NonceVerification
	foreach ( alph_query_slugs( 'cats' ) as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term ) {
			$active[] = array( $term->name, remove_query_arg( 'paged', alph_url_without( 'cats', $slug ) ) );
		}
	}
	foreach ( alph_filter_attrs() as $key => $attr ) {
		foreach ( alph_query_slugs( $key ) as $slug ) {
			$term = get_term_by( 'slug', $slug, $attr[0] );
			if ( $term ) {
				$active[] = array( $term->name, alph_url_without( $key, $slug ) );
			}
		}
	}
	$bands = alph_price_bands();
	if ( isset( $_GET['price'], $bands[ $_GET['price'] ] ) ) {
		$active[] = array( $bands[ $_GET['price'] ][2], remove_query_arg( 'price' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}
	if ( ! empty( $_GET['instock'] ) ) {
		$active[] = array( __( 'In stock', 'alphonsus' ), remove_query_arg( 'instock' ) );
	}
	if ( ! empty( $_GET['q'] ) ) {
		$active[] = array( '“' . sanitize_text_field( wp_unslash( $_GET['q'] ) ) . '”', remove_query_arg( 'q' ) );
	}
	// phpcs:enable
	return array( $active, $base );
}

/**
 * Current URL with one value removed from an array param.
 */
function alph_url_without( $key, $value ) {
	$values = array_values( array_diff( alph_query_slugs( $key ), array( $value ) ) );
	$url    = remove_query_arg( array( $key, 'paged' ) );
	return $values ? add_query_arg( array( $key => $values ), $url ) : $url;
}

/**
 * The archive URL without any filters (but keeping the category archive).
 */
function alph_shop_base_url() {
	if ( is_product_category() || is_product_tag() ) {
		return get_term_link( get_queried_object() );
	}
	return wc_get_page_permalink( 'shop' );
}

/**
 * Sidebar filter form.
 */
function alph_shop_filters() {
	$current_cat = is_product_category() ? get_queried_object() : null;
	$cats        = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	$checked     = alph_query_slugs( 'cats' );
	$bands       = alph_price_bands();
	// phpcs:disable WordPress.Security.NonceVerification
	$band  = isset( $_GET['price'] ) ? sanitize_text_field( wp_unslash( $_GET['price'] ) ) : '';
	$kw    = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	$stock = ! empty( $_GET['instock'] );
	$order = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : '';
	// phpcs:enable
	?>
	<form class="filters" method="get" action="<?php echo esc_url( alph_shop_base_url() ); ?>" data-filters>
		<?php if ( $order ) : ?>
			<input type="hidden" name="orderby" value="<?php echo esc_attr( $order ); ?>">
		<?php endif; ?>

		<div class="filters__box">
			<label class="filters__label" for="filter-q"><?php esc_html_e( 'Find within catalog', 'alphonsus' ); ?></label>
			<span class="filters__search"><?php echo alph_icon( 'search', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><input id="filter-q" type="search" name="q" value="<?php echo esc_attr( $kw ); ?>" placeholder="<?php esc_attr_e( 'Title, author or keyword…', 'alphonsus' ); ?>"></span>
		</div>

		<div class="filters__box">
			<?php if ( ! $current_cat && $cats && ! is_wp_error( $cats ) ) : ?>
				<fieldset class="filters__group">
					<legend class="filters__title"><?php esc_html_e( 'Categories', 'alphonsus' ); ?></legend>
					<?php foreach ( $cats as $cat ) : ?>
						<label class="fcheck">
							<input type="checkbox" name="cats[]" value="<?php echo esc_attr( $cat->slug ); ?>" <?php checked( in_array( $cat->slug, $checked, true ) ); ?>>
							<span><?php echo esc_html( $cat->name ); ?></span>
							<small><?php echo esc_html( $cat->count ); ?></small>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>

			<?php foreach ( alph_filter_attrs() as $key => $attr ) : ?>
				<?php
				if ( ! taxonomy_exists( $attr[0] ) ) {
					continue;
				}
				$terms = get_terms(
					array(
						'taxonomy'   => $attr[0],
						'hide_empty' => true,
					)
				);
				if ( ! $terms || is_wp_error( $terms ) ) {
					continue;
				}
				$chosen = alph_query_slugs( $key );
				?>
				<fieldset class="filters__group">
					<legend class="filters__title"><?php echo esc_html( $attr[1] ); ?></legend>
					<div class="<?php echo 'age' === $key || 'level' === $key ? 'fchips' : 'fchecks'; ?>">
						<?php foreach ( $terms as $term ) : ?>
							<label class="<?php echo 'age' === $key || 'level' === $key ? 'fchip' : 'fcheck'; ?>">
								<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( in_array( $term->slug, $chosen, true ) ); ?>>
								<span><?php echo esc_html( $term->name ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endforeach; ?>

			<fieldset class="filters__group">
				<legend class="filters__title"><?php esc_html_e( 'Price', 'alphonsus' ); ?></legend>
				<div class="fchips">
					<?php foreach ( $bands as $value => $b ) : ?>
						<label class="fchip">
							<input type="radio" name="price" value="<?php echo esc_attr( $value ); ?>" <?php checked( $band, $value ); ?>>
							<span><?php echo esc_html( $b[2] ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<fieldset class="filters__group">
				<legend class="filters__title"><?php esc_html_e( 'Availability', 'alphonsus' ); ?></legend>
				<label class="fcheck"><input type="checkbox" name="instock" value="1" <?php checked( $stock ); ?>><span><?php esc_html_e( 'In stock only', 'alphonsus' ); ?></span></label>
			</fieldset>

			<div class="filters__actions">
				<button type="submit" class="btn btn--primary btn--sm"><?php esc_html_e( 'Apply filters', 'alphonsus' ); ?></button>
				<a class="filters__reset" href="<?php echo esc_url( alph_shop_base_url() ); ?>"><?php esc_html_e( 'Reset', 'alphonsus' ); ?></a>
			</div>
		</div>

		<?php $contact = get_page_by_path( 'contact' ); ?>
		<div class="filters__promo">
			<p class="filters__promo-title"><?php echo alph_icon( 'school', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'shop_bulk_title' ) ); ?></p>
			<p><?php echo esc_html( alph_mod( 'shop_bulk_text' ) ); ?></p>
			<?php if ( $contact ) : ?>
				<a class="link-arrow" href="<?php echo esc_url( get_permalink( $contact ) ); ?>"><?php esc_html_e( 'Contact us', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php endif; ?>
		</div>
	</form>
	<?php
}

/**
 * Number of published, visible books (for "{count} titles" in the header cards).
 */
function alph_catalog_count() {
	$count = get_transient( 'alph_catalog_count' );
	if ( false === $count ) {
		$count = count(
			wc_get_products(
				array(
					'status'     => 'publish',
					'visibility' => 'catalog',
					'limit'      => -1,
					'return'     => 'ids',
				)
			)
		);
		set_transient( 'alph_catalog_count', $count, HOUR_IN_SECONDS );
	}
	return (int) $count;
}
add_action(
	'save_post_product',
	function () {
		delete_transient( 'alph_catalog_count' );
	}
);
