<?php
/**
 * Pre-order form (homepage pre-sales block, or [alphonsus_preorder_form]).
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<h3 class="form__title"><?php esc_html_e( 'Fill in to pre-order', 'alphonsus' ); ?></h3>
	<p class="form__hint"><?php esc_html_e( 'Select multiple titles to qualify for combined shipping rates.', 'alphonsus' ); ?></p>

	<?php alph_form_notice( 'preorder' ); ?>
	<?php alph_form_hidden( 'preorder' ); ?>

	<div class="form__row">
		<p class="field">
			<label for="po-first"><?php esc_html_e( 'First name', 'alphonsus' ); ?> <span class="req">*</span></label>
			<input id="po-first" name="first_name" type="text" required autocomplete="given-name" placeholder="<?php esc_attr_e( 'e.g. Chidera', 'alphonsus' ); ?>">
		</p>
		<p class="field">
			<label for="po-last"><?php esc_html_e( 'Last name', 'alphonsus' ); ?> <span class="req">*</span></label>
			<input id="po-last" name="last_name" type="text" required autocomplete="family-name" placeholder="<?php esc_attr_e( 'e.g. Adeyemi', 'alphonsus' ); ?>">
		</p>
	</div>
	<div class="form__row">
		<p class="field">
			<label for="po-email"><?php esc_html_e( 'Email address', 'alphonsus' ); ?> <span class="req">*</span></label>
			<input id="po-email" name="email" type="email" required autocomplete="email" placeholder="name@example.com">
		</p>
		<p class="field">
			<label for="po-phone"><?php esc_html_e( 'Phone number', 'alphonsus' ); ?> <span class="req">*</span></label>
			<input id="po-phone" name="phone" type="tel" required autocomplete="tel" placeholder="+234 800 000 0000">
		</p>
	</div>

	<fieldset class="field">
		<legend><?php esc_html_e( 'Select books for pre-order', 'alphonsus' ); ?> <span class="req">*</span></legend>
		<div class="check-grid" data-require-one>
			<?php foreach ( alph_lines( 'presale_books' ) as $alph_i => $alph_book ) : ?>
				<label class="check">
					<input type="checkbox" name="books[]" value="<?php echo esc_attr( $alph_book ); ?>">
					<span><?php echo esc_html( $alph_book ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</fieldset>

	<button type="submit" class="btn btn--primary"><?php echo alph_icon( 'send', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Pre-sale order', 'alphonsus' ); ?></button>
</form>
