# Curtin Primary School P&C Shop — Design Guidelines

The design system for the Curtin Primary School P&C shop
(**https://store.curtinprimarypandc.com.au**). It defines the brand look, the product
model the store is built around, the design tokens (colour, type, spacing, radii,
shadows), and the anatomy and behaviour of each page. Treat it as the source of truth when
adding or changing anything visual.

## Brand & positioning

A small, story‑led fundraising shop. It sells greeting cards — **sets of four, $10** —
drawn from a community artwork, and teases a "coming soon" school olive oil. The look is a
clean white boutique (Bricolage Grotesque + DM Sans) on the school's **blue + green**, with
the artwork and its story leading. Warm, proud, and personal — not a generic e‑commerce
catalogue. Every screen should make clear that **100% of profits support the school**.

## Product model (design constraints)

These are deliberate constraints — the store should stay simple and story‑led.

- **Not a multi‑category catalogue.** No "shop by collection" tiles, no filter chips, no
  category badges, no separate "Our story" page.
- Everything derives from one community artwork: the **2023 "Butterfly Garden"**, painted by
  the whole school community with artist **Kelly Muller**. Cards are images from it; the
  range has grown since.
- Cards sell as **sets of four** — **120 × 120 mm square**, **white envelopes**, **blank
  inside** — at **$10.00**.
- **Pickup only — no delivery.** Uses WooCommerce Local Pickup; never mention delivery.
- Olive oil (**Curtin Gold**) is a coming‑soon page with email capture, not a live product.
- All software is free (WooCommerce, WooCommerce Square, the child theme, a free form
  plugin); only Square's per‑transaction fee applies.

## Design tokens

### Colour

| Token | Hex | Use |
|---|---|---|
| Blue | `#1d6fb8` | Primary buttons, links, active nav |
| Blue deep | `#134e7e` | Logo wordmark, prices, announcement bar |
| Blue night | `#10324f` | Story band + footer |
| Blue tint | `#eaf3fb` | Cart pill, "supports our school" note |
| Green | `#2f8f5b` | Eyebrows, accents |
| Green deep | `#1f6b41` | Green text, add‑to‑cart outline + hover fill |
| Green tint | `#eaf6ef` | Mint surfaces |
| Olive gradient | `#2c4527 → #3f6135 → #52723d` | Olive hero/teaser |
| Ink / Body / Muted | `#1a2026` / `#54606a` / `#8a8378` | Headings / copy / meta |
| Surface / Line / Soft | `#f4f6f8` / `#eef0f2` / `#fbfcfe` | Image backing / borders / soft panels |

### Type

- Headings **Bricolage Grotesque** 500–800. Hero H1 ~50px desktop / ~32px mobile; section
  H2 ~30px; letter‑spacing −.01 to −.02em.
- Body **DM Sans** 400–700. Body 15–17px; eyebrows 11–12px uppercase, tracked ~.14–.16em.

### Radius, shadow, layout

Pills `999px` · cards `12px` · images `14px` · bands `16px`. Card hover
`translateY(-4px)` + `0 18px 40px rgba(35,42,49,.14)`. CTA shadow
`0 10px 22px rgba(29,111,184,.28)`. Content max‑width `1200px`, centred. Horizontal gutter
(`--gutter`) `48px` desktop / `22px` mobile.

### Vertical rhythm

On the bespoke stacked‑section pages (Home, Shop, Art cards, Olive oil) the vertical spacing
between header and footer is driven by one token, **`--cpc-flow`** (`40px` desktop / `28px`
mobile), not by per‑section padding. The `<main>` wrapper carries a **`.cpc-flow`** class —
added in `header.php` for those pages only — which applies `--cpc-flow` identically as: the
gap **below the header**, the gap **between every top‑level component**, and the gap **above
the footer**. So the same single gap appears everywhere, with no section sitting flush
against the header.

How it works: background‑less sections (hero, product grid, features, delivery, FAQ, signup)
have their vertical padding zeroed, so the flow token is the *only* source of vertical space;
coloured bands (story, trust, olive hero/thanks) keep their internal padding and are
separated from their neighbours by the same flow gap. Empty scroll‑anchor `<div>`s
(`#cpc-shop`, `#cpc-cards`) are excluded so they never add a double gap.

Single‑product, cart, checkout, account and other WooCommerce/generic pages are deliberately
**not** given `.cpc-flow`; their spacing stays owned by their own templates. When adding a
new bespoke section page, wire it into `$cpc_is_flow` in `header.php` and let the flow handle
spacing — avoid re‑introducing ad‑hoc top/bottom padding or inline `margin-bottom` hacks
between sections.

## Pages

### Home

- **Announcement bar** — deep‑blue, centred: "100% of profits support our school · Free
  pickup from the front office". (No delivery.)
- **Header** — bird mark + "Curtin Primary School" / "P&C Shop"; nav **Home · Art Cards ·
  Olive oil** (active item blue); blue‑tint cart pill.
- **Hero (story‑led, sells inline)** — left: green eyebrow "The Butterfly Garden · painted
  2023", H1 "Cards our whole school painted together", short paragraph, then **price $10.00
  + "Set of four · 120 × 120 mm · white envelopes · blank inside"**, an **Add a set to
  cart** button + "Read the story" link, and a green "100% of profits fund our school"
  line. Right: the artwork image with a white credit chip. **Hero image is always the
  artwork/pack — never a single card.**
- **Story band** — navy `#10324f` rounded panel: H2 "From a school‑hall canvas to your
  letterbox" + two short paragraphs (2023 collaboration with Kelly Muller; the range has
  grown since).
