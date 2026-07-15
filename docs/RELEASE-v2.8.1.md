# Release v2.8.1 — New "Donate" page ("Help Good Ideas Grow")

On top of v2.7.6. **PHP-only change** — no CSS/JS edits, so the `curtin-2620.*` asset pair is
**unchanged** (not renamed) per Deployment-Notes §7/§8. Supersedes the interim v2.8.0 (same page,
plus the section-width fix below); only v2.8.1 should be released.

## What this adds
A new bespoke section page, **`page-donate.php`** (slug `donate`), built entirely from existing
`cpc-` classes so it needs no new CSS:

- **Hero** — eyebrow "Support our P&C", H1 "Help Good Ideas Grow", the appeal copy, a **Donate Now**
  button (`.cpc-cta`) + a "See what you help grow" jump link, and a placeholder seedling graphic in
  the standard `.cpc-hero-art-frame` (the P&C will supply a photo).
- **Breakout** — navy `.cpc-story` band: *"Every donation plants the seed for our next community project."*
- **The Ideas You Help Grow** — intro + a 6-item `.cpc-trust` grid + closing copy.
- **Every Contribution Matters** — closing navy `.cpc-story` band with the thank-you copy.

**Donate Now target:** a WooCommerce product with slug **`donation`** (payment via the existing Square
checkout); falls back to `/shop/` until that product exists.

Also: `header.php` adds `'donate'` to `$cpc_is_flow`; `functions.php` `CPC_VERSION` and `style.css`
`Version:` are **2.8.1**.

## The v2.8.0 → v2.8.1 fix (section width / alignment)
In v2.8.0 the "The Ideas You Help Grow" section used `class="cpc-container cpc-page-content"`, which has
**no horizontal gutter**, so its heading and paragraphs spanned the full 1200px content box — 48px wider
on each side than the hero and the navy bands (which sit at the 1104px content width). On desktop this
read as sections not lining up.

Fix: the Ideas section is now `class="cpc-container cpc-collection cpc-page-content"`. `.cpc-collection`
supplies the standard `var(--gutter)` (48px) horizontal padding and is already in the `.cpc-flow`
vertical-padding-reset list, so its content insets to the same **1104px** width as every other section —
matching the proven `.cpc-collection` / `.cpc-olive-delivery` sections on the shop and olive-oil pages.
No CSS was added; it's a one-class change in the template.

## Build verification
- `php -l` clean on all 13 PHP files — pre-zip **and** after re-extracting the delivered zip.
- Text files NUL-clean; no root `woocommerce.php`; single `curtin-2620.*` pair (unchanged).
- `CPC_VERSION` 2.8.1 == `style.css` Version 2.8.1.
- Built on native fs from the known-good **v2.7.6 zip** base (Deployment-Notes §2/§9/§10).

Deliverable (project folder): `curtin-pc-shop-v2.8.1.zip`. Preview: `donate-preview.html`
(open it directly in a browser to review the desktop layout).

## Git sync + release (Windows — git works in your clone)
```powershell
cd "$env:USERPROFILE\Claude\Projects\Curtin Square Site\CurtinPrimaryPandC"

# 1. Replace the theme folder with the verified v2.8.1 contents.
Remove-Item -Recurse -Force curtin-pc-shop
Expand-Archive "..\curtin-pc-shop-v2.8.1.zip" -DestinationPath .
Copy-Item "..\RELEASE-v2.8.1.md" "docs\" -Force -ErrorAction SilentlyContinue

# 2. Commit as yourself (no Claude co-author), tag, push.
git status
git add -A
git commit -m "Theme v2.8.1: add Donate page (page-donate.php); align Ideas section to content width; wire 'donate' into header flow"
git tag v2.8.1
git push origin main --tags

# 3. GitHub Release (title MUST equal the tag).
gh release create v2.8.1 "..\curtin-pc-shop-v2.8.1.zip" --title "v2.8.1" `
  --notes "New Donate page 'Help Good Ideas Grow' (page-donate.php, slug 'donate'), built from existing cpc- classes. Ideas section aligned to the 1104px content width (was full-width in 2.8.0). Donate Now -> 'donation' product (falls back to /shop/). Header flow wired for 'donate'. PHP-only; assets unchanged."
```

> If you already committed/released v2.8.0, this is just the next tag on top. If you hadn't yet,
> release v2.8.1 directly — it contains everything v2.8.0 did plus the alignment fix.

## Deploy
Confirm the live theme's current version via Appearance → Themes → Theme Details (Deployment-Notes
§1/§6), then **Appearance → Add Theme → Upload → Replace installed with uploaded** (§3). Verify in an
authenticated, cache-busted browser (§4).

## The draft page (already created)
A **Donate** page (slug `donate`, ID 189) already exists as a **Draft** on the live site. After deploying
v2.8.1, open it via **Preview** to review. Create the **`donation`** WooCommerce product so the button
has a target. When happy, **Publish** and add it to the primary menu between **Art Cards** and **Contact**.

## Must-test (authenticated, cache-busted — Deployment-Notes §4)
- Existing pages unaffected: shop archive, single product, cart/checkout render as before.
- `/donate/` (draft preview): **all four sections share the same left/right edges** at desktop width —
  hero, navy breakout, Ideas heading/paragraphs/grid, and the closing band all line up.
- Donate Now → the `donation` product (or `/shop/` until it's made).
- Mobile: hero stacks, the 6 ideas reflow to one column, nothing overflows.
