<?php
/**
 * Curtin Primary P&C Shop — child theme functions.
 *
 * Strategy (see README): neutralise Storefront + WooCommerce default CSS,
 * then load our own stylesheet LAST so the design can't be overridden.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CPC_VERSION', '2.8.0' );

/* -----------------------------------------------------------------
 * 1. Theme supports
 * --------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'curtin-pc-shop' ),
	) );
}, 20 );

/* -----------------------------------------------------------------
 * 2. Neutralise the parent theme + WooCommerce default styling.
 *    Runs late (priority 99) so it removes what Storefront/Woo added.
 * --------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', function () {

	// Drop Storefront's own stylesheets — we replace the whole look.
	foreach ( array(
		'storefront-style',
		'storefront-woocommerce-style',
		'storefront-gutenberg-blocks',
		'storefront-fonts',
		'storefront-icons',
	) as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}
}, 99 );

// Remove WooCommerce's three default stylesheets (general / layout / smallscreen).
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/* -----------------------------------------------------------------
 * 3. Enqueue our fonts + stylesheet LAST (so the cascade favours us).
 * --------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', function () {

	wp_enqueue_style(
		'cpc-fonts',
		'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap',
		array(),
		null
	);

	// NOTE: the CSS/JS filenames carry the version (curtin-2620.*) because the
	// SWAG/nginx proxy caches these static assets by PATH and ignores the ?ver
	// query string — a plain version bump does NOT bust it (see
	// Theme-Deployment-Notes.md §8). Renaming the file on every CSS/JS change is
	// the reliable cache-bust. Bump both the filename and CPC_VERSION together.
	wp_enqueue_style(
		'cpc-main',
		get_stylesheet_directory_uri() . '/assets/css/curtin-2620.css',
		array( 'cpc-fonts' ),
		CPC_VERSION
	);

	wp_enqueue_script(
		'cpc-ui',
		get_stylesheet_directory_uri() . '/assets/js/curtin-2620.js',
		array(),
		CPC_VERSION,
		true
	);
}, 100 ); // after everything else

// Add a body class so all our CSS can be tightly scoped under .cpc-theme.
// Also flag the olive-oil story page so its banded sections (Our Story, hero,
// thank-you, FAQ) can be styled green without affecting the same components on
// the front page or cards page, which keep the default blue.
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'cpc-theme';
	if ( function_exists( 'is_page' ) && is_page( 'olive-oil' ) ) {
		$classes[] = 'cpc-olive-page';
	}
	return $classes;
} );

/* -----------------------------------------------------------------
 * cpc_card_meta_text( $product ) — the product short description
 * reduced to plain text for the .cpc-card-meta line, with the
 * author's intended line breaks preserved. WooCommerce stores the
 * short description as HTML (<p>/<br> from the editor), so we convert
 * those breaks to real newlines BEFORE stripping tags. Card templates
 * then output it with nl2br( esc_html() ) so the newlines actually
 * render — previously wp_strip_all_tags() + esc_html() collapsed a
 * multi-line short description onto a single line.
 * --------------------------------------------------------------- */
if ( ! function_exists( 'cpc_card_meta_text' ) ) {
	function cpc_card_meta_text( $product ) {
		if ( ! $product ) {
			return '';
		}
		$raw = $product->get_short_description();
		// Paragraph boundaries and <br> become real newlines first...
		$raw = preg_replace( '#</p>\s*<p[^>]*>#i', "\n", $raw );
		$raw = preg_replace( '#<br\s*/?>#i', "\n", $raw );
		// ...then drop the remaining tags, keeping the newlines.
		return trim( wp_strip_all_tags( $raw ) );
	}
}

/* -----------------------------------------------------------------
 * 4. Strip Storefront/Woo page furniture we don't want.
 * --------------------------------------------------------------- */
add_action( 'init', function () {
	// No sidebar, no Storefront homepage content blocks.
	remove_action( 'homepage', 'storefront_homepage_content', 10 );
	// Remove default Woo breadcrumb (we render our own in the product template).
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	// No related products on the single product page.
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	// No "upsell" cross-sells display.
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	// Storefront wraps content with its own header/footer hooks we don't need.
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
}, 20 );

// Belt-and-braces: ensure related products query returns nothing.
add_filter( 'woocommerce_related_products', '__return_empty_array' );

