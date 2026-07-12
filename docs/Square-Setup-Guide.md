# Connecting Square payments — Curtin P&C Shop

This is the step-by-step to switch on **Square** card payments. Most of it can only be
done on the **live VentraIP site over HTTPS** (Square won't connect to the local test box
at `192.168.21.169`, and it needs your real Square login). The **WooCommerce Square**
plugin is already installed on the site (currently inactive on the test box; it travels
to VentraIP in the UpdraftPlus migration archive).

> I couldn't read your Square account settings directly (the Square Dashboard is blocked
> for the assistant's browser tool), so a few spots below are marked **[confirm]** — fill
> those in from your Square account, or send them to me and I'll finalise this guide.

---

## 0. Your account details (fill these in)
- Square account login (email): **[confirm]**
- Business/location name as it appears in Square → **Account & Settings → Business →
  Locations**: **[confirm]**
- Country / currency: should be **Australia / AUD** to match the store. **[confirm]**
- Do you want **Apple Pay / Google Pay** enabled at checkout as well as cards? **[confirm]**
- Is there an existing **Square POS / item catalogue** to sync, or is the website the only
  sales channel? (Decides inventory "system of record".) **[confirm]**

---

## 1. Prerequisites (on VentraIP, before connecting)
1. Site is live on the real domain over **HTTPS** (Let's Encrypt enabled, Site URL uses
   `https://`). Square will refuse to connect otherwise.
2. WooCommerce store set to **Australia / Perth WA**, currency **AUD**.
3. **WooCommerce Square** plugin present (it is) and **activated** (step 2).
4. You're logged into WordPress admin, and you have the **P&C's Square account** login.

## 2. Activate the plugin
- WordPress admin → **Plugins** → find **WooCommerce Square** → **Activate**.
  (Leave it deactivated on the local test box — it can't connect there and just shows
  "connect" nags.)

## 3. Connect your Square account
1. Go to **WooCommerce → Settings → Square** (or the **Square** item that appears in the
   admin menu / the prompt under Plugins).
2. Set **Environment** to **Sandbox** first (for testing) — see step 4. You can switch to
   Production later in step 6.
3. Click **Connect with Square**. You'll be sent to Square to log in.
4. Log into the **P&C Square account** and **authorise** the connection. *(You do this part
   — it needs your Square credentials, which the assistant can't enter.)*
5. Back in WooCommerce, choose the **business location** (**[confirm]** which one) that
   sales should post to.

## 4. Sandbox test (prove it works before taking real money)
1. With Environment = **Sandbox** and connected to your Square **sandbox** test account,
   go to **WooCommerce → Settings → Payments** and make sure **Square** (Credit/Debit
   Card) is **enabled**.
2. On the front end, add a card set to the cart → **Checkout**.
3. Pay with a **Square sandbox test card** (from Square's developer docs — e.g. a Visa
   test number with any future expiry and any CVV). No real money moves in sandbox.
4. Confirm: the order is created (**WooCommerce → Orders**), the customer + admin
   **order emails** arrive, and the total is **$10.00 / set, AUD**.

## 5. Pickup-only check (important for this store)
- **WooCommerce → Settings → Shipping**: only **Local Pickup ("Pickup from the school —
  front office")** should exist; **no delivery/flat-rate methods**. The theme also enforces
  pickup-only, but confirm here so checkout never asks for a shipping address/fee.
- At checkout the order summary should read **"Pickup from the school (front office) —
  FREE"** and **Total $10.00**.

## 6. Go live (Production)
1. In **WooCommerce → Settings → Square**, switch **Environment** to **Production** and
   **Connect with Square** again using the **live** P&C account (repeat step 3 with the
   real account, not sandbox).
2. Place **one small real order** ($10) with a real card to confirm money lands in Square,
   then **refund** it from **WooCommerce → Orders** (which also tests refunds).
3. Card data is handled by **Square, not stored on the site** (SAQ-A PCI compliant);
   Apple Pay / Google Pay can be enabled in the Square gateway settings if you chose to
   (**[confirm]**).

## 7. Inventory sync (optional)
- If a **Square POS catalogue** already exists (**[confirm]**), decide the **system of
  record** (usually Square) and enable **sync** in the WooCommerce Square settings so
  online and in-person stock/prices stay aligned. If the website is the only channel,
  leave sync off.

---

## What I've already done
- Installed the **WooCommerce Square** plugin on the site (inactive on the test box; it
  migrates to VentraIP with the rest of the site).

## What needs you (can't be done from here)
- The actual **Connect with Square** authorisation (your Square login) — steps 3 and 6.
- Done on the **live HTTPS VentraIP site**, not the local test box.
