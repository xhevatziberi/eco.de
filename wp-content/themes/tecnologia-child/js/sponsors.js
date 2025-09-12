(function () {
  function initOne(root) {
    if (!root) return;
    var swiperEl = root.querySelector('.swiper');
    if (!swiperEl) return;

    var slides   = parseInt(root.dataset.slides || '4', 10);
    var space    = parseInt(root.dataset.space  || '16', 10);
    var autoplay = (root.dataset.autoplay || 'true') === 'true';
    var delay    = parseInt(root.dataset.delay || '2500', 10);
    var loop     = (root.dataset.loop    || 'true') === 'true';
    var arrows   = (root.dataset.arrows  || 'true') === 'true';
    var dots     = (root.dataset.dots    || 'false') === 'true';

    if (window.Swiper) {
      var opts = {
        slidesPerView: slides,
        spaceBetween: space,
        loop: loop,
        autoplay: autoplay ? { delay: delay, disableOnInteraction: false } : false,
        breakpoints: {
          0:    { slidesPerView: Math.min(2, slides), spaceBetween: Math.min(12, space) },
          480:  { slidesPerView: Math.max(2, Math.min(slides, 3)), spaceBetween: space },
          768:  { slidesPerView: Math.max(3, Math.min(slides, 4)), spaceBetween: space },
          1024: { slidesPerView: slides, spaceBetween: space }
        }
      };

      if (dots) {
        opts.pagination = { el: root.querySelector('.swiper-pagination'), clickable: true };
        root.classList.add('eco-show-dots');
      }
      if (arrows) {
        opts.navigation = {
          nextEl: root.querySelector('.swiper-button-next'),
          prevEl: root.querySelector('.swiper-button-prev')
        };
        root.classList.add('eco-show-arrows');
      }

      try {
        // eslint-disable-next-line no-new
        new Swiper(swiperEl, opts);
        root.classList.add('swiper-ready');
      } catch (e) {
        // If Swiper throws, do nothing—fallback grid remains
        // console.warn('Swiper init failed:', e);
      }
    }
  }

  function initAll() {
    document.querySelectorAll('.eco-sponsors-wrap').forEach(initOne);
  }

  // Run when DOM is ready
  if (document.readyState !== 'loading') initAll();
  else document.addEventListener('DOMContentLoaded', initAll);

  // If Elementor loads things async, try again on its hooks (if present)
  if (window.elementorFrontend && typeof window.elementorFrontend.hooks?.addAction === 'function') {
    elementorFrontend.hooks.addAction('frontend/element_ready/global', initAll);
    elementorFrontend.hooks.addAction('frontend/element_ready/widget', initAll);
  } else if (window.elementorFrontend && elementorFrontend.on) {
    // Older API
    elementorFrontend.on('frontend:init', initAll);
    elementorFrontend.on('components:init', initAll);
  }
})();