/* -----------------------------------------------------------------
 * 5b. Editable navigation.
 *     The header now renders the "primary" menu (Appearance → Menus)
 *     via wp_nav_menu, so the P&C can edit nav labels/links from the
 *     admin UI. This filter keeps the boutique styling by adding the
 *     .cpc-lnk class (and .cpc-active for the current page) to each
 *     menu link, matching the old hardcoded markup.
 * --------------------------------------------------------------- */
add_filter( 'nav_menu_link_attributes', function ( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$classes = 'cpc-lnk';
		if ( ! empty( $item->current ) || in_array( 'current-menu-item', (array) $item->classes, true ) ) {
			$classes .= ' cpc-active';
		}
		$atts['class'] = trim( ( isset( $atts['class'] ) ? $atts['class'] . ' ' : '' ) . $classes );
	}
	return $atts;
}, 10, 3 );

/* -----------------------------------------------------------------
 * 5. Pickup-only safety net.
 *    Even if a shipping method is mis-configured, never advertise delivery.
 * --------------------------------------------------------------- */
add_filter( 'woocommerce_cart_needs_shipping', function ( $needs ) {
	return $needs; // Local Pickup is configured in WooCommerce → Shipping.
} );

/* -----------------------------------------------------------------
 * 1b. IMPORTANT — no woocommerce.php file in this theme, by design.
 *     WooCommerce's template loader (WC_Template_Loader::get_template_loader_files)
 *     unconditionally appends 'woocommerce.php' to its search list BEFORE
 *     'archive-product.php' / 'single-product.php', and does this AFTER the
 *     'woocommerce_template_loader_files' filter runs — so a theme-root
 *     woocommerce.php file can never be filtered out once it exists; it always
 *     wins over our dedicated shop/product templates (confirmed via WooCommerce's
 *     own core source and its Status page warning: "woocommerce.php has priority
 *     over archive-product.php... This is intended to prevent display issues.").
 *     A previous fix here tried to filter it away with a nonexistent hook name
 *     ('wc_get_template_loader_files') — that filter never fires, and even a
 *     correctly named one couldn't remove an entry appended after it runs.
 *
 *     WC_Template_Loader only overrides the template at all for the single
 *     product page, product taxonomy pages, and the shop archive. Cart,
 *     Checkout and My Account are plain WordPress Pages it never touches, so
 *     they render via our normal page.php — no separate wrapper template
 *     needed. We just flag those specific pages with an extra body class so
 *     the Woo-block button styling (already scoped to .cpc-page) still applies.
 * --------------------------------------------------------------- */
add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		$classes[] = 'cpc-woo';
	}
	// Flag carts containing olive oil so the block cart/checkout JS + CSS can
	// mirror the 6152 delivery rule in the UI (server-side block is §7b).
	if ( function_exists( 'WC' ) && WC()->cart ) {
		foreach ( WC()->cart->get_cart() as $cpc_item ) {
			if ( ! empty( $cpc_item['product_id'] ) && has_term( 'olive-oil', 'product_cat', $cpc_item['product_id'] ) ) {
				$classes[] = 'cpc-has-oil';
				break;
			}
		}
	}
	return $classes;
} );

/* -----------------------------------------------------------------
 * Helpers used by the templates
 * --------------------------------------------------------------- */

/** Cart item count for the header pill. */
function cpc_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}
	return 0;
}

/** Cart URL (falls back gracefully if Woo not active). */
function cpc_cart_url() {
	return function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
}

/**
 * Art cards page URL by slug, with a path fallback.
 *
 * Points at the dedicated /art-cards/ Page (not the WooCommerce shop base,
 * which is repointed to the hidden "All products" archive). Used by the
 * product-page breadcrumb, the footer "Art cards" link and the 404 page.
 */
function cpc_shop_url() {
	$page = get_page_by_path( 'art-cards' );
	return $page ? get_permalink( $page ) : home_url( '/art-cards/' );
}

/** Olive-oil page URL by slug, with a hash fallback. */
function cpc_olive_url() {
	$page = get_page_by_path( 'olive-oil' );
	return $page ? get_permalink( $page ) : home_url( '/olive-oil/' );
}

/**
 * The "hero" product for the homepage: a Featured product if one exists,
 * otherwise the most recent published product.
 *
 * @return WC_Product|null
 */
