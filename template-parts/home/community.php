<?php
/**
 * Events & news (latest post) beside the foundation card.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

$alph_events = get_category_by_slug( 'events' );
$alph_query  = new WP_Query(
	array(
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'cat'                 => ( $alph_events && $alph_events->count ) ? $alph_events->term_id : 0,
	)
);
$alph_blog   = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : '';
?>
<section class="section container" id="events" aria-labelledby="events-title">
	<div class="section-head">
		<div>
			<?php if ( alph_mod( 'news_eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( alph_mod( 'news_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h2 class="section-title section-title--dark" id="events-title"><?php echo esc_html( alph_mod( 'news_title' ) ); ?></h2>
		</div>
		<?php if ( $alph_blog && $alph_query->have_posts() ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( $alph_blog ); ?>"><?php esc_html_e( 'All events & news', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<?php endif; ?>
	</div>

	<div class="community<?php echo $alph_query->have_posts() ? '' : ' community--solo'; ?>">
		<?php
		while ( $alph_query->have_posts() ) :
			$alph_query->the_post();
			$alph_cat = get_the_category();
			?>
			<article class="card news-card">
				<a class="news-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'alph-wide', array( 'loading' => 'lazy' ) );
					} else {
						echo '<span class="news-card__placeholder">' . alph_icon( 'calendar', 48 ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
					<?php if ( $alph_cat ) : ?>
						<span class="badge badge--light"><?php echo esc_html( $alph_cat[0]->name ); ?></span>
					<?php endif; ?>
				</a>
				<div class="news-card__body">
					<?php alph_post_meta(); ?>
					<h3 class="news-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 40 ) ); ?></p>
					<a class="link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'alphonsus' ); ?><span class="screen-reader-text">: <?php the_title(); ?></span><?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				</div>
			</article>
			<?php
		endwhile;
		wp_reset_postdata();
		?>

		<aside class="foundation-card" aria-labelledby="foundation-title">
			<div class="foundation-card__main">
				<span class="chip chip--glass"><?php echo alph_icon( 'users', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'found_badge' ) ); ?></span>
				<h3 id="foundation-title"><?php echo esc_html( alph_mod( 'found_title' ) ); ?></h3>
				<p><?php echo esc_html( alph_mod( 'found_text' ) ); ?></p>
				<?php if ( alph_lines( 'found_points' ) ) : ?>
					<ul>
						<?php foreach ( alph_lines( 'found_points' ) as $alph_point ) : ?>
							<li><?php echo esc_html( $alph_point ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="foundation-card__actions">
				<?php if ( alph_mod( 'donate_url' ) ) : ?>
					<a class="btn btn--amber btn--block" href="<?php echo esc_url( alph_url( 'donate_url' ) ); ?>"><?php echo alph_icon( 'heart', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'found_btn' ) ); ?></a>
				<?php endif; ?>
				<?php $alph_found_page = get_page_by_path( 'foundation' ); ?>
				<?php if ( $alph_found_page ) : ?>
					<a class="link-arrow link-arrow--light" href="<?php echo esc_url( get_permalink( $alph_found_page ) ); ?>"><?php esc_html_e( 'Learn about the foundation', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
			</div>
		</aside>
	</div>
</section>
