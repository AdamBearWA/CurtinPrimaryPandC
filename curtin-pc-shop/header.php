<?php
/**
 * Global header — announcement bar + brand + nav + cart pill.
 * Replaces Storefront's header.php so we fully control the markup.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_home  = is_front_page();
$is_shop  = ( function_exists( 'is_shop' ) && is_shop() ) || is_product() || ( function_exists( 'is_product_category' ) && is_product_category() );
$is_olive = is_page( 'olive-oil' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<div class="cpc-nav-backdrop" aria-hidden="true"></div>

<div class="cpc-announce">
	<div class="cpc-container">
		<b><?php esc_html_e( 'Created by our community', 'curtin-pc-shop' ); ?></b>
		&nbsp;&nbsp;&middot;&nbsp;&nbsp;<?php esc_html_e( '100% of profits support our P&C', 'curtin-pc-shop' ); ?>
	</div>
</div>

<header class="cpc-header">
	<div class="cpc-container" style="display:flex;align-items:center;justify-content:space-between;width:100%">

		<button class="cpc-hamburger" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'curtin-pc-shop' ); ?>" aria-expanded="false" aria-controls="cpc-primary-nav">
			<svg width="24" height="24" viewBox="0 0 24 24" stroke="#1a2026" stroke-width="2" stroke-linecap="round" fill="none"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
		</button>

		<a class="cpc-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo cpc_bird( 42, 29, '#1d6fb8' ); ?>
			<span class="cpc-brand-text">
				<span class="cpc-brand-name"><?php esc_html_e( 'Curtin Primary School', 'curtin-pc-shop' ); ?></span>
				<span class="cpc-brand-sub"><?php esc_html_e( 'P&C Shop', 'curtin-pc-shop' ); ?></span>
			</span>
		</a>

		<div class="cpc-right">
			<nav class="cpc-nav" id="cpc-primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'curtin-pc-shop' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					// Editable menu (Appearance → Menus). The nav_menu_link_attributes
					// filter in functions.php adds .cpc-lnk / .cpc-active to each link.
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'depth'          => 1,
						'fallback_cb'    => false,
					) );
				} else {
					// Fallback until a menu is assigned — keeps the site navigable.
					?>
					<a class="cpc-lnk<?php echo $is_home ? ' cpc-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'curtin-pc-shop' ); ?></a>
					<a class="cpc-lnk<?php echo $is_shop ? ' cpc-active' : ''; ?>" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop', 'curtin-pc-shop' ); ?></a>
					<a class="cpc-lnk<?php echo $is_olive ? ' cpc-active' : ''; ?>" href="<?php echo esc_url( cpc_olive_url() ); ?>"><?php esc_html_e( 'Olive oil', 'curtin-pc-shop' ); ?></a>
					<a class="cpc-lnk" href="<?php echo esc_url( home_url( '/art-cards/' ) ); ?>"><?php esc_html_e( 'Art cards', 'curtin-pc-shop' ); ?></a>
					<?php
				}
				?>
			</nav>
			<?php $cpc_count = (int) cpc_cart_count(); ?>
			<a class="cpc-btn cpc-cart<?php echo $cpc_count > 0 ? ' cpc-has-items' : ''; ?>" href="<?php echo esc_url( cpc_cart_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: item count */ _n( 'Cart, %d item', 'Cart, %d items', $cpc_count, 'curtin-pc-shop' ), $cpc_count ) ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
				<span class="cpc-cart-label"><?php esc_html_e( 'Cart', 'curtin-pc-shop' ); ?>&nbsp;&middot;&nbsp;<?php echo $cpc_count; ?></span>
				<span class="cpc-cart-badge" aria-hidden="true"><?php echo $cpc_count > 9 ? '9+' : $cpc_count; ?></span>
			</a>
		</div>

	</div>
</header>

<main id="cpc-content" class="cpc-content">