function cpc_hero_product() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return null;
	}
	// The hero is always the founding artwork: the "Butterfly Garden" product,
	// so the top image stays consistent with its card in the collection.
	$bg = get_page_by_path( 'butterfly-garden-cards', OBJECT, 'product' );
	if ( $bg ) {
		$product = wc_get_product( $bg->ID );
		if ( $product ) {
			return $product;
		}
	}
	// Fallback: first product by menu order (matches the first collection card).
	$ordered = wc_get_products( array( 'limit' => 1, 'orderby' => 'menu_order', 'order' => 'ASC', 'status' => 'publish' ) );
	if ( ! empty( $ordered ) ) {
		return $ordered[0];
	}
	$recent = wc_get_products( array( 'limit' => 1, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish' ) );
	return ! empty( $recent ) ? $recent[0] : null;
}

/**
 * The Curtin Gold olive oil product, if it has been created in WooCommerce yet.
 *
 * @return WC_Product|null
 */
function cpc_olive_product() {
	if ( ! function_exists( 'get_page_by_path' ) ) {
		return null;
	}
	$post = get_page_by_path( 'curtin-gold-extra-virgin-olive-oil', OBJECT, 'product' );
	if ( $post ) {
		$product = wc_get_product( $post->ID );
		if ( $product ) {
			return $product;
		}
	}
	// Fallback: any product in the "olive-oil" category.
	if ( function_exists( 'wc_get_products' ) ) {
		$found = wc_get_products( array(
			'limit'    => 1,
			'status'   => 'publish',
			'category' => array( 'olive-oil' ),
		) );
		if ( ! empty( $found ) ) {
			return $found[0];
		}
	}
	return null;
}

/** URL to buy the Curtin Gold olive oil product (falls back to the olive-oil story page). */
function cpc_olive_product_url() {
	$product = cpc_olive_product();
	return $product ? $product->get_permalink() : cpc_olive_url();
}

/** The bird mark, inline so it needs no asset. */
function cpc_bird( $w = 42, $h = 29, $fill = '#1d6fb8' ) {
	return '<svg class="cpc-brand-mark" viewBox="0 0 100 70" width="' . esc_attr( $w ) . '" height="' . esc_attr( $h ) . '" aria-hidden="true"><path d="M6 41 C21 38 31 36 41 33 C45 25 51 17 61 13 C55 22 53 30 55 34 C67 31 81 29 96 21 C84 34 70 43 56 45 C50 53 44 58 36 60 C40 52 40 47 38 44 C28 46 16 47 6 41 Z" fill="' . esc_attr( $fill ) . '"/></svg>';
}

/** A green check tick. */
function cpc_tick( $size = 17, $stroke = '#2f8f5b', $sw = '2.4' ) {
	return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr( $stroke ) . '" stroke-width="' . esc_attr( $sw ) . '" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>';
}

/* -----------------------------------------------------------------
 * [cpc_products] — boutique product grid for editable Pages.
 *
 * Lets any block-edited Page (Home, Shop, Olive oil, Cards) drop in a
 * live WooCommerce product grid that matches the site's card design,
 * without the editor having to touch markup. Products stay managed in
 * WooCommerce → Products; the surrounding hero/story copy stays fully
 * editable in the block editor.
 *
 * Attributes:
 *   category  product_cat slug(s), comma separated  (e.g. "cards" or "olive-oil")
 *   limit     max products, -1 for all              (default -1)
 *   heading   optional section heading
 *   note      optional right-aligned note next to the heading
 *   meta      optional per-card meta line (e.g. "Set of four · blank inside");
 *             falls back to the product short description
 *   orderby / order  standard wc_get_products sorting (default menu_order ASC)
 * --------------------------------------------------------------- */
function cpc_products_shortcode( $atts ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return '';
	}
	$atts = shortcode_atts( array(
		'category' => '',
		'limit'    => -1,
		'heading'  => '',
		'note'     => '',
		'meta'     => '',
		'orderby'  => 'menu_order',
		'order'    => 'ASC',
	), $atts, 'cpc_products' );

	$args = array(
		'status'  => 'publish',
		'limit'   => (int) $atts['limit'],
		'orderby' => sanitize_text_field( $atts['orderby'] ),
		'order'   => sanitize_text_field( $atts['order'] ),
	);
	if ( '' !== $atts['category'] ) {
		$args['category'] = array_map( 'trim', explode( ',', $atts['category'] ) );
	}
	$products = wc_get_products( $args );

	ob_start();
	?>
	<div class="cpc-collection cpc-container">
		<?php if ( '' !== $atts['heading'] || '' !== $atts['note'] ) : ?>
			<div class="cpc-collection-head">
				<?php if ( '' !== $atts['heading'] ) : ?><h2><?php echo esc_html( $atts['heading'] ); ?></h2><?php endif; ?>
				<?php if ( '' !== $atts['note'] ) : ?><div class="cpc-collection-note"><?php echo esc_html( $atts['note'] ); ?></div><?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="cpc-grid3">
			<?php if ( ! empty( $products ) ) : ?>
				<?php foreach ( $products as $p ) :
					$link = get_permalink( $p->get_id() );
					$img  = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'large' ) : wc_placeholder_img_src( 'large' );
					$meta = '' !== $atts['meta'] ? $atts['meta'] : cpc_card_meta_text( $p );
					// Olive-oil products get a green Add-to-cart button; everything else stays blue.
					$add_olive = has_term( 'olive-oil', 'product_cat', $p->get_id() ) ? ' cpc-add--olive' : '';
					?>
					<div class="cpc-card cpc-lift<?php echo '' !== $add_olive ? ' cpc-card--olive' : ''; ?>">
						<a class="cpc-card-imglink" href="<?php echo esc_url( $link ); ?>">
							<div class="cpc-card-img"><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $p->get_name() ); ?>"></div>
						</a>
						<div class="cpc-card-body">
							<a class="cpc-card-titlelink" href="<?php echo esc_url( $link ); ?>"><span class="cpc-card-title"><?php echo esc_html( $p->get_name() ); ?></span></a>
							<?php if ( '' !== $meta ) : ?><div class="cpc-card-meta"><?php echo nl2br( esc_html( $meta ) ); ?></div><?php endif; ?>
							<div class="cpc-card-foot">
								<div class="cpc-card-price"><?php echo wp_kses_post( $p->get_price_html() ); ?></div>
								<?php if ( $p->is_purchasable() && $p->is_in_stock() ) : ?>
									<a href="<?php echo esc_url( $p->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $p->get_id() ); ?>" class="cpc-add cpc-card-add<?php echo esc_attr( $add_olive ); ?> add_to_cart_button ajax_add_to_cart" rel="nofollow"><?php esc_html_e( 'Add to cart', 'curtin-pc-shop' ); ?></a>
								<?php else : ?>
									<a class="cpc-add cpc-card-add<?php echo esc_attr( $add_olive ); ?>" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View', 'curtin-pc-shop' ); ?></a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="cpc-hero-lede"><?php esc_html_e( 'Products will appear here soon.', 'curtin-pc-shop' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cpc_products', 'cpc_products_shortcode' );

