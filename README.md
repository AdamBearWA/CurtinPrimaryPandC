# Curtin Primary School P&C Shop — `curtin-pc-shop` theme

The custom WordPress theme powering the **Curtin Primary School P&C** fundraising
store, live at **https://store.curtinprimarypandc.com.au**.

It is a child theme of **Storefront** (WooCommerce) for a small, story‑led shop that
sells greeting‑card sets ($10, set of four) drawn from the school community's 2023
"Butterfly Garden" artwork, plus **Curtin Gold** extra‑virgin olive oil. The look is a
clean white boutique (Bricolage Grotesque + DM Sans) on the school's blue + green, with
a **home → shop → category → product** structure built from polished PHP templates.

## Repository layout

```
curtin-pc-shop/     The theme itself (this is what gets zipped and deployed)
  ├─ style.css               Theme header + Version (kept in sync with CPC_VERSION)
  ├─ functions.php           Setup, asset enqueues, WooCommerce hooks
  ├─ front-page.php          Home (hero + art‑cards carousel + olive teaser)
  ├─ page-shop.php           Shop landing (category tiles)
  ├─ page-art-cards.php      Art Cards category page
  ├─ page-olive-oil.php      Curtin Gold olive‑oil page
  ├─ header.php / footer.php / page.php / 404.php / template-canvas.php
  ├─ woocommerce/            Template overrides (archive-product.php, single-product.php)
  └─ assets/css|js|img       Versioned stylesheets/scripts (curtin-26x.*)
docs/               Project docs (handoff brief, deployment notes, Square setup)
.gitignore
LICENSE             GPL‑3.0
```

## Versioning

The theme version lives in **two places that must stay in sync**: `Version:` in
`curtin-pc-shop/style.css` and `CPC_VERSION` in `curtin-pc-shop/functions.php`. Bump both
on every change so cache‑busting and "what's actually live" checks stay meaningful.

CSS/JS are shipped as **version‑renamed files** (`curtin-264.css`, `curtin-265.css`, …)
rather than relying on `?ver=` query strings, because the production nginx cache ignores
the query string. **Any change to CSS or JS means renaming the asset pair to the next
number** and updating the two `wp_enqueue_*` lines in `functions.php`. PHP‑only changes
do **not** rename assets.

The commit history reconstructs the build progression from **v2.5.3** (the original
block‑based base) through the PHP‑template rewrite **v2.6.1 → v2.6.15**.

## Building & deploying

The deployable artifact is a zip of the `curtin-pc-shop/` folder, installed via
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

## Hosting

Self‑hosted on an Unraid server via Docker: **WordPress + MariaDB + SWAG** (reverse
proxy / Let's Encrypt SSL) — not shared/managed hosting. Payments run through
**WooCommerce Square**; see [`docs/Square-Setup-Guide.md`](docs/Square-Setup-Guide.md).

## License

GPL‑3.0 — see [`LICENSE`](LICENSE). Storefront and WooCommerce are also GPL.
