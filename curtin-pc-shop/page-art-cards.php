<?php
/**
 * Art Cards category page (slug "art-cards") — Butterfly Garden hero, story band,
 * and the greeting-card product grid. Mirrors the olive-oil page style.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/* ---- Hero image: the Butterfly Garden set (falls back gracefully) --- */
$card_hero = function_exists( 'cpc_hero_product' ) ? cpc_hero_product() : null;
$card_img  = '';
$card_alt  = __( 'The Butterfly Garden card set', 'curtin-pc-shop' );
if ( $card_hero && $card_hero->get_image_id() ) {
	$src = wp_get_attachment_image_url( $card_hero->get_image_id(), 'large' );
	if ( $src ) {
		$card_img = $src;
	}
	$card_alt = $card_hero->get_name();
}
if ( ! $card_img && function_exists( 'wc_placeholder_img_src' ) ) {
	$card_img = wc_placeholder_img_src( 'large' );
}
?>

<!-- HERO -->
<section class="cpc-hero cpc-container">
	<div class="cpc-hero-copy">
		<div class="cpc-eyebrow"><?php esc_html_e( 'The Butterfly Garden', 'curtin-pc-shop' ); ?></div>
		<h1 class="cpc-h1"><?php esc_html_e( 'Cards our whole school painted together', 'curtin-pc-shop' ); ?></h1>
		<p class="cpc-hero-lede"><?php esc_html_e( 'One big community artwork, turned into sets of four greeting cards. Blank inside, ready to send — and every set funds our classrooms.', 'curtin-pc-shop' ); ?></p>
		<div class="cpc-price-row">
			<div class="cpc-price"><?php esc_html_e( '$10.00', 'curtin-pc-shop' ); ?></div>
		</div>
		<div class="cpc-cta-row">
			<a class="cpc-btn cpc-cta" href="#cpc-cards"><?php esc_html_e( 'Shop the cards', 'curtin-pc-shop' ); ?></a>
		</div>
	</div>
	<div class="cpc-hero-art">
		<?php if ( $card_img ) : ?>
			<div class="cpc-hero-art-frame"><img src="<?php echo esc_url( $card_img ); ?>" alt="<?php echo esc_attr( $card_alt ); ?>"></div>
		<?php endif; ?>
		<div class="cpc-credit">
			<div class="cpc-credit-title"><?php esc_html_e( '"Butterfly Garden", 2023', 'curtin-pc-shop' ); ?></div>
			<div class="cpc-credit-sub"><?php esc_html_e( 'School community × Kelly Muller', 'curtin-pc-shop' ); ?></div>
		</div>
	</div>
</section>

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
<?php echo do_shortcode( '[cpc_products category="art-cards" heading="Art Cards"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

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
