(function () {
  function init(root) {
    if (!root) return;

    const mode = root.getAttribute('data-info-display') || 'modal';
    const modal = root.parentElement.querySelector('.eco-pv-modal');

    function openModal(html) {
      if (!modal) return;
      const content = modal.querySelector('.eco-pv-modal__content');
      content.innerHTML = html;

      modal.hidden = false;
      modal.setAttribute('aria-hidden', 'false');
      document.documentElement.classList.add('eco-pv-modal-open');
      document.body.classList.add('eco-pv-modal-open');
    }

    function closeModal() {
      if (!modal) return;
      modal.hidden = true;
      modal.setAttribute('aria-hidden', 'true');
      const content = modal.querySelector('.eco-pv-modal__content');
      content.innerHTML = '';
      document.documentElement.classList.remove('eco-pv-modal-open');
      document.body.classList.remove('eco-pv-modal-open');
    }

    if (modal) {
      modal.addEventListener('click', (e) => {
        if (
          e.target.classList.contains('eco-pv-modal__backdrop') ||
          e.target.classList.contains('eco-pv-modal__close')
        ) {
          e.preventDefault();
          closeModal();
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
      });
    }

    root.addEventListener('click', (e) => {
      const btn = e.target.closest('.eco-pv-toggle');
      if (!btn) return;

      e.preventDefault();

      const item = btn.closest('.eco-pv-item');
      if (!item) return;

      const info = item.querySelector('.eco-pv-info');
      if (!info) return;

      if (mode === 'expand') {
        const isOpen = !info.hidden;

        info.hidden = isOpen; // close if open, open if closed
        const willBeOpen = !isOpen;

        btn.setAttribute('aria-expanded', String(willBeOpen));
        item.classList.toggle('eco-pv-item--expanded', willBeOpen);

        // ✅ if any item is expanded, mark the whole widget as expanded
        const anyOpen = !!root.querySelector('.eco-pv-item--expanded');
        root.classList.toggle('eco-people-vertical--expanded', anyOpen);
      } else {
        openModal(info.innerHTML);
      }

    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.eco-people-vertical').forEach(init);
  });
})();
