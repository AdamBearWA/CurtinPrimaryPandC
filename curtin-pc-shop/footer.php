<?php
/**
 * Global footer — navy, Shop/Help columns + Whadjuk Noongar acknowledgement.
 * Replaces Storefront's footer.php.
 *
 * @package curtin-pc-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main><!-- #cpc-content -->

<!-- FOOTER -->
<footer class="cpc-footer">
	<div class="cpc-container">
		<div class="cpc-footer-top">
			<div class="cpc-footer-brand">
				<div class="cpc-footer-brand-row">
					<div class="cpc-footer-brand-name"><?php esc_html_e( 'Curtin Primary P&C', 'curtin-pc-shop' ); ?></div>
				</div>
				<p class="cpc-footer-blurb"><?php esc_html_e( 'An initiative of the Curtin Primary School Parents & Citizens Association.', 'curtin-pc-shop' ); ?></p>
			</div>
			<div class="cpc-footer-cols">
				<div>
					<div class="cpc-footer-coltitle"><?php esc_html_e( 'Shop', 'curtin-pc-shop' ); ?></div>
					<div class="cpc-footer-links">
						<a class="cpc-lnk" href="<?php echo esc_url( cpc_shop_url() ); ?>"><?php esc_html_e( 'Art cards', 'curtin-pc-shop' ); ?></a>
						<a class="cpc-lnk" href="<?php echo esc_url( cpc_olive_url() ); ?>"><?php esc_html_e( 'Olive oil', 'curtin-pc-shop' ); ?></a>
					</div>
				</div>
				<div>
					<div class="cpc-footer-coltitle"><?php esc_html_e( 'Help', 'curtin-pc-shop' ); ?></div>
					<div class="cpc-footer-links">
						<a class="cpc-lnk" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact the P&C', 'curtin-pc-shop' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="cpc-footer-base">
			<div><?php esc_html_e( 'We acknowledge the Whadjuk Noongar people, traditional custodians of this land.', 'curtin-pc-shop' ); ?></div>
			<div><?php printf( esc_html__( '© %s Curtin Primary P&C · Built on WooCommerce · Secure payments by Square', 'curtin-pc-shop' ), esc_html( gmdate( 'Y' ) ) ); ?></div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
