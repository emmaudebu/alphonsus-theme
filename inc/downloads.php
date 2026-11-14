<?php
/**
 * E-book downloads.
 *
 * - Download files kept in the uploads folder are handed to WooCommerce as a
 *   path relative to the WordPress folder. That skips its URL parsing, which
 *   garbles non-ASCII file names (e.g. "Limba-—-Print.pdf" → "File not found"),
 *   and also keeps links working when they still point at an old domain.
 * - Shop managers get a notice listing download files missing from the server.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

/**
 * This server's copy of a download that lives in any site's /wp-content/uploads/,
 * or '' when the link points elsewhere or the file isn't here.
 */
function alph_download_local_file( $url ) {
	$marker = '/wp-content/uploads/';
	$pos    = strpos( (string) $url, $marker );
	if ( false === $pos ) {
		return '';
	}
	$rest = strtok( substr( $url, $pos + strlen( $marker ) ), '?#' );
	$file = wp_get_upload_dir()['basedir'] . '/' . rawurldecode( $rest );
	return is_file( $file ) ? $file : '';
}

add_filter(
	'woocommerce_product_file_download_path',
	function ( $path ) {
		$file = alph_download_local_file( $path );
		return ( $file && 0 === strpos( $file, ABSPATH ) ) ? substr( $file, strlen( ABSPATH ) ) : $path;
	},
	20
);

/**
 * Download files meant to be in the uploads folder that aren't on this server.
 *
 * @return array[] Each item: [ 'product' => title, 'file' => path from wp-content ].
 */
function alph_missing_downloads() {
	global $wpdb;

	$missing = array();
	$rows    = $wpdb->get_results(
		"SELECT m.post_id, m.meta_value FROM {$wpdb->postmeta} m
		JOIN {$wpdb->posts} p ON p.ID = m.post_id
		WHERE m.meta_key = '_downloadable_files' AND p.post_status NOT IN ( 'trash', 'auto-draft' )"
	);

	foreach ( $rows as $row ) {
		foreach ( (array) maybe_unserialize( $row->meta_value ) as $file ) {
			$url = is_array( $file ) ? (string) ( $file['file'] ?? '' ) : '';
			if ( false === strpos( $url, '/wp-content/uploads/' ) || alph_download_local_file( $url ) ) {
				continue;
			}
			$post      = get_post( $row->post_id );
			$missing[] = array(
				'product' => get_the_title( $post->post_parent ? $post->post_parent : $post ),
				'file'    => rawurldecode( strtok( substr( $url, strpos( $url, '/wp-content/' ) + 1 ), '?#' ) ),
			);
		}
	}

	return $missing;
}

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! current_user_can( 'manage_woocommerce' ) || ! $screen || ! in_array( $screen->id, array( 'dashboard', 'edit-product', 'product', 'woocommerce_page_wc-orders', 'edit-shop_order' ), true ) ) {
			return;
		}

		$missing = alph_missing_downloads();
		if ( ! $missing ) {
			return;
		}
		?>
		<div class="notice notice-error">
			<p><strong>
				<?php
				/* translators: %d: number of files */
				echo esc_html( sprintf( _n( '%d e-book download file is missing from this server.', '%d e-book download files are missing from this server.', count( $missing ), 'alphonsus' ), count( $missing ) ) );
				?>
			</strong>
			<?php esc_html_e( 'Customers who click these downloads see “File not found”. Upload each file to the same place inside wp-content (for example by extracting the e-books zip in the wp-content folder).', 'alphonsus' ); ?></p>
			<ul style="list-style:disc;margin-left:20px">
				<?php foreach ( array_slice( $missing, 0, 8 ) as $item ) : ?>
					<li><?php echo esc_html( $item['product'] ); ?> — <code><?php echo esc_html( $item['file'] ); ?></code></li>
				<?php endforeach; ?>
				<?php if ( count( $missing ) > 8 ) : ?>
					<li>
						<?php
						/* translators: %d: number of further files */
						echo esc_html( sprintf( __( 'and %d more', 'alphonsus' ), count( $missing ) - 8 ) );
						?>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
	}
);
