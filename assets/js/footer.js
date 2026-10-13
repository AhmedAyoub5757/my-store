(function () {
  'use strict';

  function init(root) {
    var form = root.querySelector('[data-ms-subscribe]');
    if (!form || form.dataset.msReady) return;
    form.dataset.msReady = '1';

    var input = form.querySelector('input[type="email"]');
    var btn = form.querySelector('.ms-foot__submit');
    var msg = form.querySelector('.ms-foot__msg');

    function say(text, type) {
      msg.textContent = text || '';
      msg.className = 'ms-foot__msg' + (type ? ' is-' + type : '');
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (btn.classList.contains('is-loading')) return;

      var email = input.value.trim();

      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        say(form.dataset.invalid, 'error');
        input.focus();
        return;
      }

      if (!window.msFooter) {
        say(form.dataset.error, 'error');
        return;
      }

      var data = new FormData(form);
      data.append('action', 'ms_subscribe');
      data.append('nonce', window.msFooter.nonce);

      btn.classList.add('is-loading');
      say('');

      fetch(window.msFooter.ajaxUrl, {
        method: 'POST',
        body: data,
        credentials: 'same-origin'
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && res.success) {
            form.reset();
            form.classList.add('is-done');
            say(form.dataset.success, 'success');
          } else {
            say((res && res.data && res.data.message) || form.dataset.error, 'error');
          }
        })
        .catch(function () {
          say(form.dataset.error, 'error');
        })
        .then(function () {
          btn.classList.remove('is-loading');
        });
    });
  }

  function initAll(scope) {
    (scope || document).querySelectorAll('.ms-foot').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { initAll(); });
  } else {
    initAll();
  }

  // Elementor editor: re-init whenever the widget re-renders
  function hookElementor() {
    elementorFrontend.hooks.addAction('frontend/element_ready/ms-footer.default', function ($scope) {
      initAll($scope[0]);
    });
  }

  if (window.elementorFrontend && elementorFrontend.hooks) hookElementor();
  else window.addEventListener('elementor/frontend/init', hookElementor);
})();