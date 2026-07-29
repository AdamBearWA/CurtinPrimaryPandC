# Curtin P&C Order Columns

A companion plugin for the Curtin Primary P&C shop. It surfaces the information
needed to actually fulfil an order — who to contact, where it's going, and
whether it's being collected or delivered — in both admin order views and in the
Analytics CSV download.

Kept as a **plugin, not part of the theme**, so it has its own version line and
survives theme releases: tag `order-columns-vX.Y.Z`, Release title
`Order Columns vX.Y.Z`, commit prefix `Order Columns vX.Y.Z:`.

> **The folder is `plugin/`, but the plugin slug is `curtin-order-columns`.**
> Like `theme/`, don't zip this folder directly — copy it to a staging folder
> named **`curtin-order-columns/`** and zip that, so the archive has a single
> correctly-named top-level folder.

## What it adds

**WooCommerce → Orders** (works with HPOS and the legacy posts table)

| Column | Notes |
| --- | --- |
| Fulfilment | Pickup / Delivery pill, plus the shipping method name and (for pickup) the location address |
| Email | `billing_email`, as a `mailto:` link |
| Phone | `billing_phone`, falling back to `shipping_phone`, as a `tel:` link |
| Ship to | Shipping address, falling back to the billing address (labelled) |

Plus an **All fulfilment / Pickup / Delivery / No shipping** filter dropdown
next to the status filters.

**Analytics → Orders** — the same four columns in the table, in the ⋮ show/hide
columns menu, and in the **Download** CSV (both the in-browser CSV for a single
page of results and the server-generated CSV WooCommerce emails for larger
result sets).

## How pickup vs delivery is decided

Read live from the order's shipping line, matching the theme's shipping rules
(`theme/functions.php` §7). No custom checkout field is involved — the old
"Collection or delivery?" field was removed in favour of the native toggle.

| Shipping line method ID | Result |
| --- | --- |
| `pickup_location` (block Local pickup) | **Pickup** — label names the location (Olive Oil vs Curtin Primary School) |
| `local_pickup` (legacy zone method) | **Pickup** |
| `flat_rate`, or any other rate | **Delivery** |
| no shipping line | **No shipping** (donations, virtual items) |

The type is also cached to `_cpc_fulfilment` order meta so the Orders list can
be filtered by it. Existing orders are backfilled on activation; new orders are
written from both the classic and block checkout (including express wallets),
and on manual admin edits.

## Install

Plugins → Add New → Upload Plugin → the `curtin-order-columns-vX.Y.Z` zip →
Activate. Activation runs the backfill over existing orders.

## Changelog

### 1.1.0

- Analytics → Orders: added Fulfilment, Email, Phone and Ship to columns to the
  table and to the CSV Download. Fed via
  `woocommerce_rest_prepare_report_orders` (table + in-browser CSV),
  `woocommerce_report_orders_export_columns` /
  `woocommerce_report_orders_prepare_export_item` (server-side CSV) and a plain
  `wp.hooks` `woocommerce_admin_report_table` JS filter (no build step).

### 1.0.0

- Initial release: Fulfilment / Email / Phone / Ship to columns and a
  pickup-delivery filter on the WooCommerce → Orders list; `_cpc_fulfilment`
  meta with activation backfill; HPOS compatibility declared.
