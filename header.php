<?php
/**
 * Site header.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

$alph_has_wc   = class_exists( 'WooCommerce' );
$alph_phone    = alph_mod( 'phone' );
$alph_email    = alph_mod( 'email' );
$alph_contact  = get_page_by_path( 'contact' );
$alph_donate   = alph_url( 'donate_url' );
$alph_ext      = alph_is_external( $alph_donate ) ? ' target="_blank" rel="noopener"' : '';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'alphonsus' ); ?></a>

<div class="topbar">
	<div class="container topbar__inner">
		<ul class="topbar__info">
			<?php if ( $alph_phone || $alph_email ) : ?>
				<li>
					<?php echo alph_icon( 'phone', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php if ( $alph_phone ) : ?>
						<a href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D+/', '', $alph_phone ) ); ?>"><span class="topbar__label"><?php esc_html_e( 'Call/WhatsApp:', 'alphonsus' ); ?> </span><?php echo esc_html( $alph_phone ); ?></a>
					<?php endif; ?>
					<?php if ( $alph_email ) : ?>
						<span class="topbar__sep" aria-hidden="true">·</span><a href="mailto:<?php echo esc_attr( $alph_email ); ?>"><?php echo esc_html( $alph_email ); ?></a>
					<?php endif; ?>
				</li>
			<?php endif; ?>
			<?php if ( alph_mod( 'topbar_msg_1' ) ) : ?>
				<li class="topbar__msg"><?php echo alph_icon( 'languages', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( alph_mod( 'topbar_msg_1' ) ); ?></li>
			<?php endif; ?>
			<?php if ( alph_mod( 'topbar_msg_2' ) ) : ?>
				<li class="topbar__msg topbar__msg--strong"><?php echo esc_html( alph_mod( 'topbar_msg_2' ) ); ?></li>
			<?php endif; ?>
		</ul>
		<div class="topbar__tools">
			<?php
			if ( function_exists( 'alph_currency_button' ) ) {
				alph_currency_button();
			}
			?>
			<?php if ( $alph_contact ) : ?>
				<a class="topbar__support" href="<?php echo esc_url( get_permalink( $alph_contact ) ); ?>"><?php esc_html_e( 'Support', 'alphonsus' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>

<header class="site-header" data-sticky-header>
	<div class="container site-header__main">
		<button type="button" class="icon-btn nav-toggle" aria-controls="site-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'alphonsus' ); ?>">
			<?php echo alph_icon( 'menu', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>

		<?php alph_logo(); ?>

		<?php
		if ( $alph_has_wc ) {
			alph_search_form();
		} else {
			get_search_form();
		}
		?>

		<div class="header-actions">
			<button type="button" class="icon-btn search-toggle" aria-controls="header-search" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'alphonsus' ); ?>" data-search-toggle>
				<?php echo alph_icon( 'search', 21 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
			<?php if ( $alph_has_wc ) : ?>
				<a class="icon-btn" href="<?php echo esc_url( alph_wishlist_page_url() ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'alphonsus' ); ?>">
					<?php echo alph_icon( 'heart', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="count-dot wishlist-count<?php echo alph_wishlist_ids() ? '' : ' is-empty'; ?>" data-wishlist-count><?php echo esc_html( count( alph_wishlist_ids() ) ); ?></span>
				</a>
				<a class="account-link" href="<?php echo esc_url( is_user_logged_in() || ! function_exists( 'alph_auth_url' ) ? wc_get_page_permalink( 'myaccount' ) : alph_auth_url( 'login' ) ); ?>">
					<span class="account-link__icon"><?php echo alph_icon( 'user', 21 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="account-link__text"><small><?php echo is_user_logged_in() ? esc_html__( 'Reader & parent', 'alphonsus' ) : esc_html__( 'Sign in', 'alphonsus' ); ?></small><strong><?php esc_html_e( 'My Account', 'alphonsus' ); ?></strong></span>
				</a>
				<a class="header-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<span class="header-cart__icon"><?php echo alph_icon( 'bag', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo alph_cart_count_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="header-cart__text"><small><?php esc_html_e( 'Cart', 'alphonsus' ); ?></small><?php echo alph_cart_total_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</a>
			<?php endif; ?>
			<?php if ( $alph_donate ) : ?>
				<a class="btn btn--amber btn--sm donate-btn" href="<?php echo esc_url( $alph_donate ); ?>"<?php echo $alph_ext; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php echo alph_icon( 'hand-heart', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Donate', 'alphonsus' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="site-header__nav">
		<div class="container">
			<nav id="site-nav" class="site-nav" aria-label="<?php esc_attr_e( 'Main', 'alphonsus' ); ?>">
				<div class="site-nav__head">
					<?php alph_logo( 'site-logo site-logo--sm' ); ?>
					<button type="button" class="icon-btn nav-close" aria-label="<?php esc_attr_e( 'Close menu', 'alphonsus' ); ?>"><?php echo alph_icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</div>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'menu',
						'depth'          => 2,
						'fallback_cb'    => 'wp_page_menu',
					)
				);
				?>
				<div class="translate">
					<button type="button" class="translate__btn" data-translate aria-expanded="false" aria-controls="google_translate_element">
						<span class="dot" aria-hidden="true"></span><?php echo alph_icon( 'globe', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php esc_html_e( 'Language:', 'alphonsus' ); ?> <strong><?php esc_html_e( 'Choose Language', 'alphonsus' ); ?></strong></span>
					</button>
					<div id="google_translate_element" class="translate__panel" hidden></div>
				</div>
				<?php if ( $alph_donate ) : ?>
					<a class="btn btn--amber site-nav__donate" href="<?php echo esc_url( $alph_donate ); ?>"<?php echo $alph_ext; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo alph_icon( 'hand-heart', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Donate', 'alphonsus' ); ?></a>
				<?php endif; ?>
			</nav>
		</div>
	</div>
</header>
<div class="nav-overlay" hidden></div>
