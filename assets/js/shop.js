(function () {
  'use strict';

  function init(root) {
    if (!root || root.dataset.msReady) return;
    root.dataset.msReady = '1';

    function setDrawer(open) {
      root.classList.toggle('is-filters-open', open);
      document.documentElement.classList.toggle('ms-lock', open);
    }

    root.addEventListener('click', function (e) {
      if (e.target.closest('[data-ms-filters-open]')) setDrawer(true);
      else if (e.target.closest('[data-ms-filters-close]')) setDrawer(false);
    });

    document.addEventListener('keydown', function (e) {
      if (!root.isConnected) return;
      if (e.key === 'Escape') setDrawer(false);
    });

    // Sorting: submit as soon as the choice changes
    root.querySelectorAll('[data-ms-sort]').forEach(function (select) {
      select.addEventListener('change', function () {
        if (select.form) select.form.submit();
      });
    });
  }

  function initAll(scope) {
    (scope || document).querySelectorAll('.ms-shop').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { initAll(); });
  } else {
    initAll();
  }

  // Elementor editor: re-init whenever the widget re-renders
  function hookElementor() {
    elementorFrontend.hooks.addAction('frontend/element_ready/ms-shop.default', function ($scope) {
      initAll($scope[0]);
    });
  }

  if (window.elementorFrontend && elementorFrontend.hooks) hookElementor();
  else window.addEventListener('elementor/frontend/init', hookElementor);
})();