/* -----------------------------------------------------------------
 * cpc_render_product_card( $p ) — canonical .cpc-card markup used by
 * the product grids. Registered here (not only in
 * woocommerce/archive-product.php) so single-product.php can reuse it
 * for the up-sell / cross-sell rows. archive-product.php keeps its own
 * function_exists-guarded copy, which this definition pre-empts.
 * --------------------------------------------------------------- */
if ( ! function_exists( 'cpc_render_product_card' ) ) {
	function cpc_render_product_card( $p ) {
		if ( ! $p ) {
			return;
		}
		$link = get_permalink( $p->get_id() );
		$img  = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'large' ) : wc_placeholder_img_src( 'large' );
		$meta = cpc_card_meta_text( $p );
		// Olive-oil products get a green Add-to-cart button; everything else stays blue.
		$add_olive = has_term( 'olive-oil', 'product_cat', $p->get_id() ) ? ' cpc-add--olive' : '';
		?>
		<div class="cpc-card cpc-lift<?php echo '' !== $add_olive ? ' cpc-card--olive' : ''; ?>">
			<a class="cpc-card-imglink" href="<?php echo esc_url( $link ); ?>">
				<div class="cpc-card-img"><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $p->get_name() ); ?>"></div>
			</a>
			<div class="cpc-card-body">
				<a class="cpc-card-titlelink" href="<?php echo esc_url( $link ); ?>"><span class="cpc-card-title"><?php echo esc_html( $p->get_name() ); ?></span></a>
				<?php if ( '' !== $meta ) : ?><div class="cpc-card-meta"><?php echo nl2br( esc_html( $meta ) ); ?></div><?php endif; ?>
				<div class="cpc-card-foot">
					<div class="cpc-card-price"><?php echo wp_kses_post( $p->get_price_html() ); ?></div>
					<?php if ( $p->is_purchasable() && $p->is_in_stock() ) : ?>
						<a href="<?php echo esc_url( $p->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $p->get_id() ); ?>" class="cpc-add cpc-card-add<?php echo esc_attr( $add_olive ); ?> add_to_cart_button ajax_add_to_cart" rel="nofollow"><?php esc_html_e( 'Add to cart', 'curtin-pc-shop' ); ?></a>
					<?php else : ?>
						<a class="cpc-add cpc-card-add<?php echo esc_attr( $add_olive ); ?>" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View', 'curtin-pc-shop' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