- **Art cards** — an auto‑rotating, swipeable carousel of the art‑card product cards (square
  set image, name, "Set of four · blank inside", price `$10.00` + outline "Add to cart").
  All images are pack/set shots — **no single cards**.
- **Trust strip** — three items: "100% funds our school", "Easy office pickup", "One‑of‑a‑
  kind artwork".
- **Olive teaser** — slim olive‑gradient band, "Coming soon", "Register interest".
- **Footer** — navy. Bird mark + blurb; Shop / Help columns (Help = Contact the P&C, Pickup
  from the office); Whadjuk Noongar acknowledgement; "© Curtin Primary School P&C ·
  WooCommerce · Square".
- **Mobile** — single column; hamburger · centred logo · cart; hero stacks (text → artwork →
  price → Add); story band, carousel, trust strip, olive teaser and footer stacked.

### Single product

- Breadcrumb: Home / Art Cards / <product>.
- **Gallery**: featured image = **pack flat‑lay (all four cards)**; thumbnails = the four
  individual card designs (first thumbnail is the featured image, active with a blue
  border). Clicking a thumbnail swaps the main image.
- **Info**: H1 product name, price `$10.00` + "Set of four cards · $2.50 each",
  description (120 × 120 mm, white envelopes, blank inside, set of four), the **artwork
  story** (muted paragraph), a green‑tick feature list (Set of four · 120 × 120 mm square /
  White envelopes included / Blank inside), quantity stepper + **Add to cart · $10.00**, a
  blue‑tint "supports our school" note, and **Pickup** + **Product details** rows. **No
  related‑products row.**

### Olive oil (Curtin Gold)

Standalone page, not a product. Green hero with email capture ("Notify me"); three
features — **Grown in Perth** / Cold‑pressed locally / Funds our school; a "What to expect"
band with 250 ml / 500 ml bottle cards (price TBC).

## Interactions

Links go blue on hover. Cards lift `translateY(-4px)` + shadow. The outline add‑to‑cart
fills green on hover. Buttons brighten ~6%. Product thumbnails swap the main image (active =
blue border). Quantity stepper −/+. Pickup / Product‑details rows expand. The home art‑cards
carousel auto‑rotates, pauses on hover and when the tab is hidden, honours
`prefers‑reduced‑motion`, and supports touch‑swipe / mouse‑drag.

## Imagery

- **Pack / set shots** (all four cards) are used for the hero, the art‑cards carousel and
  each product's featured image. Every set needs its own flat‑lay.
- **Individual card designs** are used **only** as product‑page gallery thumbnails — never as
  a hero or listing image.
- Keep aspect ratios; the P&C supplies final photography. Squares stay square.
