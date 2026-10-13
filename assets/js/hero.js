(function () {
  'use strict';

  var DURATION = 900;

  function init(root) {
    if (!root || root.dataset.msReady) return;
    root.dataset.msReady = '1';

    var slides = [].slice.call(root.querySelectorAll('.ms-hero__slide'));
    var thumbs = [].slice.call(root.querySelectorAll('.ms-hero__thumb'));
    var counter = root.querySelector('[data-ms-hero-current]');
    var total = slides.length;
    if (!total) return;

    var current = 0;
    var busy = false;
    var timer = null;

    var inEditor = !!(window.elementorFrontend &&
      elementorFrontend.isEditMode && elementorFrontend.isEditMode());
    var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var fine = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    var autoplay = root.dataset.autoplay === 'yes' && total > 1 && !inEditor && !reduced;
    var speed = parseInt(root.dataset.speed, 10) || 6000;
    var pauseOnHover = root.dataset.pause === 'yes';

    root.style.setProperty('--ms-hero-speed', speed + 'ms');
    if (autoplay) root.classList.add('is-autoplay');

    function place(slide, state) {
      slide.classList.remove('is-before', 'is-after');
      if (state !== 'active') slide.classList.add('is-' + state);
    }

    function updateUI() {
      var color = getComputedStyle(slides[current]).color;
      root.style.setProperty('--ms-hero-ui', color);

      thumbs.forEach(function (t, i) {
        t.classList.toggle('is-active', i === current);
        if (i === current) t.setAttribute('aria-current', 'true');
        else t.removeAttribute('aria-current');
      });

      slides.forEach(function (s, i) {
        s.setAttribute('aria-hidden', i === current ? 'false' : 'true');
      });

      if (counter) counter.textContent = ('0' + (current + 1)).slice(-2);
    }

    function go(next, dir) {
      if (busy || next === current) return;

      dir = dir || (next > current ? 1 : -1);
      busy = true;

      var out = slides[current];
      var inn = slides[next];

      // 1. Park the incoming slide at its start position, without animation
      inn.classList.add('no-anim');
      place(inn, dir > 0 ? 'after' : 'before');
      void inn.offsetWidth; // force reflow
      inn.classList.remove('no-anim');

      // 2. Animate: outgoing leaves, incoming arrives
      out.classList.remove('is-active');
      out.classList.add('is-leaving');
      place(out, dir > 0 ? 'before' : 'after');

      inn.classList.add('is-active');
      place(inn, 'active');

      current = next;
      updateUI();

      setTimeout(function () {
        out.classList.remove('is-leaving');
        busy = false;
      }, DURATION);
    }

    function next() { go((current + 1) % total, 1); }
    function prev() { go((current - 1 + total) % total, -1); }

    // Restart the progress bar on the active thumbnail
    function restartBar() {
      var bar = root.querySelector('.ms-hero__thumb.is-active .ms-hero__bar');
      if (!bar) return;
      bar.style.animation = 'none';
      void bar.offsetWidth;
      bar.style.animation = '';
    }

    function schedule() {
      clearTimeout(timer);
      if (!autoplay) return;
      restartBar();
      timer = setTimeout(function () {
        if (!root.isConnected) return; // widget was re-rendered
        next();
        schedule();
      }, speed);
    }

    function stop() { clearTimeout(timer); }

    // Thumbnails
    thumbs.forEach(function (t) {
      t.addEventListener('click', function () {
        go(parseInt(t.dataset.index, 10));
        schedule();
      });
    });

    // Pause on hover (progress bar resets and restarts together with the timer)
    if (autoplay && pauseOnHover) {
      root.addEventListener('mouseenter', function () {
        root.classList.add('is-paused');
        stop();
      });
      root.addEventListener('mouseleave', function () {
        root.classList.remove('is-paused');
        schedule();
      });
    }

    // Mouse parallax (desktop only)
    if (fine && !reduced) {
      var raf = null;
      var px = 0;
      var py = 0;

      var apply = function () {
        raf = null;
        root.style.setProperty('--ms-px', px.toFixed(3));
        root.style.setProperty('--ms-py', py.toFixed(3));
      };

      root.addEventListener('mousemove', function (e) {
        var r = root.getBoundingClientRect();
        px = ((e.clientX - r.left) / r.width - 0.5) * 2;
        py = ((e.clientY - r.top) / r.height - 0.5) * 2;
        if (!raf) raf = requestAnimationFrame(apply);
      });

      root.addEventListener('mouseleave', function () {
        px = 0;
        py = 0;
        if (!raf) raf = requestAnimationFrame(apply);
      });
    }

    // Touch swipe
    var startX = null;
    root.addEventListener('touchstart', function (e) {
      startX = e.changedTouches[0].clientX;
    }, { passive: true });

    root.addEventListener('touchend', function (e) {
      if (startX === null) return;
      var dx = e.changedTouches[0].clientX - startX;
      startX = null;
      if (Math.abs(dx) < 50) return;
      if (dx < 0) next(); else prev();
      schedule();
    }, { passive: true });

    updateUI();
    schedule();
  }

  function initAll(scope) {
    (scope || document).querySelectorAll('.ms-hero').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { initAll(); });
  } else {
    initAll();
  }

  // Elementor editor: re-init whenever the widget re-renders
  function hookElementor() {
    elementorFrontend.hooks.addAction('frontend/element_ready/ms-hero.default', function ($scope) {
      initAll($scope[0]);
    });
  }

  if (window.elementorFrontend && elementorFrontend.hooks) hookElementor();
  else window.addEventListener('elementor/frontend/init', hookElementor);
})();