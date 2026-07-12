/* Curtin P&C Shop — light UI behaviour (no dependencies). */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    /* ---- Mobile nav toggle ---- */
    var burger = document.querySelector('.cpc-hamburger');
    var nav = document.querySelector('.cpc-nav');
    var backdrop = document.querySelector('.cpc-nav-backdrop');
    function closeNav() {
      if (nav) nav.classList.remove('cpc-open');
      if (backdrop) backdrop.classList.remove('cpc-open');
      if (burger) burger.setAttribute('aria-expanded', 'false');
    }
    if (burger && nav) {
      burger.addEventListener('click', function () {
        var open = nav.classList.toggle('cpc-open');
        if (backdrop) backdrop.classList.toggle('cpc-open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }
    if (backdrop) backdrop.addEventListener('click', closeNav);

    /* ---- Product gallery: thumbnail → main image swap ---- */
    var main = document.querySelector('.cpc-gallery-main img');
    var thumbs = document.querySelectorAll('.cpc-thumb');
    if (main && thumbs.length) {
      thumbs.forEach(function (t) {
        t.addEventListener('click', function () {
          var img = t.querySelector('img');
          if (!img) return;
          main.src = img.getAttribute('data-full') || img.src;
          main.alt = img.alt;
          thumbs.forEach(function (x) { x.classList.remove('cpc-thumb-active'); });
          t.classList.add('cpc-thumb-active');
        });
      });
    }

    /* ---- Quantity stepper (−/+) ---- */
    document.querySelectorAll('.cpc-qty').forEach(function (q) {
      var input = q.querySelector('input');
      var minus = q.querySelector('[data-step="down"]');
      var plus = q.querySelector('[data-step="up"]');
      if (!input) return;
      function clamp(v) {
        var min = parseInt(input.min, 10); if (isNaN(min)) min = 1;
        return v < min ? min : v;
      }
      if (minus) minus.addEventListener('click', function () {
        input.value = clamp((parseInt(input.value, 10) || 1) - 1);
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
      if (plus) plus.addEventListener('click', function () {
        input.value = clamp((parseInt(input.value, 10) || 1) + 1);
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });

    /* ---- Expandable Pickup / Product-details rows ---- */
    document.querySelectorAll('.cpc-prow[data-toggle]').forEach(function (row) {
      row.addEventListener('click', function () {
        var detail = row.nextElementSibling;
        if (detail && detail.classList.contains('cpc-prow-detail')) {
          detail.classList.toggle('cpc-open');
        }
      });
    });
  });
})();

/* ============================================================
   v2.1 enhancements — trust icons, cart badge, sticky buy bar,
   branded empty cart. Progressive enhancement (no dependencies).
   ============================================================ */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') { fn(); }
    else { document.addEventListener('DOMContentLoaded', fn); }
  }

  ready(function () {

    /* Trust-strip icons and the cart count badge are now server-rendered
       in front-page.php / header.php (baked into PHP, no first-paint flash). */

    /* ---- Sticky mobile "Add to cart" bar (single product) ---- */
    var buyrow = document.querySelector('.cpc-buyrow');
    var inlineBtn = document.querySelector('.cpc-addtocart');
    if (buyrow && inlineBtn && !document.querySelector('.cpc-buybar')) {
      var price = document.querySelector('.cpc-pprice');
      var meta = document.querySelector('.cpc-pprice-meta');
      var bar = document.createElement('div');
      bar.className = 'cpc-buybar';
      bar.innerHTML =
        '<div class="cpc-buybar-info">' +
          '<div class="cpc-buybar-price">' + (price ? price.textContent.trim() : '') + '</div>' +
          '<div class="cpc-buybar-meta">' + (meta ? meta.textContent.trim() : '') + '</div>' +
        '</div>' +
        '<button type="button" class="cpc-buybar-btn">Add to cart</button>';
      document.body.appendChild(bar);
      document.body.classList.add('cpc-has-buybar');
      bar.querySelector('.cpc-buybar-btn').addEventListener('click', function () {
        inlineBtn.click();
      });
      if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
          entries.forEach(function (en) {
            bar.classList.toggle('cpc-show', !en.isIntersecting && en.boundingClientRect.top < 0);
          });
        }, { threshold: 0 });
        io.observe(buyrow);
      } else {
        bar.classList.add('cpc-show');
      }
    }

    /* ---- Branded empty-cart state ---- */
    function brandEmptyCart() {
      var block = document.querySelector('.wp-block-woocommerce-empty-cart-block');
      if (!block || block.classList.contains('cpc-emptied')) return false;
      if (!block.querySelector('.wc-block-cart__empty-cart__title')) return false;

      var nav = document.querySelector('.cpc-nav');
      function navHref(re) {
        if (!nav) return '#';
        var a = [].slice.call(nav.querySelectorAll('a')).find(function (x) { return re.test(x.textContent); });
        return a ? a.getAttribute('href') : '#';
      }
      var cardsUrl = navHref(/card/i);
      var oliveUrl = navHref(/olive/i);

      var wrap = document.createElement('div');
      wrap.className = 'cpc-empty';
      wrap.innerHTML =
        '<svg class="cpc-empty-art" viewBox="0 0 160 160" role="img" aria-label="An empty shopping bag with a greeting card">' +
          '<circle cx="80" cy="80" r="78" fill="#eaf3fb"/>' +
          '<g transform="rotate(-10 80 62)">' +
            '<rect x="66" y="38" width="28" height="48" rx="3.5" fill="#fff" stroke="#2f8f5b" stroke-width="2.6"/>' +
            '<g transform="translate(80,58)">' +
              '<circle cx="0" cy="-6" r="3.1" fill="#1d6fb8"/><circle cx="6" cy="0" r="3.1" fill="#1d6fb8"/><circle cx="0" cy="6" r="3.1" fill="#1d6fb8"/><circle cx="-6" cy="0" r="3.1" fill="#1d6fb8"/><circle cx="0" cy="0" r="3" fill="#f0a93b"/>' +
            '</g>' +
          '</g>' +
          '<path d="M50 72 H110 A4 4 0 0 1 114 76.4 L109 130 A10 10 0 0 1 99 139 H61 A10 10 0 0 1 51 130 L46 76.4 A4 4 0 0 1 50 72 Z" fill="#fff" stroke="#1d6fb8" stroke-width="4" stroke-linejoin="round"/>' +
          '<path d="M64 72 v-7 a16 16 0 0 1 32 0 v7" fill="none" stroke="#2f8f5b" stroke-width="4" stroke-linecap="round"/>' +
        '</svg>' +
        '<h2>Your cart is empty</h2>' +
        '<p>Every set of cards is one community artwork, turned into something you can send — and 100% of profits go to our school.</p>' +
        '<div class="cpc-empty-actions">' +
          '<a class="cpc-empty-cta" href="' + cardsUrl + '">Browse the cards</a>' +
          '<a class="cpc-empty-link" href="' + oliveUrl + '">See the olive oil</a>' +
        '</div>' +
        '<div class="cpc-empty-note"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1f6b41" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Free pickup</div>';

      block.classList.add('cpc-emptied');
      block.appendChild(wrap);
      return true;
    }

    if (document.querySelector('.wp-block-woocommerce-cart')) {
      // The Woo cart block hydrates client-side; poll until the empty state
      // renders (mutation-count caps can expire during rapid hydration).
      var __cpcStart = Date.now();
      var __cpcIv = setInterval(function () {
        if (brandEmptyCart() || Date.now() - __cpcStart > 8000) { clearInterval(__cpcIv); }
      }, 200);
    }

  });
})();

