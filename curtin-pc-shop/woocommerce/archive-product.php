<?php
/**
 * Shop / product-category archive — boutique grid, fully data-driven.
 *
 * No hard-coded product, category name or marketing copy. Two modes:
 *
 *   • Single product-category archive (/product-category/<slug>/):
 *     shows that one category — its name as the page heading, its
 *     WooCommerce category description directly below the title, then
 *     the products in that category.
 *
 *   • Shop / "All products" archive: lists every non-empty top-level
 *     product category in turn. Each category renders its name, its
 *     category description below the title, then its own products — so
 *     olive oil sits under Olive oil, cards under Art cards, etc.
 *
 * Per-card meta is each product's short description. Category descriptions
 * come from WooCommerce → Products → Categories (the term Description field).
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render one product as a boutique card. The meta line is the product's
 * short description (never a hard-coded string).
 *
 * @param WC_Product $p Product object.
 */
if ( ! function_exists( 'cpc_render_product_card' ) ) {
	function cpc_render_product_card( $p ) {
		if ( ! $p ) {
			return;
		}
		$link = get_permalink( $p->get_id() );
		$img  = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'large' ) : wc_placeholder_img_src( 'large' );
		$meta = wp_strip_all_tags( $p->get_short_description() );
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
		<?php
	}
}

/**
 * Render a whole category section: heading, its description (below the
 * title) and its product grid. Skips categories with no purchasable
 * products so empty headings never appear.
 *
 * @param WP_Term $term Product category term.
 * @param bool    $solo True for a single-category archive (uses <h1>);
 *                      false for the grouped shop archive (uses <h2>).
 */
if ( ! function_exists( 'cpc_render_category_block' ) ) {
	function cpc_render_category_block( $term, $solo = false ) {
		if ( ! $term || is_wp_error( $term ) ) {
			return;
		}
		$products = wc_get_products( array(
			'status'   => 'publish',
			'limit'    => -1,
			'orderby'  => 'menu_order',
			'order'    => 'ASC',
			'category' => array( $term->slug ),
		) );
		if ( empty( $products ) ) {
			return;
		}
		$desc_raw = term_description( $term->term_id, 'product_cat' );
		$has_desc = ( '' !== trim( wp_strip_all_tags( (string) $desc_raw ) ) );
		?>
		<section class="cpc-collection cpc-container">
			<?php if ( $solo ) : ?>
				<div class="cpc-collection-intro">
					<h1><?php echo esc_html( $term->name ); ?></h1>
					<?php if ( $has_desc ) : ?>
						<div class="cpc-cat-desc"><?php echo wp_kses_post( $desc_raw ); ?></div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="cpc-collection-head"><h2><?php echo esc_html( $term->name ); ?></h2></div>
				<?php if ( $has_desc ) : ?>
					<div class="cpc-cat-desc"><?php echo wp_kses_post( $desc_raw ); ?></div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="cpc-grid3">
				<?php
				foreach ( $products as $p ) {
					cpc_render_product_card( $p );
				}
				?>
			</div>
		</section>
		<?php
	}
}

get_header();

if ( is_product_category() ) {

	// Single product-category archive — one category, its description, its products.
	cpc_render_category_block( get_queried_object(), true );

} else {

	// Shop / "All products" — every non-empty top-level category, grouped.
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'exclude'    => array( (int) get_option( 'default_product_cat', 0 ) ),
	) );

	$rendered = 0;
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		foreach ( $terms as $term ) {
			// Top-level categories only, so a product in a sub-category isn't listed twice.
			if ( (int) $term->parent !== 0 ) {
				continue;
			}
			cpc_render_category_block( $term, false );
			$rendered++;
		}
	}

	if ( 0 === $rendered ) {
		echo '<section class="cpc-collection cpc-container"><p class="cpc-hero-lede">' . esc_html__( 'Our products will appear here soon. Check back shortly.', 'curtin-pc-shop' ) . '</p></section>';
	}
}

get_footer();