/* -----------------------------------------------------------------
 * [cpc_category_tiles] — the two-up "shop by category" tiles used on
 * the editable Shop page. Links go to the editable /olive-oil/ and
 * /cards/ Pages. Managed here (not in the editor) so the tiles always
 * match the card design; the Shop page's intro copy stays editable.
 * --------------------------------------------------------------- */
function cpc_category_tiles_shortcode() {
	$tiles = array(
		array(
			'title' => __( 'Olive oil', 'curtin-pc-shop' ),
			'sub'   => __( 'Curtin Gold extra virgin olive oil', 'curtin-pc-shop' ),
			'url'   => cpc_olive_url(),
			'cat'   => 'olive-oil',
		),
		array(
			'title' => __( 'Art cards', 'curtin-pc-shop' ),
			'sub'   => __( 'Greeting-card sets from our community artwork', 'curtin-pc-shop' ),
			'url'   => home_url( '/art-cards/' ),
			'cat'   => 'art-cards',
		),
	);

	ob_start();
	echo '<div class="cpc-collection cpc-container"><div class="cpc-cats">';
	foreach ( $tiles as $t ) {
		// Grab a representative image from the first product in the category.
		$img = '';
		if ( function_exists( 'wc_get_products' ) ) {
			$found = wc_get_products( array( 'status' => 'publish', 'limit' => 1, 'category' => array( $t['cat'] ) ) );
			if ( ! empty( $found ) && $found[0]->get_image_id() ) {
				$img = wp_get_attachment_image_url( $found[0]->get_image_id(), 'large' );
			}
		}
		if ( ! $img && function_exists( 'wc_placeholder_img_src' ) ) {
			$img = wc_placeholder_img_src( 'large' );
		}
		?>
		<a class="cpc-cat cpc-lift" href="<?php echo esc_url( $t['url'] ); ?>">
			<div class="cpc-cat-img"><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $t['title'] ); ?>"></div>
			<div class="cpc-cat-body">
				<span class="cpc-cat-title"><?php echo esc_html( $t['title'] ); ?></span>
				<span class="cpc-cat-sub"><?php echo esc_html( $t['sub'] ); ?></span>
			</div>
		</a>
		<?php
	}
	echo '</div></div>';
	return ob_get_clean();
}
add_shortcode( 'cpc_category_tiles', 'cpc_category_tiles_shortcode' );

/* -----------------------------------------------------------------
 * 6. Live cart count — refresh the header cart pill via WooCommerce
 *    AJAX fragments so it updates immediately on add-to-cart.
 * --------------------------------------------------------------- */
function cpc_cart_label_html() {
	ob_start();
	?><span class="cpc-cart-label"><?php esc_html_e( 'Cart', 'curtin-pc-shop' ); ?>&nbsp;&middot;&nbsp;<?php echo (int) cpc_cart_count(); ?></span><?php
	return ob_get_clean();
}
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['span.cpc-cart-label'] = cpc_cart_label_html();
	return $fragments;
} );
// Make sure the cart-fragments script is available on the home/landing pages too.
add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'is_woocommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}, 101 );

