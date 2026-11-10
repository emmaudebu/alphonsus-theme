<?php
/**
 * Blog / Events & News listing and archives.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() && get_option( 'page_for_posts' ) ) {
	$alph_title = get_the_title( get_option( 'page_for_posts' ) );
	$alph_sub   = __( 'Workshops, book launches, school visits and stories from our community.', 'alphonsus' );
} elseif ( is_search() ) {
	/* translators: %s: search query. */
	$alph_title = sprintf( __( 'Search results for “%s”', 'alphonsus' ), get_search_query() );
	$alph_sub   = '';
} elseif ( is_archive() ) {
	$alph_title = get_the_archive_title();
	$alph_sub   = get_the_archive_description();
} else {
	$alph_title = __( 'Events & News', 'alphonsus' );
	$alph_sub   = '';
}
?>
<main id="main" class="site-main">
	<?php alph_page_header( $alph_title, $alph_sub, __( 'Stay connected', 'alphonsus' ) ); ?>
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'card news-card' ); ?>>
						<a class="news-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'alph-wide', array( 'loading' => 'lazy' ) );
							} else {
								echo '<span class="news-card__placeholder">' . alph_icon( 'book', 40 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
						</a>
						<div class="news-card__body">
							<?php if ( 'post' === get_post_type() ) : ?>
								<?php alph_post_meta(); ?>
							<?php endif; ?>
							<h2 class="news-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
							<a class="link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'alphonsus' ); ?><span class="screen-reader-text">: <?php the_title(); ?></span><?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php alph_pagination(); ?>
		<?php else : ?>
			<div class="empty-state">
				<?php echo alph_icon( 'calendar', 40 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2><?php esc_html_e( 'Nothing here yet', 'alphonsus' ); ?></h2>
				<p><?php esc_html_e( 'New events and stories will appear here soon. Meanwhile, browse our books.', 'alphonsus' ); ?></p>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Visit the shop', 'alphonsus' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
