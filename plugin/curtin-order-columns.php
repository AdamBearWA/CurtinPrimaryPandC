<?php
/**
 * Plugin Name:       Curtin P&C Order Columns
 * Plugin URI:        https://github.com/AdamBearWA/CurtinPrimaryPandC
 * Description:       Adds Fulfilment (pickup vs delivery), Email, Phone and Shipping address columns to the WooCommerce Orders list (with a Pickup/Delivery filter) and to Analytics &rarr; Orders, including its CSV Download.
 * Version:           1.1.0
 * Author:            Curtin Primary P&C
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * WC requires at least: 8.0
 * License:           GPL-3.0-or-later
 * Text Domain:       curtin-order-columns
 *
 * Fulfilment is read from the order's shipping line, matching the theme's
 * shipping rules (curtin-pc-shop §7):
 *   pickup_location / local_pickup  -> Pickup  (label = which pickup location)
 *   flat_rate (or any other rate)   -> Delivery
 *   no shipping line                -> No shipping (e.g. donations, virtual items)
 *
 * Sections:
 *   1. Fulfilment detection
 *   2. Order meta (so the Orders list can be filtered by fulfilment)
 *   3. Orders list columns
 *   4. Orders list Pickup/Delivery filter
 *   5. Admin CSS
 *   6. Analytics -> Orders columns + CSV download
 */

defined( 'ABSPATH' ) || exit;

define( 'CPC_OC_VERSION', '1.1.0' );
define( 'CPC_OC_META', '_cpc_fulfilment' );

/* -----------------------------------------------------------------
 * HPOS compatibility declaration.
 * --------------------------------------------------------------- */
add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/* -----------------------------------------------------------------
 * 1. Fulfilment detection.
 * --------------------------------------------------------------- */

/**
 * Work out whether an order is collection or delivery.
 *
 * @param WC_Order|null $order Order object.
 * @return array{type:string,label:string,address:string}
 */
function cpc_oc_fulfilment( $order ) {

	$out = array(
		'type'    => 'none',
		'label'   => '',
		'address' => '',
	);

	if ( ! $order instanceof WC_Order ) {
		return $out;
	}

	foreach ( $order->get_shipping_methods() as $item ) {

		$method_id = $item->get_method_id();

		// Block "Local pickup" toggle (pickup_location) or a legacy zone
		// Local Pickup rate. Either way the customer is collecting.
		if ( 'pickup_location' === $method_id || 'local_pickup' === $method_id ) {

			$out['type']  = 'pickup';
			$out['label'] = $item->get_name();

			// Blocks stores the chosen pickup location's address in item meta;
			// the key has changed across versions, so try the known variants.
			foreach ( array( 'pickup_address', 'pickup_location', 'Address', 'pickup_details' ) as $key ) {
				$value = $item->get_meta( $key, true );
				if ( is_string( $value ) && '' !== $value ) {
					$out['address'] = $value;
					break;
				}
			}

			return $out; // Pickup wins outright.
		}

		if ( 'none' === $out['type'] ) {
			$out['type']  = 'delivery';
			$out['label'] = $item->get_name();
		}
	}

	return $out;
}

/** Human label for a fulfilment type. */
function cpc_oc_type_label( $type ) {
	$labels = array(
		'pickup'   => __( 'Pickup', 'curtin-order-columns' ),
		'delivery' => __( 'Delivery', 'curtin-order-columns' ),
		'none'     => __( 'No shipping', 'curtin-order-columns' ),
	);
	return isset( $labels[ $type ] ) ? $labels[ $type ] : $type;
}

/* -----------------------------------------------------------------
 * 2. Store the type as order meta so the list can be filtered by it.
 * --------------------------------------------------------------- */

/**
 * Save the fulfilment type on an order (only writes when it changed).
 *
 * @param WC_Order|int $order Order or order ID.
 */
