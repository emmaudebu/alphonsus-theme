<?php
/**
 * Currency picker modal on top of "PSM Multi-Currency for WooCommerce".
 *
 * The plugin decides the visitor's currency from the `psmmc_currency` cookie;
 * this modal just lists the enabled currencies with their rates and sets it.
 * Without the plugin, nothing is rendered.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enabled currencies: code => [symbol, rate vs store currency, name].
 */
function alph_currencies() {
	$all = get_option( 'psmwoo_mc_currencies' );
	if ( ! is_array( $all ) || ! class_exists( 'PSMWOO_MC_Switcher' ) ) {
		return array();
	}
	$names = get_woocommerce_currencies();
	$base  = get_option( 'woocommerce_currency' );
	$out   = array();
	foreach ( $all as $code => $c ) {
		if ( empty( $c['enabled'] ) ) {
			continue;
		}
		$out[ $code ] = array(
			html_entity_decode( $c['symbol'] ?? get_woocommerce_currency_symbol( $code ) ),
			$code === $base ? 1.0 : (float) ( $c['rate'] ?? 0 ),
			$names[ $code ] ?? $code,
		);
	}
	return $out;
}

function alph_current_currency() {
	if ( class_exists( 'PSMWOO_MC_Switcher' ) && method_exists( 'PSMWOO_MC_Switcher', 'get_current_currency' ) ) {
		$code = PSMWOO_MC_Switcher::get_current_currency();
		if ( $code ) {
			return $code;
		}
	}
	return get_option( 'woocommerce_currency' );
}

/**
 * Top-bar trigger button.
 */
function alph_currency_button() {
	$list = alph_currencies();
	if ( count( $list ) < 2 ) {
		return;
	}
	$code = alph_current_currency();
	$sym  = $list[ $code ][0] ?? '';
	printf(
		'<button type="button" class="topbar__currency" data-currency-open aria-haspopup="dialog">%1$s (%2$s)%3$s</button>',
		esc_html( $code ),
		esc_html( $sym ),
		alph_icon( 'chevron', 13 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/**
 * The modal itself (printed once in the footer).
 */
add_action(
	'wp_footer',
	function () {
		$list = alph_currencies();
		if ( count( $list ) < 2 ) {
			return;
		}
		$base    = get_option( 'woocommerce_currency' );
		$current = alph_current_currency();
		$bsym    = $list[ $base ][0] ?? '';
		?>
		<dialog class="modal currency-modal" id="currency-modal" aria-labelledby="currency-title">
			<form method="dialog" class="modal__inner" data-currency-form>
				<header class="modal__head">
					<span class="modal__icon"><?php echo alph_icon( 'globe', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<div>
						<h2 id="currency-title"><?php esc_html_e( 'Select currency', 'alphonsus' ); ?></h2>
						<p><?php esc_html_e( 'Prices across the shop update to your chosen currency. Rates are set by Alphonsus Library.', 'alphonsus' ); ?></p>
					</div>
					<button class="icon-btn" value="cancel" aria-label="<?php esc_attr_e( 'Close', 'alphonsus' ); ?>"><?php echo alph_icon( 'close', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</header>
				<label class="modal__search"><?php echo alph_icon( 'search', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Search currencies', 'alphonsus' ); ?></span><input type="search" placeholder="<?php esc_attr_e( 'Search by name or code…', 'alphonsus' ); ?>" data-currency-search></label>
				<fieldset class="currency-list">
					<legend class="screen-reader-text"><?php esc_html_e( 'Currencies', 'alphonsus' ); ?></legend>
					<?php foreach ( $list as $code => $c ) : ?>
						<label class="currency-row" data-search="<?php echo esc_attr( strtolower( $code . ' ' . $c[2] ) ); ?>">
							<input type="radio" name="currency" value="<?php echo esc_attr( $code ); ?>" <?php checked( $current, $code ); ?>>
							<span class="currency-row__sym"><?php echo esc_html( $c[0] ); ?></span>
							<span class="currency-row__name"><strong><?php echo esc_html( $c[2] ); ?></strong><small><?php echo esc_html( $code ); ?><?php echo $code === $base ? ' · ' . esc_html__( 'Store currency', 'alphonsus' ) : ''; ?></small></span>
							<span class="currency-row__rate">
								<?php
								if ( $code !== $base && $c[1] > 0 ) {
									echo esc_html( $bsym . number_format_i18n( 1000 ) . ' ≈ ' . $c[0] . number_format_i18n( 1000 * $c[1], 2 ) );
								}
								?>
							</span>
						</label>
					<?php endforeach; ?>
				</fieldset>
				<footer class="modal__foot">
					<p><?php echo alph_icon( 'shield', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Checkout may charge in the store currency where a payment method requires it.', 'alphonsus' ); ?></p>
					<div>
						<button class="btn btn--ghost btn--sm" value="cancel"><?php esc_html_e( 'Cancel', 'alphonsus' ); ?></button>
						<button class="btn btn--primary btn--sm" value="save"><?php esc_html_e( 'Save preference', 'alphonsus' ); ?></button>
					</div>
				</footer>
			</form>
		</dialog>
		<?php
	}
);

// The plugin's own per-product switcher duplicates the modal.
add_filter(
	'option_psmwoo_mc_display_options',
	function ( $opts ) {
		if ( is_array( $opts ) ) {
			$opts['enableSingleProduct'] = false;
		}
		return $opts;
	}
);
