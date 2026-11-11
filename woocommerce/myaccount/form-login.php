<?php
/**
 * Signed-out My Account page (overrides WooCommerce's myaccount/form-login.php).
 *
 * @package Alphonsus
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );

alph_auth_open( array( 'active' => 'login' ) );
get_template_part( 'template-parts/auth/login-form', null, array( 'heading' => 'h2' ) );
alph_auth_close();

do_action( 'woocommerce_after_customer_login_form' );
