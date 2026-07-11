<?php
/**
 * Shop archive ("Cards") — set cards in the boutique style with a working
 * Add-to-cart button. No category tiles, filters, sidebar or badges.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="cpc-collection cpc-container">
	<div class="cpc-collection-intro">
		<h1><?php esc_html_e( 'Curtin Primary P&C Art Card Series', 'curtin-pc-shop' ); ?></h1>
		<p class="cpc-hero-lede"><?php esc_html_e( "Celebrate life's special moments while supporting our school community.", 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'The Curtin Primary P&C Art Card Series showcases a beautiful collection of floral illustrations created by students, parents and staff from the Curtin Primary School community as part of a whole-school art project facilitated by local artist Kelly Muller.', 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'Each card celebrates the creativity of our school, making them perfect for birthdays, thank yous, celebrations or simply staying in touch. Best of all, every purchase helps raise funds for the Curtin Primary P&C, supporting projects, resources and opportunities that benefit our students.', 'curtin-pc-shop' ); ?></p>
		<p><?php esc_html_e( 'Created by our community, for our community, these cards are a meaningful way to share a thoughtful message while giving back to Curtin Primary School P&C community.', 'curtin-pc-shop' ); ?></p>
	</div>
	<div class="cpc-collection-head">
		<h2><?php esc_html_e( 'Greeting cards', 'curtin-pc-shop' ); ?></h2>
		<div class="cpc-collection-note"><?php esc_html_e( 'Every set &middot; four cards &middot; $10', 'curtin-pc-shop' ); ?></div>
	</div>

	<?php if ( have_posts() ) : ?>
		<div class="cpc-grid3">
			<?php
			while ( have_posts() ) :
				the_post();
				$p = wc_get_product( get_the_ID() );
				if ( ! $p ) {
					continue;
				}
				$link = get_permalink( $p->get_id() );
				$img  = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'large' ) : wc_placeholder_img_src( 'large' );
				?>
				<div class="cpc-card cpc-lift">
					<a class="cpc-card-imglink" href="<?php echo esc_url( $link ); ?>">
						<div class="cpc-card-img"><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $p->get_name() ); ?>"></div>
					</a>
					<div class="cpc-card-body">
						<a class="cpc-card-titlelink" href="<?php echo esc_url( $link ); ?>"><span class="cpc-card-title"><?php echo esc_html( $p->get_name() ); ?></span></a>
						<div class="cpc-card-meta"><?php esc_html_e( 'Set of four &middot; blank inside', 'curtin-pc-shop' ); ?></div>
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
			endwhile;
			?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<p class="cpc-hero-lede"><?php esc_html_e( 'Our cards will appear here soon. Check back shortly.', 'curtin-pc-shop' ); ?></p>
	<?php endif; ?>
</section>

<?php
get_footer();
