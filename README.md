# Curtin Primary P&C Shop — WordPress theme + plugins

The custom WordPress theme powering the **Curtin Primary P&C** fundraising
store, live at **https://www.curtinprimarypandc.com.au**.

It is a child theme of **Storefront** (WooCommerce) for a small, story‑led shop that
sells greeting‑card sets ($10, set of four) drawn from the school community's 2023
"Butterfly Garden" artwork, plus **Curtin Gold** extra‑virgin olive oil. The look is a
clean white boutique (Bricolage Grotesque + DM Sans) on the school's blue + green, with
a **home → shop → category → product** structure built from polished PHP templates.

## Repository layout

```
theme/              The theme source (zipped as curtin-pc-shop/ and deployed)
  ├─ style.css               Theme header + Version (kept in sync with CPC_VERSION)
  ├─ functions.php           Setup, asset enqueues, WooCommerce hooks
  ├─ front-page.php          Home (hero + art‑cards carousel + olive teaser)
  ├─ page-shop.php           Shop landing (category tiles)
  ├─ page-art-cards.php      Art Cards category page
  ├─ page-olive-oil.php      Curtin Gold olive‑oil page
  ├─ header.php / footer.php / page.php / 404.php / template-canvas.php
  ├─ woocommerce/            Template overrides (archive-product.php, single-product.php)
  └─ assets/css|js|img       Versioned stylesheets/scripts (curtin-26x.*)
plugin/             The Order Columns plugin source (zipped as curtin-order-columns/)
  ├─ curtin-order-columns.php  Admin order-list + Analytics columns (plugin-vX.Y.Z)
  └─ readme.md                 What it adds, how pickup vs delivery is detected, changelog
docs/               Project docs (handoff brief, deployment notes, Square setup)
.gitignore
LICENSE             GPL‑3.0
```

> **The folders are `theme/` and `plugin/`, but the slugs are `curtin-pc-shop` and
> `curtin-order-columns`.** WordPress identifies the active theme by its directory name,
> so each release zip must contain a single top‑level folder with the *slug* name — build
> it by copying `theme/` (or `plugin/`) to a staging folder of that name and zipping that.
> Zipping `theme/` directly installs a second, inactive theme and leaves the site on the
> old files.

Admin-only order and reporting tweaks belong in `plugin/`, not in the theme's
`functions.php`, so they survive theme releases. Theme and plugin version independently:
never bump one to match the other.

## Versioning

The theme version lives in **two places that must stay in sync**: `Version:` in
`theme/style.css` and `CPC_VERSION` in `theme/functions.php`. Bump both
on every change so cache‑busting and "what's actually live" checks stay meaningful.

CSS/JS are shipped as **version‑renamed files** (`curtin-264.css`, `curtin-265.css`, …)
rather than relying on `?ver=` query strings, because the production nginx cache ignores
the query string. **Any change to CSS or JS means renaming the asset pair to the next
number** and updating the two `wp_enqueue_*` lines in `functions.php`. PHP‑only changes
do **not** rename assets.

The commit history reconstructs the build progression from **v2.5.3** (the original
block‑based base) through the PHP‑template rewrite **v2.6.1 → v2.6.15**.

## Releases

Every version lands as a commit **and** a GitHub Release with the zip attached.
Because theme and plugins share this repo, tags and Release titles are namespaced:

| | Tag | Release title | Commit prefix |
|---|---|---|---|
| Theme | `theme-vX.Y.Z` | `Theme vX.Y.Z` | `Theme vX.Y.Z: …` |
| Plugin (Order Columns) | `plugin-vX.Y.Z` | `Plugin vX.Y.Z` | `Plugin vX.Y.Z: …` |

Tags were renamed on 2026‑07‑29 so both lines read the same way: the theme's `vX.Y.Z`
tags became `theme-vX.Y.Z`, and the plugin's first two tags (`order-columns-v1.0.0`,
`order-columns-v1.1.0`) became `plugin-v1.0.0` / `plugin-v1.1.0`. Old tag names no
longer exist — clone URLs and Release links use the new ones. Commits made before the
rename still carry the `Order Columns vX.Y.Z:` prefix; new ones use `Plugin vX.Y.Z:`.

