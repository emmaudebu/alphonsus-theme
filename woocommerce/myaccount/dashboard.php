<?php
/**
 * My Account dashboard (overrides WooCommerce's myaccount/dashboard.php).
 *
 * @package Alphonsus
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$alph_user   = wp_get_current_user();
$alph_rj     = alph_rj_get();
$alph_today  = alph_rj_minutes_on( $alph_rj, alph_rj_today() );
$alph_goal   = (int) $alph_rj['goal'];
$alph_orders = wc_get_orders(
	array(
		'customer_id' => $alph_user->ID,
		'limit'       => 3,
		'orderby'     => 'date',
		'order'       => 'DESC',
		'status'      => array_keys( wc_get_order_statuses() ),
	)
);
$alph_enrol  = alph_customer_enrolments( $alph_user->user_email );
$alph_rj_url = wc_get_account_endpoint_url( ALPH_RJ_ENDPOINT );
?>
<section class="dash-block">
	<div class="dash-head">
		<div><p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Today', 'alphonsus' ); ?></p><h2 class="dash-title"><?php esc_html_e( 'Your Reading Journey', 'alphonsus' ); ?></h2></div>
		<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( $alph_rj_url ); ?>"><?php esc_html_e( 'Open tracker', 'alphonsus' ); ?></a>
	</div>
	<div class="card rj-mini">
		<div class="ring ring--sm">
			<?php echo alph_ring_svg( $alph_today, $alph_goal ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="ring__center"><strong><?php echo esc_html( $alph_today ); ?></strong><small>/ <?php echo esc_html( $alph_goal ); ?> min</small></div>
		</div>
		<div class="rj-mini__copy">
			<h3>
				<?php
				if ( $alph_today >= $alph_goal ) {
					esc_html_e( 'Goal reached today — wonderful!', 'alphonsus' );
				} else {
					/* translators: %d: minutes */
					echo esc_html( sprintf( __( '%d more minutes to reach today’s goal', 'alphonsus' ), $alph_goal - $alph_today ) );
				}
				?>
			</h3>
			<?php /* translators: 1: streak days, 2: XP */ ?>
			<p><?php echo esc_html( sprintf( __( '%1$d-day streak • %2$s XP', 'alphonsus' ), alph_rj_streak( $alph_rj ), number_format_i18n( alph_rj_xp( $alph_rj ) ) ) ); ?></p>
			<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $alph_rj_url . '#timer' ); ?>"><?php echo alph_icon( 'play', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Start reading', 'alphonsus' ); ?></span></a>
		</div>
	</div>
</section>

<?php if ( $alph_enrol ) : ?>
	<section class="dash-block">
		<div class="dash-head"><div><p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Swedish School', 'alphonsus' ); ?></p><h2 class="dash-title"><?php esc_html_e( 'Enrolment Requests', 'alphonsus' ); ?></h2></div></div>
		<?php foreach ( $alph_enrol as $alph_e ) : ?>
			<?php $alph_d = (array) get_post_meta( $alph_e->ID, '_alph_data', true ); ?>
			<div class="card enrol-card">
				<span class="chip chip--green"><?php esc_html_e( 'Request received', 'alphonsus' ); ?></span>
				<h3><?php echo esc_html( $alph_d['track'] ?? __( 'Swedish course', 'alphonsus' ) ); ?></h3>
				<?php /* translators: %s: date */ ?>
				<p><?php echo esc_html( sprintf( __( 'Sent %s', 'alphonsus' ), get_the_date( '', $alph_e ) ) . ( ! empty( $alph_d['schedule'] ) ? ' • ' . $alph_d['schedule'] : '' ) ); ?></p>
				<p class="enrol-card__note"><?php esc_html_e( 'Our team will contact you to confirm your class, schedule and fees.', 'alphonsus' ); ?></p>
			</div>
		<?php endforeach; ?>
	</section>
<?php endif; ?>

