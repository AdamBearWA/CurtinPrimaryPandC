<?php
/**
 * Shop landing (slug "shop") — "shop by category" tiles: Olive oil + Cards.
 * Each tile links to that category's page. Rendered in PHP (polished style)
 * rather than as editable blocks.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="cpc-collection cpc-container" style="padding-bottom:8px">
	<div class="cpc-collection-intro">
		<h1><?php esc_html_e( 'Shop', 'curtin-pc-shop' ); ?></h1>
		<p><?php esc_html_e( 'Two ranges, one cause. Everything we sell is created by our community, and 100% of profits support the Curtin Primary School P&C.', 'curtin-pc-shop' ); ?></p>
	</div>
</section>

<?php echo do_shortcode( '[cpc_category_tiles]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<?php
get_footer();
