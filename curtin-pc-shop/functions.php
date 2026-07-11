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

define( 'CPC_VERSION', '2.6.3' );

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

	// NOTE: the CSS/JS filenames carry the version (curtin-263.*) because the
	// SWAG/nginx proxy caches these static assets by PATH and ignores the ?ver
	// query string — a plain version bump does NOT bust it (see
	// Theme-Deployment-Notes.md §8). Renaming the file on every CSS/JS change is
	// the reliable cache-bust. Bump both the filename and CPC_VERSION together.
	wp_enqueue_style(
		'cpc-main',
		get_stylesheet_directory_uri() . '/assets/css/curtin-263.css',
		array( 'cpc-fonts' ),
		CPC_VERSION
	);

	wp_enqueue_script(
		'cpc-ui',
		get_stylesheet_directory_uri() . '/assets/js/curtin-263.js',
		array(),
		CPC_VERSION,
		true
	);
}, 100 ); // after everything else

// Add a body class so all our CSS can be tightly scoped under .cpc-theme.
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'cpc-theme';
	return $classes;
} );

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

/** Shop (Cards) URL. */
function cpc_shop_url() {
	$id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	return $id ? get_permalink( $id ) : home_url( '/shop/' );
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
					$meta = '' !== $atts['meta'] ? $atts['meta'] : wp_strip_all_tags( $p->get_short_description() );
					?>
					<div class="cpc-card cpc-lift">
						<a class="cpc-card-imglink" href="<?php echo esc_url( $link ); ?>">
							<div class="cpc-card-img"><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $p->get_name() ); ?>"></div>
						</a>
						<div class="cpc-card-body">
							<a class="cpc-card-titlelink" href="<?php echo esc_url( $link ); ?>"><span class="cpc-card-title"><?php echo esc_html( $p->get_name() ); ?></span></a>
							<?php if ( '' !== $meta ) : ?><div class="cpc-card-meta"><?php echo esc_html( $meta ); ?></div><?php endif; ?>
							<div class="cpc-card-foot">
								<div class="cpc-card-price"><?php echo wp_kses_post( $p->get_price_html() ); ?></div>
								<?php if ( $p->is_purchasable() && $p->is_in_stock() ) : ?>
									<a href="<?php echo esc_url( $p->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $p->get_id() ); ?>" class="cpc-add cpc-card-add add_to_cart_button ajax_add_to_cart" rel="nofollow"><?php esc_html_e( 'Add to cart', 'curtin-pc-shop' ); ?></a>
								<?php else : ?>
									<a class="cpc-add cpc-card-add" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View', 'curtin-pc-shop' ); ?></a>
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
			'title' => __( 'Art Cards', 'curtin-pc-shop' ),
			'sub'   => __( 'Greeting-card sets from our community artwork', 'curtin-pc-shop' ),
			'url'   => home_url( '/cards/' ),
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
 * 7. Delivery pricing — Curtin Gold olive oil.
 *
 *    Collection vs delivery is chosen with WooCommerce's native
 *    Ship / Pickup toggle (Local Pickup + a $5 Flat rate limited to
 *    the 6152 postcode zone = Karawara, Manning, Salter Point, Como).
 *    That single control is the source of truth. The old custom
 *    "Collection or delivery?" checkout field duplicated the toggle
 *    and could stack a second $5 fee on top of the flat rate, so it
 *    (and its suburb/address fields) has been removed.
 *
 *    Rule: $5 flat-rate delivery for a single bottle; FREE for 2+
 *    bottles of Curtin Gold. "Free for 2+" is implemented by zeroing
 *    the flat-rate cost when the cart holds 2 or more bottles.
 * --------------------------------------------------------------- */

/** How many Curtin Gold (olive-oil category) bottles are in the cart? */
function cpc_olive_qty_in_cart( $cart ) {
	$qty = 0;
	foreach ( $cart->get_cart() as $item ) {
		if ( ! empty( $item['product_id'] ) && has_term( 'olive-oil', 'product_cat', $item['product_id'] ) ) {
			$qty += (int) $item['quantity'];
		}
	}
	return $qty;
}

// Free local delivery for 2+ bottles of Curtin Gold — zero the flat-rate cost.
add_filter( 'woocommerce_package_rates', function ( $rates, $package ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $rates;
	}
	if ( cpc_olive_qty_in_cart( WC()->cart ) < 2 ) {
		return $rates; // single bottle keeps the $5 flat rate
	}
	foreach ( $rates as $rate ) {
		if ( 'flat_rate' === $rate->get_method_id() ) {
			$rate->set_cost( 0 );
			$taxes = array();
			foreach ( (array) $rate->get_taxes() as $k => $v ) {
				$taxes[ $k ] = 0;
			}
			$rate->set_taxes( $taxes );
		}
	}
	return $rates;
}, 10, 2 );
