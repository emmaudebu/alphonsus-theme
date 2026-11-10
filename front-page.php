<?php
/**
 * Homepage. Sections are edited in Appearance → Customize → Alphonsus Homepage & Site.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main home">
	<?php get_template_part( 'template-parts/home/hero' ); ?>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<div class="container"><?php alph_category_pills(); ?></div>

		<?php
		get_template_part(
			'template-parts/home/products',
			null,
			array(
				'id'    => 'story',
				'title' => alph_mod( 'story_title' ),
				'sub'   => alph_mod( 'story_sub' ),
				'cat'   => alph_mod( 'story_cat' ),
				'ids'   => alph_mod( 'story_ids' ),
				'link'  => alph_mod( 'story_link' ),
			)
		);

		get_template_part( 'template-parts/home/promo' );

		get_template_part(
			'template-parts/home/products',
			null,
			array(
				'id'    => 'swedish',
				'title' => alph_mod( 'swedish_title' ),
				'sub'   => alph_mod( 'swedish_sub' ),
				'cat'   => alph_mod( 'swedish_cat' ),
				'ids'   => alph_mod( 'swedish_ids' ),
				'note'  => alph_mod( 'swedish_link' ),
			)
		);

		get_template_part(
			'template-parts/home/products',
			null,
			array(
				'id'    => 'wellness',
				'title' => alph_mod( 'wellness_title' ),
				'sub'   => alph_mod( 'wellness_sub' ),
				'cat'   => alph_mod( 'wellness_cat' ),
				'ids'   => alph_mod( 'wellness_ids' ),
				'note'  => alph_mod( 'wellness_link' ),
			)
		);
		?>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/home/presale' ); ?>
	<?php get_template_part( 'template-parts/home/community' ); ?>
</main>
<?php
get_footer();
