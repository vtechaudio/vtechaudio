/* VTECH — front-end JS. Vanilla ES modules, no jQuery. Loaded with defer.
   Modules: sticky header shadow, mobile nav, sticky-CTA reveal, project
   filters, exit-intent lead popup, and the cost estimator AJAX call. */
(function () {
  'use strict';
  var cfg = window.VTECH || {};

  /* ---- Sticky header shadow ---- */
  var header = document.querySelector('.site-header[data-sticky]');
  if (header) {
    var onScroll = function () {
      if (window.scrollY > 8) header.setAttribute('data-scrolled', '');
      else header.removeAttribute('data-scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---- Mobile nav ---- */
  var toggle = document.querySelector('.nav-toggle');
  var mobileNav = document.getElementById('mobile-nav');
  if (toggle && mobileNav) {
    var closeNav = function () {
      mobileNav.setAttribute('hidden', '');
      document.body.classList.remove('nav-open');
      toggle.setAttribute('aria-expanded', 'false');
    };
    toggle.addEventListener('click', function () {
      var willOpen = mobileNav.hasAttribute('hidden');
      if (willOpen) { mobileNav.removeAttribute('hidden'); document.body.classList.add('nav-open'); }
      else { closeNav(); }
      toggle.setAttribute('aria-expanded', String(willOpen));
    });
    var closeBtn = mobileNav.querySelector('.mobile-nav__close');
    if (closeBtn) { closeBtn.addEventListener('click', closeNav); }
    mobileNav.addEventListener('click', function (e) {
      var a = e.target.closest('a');
      if (a && a.parentNode && a.parentNode.className.indexOf('menu-item-has-children') === -1) { closeNav(); }
    });
    document.addEventListener('keyup', function (e) {
      if ((e.key === 'Escape' || e.keyCode === 27) && !mobileNav.hasAttribute('hidden')) { closeNav(); }
    });
  }

  /* ---- Sticky CTA reveal after scrolling past hero ---- */
  var stickyCta = document.querySelector('[data-sticky-cta]');
  if (stickyCta) {
    var hero = document.querySelector('.hero, .svc-hero');
    var reveal = function () {
      var past = hero ? (hero.getBoundingClientRect().bottom < 0) : (window.scrollY > 600);
      if (past) stickyCta.removeAttribute('hidden'); else stickyCta.setAttribute('hidden', '');
    };
    window.addEventListener('scroll', reveal, { passive: true });
  }

  /* ---- Project filters (progressive enhancement) ---- */
  var filterBar = document.querySelector('[data-filter]');
  var grid = document.querySelector('[data-filter-grid]');
  if (filterBar && grid) {
    filterBar.addEventListener('click', function (e) {
      var btn = e.target.closest('.chip');
      if (!btn) return;
      filterBar.querySelectorAll('.chip').forEach(function (c) { c.classList.remove('is-active'); });
      btn.classList.add('is-active');
      var val = btn.getAttribute('data-filter-value');
      grid.querySelectorAll('[data-tags]').forEach(function (card) {
        var tags = card.getAttribute('data-tags') || '';
        card.style.display = (val === '*' || tags.split(' ').indexOf(val) > -1) ? '' : 'none';
      });
    });
  }

  /* ---- Exit-intent lead popup (once per session) ---- */
  if (document.body.dataset.exitIntent !== 'off' && !sessionStorage.getItem('vtech_exit')) {
    var fired = false;
    document.addEventListener('mouseout', function (e) {
      if (fired || e.clientY > 0 || e.relatedTarget) return;
      fired = true; sessionStorage.setItem('vtech_exit', '1');
      var popup = document.getElementById('vtech-exit-popup');
      if (popup) popup.removeAttribute('hidden');
    });
  }

  /* ---- Cost estimator (calls stubbed AJAX endpoint) ---- */
  var estForm = document.querySelector('[data-estimator]');
  if (estForm && cfg.ajaxUrl) {
    estForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var out = estForm.querySelector('[data-estimate-result]');
      var body = new URLSearchParams({
        action: 'vtech_estimate',
        nonce: cfg.nonce,
        service: estForm.service.value,
        qty: estForm.qty.value
      });
      out.textContent = 'Calculating…';
      fetch(cfg.ajaxUrl, { method: 'POST', body: body })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res.success) {
            var d = res.data;
            out.innerHTML = '<strong>Estimated range:</strong> KES ' +
              d.low.toLocaleString() + ' – ' + d.high.toLocaleString() +
              '<br><small>' + d.note + '</small>';
          } else { out.textContent = (res.data && res.data.message) || 'Please try again.'; }
        })
        .catch(function () { out.textContent = 'Network error — please call us instead.'; });
    });
  }

  /* ---- Count-up stats when in view ---- */
  var nums = document.querySelectorAll('[data-countup]');
  if (nums.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target, target = parseFloat(el.dataset.countup), start = null;
        var step = function (ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / 1200, 1);
          el.textContent = Math.floor(p * target).toLocaleString() + (el.dataset.suffix || '');
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step); io.unobserve(el);
      });
    }, { threshold: 0.4 });
    nums.forEach(function (n) { io.observe(n); });
  }

  /* ---- Quote/contact form: native POST (no interception) ---- */
  var qf = document.getElementById('vtech-quote-form');
  if (qf) {
    qf.addEventListener('submit', function () {
      var btn = qf.querySelector('button[type=submit]');
      if (btn) { btn.disabled = true; btn.textContent = 'Sending...'; }
    });
  }


  /* ---- Multi-step consultation form ---- */
  (function () {
    var form = document.getElementById('vtech-consult-form');
    if (!form) return;
    var steps = Array.prototype.slice.call(form.querySelectorAll('.cform__step'));
    var bar = form.querySelector('[data-cform-bar]');
    var note = form.querySelector('[data-cform-note]');
    var prevBtn = form.querySelector('[data-cform-prev]');
    var nextBtn = form.querySelector('[data-cform-next]');
    var subBtn = form.querySelector('[data-cform-submit]');
    var status = form.querySelector('.cf-status');
    var cur = 0;

    // Adaptive: reveal sub-sections based on selected systems.
    function selectedSystems() {
      return Array.prototype.slice.call(form.querySelectorAll('[data-cform-systems] input:checked')).map(function (c) { return c.getAttribute('data-system'); });
    }
    function applyAdaptive() {
      var sys = selectedSystems();
      form.querySelectorAll('.cf-sub[data-when]').forEach(function (sub) {
        var want = sub.getAttribute('data-when');
        sub.hidden = sys.indexOf(want) === -1;
      });
      // Sector step: show note if nothing sector-specific selected.
      var sectorStep = form.querySelector('[data-step="5"]');
      if (sectorStep) {
        var anyVisible = Array.prototype.slice.call(sectorStep.querySelectorAll('.cf-sub')).some(function (s) { return !s.hidden; });
        var emptyNote = sectorStep.querySelector('[data-sector-empty]');
        if (emptyNote) emptyNote.hidden = anyVisible;
      }
    }
    form.addEventListener('change', function (e) {
      if (e.target.closest('[data-cform-systems]')) applyAdaptive();
    });

    function show(i) {
      cur = Math.max(0, Math.min(i, steps.length - 1));
      steps.forEach(function (s, idx) { s.classList.toggle('is-active', idx === cur); });
      if (bar) bar.style.width = ((cur + 1) / steps.length * 100) + '%';
      if (note) note.textContent = 'Step ' + (cur + 1) + ' of ' + steps.length;
      prevBtn.hidden = cur === 0;
      nextBtn.hidden = cur === steps.length - 1;
      subBtn.hidden = cur !== steps.length - 1;
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function validateStep() {
      var active = steps[cur];
      var reqs = active.querySelectorAll('[required]');
      for (var k = 0; k < reqs.length; k++) {
        if (!reqs[k].value || (reqs[k].type === 'checkbox' && !reqs[k].checked)) {
          reqs[k].reportValidity ? reqs[k].reportValidity() : reqs[k].focus();
          return false;
        }
      }
      return true;
    }
    nextBtn.addEventListener('click', function () { if (validateStep()) { applyAdaptive(); show(cur + 1); } });
    prevBtn.addEventListener('click', function () { show(cur - 1); });

    form.addEventListener('submit', function (e) {
      // Multi-step: advance on earlier steps; on the final valid step allow the
      // normal browser POST (Post/Redirect/Get) to admin-post.php — no fetch.
      if (cur < steps.length - 1) { e.preventDefault(); if (validateStep()) { if (typeof applyAdaptive === 'function') applyAdaptive(); show(cur + 1); } return; }
      if (!validateStep()) { e.preventDefault(); return; }
      subBtn.disabled = true; subBtn.textContent = 'Submitting...';
    });

    show(0);
  })();


  /* ---- Multi-step hire request form ---- */
  (function () {
    var form = document.getElementById('vtech-hire-form');
    if (!form) return;
    var steps = Array.prototype.slice.call(form.querySelectorAll('.cform__step'));
    var bar = form.querySelector('[data-cform-bar]');
    var note = form.querySelector('[data-cform-note]');
    var prevBtn = form.querySelector('[data-cform-prev]');
    var nextBtn = form.querySelector('[data-cform-next]');
    var subBtn = form.querySelector('[data-cform-submit]');
    var status = form.querySelector('.cf-status');
    var cur = 0;
    function show(i) {
      cur = Math.max(0, Math.min(i, steps.length - 1));
      steps.forEach(function (s, idx) { s.classList.toggle('is-active', idx === cur); });
      if (bar) bar.style.width = ((cur + 1) / steps.length * 100) + '%';
      if (note) note.textContent = 'Step ' + (cur + 1) + ' of ' + steps.length;
      prevBtn.hidden = cur === 0;
      nextBtn.hidden = cur === steps.length - 1;
      subBtn.hidden = cur !== steps.length - 1;
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function validateStep() {
      var reqs = steps[cur].querySelectorAll('[required]');
      for (var k = 0; k < reqs.length; k++) {
        if (!reqs[k].value || (reqs[k].type === 'checkbox' && !reqs[k].checked)) {
          reqs[k].reportValidity ? reqs[k].reportValidity() : reqs[k].focus();
          return false;
        }
      }
      return true;
    }
    nextBtn.addEventListener('click', function () { if (validateStep()) show(cur + 1); });
    prevBtn.addEventListener('click', function () { show(cur - 1); });
    form.addEventListener('submit', function (e) {
      // Multi-step: advance on earlier steps; on the final valid step allow the
      // normal browser POST (Post/Redirect/Get) to admin-post.php — no fetch.
      if (cur < steps.length - 1) { e.preventDefault(); if (validateStep()) { if (typeof applyAdaptive === 'function') applyAdaptive(); show(cur + 1); } return; }
      if (!validateStep()) { e.preventDefault(); return; }
      subBtn.disabled = true; subBtn.textContent = 'Submitting...';
    });
    show(0);
  })();


  /* ---- Brands carousel (About page) ---- */
  (function () {
    var wrap = document.querySelector('[data-brand-carousel]');
    if (!wrap) return;
    var track = wrap.querySelector('[data-brand-track]');
    var prev = wrap.querySelector('[data-brand-prev]');
    var next = wrap.querySelector('[data-brand-next]');
    if (!track) return;
    function step() {
      var item = track.querySelector('.brand-carousel__item');
      var w = item ? item.getBoundingClientRect().width + 20 : 200;
      return Math.max(w, Math.floor(track.clientWidth * 0.8));
    }
    if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
  })();

})();
