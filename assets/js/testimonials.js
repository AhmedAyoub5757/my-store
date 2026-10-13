(function () {
  'use strict';

  function init(root) {
    if (!root || root.dataset.msReady) return;
    root.dataset.msReady = '1';

    var track = root.querySelector('[data-ms-testi-track]');
    var bar = root.querySelector('[data-ms-testi-bar]');
    var prev = root.querySelector('[data-ms-testi-prev]');
    var next = root.querySelector('[data-ms-testi-next]');
    if (!track) return;

    function step() {
      var card = track.querySelector('.ms-testi__card');
      if (!card) return track.clientWidth;
      var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
      return card.getBoundingClientRect().width + gap;
    }

    function update() {
      var max = track.scrollWidth - track.clientWidth;
      var ratio = max > 0 ? track.scrollLeft / max : 1;
      var visible = track.clientWidth / track.scrollWidth;

      // Bar width = how much of the track is visible, position = scroll progress
      if (bar) {
        bar.style.transform = 'translateX(' + (ratio * (1 - visible) * 100 / visible).toFixed(2) + '%) scaleX(' + visible.toFixed(3) + ')';
      }

      if (prev) prev.disabled = track.scrollLeft <= 2;
      if (next) next.disabled = track.scrollLeft >= max - 2;
    }

    if (prev) prev.addEventListener('click', function () {
      track.scrollBy({ left: -step(), behavior: 'smooth' });
    });

    if (next) next.addEventListener('click', function () {
      track.scrollBy({ left: step(), behavior: 'smooth' });
    });

    track.addEventListener('scroll', function () {
      window.requestAnimationFrame(update);
    }, { passive: true });

    window.addEventListener('resize', update);

    // Keyboard arrows when the track is focused
    track.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { e.preventDefault(); track.scrollBy({ left: step(), behavior: 'smooth' }); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); track.scrollBy({ left: -step(), behavior: 'smooth' }); }
    });

    // Mouse drag (touch uses native scrolling)
    var down = false;
    var startX = 0;
    var startScroll = 0;
    var moved = false;

    track.addEventListener('mousedown', function (e) {
      down = true;
      moved = false;
      startX = e.pageX;
      startScroll = track.scrollLeft;
    });

    window.addEventListener('mousemove', function (e) {
      if (!down) return;
      var dx = e.pageX - startX;
      if (Math.abs(dx) > 4) {
        moved = true;
        track.classList.add('is-dragging');
      }
      if (moved) track.scrollLeft = startScroll - dx;
    });

    window.addEventListener('mouseup', function () {
      if (!down) return;
      down = false;
      if (moved) {
        track.classList.remove('is-dragging');
        // Snap to the nearest card
        var s = step();
        track.scrollTo({ left: Math.round(track.scrollLeft / s) * s, behavior: 'smooth' });
      }
    });

    update();
    // Images and fonts can change widths after load
    window.addEventListener('load', update);
  }

  function initAll(scope) {
    (scope || document).querySelectorAll('.ms-testi').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { initAll(); });
  } else {
    initAll();
  }

  // Elementor editor: re-init whenever the widget re-renders
  function hookElementor() {
    elementorFrontend.hooks.addAction('frontend/element_ready/ms-testimonials.default', function ($scope) {
      initAll($scope[0]);
    });
  }

  if (window.elementorFrontend && elementorFrontend.hooks) hookElementor();
  else window.addEventListener('elementor/frontend/init', hookElementor);
})();