<?php
/**
 * Product card in shop loops (overrides WooCommerce's content-product.php).
 *
 * @package Alphonsus
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'product-grid__item', $product ); ?>>
	<?php alph_product_card( $product, 'h2' ); ?>
</li>
