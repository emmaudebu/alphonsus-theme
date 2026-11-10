<?php
/**
 * Foundation donations through the normal WooCommerce checkout (Paystack etc.).
 *
 * A hidden virtual product ("Foundation donation") is added to the cart with
 * the donor's chosen amount stored on the cart item; the price is set from it.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

const ALPH_DONATION_OPTION = 'alph_donation_product';
const ALPH_DONATION_MIN    = 500;
const ALPH_DONATION_MAX    = 50000000;

/**
 * ID of the donation product, creating it on first use.
 */
function alph_donation_product_id() {
	$id = (int) get_option( ALPH_DONATION_OPTION );
	if ( $id && 'product' === get_post_type( $id ) && 'trash' !== get_post_status( $id ) ) {
		return $id;
	}
	$product = new WC_Product_Simple();
	$product->set_name( __( 'Foundation donation', 'alphonsus' ) );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'hidden' );
	$product->set_virtual( true );
	$product->set_regular_price( (string) ALPH_DONATION_MIN );
	$product->set_tax_status( 'none' );
	$product->set_sold_individually( false );
	$product->set_short_description( __( 'A gift to the Alphonsus Foundation.', 'alphonsus' ) );
	$id = $product->save();
	update_option( ALPH_DONATION_OPTION, $id, false );
	return $id;
}

/**
 * Donation form: preset tiers + custom amount.
 *
 * @param string[] $tiers Lines "amount | title | text | tag".
 */
function alph_donation_form( array $tiers ) {
	$parsed = array();
	foreach ( $tiers as $line ) {
		$p = array_map( 'trim', explode( '|', $line ) );
		$a = (int) preg_replace( '/\D/', '', $p[0] ?? '' );
		if ( $a >= ALPH_DONATION_MIN ) {
			$parsed[] = array( $a, $p[1] ?? '', $p[2] ?? '', $p[3] ?? '' );
		}
	}
	$default = $parsed ? $parsed[ min( 1, count( $parsed ) - 1 ) ][0] : 10000;
	$symbol  = html_entity_decode( get_woocommerce_currency_symbol( get_option( 'woocommerce_currency' ) ) );
	$icons   = array( 'book-open', 'package', 'home' );
	?>
	<form class="donate__form" method="post" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-donate>
		<?php wp_nonce_field( 'alph_donate', '_alph_donate_nonce' ); ?>
		<input type="hidden" name="alph_donate" value="1">
		<?php alph_form_notice( 'donate' ); ?>
		<?php if ( $parsed ) : ?>
			<fieldset class="tiers">
				<legend class="screen-reader-text"><?php esc_html_e( 'Choose a gift', 'alphonsus' ); ?></legend>
				<?php foreach ( $parsed as $i => $tier ) : ?>
					<label class="tier<?php echo $tier[3] ? ' tier--tagged' : ''; ?>">
						<input type="radio" name="tier" value="<?php echo esc_attr( $tier[0] ); ?>" <?php checked( $tier[0], $default ); ?>>
						<?php if ( $tier[3] ) : ?>
							<span class="tier__tag"><?php echo esc_html( $tier[3] ); ?></span>
						<?php endif; ?>
						<span class="tier__top"><strong class="tier__amount"><?php echo esc_html( $symbol . number_format_i18n( $tier[0] ) ); ?></strong><?php echo alph_icon( $icons[ $i % 3 ], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="tier__title"><?php echo esc_html( $tier[1] ); ?></span>
						<span class="tier__text"><?php echo esc_html( $tier[2] ); ?></span>
					</label>
				<?php endforeach; ?>
			</fieldset>
		<?php endif; ?>
		<div class="donate__bar">
			<label for="donate-amount"><?php esc_html_e( 'Chosen contribution', 'alphonsus' ); ?></label>
			<span class="donate__amount"><span aria-hidden="true"><?php echo esc_html( $symbol ); ?></span><input id="donate-amount" name="amount" type="number" inputmode="numeric" min="<?php echo esc_attr( ALPH_DONATION_MIN ); ?>" step="100" value="<?php echo esc_attr( $default ); ?>" required></span>
			<span class="donate__once"><?php echo alph_icon( 'heart', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'One-time gift', 'alphonsus' ); ?></span>
			<button type="submit" class="btn btn--amber"><span><?php esc_html_e( 'Complete my gift', 'alphonsus' ); ?></span><?php echo alph_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</div>
		<p class="donate__fine"><?php echo alph_icon( 'shield', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'You’ll pay securely at checkout. Your receipt is emailed to you.', 'alphonsus' ); ?></p>
	</form>
	<?php
}

/**
 * Handle the donation form: add the donation to the cart and go to checkout.
 */
add_action(
	'wp_loaded',
	function () {
		if ( empty( $_POST['alph_donate'] ) ) {
			return;
		}
		$back = wp_get_referer() ? remove_query_arg( array( 'alph_error', 'alph_sent' ), wp_get_referer() ) : home_url( '/' );
		if ( ! isset( $_POST['_alph_donate_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_alph_donate_nonce'] ) ), 'alph_donate' ) ) {
			wp_safe_redirect( add_query_arg( 'alph_error', 'donate', $back ) . '#donate' );
			exit;
		}
		$amount = isset( $_POST['amount'] ) ? (int) $_POST['amount'] : 0;
		if ( $amount < ALPH_DONATION_MIN || $amount > ALPH_DONATION_MAX ) {
			wp_safe_redirect( add_query_arg( 'alph_error', 'donate', $back ) . '#donate' );
			exit;
		}
		if ( ! WC()->cart ) {
			wc_load_cart();
		}
		WC()->cart->add_to_cart( alph_donation_product_id(), 1, 0, array(), array( 'alph_donation' => $amount ) );
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	},
	30
);

