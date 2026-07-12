<?php
/**
 * Home (static front page) — olive-oil hero (product card),
 * art-cards hero (3 auto-rotating product cards), navy story band,
 * trust strip.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Render one product in the shared collection-card format
 * (image, title, short-description meta, price, add-to-cart) — the same
 * markup the product grids use, so the hero cards match the rest of the site.
 */
if ( ! function_exists( 'cpc_fp_render_card' ) ) {
	function cpc_fp_render_card( $p ) {
		if ( ! $p ) {
			return;
		}
		$img  = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'large' ) : wc_placeholder_img_src( 'large' );
		$meta = wp_strip_all_tags( $p->get_short_description() );
		?>
		<div class="cpc-card cpc-lift">
			<a class="cpc-card-imglink" href="<?php echo esc_url( get_permalink( $p->get_id() ) ); ?>">
				<div class="cpc-card-img"><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $p->get_name() ); ?>"></div>
			</a>
			<div class="cpc-card-body">
				<a class="cpc-card-titlelink" href="<?php echo esc_url( get_permalink( $p->get_id() ) ); ?>"><span class="cpc-card-title"><?php echo esc_html( $p->get_name() ); ?></span></a>
				<?php if ( '' !== $meta ) : ?><div class="cpc-card-meta"><?php echo esc_html( $meta ); ?></div><?php endif; ?>
				<div class="cpc-card-foot">
					<div class="cpc-card-price"><?php echo wp_kses_post( $p->get_price_html() ); ?></div>
					<?php if ( $p->is_purchasable() && $p->is_in_stock() ) : ?>
						<a href="<?php echo esc_url( $p->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $p->get_id() ); ?>" class="cpc-add cpc-card-add add_to_cart_button ajax_add_to_cart" rel="nofollow"><?php esc_html_e( 'Add to cart', 'curtin-pc-shop' ); ?></a>
					<?php else : ?>
						<a class="cpc-add cpc-card-add" href="<?php echo esc_url( get_permalink( $p->get_id() ) ); ?>"><?php esc_html_e( 'View', 'curtin-pc-shop' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

/* ---- Hero product: Curtin Gold olive oil (falls back gracefully) --- */
$olive_product = function_exists( 'cpc_olive_product' ) ? cpc_olive_product() : null;
$hero_alt   = __( 'Curtin Gold extra virgin olive oil', 'curtin-pc-shop' );
$hero_url   = cpc_olive_url();
$hero_price = '$18.00';
if ( $olive_product ) {
	$price_html = wc_price( wc_get_price_to_display( $olive_product ) );
	$hero_price = wp_strip_all_tags( $price_html );
}
?>

<!-- HERO (Curtin Gold olive oil — shown as a product card) -->
<section class="cpc-hero cpc-container">
	<div class="cpc-hero-copy">
		<div class="cpc-eyebrow"><?php esc_html_e( 'Curtin Gold Olive Oil · Available Now!', 'curtin-pc-shop' ); ?></div>
		<h1 class="cpc-h1"><?php esc_html_e( "From our neighbourhood's olive trees to your kitchen.", 'curtin-pc-shop' ); ?></h1>
		<p class="cpc-hero-lede"><?php esc_html_e( 'Curtin Primary volunteers harvested local Karawara olives to be professionally cold pressed into premium extra virgin olive oil. The result is a fresh, flavourful oil you\'ll love using every day.', 'curtin-pc-shop' ); ?></p>
		<div class="cpc-price-row">
			<div class="cpc-price"><?php echo esc_html( $hero_price ); ?></div>
		</div>
		<div class="cpc-cta-row">
			<a class="cpc-btn cpc-cta" href="<?php echo esc_url( $hero_url ); ?>"><?php esc_html_e( 'Shop Curtin Gold', 'curtin-pc-shop' ); ?></a>
		</div>
	</div>
	<div class="cpc-hero-art">
		<?php if ( $olive_product ) : ?>
			<div class="cpc-hero-card"><?php cpc_fp_render_card( $olive_product ); ?></div>
		<?php else : ?>
			<div class="cpc-hero-art-frame cpc-olive-hero-art">
				<svg width="150" height="250" viewBox="0 0 120 200" fill="none" aria-hidden="true">
					<ellipse cx="60" cy="40" rx="44" ry="36" fill="#7d9a4e" opacity="0.45"/>
					<path d="M60 10 C71 26 71 54 60 72 C49 54 49 26 60 10 Z" fill="#a9c46c"/>
					<path d="M26 32 C44 34 56 48 60 66 C40 64 28 50 26 32 Z" fill="#8fb255"/>
					<path d="M94 32 C76 34 64 48 60 66 C80 64 92 50 94 32 Z" fill="#8fb255"/>
					<circle cx="43" cy="58" r="10" fill="#3c5a2a"/>
					<circle cx="76" cy="55" r="8.5" fill="#536b2f"/>
					<rect x="45" y="92" width="30" height="92" rx="10" fill="#d8ca98"/>
					<rect x="49" y="76" width="22" height="22" rx="4" fill="#c0af75"/>
					<rect x="47" y="120" width="26" height="46" rx="5" fill="#f4f0e4"/>
				</svg>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
/* ---- Art cards: 3 products shown as auto-rotating product cards --- */
$card_products = function_exists( 'wc_get_products' )
	? wc_get_products( array(
		'status'    => 'publish',
		'limit'     => 3,
		'orderby'   => 'menu_order',
		'order'     => 'ASC',
		'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'olive-oil' ),
				'operator' => 'NOT IN',
			),
		),
	) )
	: array();
