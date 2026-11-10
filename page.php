<?php
/**
 * Default page template (block editor content).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alph_is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		alph_page_header( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
		?>
		<div class="container">
			<article id="post-<?php the_ID(); ?>" <?php post_class( $alph_is_wc_page ? 'entry entry--wide' : 'entry' ); ?>>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
			</article>
		</div>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
