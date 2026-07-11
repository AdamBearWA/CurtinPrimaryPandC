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
        '<div class="cpc-empty-note"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1f6b41" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Free pickup from the front office</div>';

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
