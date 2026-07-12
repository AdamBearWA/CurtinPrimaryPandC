# Theme Deployment Notes — curtin-pc-shop

Read this before making any change to the `curtin-pc-shop` theme or deploying a new zip. These are hard-won lessons from real production incidents, not style preferences.

## 1. Never add a `woocommerce.php` file to the theme root

This is an architectural constraint, not a choice. WooCommerce's template loader (`WC_Template_Loader::get_template_loader_files()`) unconditionally appends `woocommerce.php` to its template search list, and does so *after* its own filter hook runs — so no filter can remove it once the file exists. If present, it silently wins over `woocommerce/archive-product.php` and `woocommerce/single-product.php`, rendering the shop archive and single product pages as a bare title + description + bullet list (no images, no price, no Add to Cart button), with no error thrown. Page caching can hide this for weeks.

Cart, Checkout, and My Account don't need a dedicated template — they are plain WordPress Pages that WooCommerce's template loader never touches, so `page.php` renders them correctly on its own.

This exact mistake has been made three times on this project — most recently on 2026-07-11, when it was reintroduced locally (bumped to v2.4.4) without reaching production. If you're about to add a `woocommerce.php` file back, stop and re-read this section.

**Before trusting the local project folder's theme files, confirm their version number actually matches what Theme Details shows on the live site.** Don't assume the local copy in this folder is current just because it's the working source — another session may have changed it without deploying, or deployed and been reverted. Check Appearance → Themes → Theme Details on the live site for the true `CPC_VERSION` before building on top of local files.

## 2. When rebuilding the theme zip mid-session, always use a brand-new output folder

The build sandbox's file view can go stale on a path that was written to earlier in the same session — content can appear old or truncated when zipping, even though the actual file is correct. Increment the build folder name every rebuild (`build-241` → `build-242` → `build-243`...) rather than overwriting one used earlier.

## 3. Deploy via Appearance → Add Theme → Upload, try "Replace installed with uploaded" first

Only fall back to delete-then-fresh-install if that fails with a permissions error. The delete/reinstall path briefly leaves the site with no active theme, which is real outage risk — treat it as a last resort, not the default.

## 4. Verify every deploy in an authenticated, cache-busted browser session

Not a generic web fetch — those returned stale/cached content during past deploys. Check at minimum: the shop archive, a single product page, and cart/checkout. A broken template can look completely fine from wp-admin while the public pages are broken.

## 5. If the site goes down, get real PHP error logs before guessing

The site is self-hosted on Unraid via Docker (SWAG reverse proxy) — not VentraIP or other shared hosting. Pull logs via `docker logs` on the WordPress container rather than relying on WordPress's generic "critical error" message, which hides the actual cause.

## 6. Bump versions on every change, and record each release in Git + GitHub

`CPC_VERSION` in `functions.php` and `Version:` in `style.css` must match and increment on every deploy, so cache-busting and "what's actually live" checks stay meaningful. If CSS/JS changed, also rename the asset pair to the next `curtin-26x.*` and update both enqueues (see §8).

The per-version changelog that used to live here has been removed — **the authoritative history is now the Git commit log and GitHub Releases** at github.com/AdamBearWA/CurtinPrimaryPandC. Don't reconstruct a changelog in this file; read `git log` / the Releases page instead.

Every new version must land in Git and as a GitHub Release, not just as a zip handed to Adam. Follow the full "Release checklist" at the bottom of this file. The two things that matter most for traceability:

- **Commit message**: `Theme vX.Y.Z: <one-line summary of the change>`, committed as Adam with no Claude co-author trailers (identity `Adam Niedzwiedz <AdamBearWA@users.noreply.github.com>`). The one-line summary is what replaces the old changelog, so make it specific (what changed and where), e.g. `Theme v2.6.17: remove top hero from Art cards page (page-art-cards.php)`.
- **Release notes**: publish with `gh release create vX.Y.Z` and attach the built zip; the `--notes` text should describe the change and call out whether assets were cache-bust renamed and whether it's been verified live yet.

**Never assume the source folder matches production.** Confirm the true `CPC_VERSION` via Appearance → Themes → Theme Details on the live site (see §1) before building on top of local files — a version may have been built and handed off but not yet uploaded, or uploaded and reverted.


## Release checklist — run for EVERY new version (Git + GitHub Releases)

The theme is version-controlled at **github.com/AdamBearWA/CurtinPrimaryPandC** (branch `main`);
Adam's clone is at `...\Curtin Square Site\CurtinPrimaryPandC`. **From v2.6.15 on, every release
must also land in Git and as a GitHub Release — not just as a zip.** Do all of this for each new version:

1. **Bump the version** — `CPC_VERSION` in `functions.php` and `Version:` in `style.css` must match
   and increment (see §6). If CSS/JS changed, rename the asset pair to the next `curtin-26x.*`
   and update both enqueues (see §8).
2. **Build the zip** of `curtin-pc-shop/` on a fresh output folder / native fs, NUL-scan, confirm
   there is no root `woocommerce.php` (see §1, §2, §9).
3. **Sync source into the repo** — copy the updated `curtin-pc-shop/` over the clone's
   `curtin-pc-shop/`. Keep the repo's `docs/` copies in sync with the project-folder docs when they change.
4. **Commit as Adam, no Claude co-author trailers** (identity
   `Adam Niedzwiedz <AdamBearWA@users.noreply.github.com>`):
   `git add -A && git commit -m "Theme vX.Y.Z: <one-line summary>"`.
5. **Push**: `git push origin main`.
6. **Publish the GitHub Release** with the zip attached (tag `vX.Y.Z`, at the commit just pushed):
   `gh release create vX.Y.Z "curtin-pc-shop-vX.Y.Z.zip" --title "curtin-pc-shop vX.Y.Z" --notes "<summary>"`
   (`gh auth login` once first). `create-github-releases.ps1` in the project folder did the historical
   backfill (v2.5.3 -> v2.6.15) and is the pattern to copy for a single new release.
7. **Then deploy** to the live site and verify in an authenticated, cache-busted browser (see §3-§4).

Reminder: the Cowork/Linux sandbox corrupts Git on the mounted project folder — do any Git work on
native fs (e.g. `/tmp`) and hand off via a `git bundle`; Adam pushes from his Windows clone.
