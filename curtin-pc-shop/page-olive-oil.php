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

<!-- SECTION 1: HERO -->
<section class="cpc-olive-hero cpc-container">
	<div class="cpc-olive-hero-copy">
		<div class="cpc-olive-pill"><?php esc_html_e( 'Curtin Gold', 'curtin-pc-shop' ); ?></div>
		<h1><?php esc_html_e( 'A harvest shared by our community.', 'curtin-pc-shop' ); ?></h1>
		<p class="cpc-olive-lede"><?php esc_html_e( 'Last autumn, something special happened in Karawara. Local residents invited us to harvest the olives from the trees outside their homes. Families from the Curtin Primary community then come together to turn them into our limited seasonal release of extra virgin olive oil.', 'curtin-pc-shop' ); ?></p>
		<div class="cpc-cta-row">
			<a class="cpc-btn cpc-cta" href="#cpc-shop"><?php esc_html_e( 'Shop now', 'curtin-pc-shop' ); ?></a>
		</div>
	</div>

	<div class="cpc-olive-hero-art">
		<span style="position:static;color:#cdd9b9;font:500 14px/1.4 var(--font-body);text-align:center;padding:0 24px"><?php esc_html_e( 'Harvest photo coming soon', 'curtin-pc-shop' ); ?></span>
	</div>
</section>

<!-- SHOP CURTIN GOLD (product grid) -->
<div id="cpc-shop"></div>
<?php echo do_shortcode( '[cpc_products category="olive-oil" heading="Shop Curtin Gold"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<!-- SECTION 2: OUR STORY (story band — matches the cards page component) -->
<section class="cpc-story cpc-container">
	<h2><?php esc_html_e( 'Our Story', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-story-cols">
		<div>
			<p style="color:#fff;font-weight:600;max-width:none"><?php esc_html_e( "From Karawara's trees to your table", 'curtin-pc-shop' ); ?></p>
			<p><?php esc_html_e( 'This autumn, a simple idea brought our community together. Inspired by one of our P&C mums, who wondered whether the olives growing throughout Karawara could be put to good use, the Curtin Primary School P&C set out to create something special.', 'curtin-pc-shop' ); ?></p>
			<p><?php esc_html_e( 'After receiving the generous support of local residents, families from our school community came together to harvest thousands of olives by hand. The olives were then professionally cold pressed in York into our limited seasonal release of 100% extra virgin olive oil.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<p><?php esc_html_e( 'Every bottle celebrates the generosity of our neighbours, the efforts of our volunteers and the power of a community working together.', 'curtin-pc-shop' ); ?></p>
			<p><?php esc_html_e( "By purchasing a bottle, you're helping this project continue to grow.", 'curtin-pc-shop' ); ?></p>
			<a class="cpc-lnk cpc-cta-text" href="#cpc-shop" style="color:#fff"><?php esc_html_e( 'Shop our Olive Oil', 'curtin-pc-shop' ); ?></a>
		</div>
	</div>
</section>

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
			<p class="cpc-feature-text"><?php esc_html_e( 'Every purchase helps the Curtin Primary School P&C create projects, events and initiatives that bring people together.', 'curtin-pc-shop' ); ?></p>
		</div>
	</div>
</section>

<!-- SECTION 4: COLLECTION & DELIVERY -->
<section class="cpc-olive-delivery cpc-container">
	<h2><?php esc_html_e( 'Collection & Delivery', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-olive-delivery-grid">
		<div class="cpc-olive-delivery-card">
			<div class="cpc-olive-delivery-title"><?php esc_html_e( 'Free collection (preferred)', 'curtin-pc-shop' ); ?></div>
			<p><?php esc_html_e( 'Collect your order from Karawara on our advertised collection day.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div class="cpc-olive-delivery-card">
			<div class="cpc-olive-delivery-title"><?php esc_html_e( 'Local delivery', 'curtin-pc-shop' ); ?></div>
			<p><?php esc_html_e( 'Local delivery is available to:', 'curtin-pc-shop' ); ?></p>
			<ul class="cpc-olive-suburbs">
				<li><?php esc_html_e( 'Karawara', 'curtin-pc-shop' ); ?></li>
				<li><?php esc_html_e( 'Manning', 'curtin-pc-shop' ); ?></li>
				<li><?php esc_html_e( 'Salter Point', 'curtin-pc-shop' ); ?></li>
				<li><?php esc_html_e( 'Como', 'curtin-pc-shop' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'Delivery is $5, or free when you purchase two or more bottles.', 'curtin-pc-shop' ); ?></p>
		</div>
	</div>
</section>

<!-- SECTION 5: THANK YOU -->
<section class="cpc-olive-thanks cpc-container">
	<div class="cpc-olive-thanks-inner">
		<h2><?php esc_html_e( 'Thank You', 'curtin-pc-shop' ); ?></h2>
		<p class="cpc-olive-thanks-kicker"><?php esc_html_e( 'Behind every bottle is a community.', 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'Some people share the olives from the trees outside their homes.', 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'Some spend the morning harvesting.', 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'Others bottle the oil, apply labels, organise the online store, coordinate collections and deliveries, or quietly take care of the countless details that make a project like this possible.', 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'Together, those generous contributions become something special: a bottle of olive oil that reflects the creativity, generosity and spirit of our Curtin Primary P&C community.', 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'Thank you for helping us keep it growing.', 'curtin-pc-shop' ); ?></p>
	</div>
</section>

<!-- FAQ -->
<section class="cpc-olive-faq cpc-container">
	<h2><?php esc_html_e( 'FAQ', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-faq-list">
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( 'Can I buy oil on the collection day?', 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( 'Absolutely! Our oil will be on sale on Sunday, 2 August from 2-4pm in Karawara. If you missed out, contact president@curtinprimarypandc.com.au', 'curtin-pc-shop' ); ?></p>
		</details>
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( 'Where does the money go?', 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( 'All profits from the Curtin Primary P&C Store are invested back into projects, resources and experiences that benefit students at Curtin Primary School.', 'curtin-pc-shop' ); ?></p>
		</details>
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( 'Do I need to be a Curtin Primary family to purchase?', 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( 'Not at all! Everyone is welcome to support our fundraising initiatives.', 'curtin-pc-shop' ); ?></p>
		</details>
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( 'When will my order be ready?', 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( 'Our oil will be ready for collection on Sunday, 2 August from 2-4pm in Karawara. If you missed out, contact president@curtinprimarypandc.com.au', 'curtin-pc-shop' ); ?></p>
		</details>
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( "What if I can't make the collection time?", 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( 'If you order two bottles or more and live in Karawara, Como, Manning or Salter Point, we will deliver it to you for free on the day of collection! We will leave your oil in a safe place if nobody is home. If you only wish to order one bottle, please contact us and we\'ll work it out together.', 'curtin-pc-shop' ); ?></p>
		</details>
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( 'Can I purchase gifts?', 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( 'We would love that! Our olive oil and other fundraising products make wonderful gifts while supporting a great cause. So, thank you in advance!', 'curtin-pc-shop' ); ?></p>
		</details>
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( 'Who runs the store?', 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( 'The store is managed by volunteers from the Curtin Primary P&C.', 'curtin-pc-shop' ); ?></p>
		</details>
		<details class="cpc-faq-item">
			<summary><?php esc_html_e( 'Will there be another opportunity to purchase olive oil?', 'curtin-pc-shop' ); ?></summary>
			<p><?php esc_html_e( "We hope to sell out, but if any bottles remain, we'll make them available later in the year. To be the first to know, join our mailing list below.", 'curtin-pc-shop' ); ?></p>
		</details>
	</div>
</section>

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
