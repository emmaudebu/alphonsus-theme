<?php
/**
 * Template Name: Sign In
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main page-auth">
	<div class="container">
		<?php
		if ( function_exists( 'alph_auth_open' ) ) {
			alph_auth_open( array( 'active' => 'login' ) );
			get_template_part( 'template-parts/auth/login-form' );
			alph_auth_close();
		}
		?>
	</div>
</main>
<?php
get_footer();
