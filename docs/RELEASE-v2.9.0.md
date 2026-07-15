# Release v2.9.0 — Harvest-photo carousels (Olive oil + Donate)

On top of v2.8.1. **CSS/JS + PHP change** — the asset pair was **renamed `curtin-2620.* → curtin-2621.*`**
(Deployment-Notes §8) and the old pair deleted (§7). `CPC_VERSION` and `style.css` `Version:` are **2.9.0**.

## What this adds
An auto-rotating, swipeable **photo carousel that renders whatever images you attach to a Page** in the
Media Library — no hardcoded URLs, no redeploy to change photos. It reuses the theme's existing
`[data-cpc-carousel]` engine (the same one behind the front-page hero), so behaviour is proven:
auto-advance (5s), touch-swipe, mouse-drag, prev/next arrows, dots, pause-on-hover, pause when the tab
is hidden, and `prefers-reduced-motion` support. Images use WordPress's responsive `srcset` sizes.

- **Olive oil page** — the "Shop Curtin Gold" block is now a two-column **showcase**: the product card on
  the left, the harvest-photo carousel filling the white space on the right. On mobile it stacks (photos
  below the product). If the page has no attached images it falls back to the original product grid.
- **Donate page** — the hero's seedling **SVG placeholder is replaced by the carousel** when photos are
  attached to that page; until then it keeps showing the seedling placeholder (never a blank frame).

### New code
- `functions.php`: `cpc_page_photo_ids()` + `cpc_photo_carousel()` (helpers; render attached page images
  into the existing `.cpc-slide` / `.cpc-dot` markup). `CPC_VERSION` → 2.9.0; enqueues → `curtin-2621.*`.
- `page-olive-oil.php`: product-beside-photos showcase (with fall-back to the old grid).
- `page-donate.php`: hero carousel with the SVG placeholder as fallback.
- `assets/css/curtin-2621.css`: `.cpc-photo-carousel` (full-width photo variant, overlaid dots),
  `.cpc-carousel-arrow` (prev/next), `.cpc-photo-carousel--hero` (5/4 to match the hero frame), and the
  `.cpc-olive-showcase` two-column layout.
- `assets/js/curtin-2621.js`: prev/next arrow wiring inside `initCarousels` (guarded — the front-page
  hero carousel has no arrows and is unaffected).

## Build verification
- `php -l` clean on all **13** PHP files — pre-zip **and** after re-extracting the delivered zip.
- `node --check` clean on `curtin-2621.js`.
- Text files NUL-clean; **no root `woocommerce.php`**; single `curtin-2621.*` pair (old `curtin-2620.*` deleted).
- `CPC_VERSION` 2.9.0 == `style.css` Version 2.9.0.
- Built on native fs from the known-good **v2.8.1 zip** base (Deployment-Notes §2/§9/§10).

Deliverables (project folder): `curtin-pc-shop-v2.9.0.zip` and `olive-donate-carousel-preview.html`
(open the HTML in a browser to review both layouts — it uses the real theme CSS + carousel engine and a
sample of the harvest photos).

## Confirm what's live first (Deployment-Notes §1/§6)
This was built on the local v2.8.1 source. **Before deploying, confirm the live theme version** via
Appearance → Themes → Theme Details. If live is older than 2.8.1 (e.g. the Donate page wasn't deployed
yet), v2.9.0 still contains everything 2.8.1 did, so deploying it is fine — just be aware it brings the
Donate page along too.

## Git sync + release (Windows — git works in your clone)
```powershell
cd "$env:USERPROFILE\Claude\Projects\Curtin Square Site\CurtinPrimaryPandC"

# 1. Replace the theme folder with the verified v2.9.0 contents.
Remove-Item -Recurse -Force curtin-pc-shop
Expand-Archive "..\curtin-pc-shop-v2.9.0.zip" -DestinationPath .
Copy-Item "..\RELEASE-v2.9.0.md" "docs\" -Force -ErrorAction SilentlyContinue

# 2. Commit as yourself (no Claude co-author), tag, push.
git status
git add -A
git commit -m "Theme v2.9.0: attached-photo carousels on Olive oil (beside product) and Donate (hero); reuse [data-cpc-carousel] engine, add prev/next arrows; rename assets curtin-2620.*->curtin-2621.*"
git tag v2.9.0
git push origin main --tags

# 3. GitHub Release (title MUST equal the tag).
gh release create v2.9.0 "..\curtin-pc-shop-v2.9.0.zip" --title "v2.9.0" `
  --notes "Auto-rotating, swipeable harvest-photo carousels built from images ATTACHED to each Page (no hardcoded URLs). Olive oil: product-beside-photos showcase (stacks on mobile). Donate: hero carousel replacing the seedling placeholder (falls back to it until photos are attached). Reuses the existing [data-cpc-carousel] engine; adds prev/next arrows. Assets renamed curtin-2620.*->curtin-2621.* and old pair deleted; CPC_VERSION 2.9.0."
```

## Deploy
Appearance → Add Theme → Upload → **Replace installed with uploaded** (Deployment-Notes §3). Then verify
in an **authenticated, cache-busted** browser (§4).

## Attach the photos (this is what populates the carousels)
The Olive oil photos are already attached (you did this). For the **Donate** page:
1. Media Library → select each harvest/community photo → in the attachment details set **"Uploaded to"**
   to the **Donate** page (or upload them from within the Donate page editor). Order follows the images'
   menu order, then upload order.
2. Reload `/donate/` — the carousel appears in the hero. With no attachments it shows the seedling SVG.

To reorder or swap photos later on either page, just change what's attached — no new release needed.

## Must-test (authenticated, cache-busted — Deployment-Notes §4)
- **Olive oil**: product card on the left, photo carousel on the right; it auto-rotates, arrows + dots +
  swipe/drag work, and it pauses on hover. On mobile the photos stack below the product.
- **Donate** (once photos attached): hero shows the carousel at the 5/4 frame; before attaching, the
  seedling placeholder still shows.
- **Front page** hero carousel still rotates and swipes exactly as before (shared engine — regression check).
- Existing pages unaffected: shop archive, single product, cart/checkout render as before.
