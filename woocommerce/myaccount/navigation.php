<?php
/**
 * My Account navigation (overrides WooCommerce's myaccount/navigation.php).
 *
 * @package Alphonsus
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );
?>
<nav class="woocommerce-MyAccount-navigation account-nav" aria-label="<?php esc_attr_e( 'Account pages', 'alphonsus' ); ?>">
	<p class="account-nav__title"><?php esc_html_e( 'My account', 'alphonsus' ); ?></p>
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $alph_endpoint => $alph_label ) : ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $alph_endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $alph_endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $alph_endpoint ) ? 'aria-current="page"' : ''; ?>>
					<?php echo alph_account_icon( $alph_endpoint ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span><?php echo esc_html( $alph_label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php do_action( 'woocommerce_after_account_navigation' ); ?>