/**
 * Price the donation line from the chosen amount (store currency).
 */
add_action(
	'woocommerce_before_calculate_totals',
	function ( $cart ) {
		foreach ( $cart->get_cart() as $item ) {
			if ( ! empty( $item['alph_donation'] ) ) {
				$item['data']->set_price( (float) $item['alph_donation'] );
			}
		}
	},
	5
);

// Keep the amount when the cart is restored from the session.
add_filter(
	'woocommerce_get_cart_item_from_session',
	function ( $item, $values ) {
		if ( ! empty( $values['alph_donation'] ) ) {
			$item['alph_donation'] = (int) $values['alph_donation'];
			$item['data']->set_price( (float) $values['alph_donation'] );
		}
		return $item;
	},
	10,
	2
);

// Donations can't change quantity and don't link to a product page.
add_filter(
	'woocommerce_cart_item_quantity',
	function ( $html, $key, $item ) {
		return ! empty( $item['alph_donation'] ) ? '1' : $html;
	},
	10,
	3
);
add_filter(
	'woocommerce_cart_item_permalink',
	function ( $url, $item ) {
		return ! empty( $item['alph_donation'] ) ? '' : $url;
	},
	10,
	2
);

// Record the donation on the order line.
add_action(
	'woocommerce_checkout_create_order_line_item',
	function ( $line, $key, $values ) {
		if ( ! empty( $values['alph_donation'] ) ) {
			$line->add_meta_data( __( 'Donation', 'alphonsus' ), wc_price( $values['alph_donation'] ), true );
		}
	},
	10,
	3
);

// The hidden product must never be bought at its placeholder price on its own.
add_filter(
	'woocommerce_add_to_cart_validation',
	function ( $valid, $product_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification -- only checks which form posted.
		if ( (int) $product_id === (int) get_option( ALPH_DONATION_OPTION ) && empty( $_POST['alph_donate'] ) ) {
			wc_add_notice( __( 'Please donate from the Foundation page.', 'alphonsus' ), 'error' );
			return false;
		}
		return $valid;
	},
	10,
	2
);
