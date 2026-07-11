<?php
/**
 * Cards category page (slug "cards") — Butterfly Garden hero, story band,
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
		<div class="cpc-eyebrow"><?php esc_html_e( 'The Butterfly Garden · painted 2023', 'curtin-pc-shop' ); ?></div>
		<h1 class="cpc-h1"><?php esc_html_e( 'Cards our whole school painted together', 'curtin-pc-shop' ); ?></h1>
		<p class="cpc-hero-lede"><?php esc_html_e( 'One big community artwork, turned into sets of four greeting cards. Blank inside, ready to send — and every set funds our classrooms.', 'curtin-pc-shop' ); ?></p>
		<div class="cpc-price-row">
			<div class="cpc-price"><?php esc_html_e( '$10.00', 'curtin-pc-shop' ); ?></div>
			<div class="cpc-price-meta"><?php esc_html_e( 'Set of four · 120 × 120 mm', 'curtin-pc-shop' ); ?><br><?php esc_html_e( 'white envelopes · blank inside', 'curtin-pc-shop' ); ?></div>
		</div>
		<div class="cpc-cta-row">
			<a class="cpc-btn cpc-cta" href="#cpc-cards"><?php esc_html_e( 'Shop the cards', 'curtin-pc-shop' ); ?></a>
			<a class="cpc-lnk cpc-cta-text" href="#cpc-story"><?php esc_html_e( 'Read the story', 'curtin-pc-shop' ); ?></a>
		</div>
		<div class="cpc-fund"><?php echo cpc_tick( 15, '#1f6b41' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( '100% of profits fund our school', 'curtin-pc-shop' ); ?></div>
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
<?php echo do_shortcode( '[cpc_products category="cards" heading="Greeting cards" note="Every set · four cards · $10" meta="Set of four · blank inside"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<?php
get_footer();
