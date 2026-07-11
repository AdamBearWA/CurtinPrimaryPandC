<?php
/**
 * Template Name: Full-width canvas
 *
 * Renders a Page's block content edge-to-edge — no page title and none of
 * the padded .cpc-page wrapper — so the boutique section blocks (hero,
 * story band, collection, olive-oil sections, etc.) display exactly as
 * designed. Assign this template to Home, Shop, Olive oil and Cards so
 * their whole layout stays editable in the block editor.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="cpc-canvas"><?php the_content(); ?></div>
	<?php
endwhile;

get_footer();
