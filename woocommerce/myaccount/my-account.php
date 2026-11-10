<?php
/**
 * My Account layout (overrides WooCommerce's myaccount/my-account.php).
 *
 * @package Alphonsus
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$alph_user  = wp_get_current_user();
$alph_rj    = alph_rj_get();
$alph_name  = $alph_user->first_name ? $alph_user->first_name : $alph_user->display_name;
$alph_stats = array(
	array( __( 'Reading streak', 'alphonsus' ), sprintf( /* translators: %d: days */ _n( '%d day', '%d days', alph_rj_streak( $alph_rj ), 'alphonsus' ), alph_rj_streak( $alph_rj ) ), wc_get_account_endpoint_url( ALPH_RJ_ENDPOINT ) ),
	array( __( 'Books ordered', 'alphonsus' ), number_format_i18n( alph_customer_book_count( $alph_user->ID ) ), wc_get_account_endpoint_url( 'orders' ) ),
	array( __( 'Saved wishlist', 'alphonsus' ), sprintf( /* translators: %d: titles */ _n( '%d title', '%d titles', count( alph_wishlist_ids() ), 'alphonsus' ), count( alph_wishlist_ids() ) ), alph_wishlist_page_url() ),
	array( __( 'Polyglot XP', 'alphonsus' ), number_format_i18n( alph_rj_xp( $alph_rj ) ), wc_get_account_endpoint_url( ALPH_RJ_ENDPOINT ) ),
);
?>
<div class="account">
	<header class="account-hero card">
		<div class="account-hero__who">
			<span class="account-hero__avatar"><?php echo get_avatar( $alph_user->ID, 72, 'mystery', '' ); ?></span>
			<div>
				<?php /* translators: %s: first name */ ?>
				<h2 class="account-hero__title"><?php echo esc_html( sprintf( __( 'Welcome back, %s!', 'alphonsus' ), $alph_name ) ); ?></h2>
				<p class="account-hero__meta">
					<span class="chip chip--gold"><?php echo alph_icon( 'award', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_rj_level( alph_rj_xp( $alph_rj ) )[0] ); ?></span>
					<?php /* translators: %s: month and year */ ?>
					<span><?php echo esc_html( sprintf( __( 'Member since %s', 'alphonsus' ), wp_date( 'F Y', strtotime( $alph_user->user_registered ) ) ) ); ?></span>
				</p>
			</div>
		</div>
		<dl class="account-hero__stats">
			<?php foreach ( $alph_stats as $alph_s ) : ?>
				<div><dt><?php echo esc_html( $alph_s[0] ); ?></dt><dd><a href="<?php echo esc_url( $alph_s[2] ); ?>"><?php echo esc_html( $alph_s[1] ); ?></a></dd></div>
			<?php endforeach; ?>
		</dl>
	</header>

	<div class="account__grid">
		<aside class="account__side">
			<?php do_action( 'woocommerce_account_navigation' ); ?>
			<div class="side-promo">
				<p class="side-promo__title"><?php echo alph_icon( 'heart', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Sponsor a reading corner', 'alphonsus' ); ?></p>
				<p><?php esc_html_e( 'Your gifts help put books in the hands of children who need them most.', 'alphonsus' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( alph_url( 'donate_url' ) ); ?>"><?php esc_html_e( 'Support the foundation', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>
		</aside>
		<div class="woocommerce-MyAccount-content account__content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</div>
