<?php
/**
 * Generic page template — centres any standard WordPress Page inside the
 * 1200px container with a styled title and no sidebar (matches the boutique look).
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="cpc-container cpc-page">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<h1 class="cpc-page-title"><?php the_title(); ?></h1>
		<div class="cpc-page-content"><?php the_content(); ?></div>
		<?php
	endwhile;
	?>
</div>

<?php
get_footer();
