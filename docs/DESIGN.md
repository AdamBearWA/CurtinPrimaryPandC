# Curtin Primary School P&C Shop — Design Guidelines

The design system for the Curtin Primary School P&C shop
(**https://www.curtinprimarypandc.com.au**). It defines the brand look, the product
model the store is built around, the design tokens (colour, type, spacing, radii,
shadows), and the anatomy and behaviour of each page. Treat it as the source of truth when
adding or changing anything visual; when in doubt, the **live store** is the final word.

## Brand & positioning

A small, story‑led fundraising shop run by the Curtin Primary School P&C. It sells greeting
cards — **sets of four blank cards, $10** — drawn from community artworks, and **Curtin
Gold** extra virgin olive oil. The look is a clean white boutique (Bricolage Grotesque + DM
Sans) on the school's **blue + green**, with the artwork and its story leading. Warm, proud,
and personal — not a generic e‑commerce catalogue. Every screen should make clear that
**100% of profits support the P&C**.

## Product model (design constraints)

These are deliberate constraints — the store should stay simple and story‑led.

- **Not a multi‑category catalogue.** No "shop by collection" tiles, no filter chips, no
  category badges, no separate "Our story" page.
- **Two ranges:** art cards and Curtin Gold olive oil.
- **Art cards** come from community artworks painted with local artist **Kelly Muller** —
  the 2023 "Butterfly Garden" and later "Sense of Wonder" (2024) projects — and the range
  has grown since. Current sets: **Butterfly Garden**, **Creatures of Colour** and **A
  Feeling of Awe**, each **four different images** drawn from the artwork.
- Cards sell as **sets of four blank cards** — **white envelopes**, **blank inside** — at
  **$10.00**. **Card size varies by design:** most are **120 × 120 mm square** (Butterfly
  Garden, Creatures of Colour), some are **105 × 150 mm** (A Feeling of Awe). Do **not**
  assume every card is square.
- **Olive oil (Curtin Gold)** — a live seasonal product: **250 mL**, **$18.00**, 100% extra
  virgin, harvested from neighbourhood olive trees in Karawara and cold pressed in York;
  limited seasonal release.
- **Pickup only — no delivery for cards.** Uses WooCommerce Local Pickup ("Free pickup").
  The olive‑oil page additionally offers **local delivery** to nearby suburbs. Don't add
  delivery to the cards flow.
- All software is free (WooCommerce, WooCommerce Square, the child theme, a free form
  plugin); only Square's per‑transaction fee applies.

## Design tokens

### Colour

| Token | Hex | Use |
|---|---|---|
| Blue | `#1d6fb8` | Primary buttons, links, active nav |
| Blue deep | `#134e7e` | Logo wordmark, prices, announcement bar |
| Blue night | `#10324f` | Story band + footer |
| Blue tint | `#eaf3fb` | Cart pill, "supports our P&C" note |
| Green | `#2f8f5b` | Eyebrows, accents |
| Green deep | `#1f6b41` | Green text, add‑to‑cart outline + hover fill |
| Green tint | `#eaf6ef` | Mint surfaces |
| Olive (solid) | `#2c4527` | Olive story / Thank‑You / mailing bands — **solid, no gradient** |
| Olive fill / hover | `#3f6135` / `#2c4527` | Olive‑oil Add‑to‑cart button (fill / hover) |
| Olive price / title‑hover | `#2c4527` / `#3f6135` | Olive‑oil **product card** price + card‑title on hover (olive overrides the default blue; scoped via `.cpc-card--olive`) |
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

How it works: background‑less sections (hero, product grid, features, FAQ, signup) have
their vertical padding zeroed, so the flow token is the *only* source of vertical space;
coloured bands (the navy story band, the trust strip, and the olive harvest / Thank‑You /
mailing bands) keep their internal padding and are separated from their neighbours by the
same flow gap. Empty scroll‑anchor `<div>`s (`#cpc-shop`, `#cpc-cards`) are excluded so they
never add a double gap.

Single‑product, cart, checkout, account and other WooCommerce/generic pages are deliberately
**not** given `.cpc-flow`; their spacing stays owned by their own templates. When adding a
new bespoke section page, wire it into `$cpc_is_flow` in `header.php` and let the flow handle
spacing — avoid re‑introducing ad‑hoc top/bottom padding or inline `margin-bottom` hacks
between sections.

## Global chrome

- **Announcement bar** — deep‑blue, centred: **"Created by our community · 100% of profits
  support our P&C"**.
- **Header** — the school bird logo (JPEG) + a "Curtin Primary School P&C" wordmark in Bricolage Grotesque; primary nav **Home · Olive
  oil · Art cards · Contact** (active item blue); blue‑tint cart pill ("Cart · N").
- **Footer** — navy. "Curtin Primary School P&C" + "An initiative of the Curtin
  Primary School Parents & Citizens Association."; **Shop** column (Art cards, Olive oil) and
  **Help** column (Contact the P&C); Whadjuk Noongar acknowledgement; "© <year> Curtin
  Primary School P&C · Built on WooCommerce · Secure payments by Square".

## Pages

### Home

Stacked bespoke sections, top to bottom:

- **Olive‑oil hero** — green eyebrow "Curtin Gold Olive Oil · Available Now!", H1 "From our
  neighbourhood's olive trees to your kitchen.", a short lede, and the **Curtin Gold product
  card** (250 mL, **$18.00**, olive‑green Add to cart). The home leads with olive oil while
  it's in season.
- **Olive harvest band** — solid deep‑olive `#2c4527` `.cpc-story` panel: H2 "A harvest
  shared by our community" + two left‑aligned columns; mirrors the olive‑oil page's top band.
- **Art‑cards hero** — green eyebrow "Our Art Cards", H2 "Cards our whole school painted
  together", a short lede, and an **auto‑rotating, swipeable carousel** of the art‑card
  product cards (square set image, name, size + "White envelopes included", price `$10.00` +
  outline "Add to cart"). All images are **pack/set shots — no single cards.**
- **Story band** — navy `#10324f` rounded panel: H2 "The story behind the cards" + two short
  paragraphs (2023 collaboration with Kelly Muller; the range has grown since).
- **Trust strip** — three items: **Made by our community** ("every product has a Curtin
  story"), **Growing community** ("we bring people together through shared experiences"),
  **Supporting what's next** ("every purchase helps the Curtin Primary P&C create projects,
  events and experiences…").
- **Mobile** — single column; hamburger · centred logo · cart; the olive hero stacks (text →
  product card); harvest band, art‑cards carousel, story band, trust strip and footer
  stacked.

### Art cards

- **Story band** — navy `#10324f` panel: H2 "The story behind the cards" + two left‑aligned
  columns.
- **Product grid** — the three card sets as product cards, **no section heading** (the story
  band above does the titling). Each card: set image, name, size + "White envelopes
  included", `$10.00`, blue outline "Add to cart".

### Single product (art card)

- Breadcrumb: **Home / Cards / <product>**.
- **Gallery**: featured image = **pack flat‑lay (all four cards)**; thumbnails = the four
  individual card designs (first thumbnail is the featured image, active with a blue
  border). Clicking a thumbnail swaps the main image.
- **Info**: H1 product name, price `$10.00`, the **artwork story** (created with Kelly Muller;
  which project and year the set is drawn from), a green‑tick feature list — **"Set of 4
  blank cards" / the card's size (e.g. 120 × 120 mm or 105 × 150 mm) / "White envelopes
  included"** — a quantity stepper −/+, **Add to cart · $10.00**, and a **Free pickup** note.
- **"You may also like"** — a related‑products row of the other card sets.

### Olive oil (Curtin Gold)

The category page for the live **Curtin Gold** product (slug `olive-oil`; 250 mL, $18.00).
The page carries the `cpc-olive-page` body class, which paints its story bands in **solid
deep olive `#2c4527`** (no gradient). Top to bottom:

- **Harvest story band** — deep‑olive `.cpc-story` panel: H2 "A harvest shared by our
  community" + two left‑aligned columns (the Karawara harvest origin; cold‑pressed in York,
  and how every purchase helps).
- **Product + collection/delivery row** — a three‑up grid with **no section title**: the
  Curtin Gold product card, then two info cards filling the otherwise‑empty columns —
  **Free collection (preferred)** and **Local delivery** (suburb pills — Karawara, Manning,
  Salter Point, Como — + $5, free over two bottles). Info cards sit at natural height
  (`align-items:start`) beside the taller product card.
- **Three features** — Harvested in Karawara / Cold‑pressed in York / Supporting our
  community.
- **Thank‑You band** — the **same** deep‑olive `.cpc-story` format (H2 + two left‑aligned
  columns), not a centred panel.
- **FAQ** — expandable list.
- **Mailing list** — deep‑olive panel with the signup form.

Olive‑oil **Add‑to‑cart buttons** use the olive palette (fill `#3f6135`, hover `#2c4527`),
not the school green. Product grids on both the olive‑oil and art‑cards pages carry **no
section heading** — the story band above each grid does the titling.

## Interactions

Links go blue on hover. Cards lift `translateY(-4px)` + shadow. The outline add‑to‑cart
fills green on hover. Buttons brighten ~6%. Product thumbnails swap the main image (active =
blue border). Quantity stepper −/+. The home art‑cards carousel auto‑rotates, pauses on
hover and when the tab is hidden, honours `prefers‑reduced‑motion`, and supports
touch‑swipe / mouse‑drag.

## Imagery

- **Pack / set shots** (all four cards) are used for the hero, the art‑cards carousel and
  each product's featured image. Every set needs its own flat‑lay.
- **Individual card designs** are used **only** as product‑page gallery thumbnails — never as
  a hero or listing image.
- **Card sizes vary** 