?>

<!-- HERO (Art cards — 3 auto-rotating product cards) -->
<section class="cpc-hero cpc-container">
	<div class="cpc-hero-copy">
		<div class="cpc-eyebrow"><?php esc_html_e( 'Our Art Cards', 'curtin-pc-shop' ); ?></div>
		<h2 class="cpc-h1"><?php esc_html_e( 'Cards our whole school painted together', 'curtin-pc-shop' ); ?></h2>
		<p class="cpc-hero-lede"><?php esc_html_e( 'Three big community artworks, turned into a set of four greeting cards. Blank inside, ready to send — and every set funds our classrooms.', 'curtin-pc-shop' ); ?></p>
		<div class="cpc-price-row">
			<div class="cpc-price"><?php echo esc_html( '$10.00' ); ?></div>
		</div>
		<div class="cpc-cta-row">
			<a class="cpc-btn cpc-cta" href="<?php echo esc_url( home_url( '/art-cards/' ) ); ?>"><?php esc_html_e( 'Shop the cards', 'curtin-pc-shop' ); ?></a>
		</div>
	</div>
	<div class="cpc-hero-art">
		<?php if ( ! empty( $card_products ) ) : ?>
			<div class="cpc-carousel" data-cpc-carousel data-interval="4500">
				<div class="cpc-carousel-track">
					<?php foreach ( $card_products as $idx => $p ) : ?>
						<div class="cpc-slide<?php echo 0 === $idx ? ' cpc-slide-active' : ''; ?>">
							<div class="cpc-hero-card"><?php cpc_fp_render_card( $p ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( count( $card_products ) > 1 ) : ?>
					<div class="cpc-carousel-dots">
						<?php foreach ( $card_products as $idx => $p ) : ?>
							<button type="button" class="cpc-dot<?php echo 0 === $idx ? ' cpc-dot-active' : ''; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show %s', 'curtin-pc-shop' ), $p->get_name() ) ); ?>"></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
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

<!-- TRUST STRIP -->
<section class="cpc-trust cpc-container">
	<div class="cpc-trust-item">
		<span class="cpc-trust-ico" aria-hidden="true">&#10084;&#65039;</span>
		<div class="cpc-trust-title"><?php esc_html_e( 'Made by our community', 'curtin-pc-shop' ); ?></div>
		<div class="cpc-trust-sub"><?php esc_html_e( 'From backyard olive trees to student artwork, every product has a Curtin story.', 'curtin-pc-shop' ); ?></div>
	</div>
	<div class="cpc-trust-item">
		<span class="cpc-trust-ico" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 20h5v-2a4 4 0 0 0-3-3.87"/><path d="M2 20v-2a4 4 0 0 1 3-3.87"/><path d="M9 20h6"/><circle cx="9" cy="7" r="4"/><circle cx="17" cy="7" r="3"/></svg></span>
		<div class="cpc-trust-title"><?php esc_html_e( 'Growing community', 'curtin-pc-shop' ); ?></div>
		<div class="cpc-trust-sub"><?php esc_html_e( 'We bring people together through shared experiences.', 'curtin-pc-shop' ); ?></div>
	</div>
	<div class="cpc-trust-item">
		<span class="cpc-trust-ico" aria-hidden="true">&#127793;</span>
		<div class="cpc-trust-title"><?php esc_html_e( "Supporting what's next", 'curtin-pc-shop' ); ?></div>
		<div class="cpc-trust-sub"><?php esc_html_e( 'Every purchase helps the Curtin Primary P&C create projects, events and experiences that strengthen our school community.', 'curtin-pc-shop' ); ?></div>
	</div>
</section>

<?php
get_footer();
