(function () {
  'use strict';

  // Automatically mark the document as JS-enabled
  document.documentElement.classList.add('ms-js');

  var observer = null;

  function show(el) {
    el.classList.add('is-visible');
  }

  function scan(scope) {
    var els = [].slice.call((scope || document).querySelectorAll('[data-ms-reveal]:not(.is-visible)'));
    if (!els.length) return;

    var inEditor = !!(window.elementorFrontend &&
      elementorFrontend.isEditMode && elementorFrontend.isEditMode());

    // In the editor (or old browsers) just show everything
    if (inEditor || !('IntersectionObserver' in window)) {
      els.forEach(show);
      return;
    }

    if (!observer) {
      observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          show(entry.target);
          observer.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.1 });
    }

    els.forEach(function (el) { observer.observe(el); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { scan(); });
  } else {
    scan();
  }

  // Elementor: scan again whenever a widget renders or re-renders
  function hookElementor() {
    elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
      scan($scope[0]);
    });
  }

  if (window.elementorFrontend && window.elementorFrontend.hooks) hookElementor();
  else window.addEventListener('elementor/frontend/init', hookElementor);
})();