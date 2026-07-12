<?php
/**
 * Art cards category page (slug "art-cards") — story band and the greeting-card
 * product grid. Mirrors the olive-oil page style.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- STORY BAND -->
<section id="cpc-story" class="cpc-story cpc-container">
	<h2><?php esc_html_e( 'The story behind the cards', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-story-cols">
		<div>
			<p><?php
				printf(
					/* translators: %s: artist name (bold) */
					esc_html__( 'In 2023 the whole Curtin Primary community — every student from kindy to year 6, alongside parents, families and staff — painted a single artwork with local artist %s. In 2024 we drew four floral images from it for our first set of cards, and the collection has been growing ever since.', 'curtin-pc-shop' ),
					'<b>' . esc_html__( 'Kelly Muller', 'curtin-pc-shop' ) . '</b>'
				);
			?></p>
			<p><?php esc_html_e( 'Each card celebrates the creativity of our school, making them perfect for birthdays, thank yous, celebrations or simply staying in touch.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<p><?php esc_html_e( 'Best of all, every purchase helps raise funds for the Curtin Primary P&C, supporting projects, resources and opportunities that benefit our students.', 'curtin-pc-shop' ); ?></p>
			<p><?php esc_html_e( 'Created by our community, for our community, these cards are a meaningful way to share a thoughtful message while giving back.', 'curtin-pc-shop' ); ?></p>
		</div>
	</div>
</section>

<!-- THE COLLECTION (product grid) -->
<div id="cpc-cards"></div>
<?php echo do_shortcode( '[cpc_products category="art-cards" heading="Art cards"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>


<?php
get_footer();
