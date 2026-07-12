<?php
/**
 * Single product — fully custom layout to match reference/product.html.
 * Featured image = pack flat-lay (all four cards); gallery thumbs = the
 * four individual card designs. Uses a real WooCommerce add-to-cart form
 * so checkout still routes through Square. No related products.
 *
 * Overrides woocommerce/single-product.php.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	global $product;
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_ID() );
	}
	if ( ! $product ) {
		continue;
	}

	// --- Gallery images ---
	// Every product in the catalogue has a real featured image + gallery set
	// via the Media Library, so this only ever falls back to WooCommerce's
	// own placeholder graphic for a brand-new product with no photos yet.
	$main_src = $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'large' ) : wc_placeholder_img_src( 'large' );
	$main_alt = $product->get_name();

	$gallery_ids = $product->get_gallery_image_ids();
	$thumbs      = array();
	// Include the featured (primary) image as the FIRST thumbnail whenever there
	// are gallery images, so a visitor can always click back to it after viewing
	// another shot. Without this, clicking a gallery thumb swapped the main image
	// with no thumbnail left to return to the primary image.
	if ( $gallery_ids && $product->get_image_id() ) {
		$thumbs[] = array(
			'thumb' => wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_gallery_thumbnail' ),
			'full'  => $main_src,
			'alt'   => $main_alt,
		);
	}
	foreach ( $gallery_ids as $gid ) {
		$thumbs[] = array(
			'thumb' => wp_get_attachment_image_url( $gid, 'woocommerce_gallery_thumbnail' ),
			'full'  => wp_get_attachment_image_url( $gid, 'large' ),
			'alt'   => get_post_meta( $gid, '_wp_attachment_image_alt', true ),
		);
	}

	$price_plain = wp_strip_all_tags( wc_price( wc_get_price_to_display( $product ) ) );
	$is_olive    = has_term( 'olive-oil', 'product_cat', $product->get_id() );
	?>

	<!-- BREADCRUMB -->
	<div class="cpc-breadcrumb cpc-container">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'curtin-pc-shop' ); ?></a>
		<?php if ( $is_olive ) : ?>
			&nbsp;&nbsp;/&nbsp;&nbsp;<a href="<?php echo esc_url( cpc_olive_url() ); ?>"><?php esc_html_e( 'Olive oil', 'curtin-pc-shop' ); ?></a>
		<?php else : ?>
			&nbsp;&nbsp;/&nbsp;&nbsp;<a href="<?php echo esc_url( cpc_shop_url() ); ?>"><?php esc_html_e( 'Art cards', 'curtin-pc-shop' ); ?></a>
		<?php endif; ?>
		&nbsp;&nbsp;/&nbsp;&nbsp;<span class="cpc-current"><?php echo esc_html( $product->get_name() ); ?></span>
	</div>

	<!-- PRODUCT -->
	<section class="cpc-product cpc-container">

		<!-- gallery -->
		<div class="cpc-gallery">
			<div class="cpc-gallery-main"><img src="<?php echo esc_url( $main_src ); ?>" alt="<?php echo esc_attr( $main_alt ); ?>"></div>
			<?php if ( $thumbs ) : ?>
				<div class="cpc-thumbs">
					<?php foreach ( $thumbs as $idx => $t ) : ?>
						<div class="cpc-thumb<?php echo 0 === $idx ? ' cpc-thumb-active' : ''; ?>">
							<img src="<?php echo esc_url( $t['thumb'] ); ?>" data-full="<?php echo esc_url( $t['full'] ); ?>" alt="<?php echo esc_attr( $t['alt'] ); ?>">
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- info -->
		<div class="cpc-pinfo">
			<h1><?php echo esc_html( $product->get_name() ); ?></h1>

			<div class="cpc-pprice-row">
				<div class="cpc-pprice"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			</div>

			<?php
			// Real WooCommerce product description, shown up top (no hard-coded copy).
			$desc = $product->get_description();
			if ( $desc ) :
				?>
				<div class="cpc-pdesc"><?php echo wp_kses_post( wpautop( $desc ) ); ?></div>
			<?php endif; ?>

			<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
				<form class="cart cpc-buyrow" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
					<div class="cpc-qty">
						<button type="button" data-step="down" aria-label="<?php esc_attr_e( 'Decrease quantity', 'curtin-pc-shop' ); ?>">−</button>
						<input type="number" name="quantity" value="1" min="1" step="1" inputmode="numeric" aria-label="<?php esc_attr_e( 'Quantity', 'curtin-pc-shop' ); ?>">
						<button type="button" data-step="up" aria-label="<?php esc_attr_e( 'Increase quantity', 'curtin-pc-shop' ); ?>">+</button>
					</div>
					<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="cpc-btn cpc-addtocart<?php echo $is_olive ? ' cpc-add--olive' : ''; ?>">
						<?php printf( esc_html__( 'Add to cart · %s', 'curtin-pc-shop' ), esc_html( $price_plain ) ); ?>
					</button>
				</form>
			<?php else : ?>
				<div class="cpc-buyrow"><span class="cpc-addtocart" style="opacity:.6;cursor:default"><?php esc_html_e( 'Currently unavailable', 'curtin-pc-shop' ); ?></span></div>
			<?php endif; ?>

		</div>
	</section>

	<?php
	// --- Up-sells & cross-sells ---
	// Adam wants both on the single product page (cross-sells normally show
	// only on the cart). Each row reuses the canonical .cpc-card grid markup.
	$cpc_related_sections = array(
		array(
			'ids'     => (array) $product->get_upsell_ids(),
			'heading' => __( 'You may also like', 'curtin-pc-shop' ),
		),
		array(
			'ids'     => (array) $product->get_cross_sell_ids(),
			'heading' => __( 'Goes well with', 'curtin-pc-shop' ),
		),
	);
	foreach ( $cpc_related_sections as $cpc_sec ) :
		$cpc_ids = array_filter( array_map( 'absint', $cpc_sec['ids'] ) );
		if ( empty( $cpc_ids ) || ! function_exists( 'cpc_render_product_card' ) ) {
			continue;
		}
		$cpc_items = array();
		foreach ( $cpc_ids as $cpc_id ) {
			$cpc_p = wc_get_product( $cpc_id );
			if ( $cpc_p && 'publish' === $cpc_p->get_status() && $cpc_p->is_visible() ) {
				$cpc_items[] = $cpc_p;
			}
		}
		if ( empty( $cpc_items ) ) {
			continue;
		}
		?>
		<section class="cpc-collection cpc-container cpc-related">
			<div class="cpc-collection-head"><h2><?php echo esc_html( $cpc_sec['heading'] ); ?></h2></div>
			<div class="cpc-grid3">
				<?php foreach ( $cpc_items as $cpc_p ) { cpc_render_product_card( $cpc_p ); } ?>
			</div>
		</section>
		<?php
	endforeach;
endwhile;

get_footer();