Per‑release notes live in the GitHub Release body, **not** in `docs/` — the old
`docs/RELEASE-*.md` files were removed from the repo and its history on 2026‑07‑29. Read
`git log` and the [Releases page](https://github.com/AdamBearWA/CurtinPrimaryPandC/releases)
for history.

## Building & deploying

The deployable artifact is a zip whose root folder is **`curtin-pc-shop/`** (built from
`theme/` — see the layout note above), installed via
**Appearance → Add Theme → Upload → "Replace installed with uploaded"**.

**Read [`docs/Theme-Deployment-Notes.md`](docs/Theme-Deployment-Notes.md) before changing
the theme or deploying.** The critical rules, learned from real production incidents:

- **Never add a `woocommerce.php` file to the theme root.** WooCommerce's template loader
  appends it after its filter hook, so it silently overrides `woocommerce/archive-product.php`
  and `woocommerce/single-product.php` — shop and product pages render as a bare
  title + list with no images, price or Add‑to‑Cart, and no error. This mistake has been
  made three times on this project.
- **Don't trust the local version number** — confirm it against Theme Details on the live
  site before building on top of it.
- **Verify every deploy** in an authenticated, cache‑busted browser (shop archive, a
  single product, and cart/checkout) — a broken template can look fine from wp‑admin.

## Shipping & delivery

Shipping is calculated in code (`functions.php` §7) because the rules can't be
expressed in native WooCommerce settings — but **since v3.0.0 every number and the
olive-oil delivery on/off switch are editable in the admin**, so seasonal changes no
longer need a theme release.

WooCommerce → Shipping still needs just **one Australia-wide zone** with a **Flat
rate** (cost 0 — the code sets the real amount) plus **Local Pickup**.

### Turning olive oil delivery on and off

**WooCommerce → Settings → Curtin P&C.**

| Setting | What it does |
|---|---|
| **Olive oil delivery** | The master switch. **Unticked = pickup only.** Ticked = local delivery is offered to the postcodes below. |
| **Olive oil delivery postcodes** | Comma-separated postcodes oil can be delivered to (default `6152`). Only used when the switch is on. |
| **Olive oil delivery suburbs** | Suburb names shown to customers. **Display only** — the real restriction is the postcode list. |
| **Olive oil delivery cost** | Charged when the cart is below the free-delivery threshold (default `5`). |
| **Free olive oil delivery from** | Bottle count at which delivery becomes free (default `2`). Set to `0` to always charge. |
| **Art cards postage** | Flat cost per order for any quantity of cards, anywhere in Australia (default `5`). |

Save and the change is live immediately — cached shipping rates are invalidated
automatically, so there is no need to clear anything or empty test carts.

**Switching the master switch OFF** does all of this at once:

- every zone delivery rate is withdrawn for any cart containing oil (only Local Pickup remains);
- checkout is hard-blocked for a shipped (non-pickup) oil order, including Apple Pay / Google Pay;
- the Olive oil page stops advertising delivery — the "Local delivery" card reads
  *"Local delivery is currently unavailable — olive oil orders are pickup only"*, and the
  "What if I can't make the collection time?" FAQ switches to a contact-us answer;
- the cart/checkout notice changes to a pickup-only message.

Art cards are never affected by the olive-oil switch — they post Australia-wide in
both modes.

> Don't reintroduce hardcoded postcodes or costs anywhere (PHP, JS, page copy). The
> settings are the single source of truth; the JS gets them via `wp_localize_script`
> as `window.cpcOilRules`, and all customer-facing wording is generated from them so
> the message can't drift from the rule.

### The rules as configured

- **Art cards** — flat **$5 per order, any quantity, anywhere in Australia**.
- **Curtin Gold olive oil, delivery ON** — **$5 for one bottle, free for two or more**;
  delivery **restricted to postcode 6152** (Como, Karawara, Manning, Salter Point,
  Waterford). Local Pickup is available anywhere.
- **Curtin Gold olive oil, delivery OFF** (the v3.0.0 shipped default) — **pickup only**.
- **Combined carts** add the two together (cards + 1 oil = $10; cards + 2 oil = $5).
- If the cart holds olive oil and it can't be delivered to the address, delivery is
  **blocked** server-side across the block checkout and Apple Pay / Google Pay
  (`woocommerce_store_api_cart_errors`) and classic checkout, with a clear notice; the
  block cart/checkout JS mirrors it (hides the Shipping row, disables Place order).

### Pickup locations

Pickup locations are managed entirely in WooCommerce (Shipping → Local pickup) —
whatever is enabled is offered to every cart. Versions before v3.0.0 filtered them by
matching the word "olive" in the location label; that broke the moment the "Olive Oil"
location was disabled (oil carts matched nothing, so *every* pickup option vanished and
the oil became unbuyable). Don't reintroduce label matching — if oil ever needs its own
collection point, express it as a real WooCommerce shipping zone.

## Hosting

Self‑hosted on an Unraid server via Docker: **WordPress + MariaDB + SWAG** (reverse
proxy / Let's Encrypt SSL) — not shared/managed hosting. Payments run through
**WooCommerce Square**; see [`docs/Square-Setup-Guide.md`](docs/Square-Setup-Guide.md).

## License

GPL‑3.0 — see [`LICENSE`](LICENSE). Storefront and WooCommerce are also GPL.
