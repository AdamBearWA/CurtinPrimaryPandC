<?php
/**
 * Olive oil category page — "Curtin Gold" story + product grid.
 * Harvest story, three features, collection & delivery, thank-you band,
 * FAQ and mailing-list signup, plus a live product grid.
 *
 * Used automatically for the Page with slug "olive-oil".
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$buy_url       = function_exists( 'cpc_olive_product_url' ) ? cpc_olive_product_url() : cpc_olive_url();
$olive_product = function_exists( 'cpc_olive_product' ) ? cpc_olive_product() : null;
$hero_img      = '';
$hero_alt      = __( 'Curtin Gold extra virgin olive oil', 'curtin-pc-shop' );
$hero_has_image = false;
if ( $olive_product ) {
	$pid = $olive_product->get_image_id();
	if ( $pid ) {
		$src = wp_get_attachment_image_url( $pid, 'large' );
		if ( $src ) {
			$hero_img       = $src;
			$hero_has_image = true;
		}
	}
	$hero_alt = $olive_product->get_name();
}
?>

<!-- SECTION 1: HARVEST STORY (single green band — .cpc-olive-page body class turns .cpc-story green; merges the old hero + "Our Story") -->
<section class="cpc-story cpc-container">
	<h2><?php esc_html_e( 'A harvest shared by our community', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-story-cols">
		<div>
			<p><?php esc_html_e( 'Last autumn, a simple idea brought our community together. Inspired by a P&C mum who wondered whether the olives growing throughout Karawara could be put to good use, local residents invited us to harvest the trees outside their homes.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<p><?php esc_html_e( "Families from Curtin Primary hand-harvested thousands of olives, then had them cold pressed in York into a limited seasonal release of 100% extra virgin olive oil. Every bottle celebrates our neighbours' generosity and our volunteers' work — and every purchase helps the project grow.", 'curtin-pc-shop' ); ?></p>
		</div>
	</div>
</section>

<!-- SHOP CURTIN GOLD - product card beside the harvest-photo carousel -->
<div id="cpc-shop"></div>
<?php
$cpc_olive_carousel = function_exists( 'cpc_photo_carousel' )
	? cpc_photo_carousel( array( 'interval' => 5000, 'label' => __( 'Olive harvest photos', 'curtin-pc-shop' ) ) )
	: '';
if ( $cpc_olive_carousel ) : ?>
<section class="cpc-olive-showcase cpc-container">
	<div class="cpc-collection-head">
		<h2><?php esc_html_e( 'Shop Curtin Gold', 'curtin-pc-shop' ); ?></h2>
	</div>
	<div class="cpc-olive-showcase-grid">
		<div class="cpc-olive-showcase-product">
			<?php
			$cpc_olive_products = function_exists( 'wc_get_products' )
				? wc_get_products( array( 'status' => 'publish', 'category' => array( 'olive-oil' ), 'orderby' => 'menu_order', 'order' => 'ASC', 'limit' => -1 ) )
				: array();
			foreach ( $cpc_olive_products as $cpc_op ) {
				cpc_render_product_card( $cpc_op );
			}
			?>
		</div>
		<div class="cpc-olive-showcase-media">
			<?php echo $cpc_olive_carousel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
<?php else :
	echo do_shortcode( '[cpc_products category="olive-oil" heading="Shop Curtin Gold"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
endif; ?>

<!-- SECTION 3: THREE FEATURES -->
<section class="cpc-olive-features cpc-container">
	<div class="cpc-olive-features-grid">
		<div>
			<div class="cpc-feature-icon" aria-hidden="true">&#127795;</div>
			<div class="cpc-feature-title"><?php esc_html_e( 'Harvested in Karawara', 'curtin-pc-shop' ); ?></div>
			<p class="cpc-feature-text"><?php esc_html_e( 'Our neighbours share their trees.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<div class="cpc-feature-icon" aria-hidden="true">&#129746;</div>
			<div class="cpc-feature-title"><?php esc_html_e( 'Cold pressed in York', 'curtin-pc-shop' ); ?></div>
			<p class="cpc-feature-text"><?php esc_html_e( 'The harvest is professionally cold pressed into 100% extra virgin olive oil.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<div class="cpc-feature-icon" aria-hidden="true">&#10084;&#65039;</div>
			<div class="cpc-feature-title"><?php esc_html_e( 'Supporting our community', 'curtin-pc-shop' ); ?></div>
			<p class="cpc-feature-text"><?php esc_html_e( 'Every purchase helps the Curtin Primary P&C create projects, events and initiatives that bring people together.', 'curtin-pc-shop' ); ?></p>
		</div>
	</div>
</section>

<!-- SECTION 4: COLLECTION & DELIVERY -->
<section class="cpc-olive-delivery cpc-container">
	<h2><?php esc_html_e( 'Collection & Delivery', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-olive-delivery-grid">
		<div class="cpc-olive-delivery-card">
			<div class="cpc-olive-delivery-title"><?php esc_html_e( 'Free collection (preferred)', 'curtin-pc-shop' ); ?></div>
			<?php
			// Card body and the shared pickup text are both editable in
			// WooCommerce → Settings → Curtin P&C (functions.php §7t).
			$cpc_card_text = function_exists( 'cpc_collection_card_text' ) ? trim( cpc_collection_card_text() ) : '';
			if ( '' !== $cpc_card_text ) {
				echo wp_kses_post( wpautop( $cpc_card_text ) );
			}
			if ( function_exists( 'cpc_pickup_text_html' ) ) {
				echo cpc_pickup_text_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already wp_kses_post filtered.
			}
			?>
		</div>
		<?php
		// Local delivery is driven entirely by WooCommerce → Settings → Curtin P&C
		// (functions.php §7s). When it's switched off we say so rather
		// than advertising a delivery the checkout would refuse.
		$cpc_oil_delivery = function_exists( 'cpc_oil_delivery_enabled' ) && cpc_oil_delivery_enabled();
		$cpc_oil_suburbs  = function_exists( 'cpc_oil_delivery_suburbs' ) ? cpc_oil_delivery_suburbs() : array();
		?>
		<div class="cpc-olive-delivery-card">
			<div class="cpc-olive-delivery-title"><?php esc_html_e( 'Local delivery', 'curtin-pc-shop' ); ?></div>
			<?php if ( $cpc_oil_delivery ) : ?>
				<?php if ( ! empty( $cpc_oil_suburbs ) ) : ?>
					<p><?php esc_html_e( 'Local delivery is available to:', 'curtin-pc-shop' ); ?></p>
					<ul class="cpc-olive-suburbs">
						<?php foreach ( $cpc_oil_suburbs as $cpc_suburb ) : ?>
							<li><?php echo esc_html( $cpc_suburb ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
			<p><?php echo esc_html( function_exists( 'cpc_oil_delivery_page_copy' ) ? cpc_oil_delivery_page_copy() : '' ); ?></p>
		</div>
	</div>
</section>

<!-- SECTION 5: THANK YOU -->
<section class="cpc-olive-thanks cpc-container">
	<div class="cpc-olive-thanks-inner">
		<h2><?php esc_html_e( 'Thank You', 'curtin-pc-shop' ); ?></h2>
		<p class="cpc-olive-thanks-kicker"><?php esc_html_e( 'Behind every bottle is a community.', 'curtin-pc-shop' ); ?></p>
		<div class="cpc-olive-thanks-cols">
			<div>
				<p><?php esc_html_e( 'Some people share the olives from the trees outside their homes.', 'curtin-pc-shop' ); ?></p>
				<p><?php esc_html_e( 'Some spend the morning harvesting.', 'curtin-pc-shop' ); ?></p>
			</div>
			<div>
				<p><?php esc_html_e( 'Others bottle the oil, apply labels, organise the online store, coordinate collections and deliveries, or quietly take care of the countless details that make a project like this possible.', 'curtin-pc-shop' ); ?></p>
			</div>
			<div>
				<p><?php esc_html_e( 'Together, those generous contributions become something special: a bottle of olive oil that reflects the creativity, generosity and spirit of our Curtin Primary P&C community.', 'curtin-pc-shop' ); ?></p>
				<p><?php esc_html_e( 'Thank you for helping us keep it growing.', 'curtin-pc-shop' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- FAQ -->
<?php
// Entries, their order, their wording and whether each one shows are all
// driven by the FAQ registry + settings (functions.php §7f):
// WooCommerce → Settings → Curtin P&C → Olive oil page FAQ.
$cpc_faq = function_exists( 'cpc_faq_entries' ) ? cpc_faq_entries() : array();
if ( ! empty( $cpc_faq ) ) :
	?>
<section class="cpc-olive-faq cpc-container">
	<h2><?php esc_html_e( 'FAQ', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-faq-list">
		<?php foreach ( $cpc_faq as $cpc_item ) : ?>
			<details class="cpc-faq-item">
				<summary><?php echo esc_html( $cpc_item['q'] ); ?></summary>
				<?php echo wp_kses_post( wpautop( $cpc_item['a'] ) ); ?>
			</details>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<!-- MAILING LIST -->
<section class="cpc-olive-signup cpc-container">
	<div class="cpc-olive-signup-inner">
		<h2><?php esc_html_e( 'Join our mailing list', 'curtin-pc-shop' ); ?></h2>
		<p><?php esc_html_e( "Be the first to know when Curtin Gold is back, and hear about our other fundraising projects.", 'curtin-pc-shop' ); ?></p>
		<div class="cpc-olive-form-wrap">
			<?php echo do_shortcode( '[fluentform id="2"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>

<?php
get_footer();