function cpc_oc_sync_meta( $order ) {

	$order = is_numeric( $order ) ? wc_get_order( $order ) : $order;

	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$fulfilment = cpc_oc_fulfilment( $order );
	$type       = $fulfilment['type'];

	if ( $order->get_meta( CPC_OC_META, true ) === $type ) {
		return;
	}

	$order->update_meta_data( CPC_OC_META, $type );
	$order->save();
}

add_action( 'woocommerce_checkout_order_processed', 'cpc_oc_sync_meta', 20 );            // Classic checkout.
add_action( 'woocommerce_store_api_checkout_order_processed', 'cpc_oc_sync_meta', 20 );  // Block checkout + express wallets.
add_action( 'woocommerce_process_shop_order_meta', 'cpc_oc_sync_meta', 60 );             // Manual admin edits.

/** Backfill existing orders on activation (small store — one pass is fine). */
function cpc_oc_backfill() {

	if ( ! function_exists( 'wc_get_orders' ) ) {
		return;
	}

	$page = 1;

	do {
		$orders = wc_get_orders(
			array(
				'limit'  => 100,
				'page'   => $page,
				'status' => 'any',
				'return' => 'objects',
			)
		);

		foreach ( $orders as $order ) {
			cpc_oc_sync_meta( $order );
		}

		++$page;
	} while ( count( $orders ) === 100 && $page < 50 );
}
register_activation_hook( __FILE__, 'cpc_oc_backfill' );

/* -----------------------------------------------------------------
 * 3. Columns — HPOS (woocommerce_page_wc-orders) and legacy (shop_order).
 * --------------------------------------------------------------- */

/**
 * Insert our columns after the built-in "Order" / order-number column.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function cpc_oc_columns( $columns ) {

	$new = array(
		'cpc_fulfilment' => __( 'Fulfilment', 'curtin-order-columns' ),
		'cpc_email'      => __( 'Email', 'curtin-order-columns' ),
		'cpc_phone'      => __( 'Phone', 'curtin-order-columns' ),
		'cpc_address'    => __( 'Ship to', 'curtin-order-columns' ),
	);

	$out    = array();
	$placed = false;

	foreach ( $columns as $key => $label ) {
		$out[ $key ] = $label;
		if ( ! $placed && in_array( $key, array( 'order_number', 'order_status', 'order_date' ), true ) ) {
			$out    = array_merge( $out, $new );
			$placed = true;
		}
	}

	return $placed ? $out : array_merge( $out, $new );
}
add_filter( 'manage_woocommerce_page_wc-orders_columns', 'cpc_oc_columns', 20 );
add_filter( 'manage_edit-shop_order_columns', 'cpc_oc_columns', 20 );

/**
 * Render a column's contents.
 *
 * @param string            $column Column key.
 * @param WC_Order|int|null $order  Order (HPOS) or post ID (legacy).
 */