<section class="dash-block">
	<div class="dash-head">
		<div><p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Bookstore dispatches', 'alphonsus' ); ?></p><h2 class="dash-title"><?php esc_html_e( 'Recent Orders & Tracking', 'alphonsus' ); ?></h2></div>
		<?php if ( $alph_orders ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'All orders', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<?php endif; ?>
	</div>
	<?php if ( ! $alph_orders ) : ?>
		<div class="card empty-inline">
			<?php echo alph_icon( 'bag', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php esc_html_e( 'No orders yet. Your first book is waiting for you.', 'alphonsus' ); ?></p>
			<a class="btn btn--primary btn--sm" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the shop', 'alphonsus' ); ?></a>
		</div>
	<?php endif; ?>
	<?php foreach ( $alph_orders as $alph_order ) : ?>
		<?php
		list( $alph_steps, $alph_stopped ) = alph_order_steps( $alph_order );
		$alph_items                        = $alph_order->get_items();
		$alph_first                        = $alph_items ? reset( $alph_items ) : null;
		$alph_product                      = $alph_first ? $alph_first->get_product() : null;
		?>
		<article class="card order-card">
			<header class="order-card__head">
				<?php /* translators: %s: order number */ ?>
				<strong><?php echo esc_html( sprintf( __( 'Order #%s', 'alphonsus' ), $alph_order->get_order_number() ) ); ?></strong>
				<span><?php echo esc_html( wc_format_datetime( $alph_order->get_date_created() ) ); ?></span>
				<span class="status status--<?php echo esc_attr( $alph_order->get_status() ); ?>"><?php echo esc_html( wc_get_order_status_name( $alph_order->get_status() ) ); ?></span>
				<span class="order-card__total"><?php echo wp_kses_post( $alph_order->get_formatted_order_total() ); ?></span>
			</header>
			<div class="order-card__body">
				<?php if ( $alph_product ) : ?>
					<span class="order-card__thumb"><?php echo $alph_product->get_image( 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endif; ?>
				<div>
					<h3><?php echo esc_html( implode( ' + ', array_map( fn( $i ) => $i->get_name(), array_slice( $alph_items, 0, 2 ) ) ) . ( count( $alph_items ) > 2 ? ' …' : '' ) ); ?></h3>
					<?php /* translators: %d: items */ ?>
					<p><?php echo esc_html( sprintf( _n( '%d item', '%d items', $alph_order->get_item_count(), 'alphonsus' ), $alph_order->get_item_count() ) ); ?></p>
				</div>
				<div class="order-card__actions">
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $alph_order->get_view_order_url() ); ?>"><?php esc_html_e( 'View order', 'alphonsus' ); ?></a>
					<?php if ( $alph_order->needs_payment() ) : ?>
						<a class="btn btn--amber btn--sm" href="<?php echo esc_url( $alph_order->get_checkout_payment_url() ); ?>"><?php esc_html_e( 'Pay now', 'alphonsus' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( ! $alph_stopped ) : ?>
				<ol class="steps">
					<?php foreach ( $alph_steps as $alph_s ) : ?>
						<li class="<?php echo $alph_s[1] ? 'is-done' : ''; ?>"><span class="steps__dot"><?php echo $alph_s[1] ? alph_icon( 'tick', 13 ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><?php echo esc_html( $alph_s[0] ); ?></span></li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
</section>

<section class="dash-block">
	<div class="dash-head">
		<div><p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Account registry', 'alphonsus' ); ?></p><h2 class="dash-title"><?php esc_html_e( 'Profile & Addresses', 'alphonsus' ); ?></h2></div>
		<a class="link-arrow" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>"><?php esc_html_e( 'Edit profile', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
	</div>
	<div class="cards cards--3 cards--tight">
		<div class="card info-card">
			<p class="info-card__label"><?php echo alph_icon( 'user', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Your details', 'alphonsus' ); ?></p>
			<h3><?php echo esc_html( $alph_user->display_name ); ?></h3>
			<p><?php echo esc_html( $alph_user->user_email ); ?></p>
			<?php $alph_phone = get_user_meta( $alph_user->ID, 'billing_phone', true ); ?>
			<?php if ( $alph_phone ) : ?>
				<p><?php echo esc_html( $alph_phone ); ?></p>
			<?php endif; ?>
		</div>
		<?php foreach ( array( 'shipping' => __( 'Delivery address', 'alphonsus' ), 'billing' => __( 'Billing address', 'alphonsus' ) ) as $alph_type => $alph_label ) : ?>
			<?php $alph_addr = wc_get_account_formatted_address( $alph_type ); ?>
			<div class="card info-card">
				<p class="info-card__label"><?php echo alph_icon( 'map-pin', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $alph_label ); ?></p>
				<?php if ( $alph_addr ) : ?>
					<address><?php echo wp_kses_post( $alph_addr ); ?></address>
				<?php else : ?>
					<p class="muted"><?php esc_html_e( 'Not set yet.', 'alphonsus' ); ?></p>
				<?php endif; ?>
				<a class="link-arrow" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $alph_type ) ); ?>"><?php echo $alph_addr ? esc_html__( 'Change', 'alphonsus' ) : esc_html__( 'Add address', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php
// Recommendations: categories the customer bought from, else newest books.
$alph_owned = array_keys( alph_customer_products( $alph_user->ID ) );
$alph_cats  = array();
foreach ( $alph_owned as $alph_pid ) {
	$alph_cats = array_merge( $alph_cats, wp_get_post_terms( $alph_pid, 'product_cat', array( 'fields' => 'slugs' ) ) );
}
$alph_recs = wc_get_products(
	array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => 4,
		'exclude'    => array_merge( $alph_owned, array( (int) get_option( 'alph_donation_product' ) ) ),
		'category'   => array_values( array_unique( array_diff( $alph_cats, array( 'books' ) ) ) ),
		'orderby'    => 'date',
		'order'      => 'DESC',
	)
);
?>
<?php if ( $alph_recs ) : ?>
	<section class="dash-block">
		<div class="dash-head">
			<div><p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Handpicked for your journey', 'alphonsus' ); ?></p><h2 class="dash-title"><?php esc_html_e( 'Recommended for You', 'alphonsus' ); ?></h2></div>
			<a class="link-arrow" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Explore the shop', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<?php alph_product_grid( $alph_recs, 4 ); ?>
	</section>
<?php endif; ?>
