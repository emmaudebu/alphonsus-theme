<?php
/**
 * Template Name: Swedish School
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();

$alph_cat = get_term_by( 'slug', 'sweden-book', 'product_cat' );
?>
<main id="main" class="site-main page-swedish">
	<?php
	alph_split_hero(
		array(
			'badge'   => alph_mod( 'sw_badge' ),
			'eyebrow' => alph_mod( 'sw_eyebrow' ),
			'title'   => alph_mod( 'sw_title' ),
			'text'    => alph_mod( 'sw_text' ),
			'buttons' => array(
				array( __( 'Request enrolment', 'alphonsus' ), '#enrol', 'amber', 'arrow' ),
				array( __( 'Browse Swedish books', 'alphonsus' ), $alph_cat ? get_term_link( $alph_cat ) : '#books', 'ghost', 'book-open' ),
			),
			'stats'   => alph_lines( 'sw_stats', true ),
			'image'   => (int) alph_mod( 'sw_image' ),
			'quote'   => alph_mod( 'sw_quote' ),
		)
	);
	?>

	<?php $alph_features = alph_items_from_lines( 'sw_features' ); ?>
	<?php if ( $alph_features ) : ?>
		<section class="section container">
			<?php
			foreach ( $alph_features as $alph_i => &$alph_f ) {
				$alph_f['icon'] = array( 'video', 'headphones', 'users', 'award' )[ $alph_i % 4 ];
			}
			unset( $alph_f );
			alph_icon_cards( $alph_features, min( 4, count( $alph_features ) ) );
			?>
		</section>
	<?php endif; ?>

	<?php $alph_tracks = alph_lines( 'sw_tracks' ); ?>
	<?php if ( $alph_tracks ) : ?>
		<section class="band band--cream" id="tracks">
			<div class="container">
				<?php alph_section_head( array( 'eyebrow' => __( 'Syllabus & pathways', 'alphonsus' ), 'title' => __( 'Course Tracks', 'alphonsus' ), 'aside' => '<p class="shead__text">' . esc_html( alph_mod( 'sw_tracks_intro' ) ) . '</p>' ) ); ?>
				<div class="cards cards--<?php echo esc_attr( min( 4, count( $alph_tracks ) ) ); ?>">
					<?php foreach ( $alph_tracks as $alph_i => $alph_line ) : ?>
						<?php $alph_t = array_pad( array_map( 'trim', explode( '|', $alph_line ) ), 7, '' ); ?>
						<article class="track<?php echo $alph_t[6] ? ' track--tagged' : ''; ?>">
							<?php if ( $alph_t[6] ) : ?>
								<span class="track__tag"><?php echo esc_html( $alph_t[6] ); ?></span>
							<?php endif; ?>
							<span class="track__level"><?php echo esc_html( $alph_t[1] ); ?></span>
							<h3><?php echo esc_html( $alph_t[0] ); ?></h3>
							<p><?php echo esc_html( $alph_t[2] ); ?></p>
							<?php alph_checklist( array_filter( array_map( 'trim', explode( ';', $alph_t[3] ) ) ), 'track__list' ); ?>
							<dl class="track__meta">
								<?php if ( $alph_t[4] ) : ?>
									<div><dt><?php esc_html_e( 'Duration', 'alphonsus' ); ?></dt><dd><?php echo esc_html( $alph_t[4] ); ?></dd></div>
								<?php endif; ?>
								<?php if ( $alph_t[5] ) : ?>
									<div><dt><?php esc_html_e( 'Tuition', 'alphonsus' ); ?></dt><dd class="track__price"><?php echo esc_html( $alph_t[5] ); ?></dd></div>
								<?php endif; ?>
							</dl>
							<a class="btn btn--<?php echo $alph_t[6] ? 'amber' : 'primary'; ?> btn--block" href="#enrol" data-track="<?php echo esc_attr( $alph_t[0] ); ?>"><span><?php esc_html_e( 'Enrol', 'alphonsus' ); ?></span></a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_tt = alph_lines( 'sw_timetable' ); ?>
	<?php if ( $alph_tt ) : ?>
		<section class="section container">
			<div class="panel panel--lift">
				<?php alph_section_head( array( 'eyebrow' => __( 'Flexible schedules', 'alphonsus' ), 'title' => __( 'Live Class Timetable', 'alphonsus' ), 'align' => 'left' ) ); ?>
				<div class="table-wrap">
					<table class="table">
						<thead><tr><th><?php esc_html_e( 'Track & level', 'alphonsus' ); ?></th><th><?php esc_html_e( 'Days', 'alphonsus' ); ?></th><th><?php esc_html_e( 'Time', 'alphonsus' ); ?></th><th><?php esc_html_e( 'Format', 'alphonsus' ); ?></th><th><?php esc_html_e( 'Seats', 'alphonsus' ); ?></th><th><span class="screen-reader-text"><?php esc_html_e( 'Action', 'alphonsus' ); ?></span></th></tr></thead>
						<tbody>
							<?php foreach ( $alph_tt as $alph_line ) : ?>
								<?php $alph_r = array_pad( array_map( 'trim', explode( '|', $alph_line ) ), 5, '' ); ?>
								<tr>
									<td><span class="dot dot--navy" aria-hidden="true"></span><strong><?php echo esc_html( $alph_r[0] ); ?></strong></td>
									<td><?php echo esc_html( $alph_r[1] ); ?></td>
									<td><?php echo esc_html( $alph_r[2] ); ?></td>
									<td><?php echo esc_html( $alph_r[3] ); ?></td>
									<td><?php if ( $alph_r[4] ) : ?><span class="pill-tag"><?php echo esc_html( $alph_r[4] ); ?></span><?php endif; ?></td>
									<td><a class="link-arrow" href="#enrol" data-track="<?php echo esc_attr( $alph_r[0] ); ?>"><?php esc_html_e( 'Select', 'alphonsus' ); ?></a></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php $alph_faculty = alph_lines( 'sw_faculty' ); ?>
	<?php if ( $alph_faculty ) : ?>
		<section class="band band--cream">
			<div class="container">
				<?php alph_section_head( array( 'eyebrow' => __( 'Pedagogical mastery', 'alphonsus' ), 'title' => __( 'Our Faculty', 'alphonsus' ) ) ); ?>
				<div class="cards cards--3">
					<?php foreach ( $alph_faculty as $alph_line ) : ?>
						<?php $alph_m = array_pad( array_map( 'trim', explode( '|', $alph_line ) ), 4, '' ); ?>
						<article class="person">
							<?php if ( $alph_m[3] && wp_attachment_is_image( (int) $alph_m[3] ) ) : ?>
								<div class="person__photo"><?php echo wp_get_attachment_image( (int) $alph_m[3], 'alph-wide', false, array( 'loading' => 'lazy' ) ); ?></div>
							<?php endif; ?>
							<div class="person__body">
								<h3><?php echo esc_html( $alph_m[0] ); ?></h3>
								<p class="person__role"><?php echo esc_html( $alph_m[1] ); ?></p>
								<p><?php echo esc_html( $alph_m[2] ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$alph_results = alph_lines( 'sw_results', true );
	$alph_quotes  = alph_lines( 'sw_testimonials' );
	?>
	<?php if ( $alph_results || $alph_quotes ) : ?>
		<section class="section container">
			<div class="results">
				<?php if ( $alph_results ) : ?>
					<div class="results__stats">
						<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Proven results', 'alphonsus' ); ?></p>
						<?php foreach ( $alph_results as $alph_r ) : ?>
							<?php $alph_pct = min( 100, max( 0, (int) $alph_r[1] ) ); ?>
							<div class="meter meter--light">
								<div class="meter__head"><strong><?php echo esc_html( $alph_r[0] ); ?></strong><span><?php echo esc_html( $alph_pct ); ?>%</span></div>
								<div class="meter__bar"><span style="width:<?php echo esc_attr( $alph_pct ); ?>%"></span></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( $alph_quotes ) : ?>
					<div class="results__quotes"><?php alph_testimonials( $alph_quotes ); ?></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$alph_books = function_exists( 'alph_section_products' ) ? alph_section_products( 'sweden-book', '', 8 ) : array();
	if ( $alph_books ) :
		?>
		<section class="section container" id="books">
			<?php
			alph_section_head(
				array(
					'eyebrow' => __( 'Course materials', 'alphonsus' ),
					'title'   => __( 'Swedish Books for Every Level', 'alphonsus' ),
					'text'    => __( 'Textbooks, workbooks and pronunciation guides used across our A1–B1 pathway.', 'alphonsus' ),
					'aside'   => $alph_cat ? '<a class="link-arrow" href="' . esc_url( get_term_link( $alph_cat ) ) . '">' . esc_html__( 'All Swedish books', 'alphonsus' ) . alph_icon( 'arrow', 15 ) . '</a>' : '',
				)
			);
			alph_product_grid( $alph_books, 4 );
			?>
		</section>
	<?php endif; ?>

	<section class="section container" id="enrol" aria-labelledby="enrol-title">
		<div class="panel panel--lift split-form">
			<div class="split-form__copy">
				<p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Direct registration', 'alphonsus' ); ?></p>
				<h2 id="enrol-title" class="shead__title"><?php esc_html_e( 'Reserve Your Seat for the Upcoming Term', 'alphonsus' ); ?></h2>
				<p class="shead__text"><?php esc_html_e( 'Tell us your level and goals. We’ll confirm the right class, schedule and fees before you pay anything.', 'alphonsus' ); ?></p>
				<?php
				alph_checklist(
					array(
						__( 'A short level check if you’re not sure where to start', 'alphonsus' ),
						__( 'Course books available from our shop', 'alphonsus' ),
						__( 'Answers on WhatsApp or email — whichever you prefer', 'alphonsus' ),
					)
				);
				?>
			</div>
			<div class="split-form__form"><?php get_template_part( 'template-parts/form', 'enrol' ); ?></div>
		</div>
	</section>

	<?php $alph_faq = alph_lines( 'sw_faq' ); ?>
	<?php if ( $alph_faq ) : ?>
		<section class="section container container--mid">
			<?php alph_section_head( array( 'eyebrow' => __( 'Clarity & support', 'alphonsus' ), 'title' => __( 'Frequently Asked Questions', 'alphonsus' ), 'align' => 'center' ) ); ?>
			<?php alph_faq( $alph_faq ); ?>
		</section>
	<?php endif; ?>
</main>
<?php
get_footer();