/* -----------------------------------------------------------------
 * 7. Category-based shipping — Art Cards + Curtin Gold olive oil.
 *
 *    All the geography and pricing is handled here in code, so
 *    WooCommerce → Shipping only needs ONE Australia-wide zone
 *    containing one Flat rate (cost 0 — this filter sets the real
 *    amount) plus Local Pickup. Collection vs delivery stays the
 *    native Ship / Pickup toggle; the old custom "Collection or
 *    delivery?" checkout field was removed (it duplicated the toggle
 *    and could double-charge).
 *
 *    Rules:
 *      - Art Cards: flat $5 per order, ANY quantity, delivered
 *        ANYWHERE in Australia.
 *      - Olive oil: $5 for a single bottle, FREE for 2+ bottles;
 *        delivery restricted to postcode 6152 (Como, Karawara,
 *        Manning, Salter Point, Waterford). Local Pickup stays available for oil
 *        going anywhere.
 *      - Combined cart: the two costs add together (cards + 1 oil =
 *        $10; cards + 2 oil = $5).
 *
 *    When the cart holds olive oil but the destination isn't 6152 the
 *    paid Flat rate is withdrawn (§7a) AND checkout is hard-blocked
 *    server-side (§7b) so Apple Pay / Google Pay can't slip through.
 * --------------------------------------------------------------- */

/** Quantity of a given product_cat slug within a shipping package. */
function cpc_pkg_cat_qty( $package, $slug ) {
	$qty = 0;
	if ( empty( $package['contents'] ) ) {
		return $qty;
	}
	foreach ( $package['contents'] as $item ) {
		if ( ! empty( $item['product_id'] ) && has_term( $slug, 'product_cat', $item['product_id'] ) ) {
			$qty += (int) $item['quantity'];
		}
	}
	return $qty;
}

/* 7a. Price the flat rate per the rules; withdraw it for oil outside 6152. */
add_filter( 'woocommerce_package_rates', 'cpc_category_shipping_rates', 10, 2 );
function cpc_category_shipping_rates( $rates, $package ) {

	$card_qty = cpc_pkg_cat_qty( $package, 'art-cards' );
	$oil_qty  = cpc_pkg_cat_qty( $package, 'olive-oil' );

	// Show the correct block Local Pickup location for the cart: the "Olive Oil"
	// location when the cart holds oil, the "Curtin Primary School" location
	// otherwise (that location is marked "not for olive oil"). Matched by the
	// pickup location's label; when only one remains the block auto-selects it.
	foreach ( $rates as $cpc_rid => $cpc_rate ) {
		if ( 'pickup_location' !== $cpc_rate->get_method_id() ) {
			continue;
		}
		$cpc_is_olive_loc = ( false !== stripos( $cpc_rate->get_label(), 'olive' ) );
		if ( ( $oil_qty > 0 ) !== $cpc_is_olive_loc ) {
			unset( $rates[ $cpc_rid ] );
		}
	}

	$postcode        = isset( $package['destination']['postcode'] ) ? strtoupper( preg_replace( '/\s+/', '', $package['destination']['postcode'] ) ) : '';
	$oil_deliverable = ( '6152' === $postcode );

	// Olive oil in the cart but destination outside the delivery area:
	// withdraw paid delivery (can't be posted). Local Pickup / other
	// methods are left untouched so the oil can still be collected.
	if ( $oil_qty > 0 && ! $oil_deliverable ) {
		// Remove every zone delivery/pickup rate (Flat rate and any legacy zone
		// Local Pickup) so an express wallet (Apple Pay / Google Pay) can't
		// silently fall back to a zone pickup rate for an out-of-area oil order.
		// Genuine collection uses the block "Local pickup" toggle (pickup_location),
		// which is not a zone rate and is unaffected.
		foreach ( $rates as $rate_id => $rate ) {
			if ( in_array( $rate->get_method_id(), array( 'flat_rate', 'local_pickup' ), true ) ) {
				unset( $rates[ $rate_id ] );
			}
		}
		return $rates;
	}

	// Art cards: flat $5 if any are in the order, regardless of quantity.
	$card_cost = ( $card_qty > 0 ) ? 5.00 : 0.00;

	// Olive oil: $5 for a single bottle, free for 2 or more.
	$oil_cost = ( 1 === $oil_qty ) ? 5.00 : 0.00;

	$new_cost = $card_cost + $oil_cost;

	foreach ( $rates as $rate ) {
		if ( 'flat_rate' !== $rate->get_method_id() ) {
			continue; // leave Local Pickup / Free shipping alone
		}
		$rate->set_cost( $new_cost );
		// Zero the per-rate taxes (shipping isn't taxed on this store). If GST
		// on shipping is ever added, recalculate with WC_Tax::calc_shipping_tax().
		$taxes = array();
		foreach ( (array) $rate->get_taxes() as $k => $v ) {
			$taxes[ $k ] = 0;
		}
		$rate->set_taxes( $taxes );
	}

	return $rates;
}

