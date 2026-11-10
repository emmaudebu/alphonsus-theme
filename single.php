<?php
/**
 * Single post (events & news).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		$alph_cat = get_the_category();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
			<header class="page-hero page-hero--post">
				<div class="container container--narrow">
					<?php if ( $alph_cat ) : ?>
						<p class="eyebrow"><a href="<?php echo esc_url( get_category_link( $alph_cat[0] ) ); ?>"><?php echo esc_html( $alph_cat[0]->name ); ?></a></p>
					<?php endif; ?>
					<h1 class="page-hero__title"><?php the_title(); ?></h1>
					<?php alph_post_meta(); ?>
				</div>
			</header>
			<div class="container container--narrow">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="single-post__image"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php
				the_post_navigation(
					array(
						'prev_text' => '<small>' . esc_html__( 'Previous', 'alphonsus' ) . '</small>%title',
						'next_text' => '<small>' . esc_html__( 'Next', 'alphonsus' ) . '</small>%title',
					)
				);
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
