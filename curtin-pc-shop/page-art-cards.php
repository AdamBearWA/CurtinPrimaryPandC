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
	<h2><?php esc_html_e( 'From a school-hall canvas to your letterbox', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-story-cols">
		<p><?php
			printf(
				/* translators: %s: artist name (bold) */
				esc_html__( 'In 2023 the whole community — every student from kindy to year 6, with parents, families and staff — painted one artwork alongside artist %s.', 'curtin-pc-shop' ),
				'<b>' . esc_html__( 'Kelly Muller', 'curtin-pc-shop' ) . '</b>'
			);
		?></p>
		<p><?php esc_html_e( 'In 2024 we drew four images from it for our first set of cards. The collection has been growing ever since.', 'curtin-pc-shop' ); ?></p>
	</div>
</section>

<!-- THE COLLECTION (product grid) -->
<div id="cpc-cards"></div>
<?php echo do_shortcode( '[cpc_products category="art-cards" heading="Art cards"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<!-- THE STORY BEHIND THE CARDS (navy story band — reuses the olive-oil "Our Story" component) -->
<section class="cpc-story cpc-container" style="margin-bottom:64px">
	<h2><?php esc_html_e( 'The story behind the cards', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-story-cols">
		<div>
			<p><?php esc_html_e( 'The Curtin Primary P&C Art Card Series showcases a beautiful collection of floral illustrations created by students, parents and staff from the Curtin Primary School community as part of a whole-school art project facilitated by local artist Kelly Muller.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<p><?php esc_html_e( 'Each card celebrates the creativity of our school, making them perfect for birthdays, thank yous, celebrations or simply staying in touch. Best of all, every purchase helps raise funds for the Curtin Primary P&C, supporting projects, resources and opportunities that benefit our students.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<p><?php esc_html_e( 'Created by our community, for our community, these cards are a meaningful way to share a thoughtful message while giving back to the Curtin Primary School P&C community.', 'curtin-pc-shop' ); ?></p>
		</div>
	</div>
</section>

<?php
get_footer();
