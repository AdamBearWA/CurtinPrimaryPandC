<?php
/**
 * Donations page (slug "donations") — "Help Good Ideas Grow".
 *
 * Story-led fundraising appeal that presents the fixed donation tiers
 * from the "donations" product category as a live product grid
 * (via [cpc_products category="donations"]), mirroring the Olive oil
 * and Art cards category pages. Supporters pick a tier — or several, or
 * bump the quantity in the cart — and pay through the existing Square
 * checkout. The hero "Donate Now" button scrolls to the tier grid.
 *
 * Used automatically for the Page with slug "donations".
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- HERO — appeal + Donate Now -->
<section class="cpc-hero cpc-container">
	<div class="cpc-hero-copy">
		<div class="cpc-eyebrow"><?php esc_html_e( 'Support our P&C', 'curtin-pc-shop' ); ?></div>
		<h1 class="cpc-h1"><?php esc_html_e( 'Help Good Ideas Grow', 'curtin-pc-shop' ); ?></h1>
		<p class="cpc-hero-lede"><?php esc_html_e( 'The Curtin Primary School P&C is made up of local people who care about our community. Some volunteer their time. Some donate olives. Some share their creativity. Others lend their professional skills. Together, we create projects that bring people together and make our community stronger.', 'curtin-pc-shop' ); ?></p>
		<p class="cpc-hero-lede"><?php esc_html_e( "Many of those ideas simply wouldn't happen without financial support. Donations provide the seed funding that helps turn community ideas into reality. If you'd like to help us grow the next great idea, we'd be grateful for your donation.", 'curtin-pc-shop' ); ?></p>
		<div class="cpc-cta-row">
			<a class="cpc-cta" href="#cpc-donate-tiers"><?php esc_html_e( 'Donate Now', 'curtin-pc-shop' ); ?></a>
			<a class="cpc-cta-text" href="#cpc-ideas"><?php esc_html_e( 'See what you help grow', 'curtin-pc-shop' ); ?></a>
		</div>
	</div>
	<div class="cpc-hero-art">
<?php
		$cpc_donate_carousel = function_exists( 'cpc_photo_carousel' )
			? cpc_photo_carousel( array( 'interval' => 5000, 'class' => 'cpc-photo-carousel--hero', 'label' => __( 'Photos from our community projects', 'curtin-pc-shop' ) ) )
			: '';
		if ( $cpc_donate_carousel ) :
			echo $cpc_donate_carousel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		else :
?>
		<div class="cpc-hero-art-frame">
			<?php /* Placeholder art - shown until photos are attached to the Donate page in the Media Library. Keeps the 5/4 frame filled. */ ?>
			<svg width="100%" height="100%" viewBox="0 0 500 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="<?php esc_attr_e( 'A growing seedling — image to come', 'curtin-pc-shop' ); ?>">
				<rect width="500" height="400" fill="#eaf6ef"/>
				<circle cx="250" cy="205" r="120" fill="#dceee2"/>
				<path d="M250 300 L250 190" stroke="#1f6b41" stroke-width="10" stroke-linecap="round"/>
				<path d="M250 210 C250 165 210 140 168 140 C168 185 205 212 250 212 Z" fill="#2f8f5b"/>
				<path d="M250 190 C250 150 290 128 330 128 C330 168 296 192 250 192 Z" fill="#1f6b41"/>
				<path d="M215 300 C215 285 232 274 250 274 C268 274 285 285 285 300 Z" fill="#8a6b4a"/>
				<rect x="205" y="300" width="90" height="46" rx="8" fill="#a9805a"/>
			</svg>
		</div>
<?php endif; ?>
	</div>
</section>

<!-- BREAKOUT — standout statement (navy band, single line) -->
<section class="cpc-story cpc-container">
	<h2><?php esc_html_e( 'Every donation plants the seed for our next community project.', 'curtin-pc-shop' ); ?></h2>
</section>

<!-- DONATION TIERS — live product grid from the "donations" category -->
<div id="cpc-donate-tiers"></div>
<?php echo do_shortcode( '[cpc_products category="donations" heading="Choose your donation" note="Add more than one — or adjust quantity in your cart — to give any total."]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<!-- THE IDEAS YOU HELP GROW -->
<section id="cpc-ideas" class="cpc-container cpc-collection cpc-page-content">
	<h2><?php esc_html_e( 'The Ideas You Help Grow', 'curtin-pc-shop' ); ?></h2>
	<p><?php esc_html_e( 'Every donation helps create opportunities for our community to connect, create and belong. Your support could help bring to life:', 'curtin-pc-shop' ); ?></p>

	<div class="cpc-trust">
		<div class="cpc-trust-item">
			<span class="cpc-trust-ico" aria-hidden="true">&#127800;</span>
			<div class="cpc-trust-title"><?php esc_html_e( 'Whole-school art projects', 'curtin-pc-shop' ); ?></div>
		</div>
		<div class="cpc-trust-item">
			<span class="cpc-trust-ico" aria-hidden="true">&#129345;</span>
			<div class="cpc-trust-title"><?php esc_html_e( 'Music and drumming workshops', 'curtin-pc-shop' ); ?></div>
		</div>
		<div class="cpc-trust-item">
			<span class="cpc-trust-ico" aria-hidden="true">&#129746;</span>
			<div class="cpc-trust-title"><?php esc_html_e( 'Community olive harvests', 'curtin-pc-shop' ); ?></div>
		</div>
		<div class="cpc-trust-item">
			<span class="cpc-trust-ico" aria-hidden="true">&#127807;</span>
			<div class="cpc-trust-title"><?php esc_html_e( 'Bush clean-up days', 'curtin-pc-shop' ); ?></div>
		</div>
		<div class="cpc-trust-item">
			<span class="cpc-trust-ico" aria-hidden="true">&#129336;</span>
			<div class="cpc-trust-title"><?php esc_html_e( 'Stay & Play afternoons', 'curtin-pc-shop' ); ?></div>
		</div>
		<div class="cpc-trust-item">
			<span class="cpc-trust-ico" aria-hidden="true">&#128161;</span>
			<div class="cpc-trust-title"><?php esc_html_e( 'The next great idea our community dreams up', 'curtin-pc-shop' ); ?></div>
		</div>
	</div>

	<p><?php esc_html_e( 'Some ideas need art materials. Others need equipment, insurance, venue hire or simply a little funding to get off the ground.', 'curtin-pc-shop' ); ?></p>
	<p><?php esc_html_e( 'Your donation helps make those first steps possible.', 'curtin-pc-shop' ); ?></p>
</section>

<!-- EVERY CONTRIBUTION MATTERS — closing (navy story band) -->
<section class="cpc-story cpc-container">
	<h2><?php esc_html_e( 'Every Contribution Matters', 'curtin-pc-shop' ); ?></h2>
	<div class="cpc-story-cols">
		<div>
			<p><?php esc_html_e( "The best community projects don't happen because one person does everything. They happen because lots of people contribute in different ways.", 'curtin-pc-shop' ); ?></p>
			<p><?php esc_html_e( 'Some share their time. Others share their skills, ideas or resources. Financial donations help provide the foundation that allows those contributions to become something real.', 'curtin-pc-shop' ); ?></p>
		</div>
		<div>
			<p><?php esc_html_e( "Together, we're creating a connected, creative and thriving community.", 'curtin-pc-shop' ); ?></p>
			<p><?php esc_html_e( 'Thank you for supporting the Curtin Primary School P&C.', 'curtin-pc-shop' ); ?></p>
		</div>
	</div>
</section>

<?php
get_footer();
