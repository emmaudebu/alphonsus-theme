<?php
/**
 * Template Name: Wishlist
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main page-wishlist">
	<div class="container">
		<?php
		if ( function_exists( 'alph_wishlist_ids' ) ) {
			get_template_part( 'template-parts/wishlist', null, array( 'heading' => 'h1' ) );
		}
		?>
	</div>
</main>
<?php
get_footer();
