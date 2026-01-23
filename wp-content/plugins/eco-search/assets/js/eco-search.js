(function () {
  const body = document.body;

  function getContainer(targetId) {
    return document.getElementById(targetId) || document.querySelector('#' + CSS.escape(targetId));
  }

  function setExpanded(btn, expanded) {
    btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  }

  function openSearch(targetId, btn) {
    const el = getContainer(targetId);
    if (!el) return;

    body.classList.add('eco-search-open');
    el.classList.add('is-open');
    el.setAttribute('aria-hidden', 'false');

    if (btn) setExpanded(btn, true);

    const input = el.querySelector('input[name="s"]');
    if (input) setTimeout(() => input.focus(), 30);
  }

  function closeSearch(targetId, btn) {
    const el = getContainer(targetId);
    if (!el) return;

    body.classList.remove('eco-search-open');
    el.classList.remove('is-open');
    el.setAttribute('aria-hidden', 'true');

    if (btn) setExpanded(btn, false);
  }

  function toggleSearch(targetId, btn) {
    const el = getContainer(targetId);
    if (!el) return;

    el.classList.contains('is-open') ? closeSearch(targetId, btn) : openSearch(targetId, btn);
  }

  // Toggle click (delegation => works after dynamic swaps)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-eco-search-toggle="1"]');
    if (btn) {
      e.preventDefault();
      const target = btn.getAttribute('data-eco-search-target') || 'eco-searchbar';
      toggleSearch(target, btn);
      return;
    }

    // Click outside to close (if open)
    if (body.classList.contains('eco-search-open')) {
      const targetId = 'eco-searchbar';
      const el = getContainer(targetId);
      const toggleBtn = document.querySelector('[data-eco-search-toggle="1"][data-eco-search-target="' + CSS.escape(targetId) + '"]');

      if (el && el.classList.contains('is-open')) {
        const insideBar = e.target.closest('#' + CSS.escape(targetId));
        const insideToggle = e.target.closest('[data-eco-search-toggle="1"]');
        if (!insideBar && !insideToggle) {
          closeSearch(targetId, toggleBtn);
        }
      }
    }
  });

  // ESC closes
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;

    const targetId = 'eco-searchbar';
    const el = getContainer(targetId);
    if (!el || !el.classList.contains('is-open')) return;

    const btn = document.querySelector('[data-eco-search-toggle="1"][data-eco-search-target="' + CSS.escape(targetId) + '"]');
    closeSearch(targetId, btn || null);
  });
})();