/* ============================================================
   v2.6.1 — checkout "Order summary" de-dupe, cart continue-shopping
   link, Square card-field loading state. Progressive enhancement.
   ============================================================ */
(function () {
  'use strict';
  function ready(fn){ if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  ready(function () {
    var isCheckout = !!document.querySelector('.wp-block-woocommerce-checkout');
    var isCart = !!document.querySelector('.wp-block-woocommerce-cart');
    if (!isCheckout && !isCart) { return; }

    /* #3 — the block checkout shows a collapsible "Order summary" at the top
       (kept) and the full order-summary table lower down / in the sidebar,
       both titled "Order summary". Rename the latter so it isn't repeated. */
    function renameOrderSummary(){
      var titles = document.querySelectorAll(
        '.wp-block-woocommerce-checkout-order-summary-block .wc-block-components-checkout-step__title,' +
        '.wp-block-woocommerce-checkout-order-summary-block .wc-block-components-title');
      titles.forEach(function (h){
        if (/^\s*Order summary\s*$/i.test(h.textContent)) { h.textContent = 'Your order'; }
      });
    }

    /* #2 — mark the checkout ready once the Square secure card iframe paints,
       so the "Card details load securely below" hint can be hidden. */
    function squareReady(){
      var f = document.querySelector(
        '.wc-block-checkout__payment-method iframe[name^="single-card"],' +
        '.sq-card-iframe-container iframe, .sq-card-wrapper iframe');
      if (f) { document.body.classList.add('cpc-sq-ready'); }
    }

    /* #10 — add a low-key "Continue shopping" link on a populated cart. */
    function addCartContinue(){
      if (document.querySelector('.cpc-cart-continue')) { return; }
      var host = document.querySelector('.wp-block-woocommerce-proceed-to-checkout-block');
      var hasItems = document.querySelector('.wc-block-cart-items, .wc-block-cart__submit-button');
      if (!host || !hasItems) { return; }
      var shopHref = '/shop/';
      var nav = document.querySelector('.cpc-nav');
      if (nav){
        var s = [].slice.call(nav.querySelectorAll('a')).find(function (a){ return /shop|card/i.test(a.textContent); });
        if (s) { shopHref = s.getAttribute('href'); }
      }
      var wrap = document.createElement('div');
      wrap.className = 'cpc-cart-continue';
      wrap.innerHTML = '<a href="' + shopHref + '">← Continue shopping</a>';
      host.appendChild(wrap);
    }

    function tick(){
      if (isCheckout){ renameOrderSummary(); squareReady(); }
      if (isCart){ addCartContinue(); }
    }

    var start = Date.now();
    var iv = setInterval(function (){
      tick();
      if (Date.now() - start > 12000) { clearInterval(iv); }
    }, 300);

    /* Re-apply through the block checkout/cart's client-side re-renders. */
    if ('MutationObserver' in window){
      var mo = new MutationObserver(function (){ tick(); });
      mo.observe(document.body, { childList: true, subtree: true });
      setTimeout(function (){ mo.disconnect(); }, 15000);
    }
  });
})();

/* ============================================================
   v2.6.3 — pickup billing trim (robust) + region-specific
   "no delivery here" message. Polls so it works regardless of
   when the block checkout's data stores finish loading.
   ============================================================ */
(function () {
  'use strict';
  if (!document.querySelector('.wp-block-woocommerce-checkout')) { return; }

  var PICKUP_ADDR = { country: 'AU', address_1: '20 Goss Avenue', address_2: '', city: 'Manning', state: 'WA', postcode: '6152' };
  var DELIVERY_MSG = 'Curtin Gold olive oil can only be delivered within postcode 6152 (Como, Karawara, Manning, Salter Point, Waterford). Please choose “Pickup” above, or use an address within 6152 — greeting cards can be posted anywhere in Australia.';

  function sel(store){ try { return (window.wp && wp.data) ? wp.data.select(store) : null; } catch (e) { return null; } }
  function prefersCollection(){ try { var s = sel('wc/store/checkout'); return !!(s && s.prefersCollection && s.prefersCollection()); } catch (e) { return false; } }
  function billingAddr1(){ try { var c = sel('wc/store/cart'); var d = c && c.getCustomerData && c.getCustomerData(); return (d && d.billingAddress && d.billingAddress.address_1) || ''; } catch (e) { return ''; } }

  // #4 — pickup only needs name + phone: hide the billing ADDRESS fields
  // (via body.cpc-pickup) and auto-set the address to the pickup location so
  // card payment still validates.
  function applyPickup(){
    var pickup = prefersCollection();
    document.body.classList.toggle('cpc-pickup', pickup);
    if (pickup && billingAddr1() !== PICKUP_ADDR.address_1){
      try {
        var c = sel('wc/store/cart');
        var cur = (c && c.getCustomerData && c.getCustomerData().billingAddress) || {};
        wp.data.dispatch('wc/store/cart').setBillingAddress(Object.assign({}, cur, PICKUP_ADDR));
      } catch (e) {}
    }
  }

  // Out-of-region delivery: replace WooCommerce's generic "no shipping options"
  // notice with a message that names the delivery area.
  function applyDeliveryMsg(){
    document.querySelectorAll('.wc-block-components-shipping-rates-control__no-results-notice .wc-block-components-notice-banner__content').forEach(function (el){
      if (el.textContent !== DELIVERY_MSG) { el.textContent = DELIVERY_MSG; }
    });
  }

  function tick(){ applyPickup(); applyDeliveryMsg(); }
  var iv = setInterval(tick, 400);
  if (document.readyState !== 'loading') { tick(); } else { document.addEventListener('DOMContentLoaded', tick); }
})();

/* Curtin P&C Shop — front-page hero carousel (auto-rotating + swipeable, no deps). */
(function () {
  'use strict';
  function initCarousels() {
    document.querySelectorAll('[data-cpc-carousel]').forEach(function (car) {
      var slides = car.querySelectorAll('.cpc-slide');
      var dots = car.querySelectorAll('.cpc-dot');
      if (slides.length < 2) { return; }
      var interval = parseInt(car.getAttribute('data-interval'), 10) || 4500;
      var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var i = 0, timer = null;
      function show(n) {
        i = (n + slides.length) % slides.length;
        slides.forEach(function (s, x) { s.classList.toggle('cpc-slide-active', x === i); });
        dots.forEach(function (d, x) {
          d.classList.toggle('cpc-dot-active', x === i);
          d.setAttribute('aria-current', x === i ? 'true' : 'false');
        });
      }
      function next() { show(i + 1); }
      function prev() { show(i - 1); }
      function stop() { if (timer) { clearInterval(timer); timer = null; } }
      function start() { if (reduce) { return; } stop(); timer = setInterval(next, interval); }
      dots.forEach(function (d, x) {
        d.addEventListener('click', function () { show(x); start(); });
      });
      car.addEventListener('mouseenter', stop);
      car.addEventListener('mouseleave', start);
      document.addEventListener('visibilitychange', function () {
        if (document.hidden) { stop(); } else { start(); }
      });

      /* ---- Swipe (touch) + drag (mouse) between products ---- */
      var startX = 0, startY = 0, dragging = false, swiped = false;
      var THRESH = 40; // px of horizontal movement to count as a swipe
      function onStart(x, y) { startX = x; startY = y; dragging = true; swiped = false; stop(); }
      function horizontal(x, y) { return dragging && Math.abs(x - startX) > Math.abs(y - startY); }
      function onEnd(x) {
        if (!dragging) { return; }
        dragging = false;
        var dx = x - startX;
        if (Math.abs(dx) > THRESH) { swiped = true; if (dx < 0) { next(); } else { prev(); } }
        start();
      }
      car.addEventListener('touchstart', function (e) {
        var t = e.touches[0]; onStart(t.clientX, t.clientY);
      }, { passive: true });
      car.addEventListener('touchmove', function (e) {
        var t = e.touches[0];
        if (horizontal(t.clientX, t.clientY)) { e.preventDefault(); } // own horizontal, let vertical scroll pass
      }, { passive: false });
      car.addEventListener('touchend', function (e) {
        var t = (e.changedTouches && e.changedTouches[0]) || {};
        onEnd(typeof t.clientX === 'number' ? t.clientX : startX);
      });
      car.addEventListener('mousedown', function (e) { onStart(e.clientX, e.clientY); });
      window.addEventListener('mouseup', function (e) { if (dragging) { onEnd(e.clientX); } });
      // Swallow the click that follows a drag/swipe so it doesn't open the card link.
      car.addEventListener('click', function (e) {
        if (swiped) { e.preventDefault(); e.stopPropagation(); swiped = false; }
      }, true);

      show(0);
      start();
    });
  }
  if (document.readyState !== 'loading') { initCarousels(); }
  else { document.addEventListener('DOMContentLoaded', initCarousels); }
})();

/* ============================================================
   v2.7.1 — olive-oil out-of-area guard (block cart + checkout).
   The real block is server-side (functions.php §7b); this mirrors it in
   the React cart/checkout so the UI is consistent: toggles
   body.cpc-oil-blocked (CSS hides the misleading Shipping row), injects a
   matching alert on the checkout, and disables Place order while an
   out-of-area olive-oil delivery is selected. Runs only when the cart
   actually holds olive oil (body.cpc-has-oil).
   ============================================================ */
(function () {
  'use strict';
  var isCart = !!document.querySelector('.wp-block-woocommerce-cart');
  var isCheckout = !!document.querySelector('.wp-block-woocommerce-checkout');
  if ((!isCart && !isCheckout) || !document.body.classList.contains('cpc-has-oil')) { return; }

  function sel(store){ try { return (window.wp && wp.data) ? wp.data.select(store) : null; } catch (e) { return null; } }
  function shipPostcode(){
    try {
      var c = sel('wc/store/cart');
      var d = c && c.getCustomerData && c.getCustomerData();
      var pc = (d && d.shippingAddress && d.shippingAddress.postcode) || '';
      return String(pc).toUpperCase().replace(/\s+/g, '');
    } catch (e) { return ''; }
  }
  function prefersCollection(){
    try { var s = sel('wc/store/checkout'); return !!(s && s.prefersCollection && s.prefersCollection()); }
    catch (e) { return false; }
  }

  var OIL_NOTICE_MSG = 'Curtin Gold olive oil can only be delivered within postcode 6152 (Como, Karawara, Manning, Salter Point, Waterford). Please choose \u201CPickup\u201D above, or use an address within 6152 \u2014 greeting cards can be posted anywhere in Australia.';
  var OIL_ICON = '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10" fill="#e0a200"/><circle cx="12" cy="7.6" r="1.35" fill="#fff"/><rect x="11" y="10.5" width="2" height="7" rx="1" fill="#fff"/></svg>';
  function ensureInfoNotice(show){
    var existing = document.querySelector('.cpc-oil-notice');
    if (!show){ if (existing){ existing.remove(); } return; }
    if (existing){ return; }
    var block = document.querySelector('.wp-block-woocommerce-cart') || document.querySelector('.wp-block-woocommerce-checkout');
    if (!block || !block.parentNode){ return; }
    var el = document.createElement('div');
    el.className = 'cpc-oil-notice';
    el.setAttribute('role', 'status');
    el.innerHTML = OIL_ICON + '<span class="cpc-oil-notice-text"></span>';
    el.querySelector('.cpc-oil-notice-text').textContent = OIL_NOTICE_MSG;
    block.parentNode.insertBefore(el, block);
  }

  function apply(){
    var pc = shipPostcode();
    var blocked = !prefersCollection() && pc !== '' && pc !== '6152';
    document.body.classList.toggle('cpc-oil-blocked', blocked);
    // Cart: always show the amber notice — customers can express-checkout with
    // Google/Apple Pay straight from the cart, so they must see it there. Checkout:
    // hide it while blocked so the red server validation banner is the sole message.
    ensureInfoNotice( isCart ? true : ! blocked );
    if (isCheckout){
      document.querySelectorAll('.wc-block-components-checkout-place-order-button').forEach(function (btn){
        if (blocked){ btn.setAttribute('disabled', 'disabled'); btn.setAttribute('aria-disabled', 'true'); }
        else if (btn.getAttribute('aria-disabled') === 'true'){ btn.removeAttribute('disabled'); btn.removeAttribute('aria-disabled'); }
      });
    }
  }

  var iv = setInterval(apply, 400);
  if (document.readyState !== 'loading') { apply(); } else { document.addEventListener('DOMContentLoaded', apply); }
})();