function cpc_oc_render_column( $column, $order = null ) {

	if ( 0 !== strpos( $column, 'cpc_' ) ) {
		return;
	}

	if ( ! $order instanceof WC_Order ) {
		global $post;
		$order = wc_get_order( $order ? $order : ( $post ? $post->ID : 0 ) );
	}

	if ( ! $order instanceof WC_Order ) {
		echo '&mdash;';
		return;
	}

	switch ( $column ) {

		case 'cpc_fulfilment':
			$fulfilment = cpc_oc_fulfilment( $order );
			printf(
				'<span class="cpc-pill cpc-pill--%1$s">%2$s</span>',
				esc_attr( $fulfilment['type'] ),
				esc_html( cpc_oc_type_label( $fulfilment['type'] ) )
			);
			if ( '' !== $fulfilment['label'] ) {
				echo '<br><small>' . esc_html( $fulfilment['label'] ) . '</small>';
			}
			if ( 'pickup' === $fulfilment['type'] && '' !== $fulfilment['address'] ) {
				echo '<br><small>' . esc_html( wp_strip_all_tags( $fulfilment['address'] ) ) . '</small>';
			}
			break;

		case 'cpc_email':
			$email = $order->get_billing_email();
			echo $email
				? '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>'
				: '&mdash;';
			break;

		case 'cpc_phone':
			$phone = cpc_oc_phone( $order );
			echo $phone
				? '<a href="' . esc_url( 'tel:' . preg_replace( '/\s+/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>'
				: '&mdash;';
			break;

		case 'cpc_address':
			// Block local pickup collects no shipping address, so fall back to billing.
			$address = $order->get_formatted_shipping_address();
			$note    = '';

			if ( ! $address ) {
				$address = $order->get_formatted_billing_address();
				$note    = '<br><small>' . esc_html__( '(billing address)', 'curtin-order-columns' ) . '</small>';
			}

			echo $address ? wp_kses_post( $address ) . $note : '&mdash;';
			break;
	}
}
add_action( 'manage_woocommerce_page_wc-orders_custom_column', 'cpc_oc_render_column', 20, 2 );
add_action( 'manage_shop_order_posts_custom_column', 'cpc_oc_render_column', 20, 2 );

/**
 * Best available phone number for an order (block checkout writes billing_phone).
 *
 * @param WC_Order $order Order.
 * @return string
 */
function cpc_oc_phone( $order ) {

	$phone = $order->get_billing_phone();

	if ( ! $phone && is_callable( array( $order, 'get_shipping_phone' ) ) ) {
		$phone = $order->get_shipping_phone();
	}

	return (string) $phone;
}

/* -----------------------------------------------------------------
 * 4. Pickup / Delivery filter dropdown.
 * --------------------------------------------------------------- */

/** Output the dropdown. */
function cpc_oc_dropdown() {

	$current = isset( $_GET['cpc_fulfilment'] ) ? sanitize_key( wp_unslash( $_GET['cpc_fulfilment'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	echo '<select name="cpc_fulfilment" id="cpc_fulfilment">';
	echo '<option value="">' . esc_html__( 'All fulfilment', 'curtin-order-columns' ) . '</option>';

	foreach ( array( 'pickup', 'delivery', 'none' ) as $type ) {
		printf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_attr( $type ),
			selected( $current, $type, false ),
			esc_html( cpc_oc_type_label( $type ) )
		);
	}

	echo '</select>';
}

add_action(
	'woocommerce_order_list_table_restrict_manage_orders',
	static function ( $order_type = '', $which = 'top' ) {
		if ( 'top' === $which ) {
			cpc_oc_dropdown();
		}
	},
	20,
	2
);

add_action(
	'restrict_manage_posts',
	static function ( $post_type ) {
		if ( 'shop_order' === $post_type ) {
			cpc_oc_dropdown();
		}
	},
	20
);

/** Build the meta query for the selected type. */
function cpc_oc_meta_query() {

	$type = isset( $_GET['cpc_fulfilment'] ) ? sanitize_key( wp_unslash( $_GET['cpc_fulfilment'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! in_array( $type, array( 'pickup', 'delivery', 'none' ), true ) ) {
		return array();
	}

	return array(
		array(
			'key'     => CPC_OC_META,
			'value'   => $type,
			'compare' => '=',
		),
	);
}

// HPOS orders list.
add_filter(
	'woocommerce_order_list_table_prepare_items_query_args',
	static function ( $args ) {
		$meta_query = cpc_oc_meta_query();
		if ( $meta_query ) {
			$args['meta_query'] = array_merge( isset( $args['meta_query'] ) ? (array) $args['meta_query'] : array(), $meta_query );
		}
		return $args;
	},
	20
);

// Legacy posts-table orders list.
add_filter(
	'request',
	static function ( $vars ) {
		if ( ! is_admin() || ! isset( $vars['post_type'] ) || 'shop_order' !== $vars['post_type'] ) {
			return $vars;
		}
		$meta_query = cpc_oc_meta_query();
		if ( $meta_query ) {
			$vars['meta_query'] = array_merge( isset( $vars['meta_query'] ) ? (array) $vars['meta_query'] : array(), $meta_query );
		}
		return $vars;
	},
	20
);

/* -----------------------------------------------------------------
 * 5. A little admin CSS so the wider table stays readable.
 * --------------------------------------------------------------- */
add_action(
	'admin_head',
	static function () {

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || ! in_array( $screen->id, array( 'woocommerce_page_wc-orders', 'edit-shop_order' ), true ) ) {
			return;
		}
		?>
		<style>
			.column-cpc_fulfilment { width: 9em; }
			.column-cpc_email,
			.column-cpc_address { width: 14em; word-break: break-word; }
			.column-cpc_phone { width: 9em; white-space: nowrap; }
			.cpc-pill {
				display: inline-block;
				padding: 1px 8px;
				border-radius: 10px;
				font-size: 11px;
				font-weight: 600;
				line-height: 1.7;
			}
			.cpc-pill--pickup   { background: #e6f4ea; color: #16643a; }
			.cpc-pill--delivery { background: #e7f0fb; color: #1d4e89; }
			.cpc-pill--none     { background: #f0f0f1; color: #646970; }
		</style>
		<?php
	}
);

/* -----------------------------------------------------------------
 * 6. Analytics -> Orders: extra columns and CSV download.
 *
 *    Three paths have to be fed, all from the same helper:
 *      a) the REST response  -> the on-screen table AND the CSV that the
 *         browser builds when the result set fits on one page
 *         (woocommerce_rest_prepare_report_orders)
 *      b) the server-side CSV that WooCommerce emails when the result set
 *         spans more than one page (woocommerce_report_orders_export_columns
 *         + woocommerce_report_orders_prepare_export_item)
 *      c) the React table itself, which needs a JS filter to render the
 *         extra headers/cells (woocommerce_admin_report_table)
 * --------------------------------------------------------------- */

/** Column key => CSV/table header, in display order. */
function cpc_oc_report_columns() {
	return array(
		'cpc_fulfilment' => __( 'Fulfilment', 'curtin-order-columns' ),
		'cpc_email'      => __( 'Email', 'curtin-order-columns' ),
		'cpc_phone'      => __( 'Phone', 'curtin-order-columns' ),
		'cpc_ship_to'    => __( 'Ship to', 'curtin-order-columns' ),
	);
}

/**
 * Flat, single-line values for one order — used by the table and both CSVs.
 *
 * @param int $order_id Order ID.
 * @return array<string,string>
 */
function cpc_oc_report_fields( $order_id ) {

	static $cache = array();

	$order_id = (int) $order_id;

	if ( isset( $cache[ $order_id ] ) ) {
		return $cache[ $order_id ];
	}

	$fields = array(
		'cpc_fulfilment' => '',
		'cpc_email'      => '',
		'cpc_phone'      => '',
		'cpc_ship_to'    => '',
	);

	$order = $order_id ? wc_get_order( $order_id ) : false;

	if ( ! $order instanceof WC_Order ) {
		$cache[ $order_id ] = $fields;
		return $fields;
	}

	$fulfilment = cpc_oc_fulfilment( $order );
	$label      = cpc_oc_type_label( $fulfilment['type'] );

	if ( '' !== $fulfilment['label'] ) {
		$label .= ' — ' . $fulfilment['label'];
	}

	// Block local pickup collects no shipping address, so fall back to billing.
	$address = $order->get_formatted_shipping_address();
	$suffix  = '';

	if ( ! $address ) {
		$address = $order->get_formatted_billing_address();
		$suffix  = $address ? ' ' . __( '(billing)', 'curtin-order-columns' ) : '';
	}

	$fields['cpc_fulfilment'] = $label;
	$fields['cpc_email']      = $order->get_billing_email();
	$fields['cpc_phone']      = cpc_oc_phone( $order );
	$fields['cpc_ship_to']    = $address ? cpc_oc_one_line( $address ) . $suffix : '';

	$cache[ $order_id ] = $fields;

	return $fields;
}

/**
 * Collapse a WooCommerce formatted address (which uses <br/>) to one line.
 *
 * @param string $address Formatted address HTML.
 * @return string
 */
function cpc_oc_one_line( $address ) {
	$address = preg_replace( '#<br\s*/?>#i', ', ', (string) $address );
	$address = wp_strip_all_tags( $address );
	$address = preg_replace( '/\s+/', ' ', $address );
	return trim( str_replace( ' ,', ',', $address ), " ,\t\n\r" );
}

/* 6a. REST response — feeds the on-screen table and the in-browser CSV. */
add_filter(
	'woocommerce_rest_prepare_report_orders',
	static function ( $response, $report, $request ) {

		if ( ! $response instanceof WP_REST_Response ) {
			return $response;
		}

		$order_id = is_array( $report ) && isset( $report['order_id'] ) ? $report['order_id'] : 0;

		if ( ! $order_id ) {
			return $response;
		}

		$response->set_data( array_merge( (array) $response->get_data(), cpc_oc_report_fields( $order_id ) ) );

		return $response;
	},
	20,
	3
);

/* 6b. Server-generated CSV (emailed when the report spans several pages). */
add_filter(
	'woocommerce_report_orders_export_columns',
	static function ( $columns ) {
		return array_merge( (array) $columns, cpc_oc_report_columns() );
	},
	20
);

add_filter(
	'woocommerce_report_orders_prepare_export_item',
	static function ( $export_item, $item ) {

		$order_id = is_array( $item ) && isset( $item['order_id'] ) ? $item['order_id'] : 0;

		return array_merge( (array) $export_item, cpc_oc_report_fields( $order_id ) );
	},
	20,
	2
);

/* 6c. The React table needs a JS filter to render the extra headers/cells.
 *     Plain JS against the global wp.hooks — no build step required. */
add_action(
	'admin_enqueue_scripts',
	static function ( $hook_suffix ) {

		if ( false === strpos( (string) $hook_suffix, 'wc-admin' ) ) {
			return;
		}

		$columns = array();

		foreach ( cpc_oc_report_columns() as $key => $label ) {
			$columns[] = array(
				'key'   => $key,
				'label' => $label,
			);
		}

		$script = 'window.cpcOrderColumns = ' . wp_json_encode( $columns ) . ';
( function ( hooks, columns ) {
	if ( ! hooks || ! hooks.addFilter || ! columns ) {
		return;
	}
	hooks.addFilter( "woocommerce_admin_report_table", "curtin-pc/order-columns", function ( tableData ) {
		if ( ! tableData || "orders" !== tableData.endpoint ) {
			return tableData;
		}
		if ( ! tableData.items || ! tableData.items.data || ! tableData.headers || ! tableData.rows ) {
			return tableData;
		}
		if ( tableData.headers.some( function ( header ) { return header && header.key === columns[ 0 ].key; } ) ) {
			return tableData; // Already added.
		}
		tableData.headers = tableData.headers.concat(
			columns.map( function ( column ) {
				return { label: column.label, key: column.key, isSortable: false, isNumeric: false };
			} )
		);
		tableData.rows = tableData.rows.map( function ( row, index ) {
			var item = tableData.items.data[ index ] || {};
			return row.concat(
				columns.map( function ( column ) {
					var value = item[ column.key ] || "";
					return { display: value, value: value };
				} )
			);
		} );
		return tableData;
	} );
} )( window.wp && window.wp.hooks, window.cpcOrderColumns );';

		wp_register_script( 'cpc-order-columns-analytics', false, array( 'wp-hooks' ), CPC_OC_VERSION, false );
		wp_enqueue_script( 'cpc-order-columns-analytics' );
		wp_add_inline_script( 'cpc-order-columns-analytics', $script );
	},
	20
);
