<?php
/**
 * Header product search: category filter + live suggestions.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'alphonsus/v1',
			'/search',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'args'                => array(
					'q'   => array(
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'cat' => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_title',
					),
				),
				'callback'            => function ( WP_REST_Request $request ) {
					$q = trim( $request['q'] );
					if ( mb_strlen( $q ) < 2 ) {
						return array();
					}
					$args = array(
						'status'     => 'publish',
						'visibility' => 'search',
						'limit'      => 6,
						's'          => mb_substr( $q, 0, 80 ),
					);
					if ( $request['cat'] ) {
						$args['category'] = array( $request['cat'] );
					}
					$out = array();
					foreach ( wc_get_products( $args ) as $product ) {
						$out[] = array(
							'title' => $product->get_name(),
							'url'   => $product->get_permalink(),
							'price' => wp_strip_all_tags( html_entity_decode( wc_price( wc_get_price_to_display( $product ) ) ) ),
							'image' => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ),
						);
					}
					return $out;
				},
			)
		);
	}
);

/**
 * Header search form.
 */
function alph_search_form( $id = 'header-search' ) {
	$cats    = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	$current = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-live-search>
		<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>-cat"><?php esc_html_e( 'Category', 'alphonsus' ); ?></label>
		<span class="search__cat">
			<select id="<?php echo esc_attr( $id ); ?>-cat" name="product_cat">
				<option value=""><?php esc_html_e( 'All Categories', 'alphonsus' ); ?></option>
				<?php foreach ( (array) $cats as $cat ) : ?>
					<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $current, $cat->slug ); ?>><?php echo esc_html( $cat->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php echo alph_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</span>
		<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Search books', 'alphonsus' ); ?></label>
		<span class="search__field">
			<?php echo alph_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<input id="<?php echo esc_attr( $id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search books, authors, Swedish workbooks, ISBN…', 'alphonsus' ); ?>" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="<?php echo esc_attr( $id ); ?>-results" aria-autocomplete="list">
			<kbd class="search__kbd" aria-hidden="true">/</kbd>
		</span>
		<input type="hidden" name="post_type" value="product">
		<button type="submit" class="screen-reader-text"><?php esc_html_e( 'Search', 'alphonsus' ); ?></button>
		<div class="search__results" id="<?php echo esc_attr( $id ); ?>-results" role="listbox" hidden></div>
	</form>
	<?php
}
