(function () {
  const body = document.body;
  let openTargetId = null;

  function getContainer(targetId) {
    if (!targetId) return null;
    return document.getElementById(targetId) || document.querySelector('#' + CSS.escape(targetId));
  }

  function setExpanded(btn, expanded) {
    if (!btn) return;
    btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
  }

  function findToggleButton(targetId) {
    if (!targetId) return null;
    return document.querySelector(
      '[data-eco-search-toggle="1"][data-eco-search-target="' + CSS.escape(targetId) + '"]'
    );
  }

  function openSearch(targetId, btn) {
    const el = getContainer(targetId);
    if (!el) return;

    // Close previously open container if different
    if (openTargetId && openTargetId !== targetId) {
      closeSearch(openTargetId, findToggleButton(openTargetId));
    }

    body.classList.add('eco-search-open');
    el.classList.add('is-open');
    el.setAttribute('aria-hidden', 'false');
    setExpanded(btn, true);

    openTargetId = targetId;

    const input = el.querySelector('input[name="s"]');
    if (input) setTimeout(() => input.focus(), 30);
  }

  function closeSearch(targetId, btn) {
    const el = getContainer(targetId);
    if (!el) return;

    el.classList.remove('is-open');
    el.setAttribute('aria-hidden', 'true');
    setExpanded(btn, false);

    // If this was the open one, clear state
    if (openTargetId === targetId) {
      openTargetId = null;
    }

    // If none is open, remove body class
    if (!openTargetId) {
      body.classList.remove('eco-search-open');
    }
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

    // Click outside to close currently open
    if (openTargetId) {
      const el = getContainer(openTargetId);
      if (el && el.classList.contains('is-open')) {
        const insideBar = e.target.closest('#' + CSS.escape(openTargetId));
        const insideToggle = e.target.closest('[data-eco-search-toggle="1"]');
        if (!insideBar && !insideToggle) {
          closeSearch(openTargetId, findToggleButton(openTargetId));
        }
      }
    }
  });

  // Quick topic pills in header searchbar
  document.addEventListener('click', function (e) {
    const pill = e.target.closest('.eco-topic-pill[data-eco-topic]');
    if (!pill) return;

    const container = pill.closest('[data-eco-search-container="1"]');
    if (!container) return;

    const topic = pill.getAttribute('data-eco-topic') || '';
    const hidden = container.querySelector('input[name="topic"]');
    const form = container.querySelector('form');

    if (hidden) {
      // Toggle off if clicking active again
      if (pill.classList.contains('is-active')) {
        hidden.value = '';
      } else {
        hidden.value = topic;
      }
    }

    // Update active state UI
    container.querySelectorAll('.eco-topic-pill').forEach(btn => btn.classList.remove('is-active'));
    if (hidden && hidden.value) pill.classList.add('is-active');

    // Submit immediately (quick select behavior)
    if (form) form.submit();
  });


  // ESC closes currently open
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (!openTargetId) return;
    closeSearch(openTargetId, findToggleButton(openTargetId));
  });

  // Close on submit (nice UX)
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!openTargetId) return;
    const el = getContainer(openTargetId);
    if (!el) return;
    if (form && el.contains(form)) {
      closeSearch(openTargetId, findToggleButton(openTargetId));
    }
  });
})();
