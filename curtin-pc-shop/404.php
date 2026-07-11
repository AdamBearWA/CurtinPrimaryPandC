<?php
/**
 * 404 — branded "page not found" inside the boutique shell.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="cpc-container cpc-page cpc-404">
	<h1 class="cpc-page-title"><?php esc_html_e( 'Page not found', 'curtin-pc-shop' ); ?></h1>
	<p class="cpc-404-text"><?php esc_html_e( "The page you're after isn't here. Let's get you back to the cards.", 'curtin-pc-shop' ); ?></p>
	<div class="cpc-404-actions">
		<a class="cpc-btn cpc-cta" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'curtin-pc-shop' ); ?></a>
		<a class="cpc-lnk cpc-cta-text" href="<?php echo esc_url( cpc_shop_url() ); ?>"><?php esc_html_e( 'Browse the cards', 'curtin-pc-shop' ); ?></a>
	</div>
</div>

<?php
get_footer();
