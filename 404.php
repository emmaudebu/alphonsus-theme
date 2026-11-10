<?php
/**
 * Not found.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<div class="container">
		<div class="empty-state empty-state--page">
			<p class="eyebrow"><?php esc_html_e( 'Error 404', 'alphonsus' ); ?></p>
			<h1 class="section-title section-title--dark"><?php esc_html_e( 'This page has wandered off the shelf', 'alphonsus' ); ?></h1>
			<p><?php esc_html_e( 'The page you’re looking for doesn’t exist or has moved. Try a search, or head back to the shop.', 'alphonsus' ); ?></p>
			<div class="empty-state__search">
				<?php
				if ( function_exists( 'alph_search_form' ) ) {
					alph_search_form( 'notfound-search' );
				} else {
					get_search_form();
				}
				?>
			</div>
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to homepage', 'alphonsus' ); ?></a>
		</div>
	</div>
</main>
<?php
get_footer();
