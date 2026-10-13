(function () {
  'use strict';

  function setNav(navbar, open) {
    var toggle = navbar.querySelector('.ms-navbar__toggle');
    navbar.classList.toggle('is-nav-open', open);
    if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function setSearch(navbar, open) {
    var panel = navbar.querySelector('.ms-navbar__search');
    if (!panel) return;

    panel.hidden = !open;

    navbar.querySelectorAll('[data-ms-search-toggle]').forEach(function (btn) {
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    if (open) {
      var input = panel.querySelector('input[type="search"]');
      if (input) input.focus();
    }
  }

  function closeAll(except) {
    document.querySelectorAll('.ms-navbar').forEach(function (navbar) {
      if (navbar === except) return;
      setNav(navbar, false);
      setSearch(navbar, false);
    });
  }

  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('.ms-navbar__toggle');
    var searchBtn = e.target.closest('[data-ms-search-toggle]');
    var link = e.target.closest('.ms-navbar__link');
    var navbar = e.target.closest('.ms-navbar');

    closeAll(navbar);

    if (!navbar) return;

    if (toggle) {
      setSearch(navbar, false);
      setNav(navbar, !navbar.classList.contains('is-nav-open'));
    } else if (searchBtn) {
      var panel = navbar.querySelector('.ms-navbar__search');
      setNav(navbar, false);
      setSearch(navbar, panel ? panel.hidden : false);
    } else if (link) {
      setNav(navbar, false);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll(null);
  });
})();