/**
 * Explain why delivery is unavailable when olive oil is in the cart but the
 * destination is outside postcode 6152 (shown when no methods remain for the
 * package — e.g. if Local Pickup isn't offered).
 */
add_filter( 'woocommerce_no_shipping_available_html', 'cpc_oil_no_shipping_msg' );
add_filter( 'woocommerce_cart_no_shipping_available_html', 'cpc_oil_no_shipping_msg' );
function cpc_oil_no_shipping_msg( $html ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $html;
	}
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( ! empty( $item['product_id'] ) && has_term( 'olive-oil', 'product_cat', $item['product_id'] ) ) {
			return '<p>' . esc_html( cpc_oil_block_message() ) . '</p>';
		}
	}
	return $html;
}

/* -----------------------------------------------------------------
 * 7b. Hard block — olive oil must never be SHIPPED outside 6152,
 *     whatever the payment method.
 *
 *     Withdrawing the flat rate (§7a) hides delivery in the normal
 *     cart/checkout, but Apple Pay / Google Pay express buttons drive
 *     checkout through the WooCommerce Store API and can bypass a
 *     merely-missing shipping method. So we validate server-side on
 *     BOTH the Store API (block checkout + express wallets, via
 *     woocommerce_store_api_cart_errors) and the classic checkout: if
 *     the cart holds olive oil and the customer is shipping (not Local
 *     Pickup) to a postcode other than 6152, checkout is blocked with
 *     a clear message. Local Pickup is always allowed (oil can be
 *     collected from anywhere); cards-only carts are never affected.
 * --------------------------------------------------------------- */

/** True when the cart would ship olive oil to a non-6152 address. */
function cpc_oil_delivery_blocked() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return false;
	}
	$has_oil = false;
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( ! empty( $item['product_id'] ) && has_term( 'olive-oil', 'product_cat', $item['product_id'] ) ) {
			$has_oil = true;
			break;
		}
	}
	if ( ! $has_oil ) {
		return false;
	}
	// Local Pickup selected? Oil can be collected from anywhere.
	$chosen = ( WC()->session ) ? (array) WC()->session->get( 'chosen_shipping_methods' ) : array();
	foreach ( $chosen as $method ) {
		$method = (string) $method;
		if ( 0 === strpos( $method, 'local_pickup' ) || 0 === strpos( $method, 'pickup_location' ) ) {
			return false;
		}
	}
	// Shipping: enforce the 6152 delivery area.
	$postcode = ( WC()->customer ) ? strtoupper( preg_replace( '/\s+/', '', (string) WC()->customer->get_shipping_postcode() ) ) : '';
	if ( '' === $postcode ) {
		return false; // no address entered yet — don't error prematurely
	}
	return ( '6152' !== $postcode );
}

/** Clear message shown when olive-oil delivery is blocked. */
function cpc_oil_block_message() {
	return __( 'Curtin Gold olive oil can only be delivered within postcode 6152 (Como, Karawara, Manning, Salter Point, Waterford). Please choose Local Pickup, or remove the olive oil, to continue. Greeting cards can be posted anywhere in Australia.', 'curtin-pc-shop' );
}

// Store API — covers the block checkout AND Apple Pay / Google Pay express wallets.
add_action( 'woocommerce_store_api_cart_errors', function ( $errors ) {
	if ( cpc_oil_delivery_blocked() ) {
		$errors->add( 'cpc_oil_postcode', cpc_oil_block_message() );
	}
}, 10, 1 );

// Classic checkout fallback.
add_action( 'woocommerce_checkout_process', function () {
	if ( cpc_oil_delivery_blocked() && function_exists( 'wc_add_notice' ) ) {
		wc_add_notice( cpc_oil_block_message(), 'error' );
	}
} );
