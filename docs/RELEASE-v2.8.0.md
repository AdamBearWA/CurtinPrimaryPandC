# Release v2.8.0 — New "Donate" page ("Help Good Ideas Grow")

On top of v2.7.6. **PHP-only change** — no CSS/JS edits, so the `curtin-2620.*` asset pair is
**unchanged** (not renamed) per Deployment-Notes §7/§8.

## Change
Adds a new bespoke section page, **`page-donate.php`** (slug `donate`), built entirely from the
existing `cpc-` classes so it needs no new CSS:

- **Hero** — eyebrow "Support our P&C", H1 "Help Good Ideas Grow", the appeal copy, and a
  **Donate Now** button (`.cpc-cta`) + a "See what you help grow" jump link. Right side is a
  placeholder seedling graphic in the standard `.cpc-hero-art-frame` (the P&C will supply a photo).
- **Breakout** — navy `.cpc-story` band with the standout line *"Every donation plants the seed
  for our next community project."* (reuses the navy story-band style, as agreed).
- **The Ideas You Help Grow** — intro + a 6-item `.cpc-trust` grid (whole-school art, music/drumming,
  olive harvests, bush clean-ups, Stay & Play, "the next great idea") + closing copy.
- **Every Contribution Matters** — closing navy `.cpc-story` band with the thank-you copy.

**Donate Now target:** links to a WooCommerce product with slug **`donation`** (so payment runs
through the existing Square checkout). If that product doesn't exist yet, the button **falls back to
`/shop/`** so it is never a dead link — see "After deploy" below.

Also touched:
- `header.php` — added `'donate'` to the `$cpc_is_flow` page-slug array so the page gets the site's
  standard vertical rhythm (`.cpc-flow`). One-line change; affects only a page with slug `donate`.
- `functions.php` `CPC_VERSION` and `style.css` `Version:` bumped **2.7.6 → 2.8.0**.

Nothing else changed. No root `woocommerce.php`. This is additive — it introduces a new template
for a slug that doesn't exist on the live site yet, so it cannot affect any current page.

## Build verification
- `php -l` clean on all **13** PHP files — pre-zip **and** after re-extracting the delivered zip.
- NUL-clean (text files); no root `woocommerce.php`; single `curtin-2620.*` pair (unchanged).
- `CPC_VERSION` 2.8.0 == `style.css` Version 2.8.0.
- Built on native fs from the known-good **v2.7.6 zip** as the trusted base (Deployment-Notes §2/§9/§10).

Deliverable (project folder): `curtin-pc-shop-v2.8.0.zip` (unzip -t verified after copy).
Preview (project folder): `donate-preview.html` — static mock-up rendered with the real theme CSS.

## Review first (nothing is live yet)
Open **`donate-preview.html`** to see the page in the site chrome. This is a static mock-up, not the
WordPress page. Deploy + draft-page creation below is only for when you're happy with it.

## Git sync + release (Windows — git works in your clone)
> The Cowork mount's `.git` is unreadable, so no `.bundle` this session. Rebuild the clone's theme
> folder from the verified zip and commit from your Windows clone.

```powershell
cd "$env:USERPROFILE\Claude\Projects\Curtin Square Site\CurtinPrimaryPandC"

# 1. Replace the theme folder with the verified v2.8.0 contents (adds page-donate.php,
#    updates header.php + functions.php + style.css; assets unchanged).
Remove-Item -Recurse -Force curtin-pc-shop
Expand-Archive "..\curtin-pc-shop-v2.8.0.zip" -DestinationPath .

# (optional) keep the repo's docs copy of the release note in sync
Copy-Item "..\RELEASE-v2.8.0.md" "docs\" -Force -ErrorAction SilentlyContinue

# 2. Sanity check, then commit as yourself (no Claude co-author), tag, push.
git status
git add -A
git commit -m "Theme v2.8.0: add Donate page (page-donate.php); wire 'donate' into header flow"
git tag v2.8.0
git push origin main --tags

# 3. GitHub Release (title MUST equal the tag).
gh release create v2.8.0 "..\curtin-pc-shop-v2.8.0.zip" --title "v2.8.0" `
  --notes "New Donate page 'Help Good Ideas Grow' (page-donate.php, slug 'donate'), built from existing cpc- classes. Donate Now -> 'donation' product (falls back to /shop/). Header flow wired for 'donate'. PHP-only; assets unchanged."
```

## Deploy
Before uploading, confirm the live theme is currently **v2.7.6** via Appearance → Themes → Theme
Details (Deployment-Notes §1/§6). Then **Appearance → Add Theme → Upload → Replace installed with
uploaded** (Deployment-Notes §3). Verify in an authenticated, cache-busted browser (§4).

## After deploy — create the DRAFT page (so you can review it before it's public)
1. **Pages → Add New** → title **Donate**. Confirm the permalink slug is **`donate`** (it must match
   for `page-donate.php` to apply). Leave the content body empty — the template renders everything.
2. **Save as Draft** (do **not** Publish yet). Use **Preview** to view it live in the theme.
3. **Donate Now button:** create a WooCommerce **product** with slug **`donation`**
   (Products → Add New → set the URL slug to `donation`) — e.g. a "Donation" product, so the button
   sends people through the normal Square checkout. Until that product exists the button points at
   `/shop/`. (If you'd rather the button link somewhere else — an external Square page, etc. — say so
   and I'll switch it.)

## When you're happy — add it to the menu (not done yet, per your request)
**Appearance → Menus** → add the **Donate** page to the primary menu, positioned **between Art Cards
and Contact** (as Judith suggested), then **Publish** the page.

## Must-test (authenticated, cache-busted — Deployment-Notes §4)
- Existing pages unaffected: shop archive, a single product, cart/checkout all render as before
  (this release adds a template for a new slug; it must not change anything else).
- `/donate/` (draft preview) renders: hero + Donate Now, navy breakout line, the 6-idea grid, and the
  closing band; layout spacing matches the other bespoke pages.
- **Donate Now** goes to the `donation` product page (or `/shop/` if that product isn't made yet).
- Mobile: hero stacks, the 6 ideas reflow to one/two columns, nothing overflows.

## Cleanup note
The stray `curtin-pc-shop-v2.7.7.zip` from an interim version number was removed; the only delivered
zip is `curtin-pc-shop-v2.8.0.zip`.
