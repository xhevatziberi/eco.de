(function () {
	'use strict';

	function init(root) {
		if (!root || typeof Swiper === 'undefined') return;

		const swiperElement = root.querySelector('.eco-member-carousel__swiper');
		if (!swiperElement) return;

		if (swiperElement.swiper) {
			swiperElement.swiper.destroy(true, true);
		}

		const columns = Math.max(1, parseInt(root.dataset.columns || '5', 10));
		const columnsTablet = Math.max(1, parseInt(root.dataset.columnsTablet || '3', 10));
		const columnsMobile = Math.max(1, parseInt(root.dataset.columnsMobile || '2', 10));
		const space = Math.max(0, parseInt(root.dataset.space || '16', 10));
		const spaceTablet = Math.max(0, parseInt(root.dataset.spaceTablet || String(space), 10));
		const spaceMobile = Math.max(0, parseInt(root.dataset.spaceMobile || String(spaceTablet), 10));
		const useAutoplay = root.dataset.autoplay === '1';

		const options = {
			slidesPerView: columnsMobile,
			spaceBetween: spaceMobile,
			watchOverflow: true,
			loop: root.dataset.loop === '1',
			breakpoints: {
				768: {
					slidesPerView: columnsTablet,
					spaceBetween: spaceTablet
				},
				1025: {
					slidesPerView: columns,
					spaceBetween: space
				}
			}
		};

		if (root.dataset.arrows === '1') {
			options.navigation = {
				prevEl: root.querySelector('.eco-member-carousel__prev'),
				nextEl: root.querySelector('.eco-member-carousel__next')
			};
		}

		if (root.dataset.dots === '1') {
			options.pagination = {
				el: root.querySelector('.eco-member-carousel__pagination'),
				clickable: true
			};
		}

		if (useAutoplay) {
			options.autoplay = {
				delay: Math.max(1000, parseInt(root.dataset.autoplayDelay || '4000', 10)),
				disableOnInteraction: false,
				pauseOnMouseEnter: root.dataset.pauseOnHover === '1'
			};
		}

		new Swiper(swiperElement, options);
	}

	function initAll(scope) {
		(scope || document).querySelectorAll('.eco-member-carousel').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', function () {
		initAll(document);
	});

	window.addEventListener('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !elementorFrontend.hooks) return;

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/eco-member-carousel.default',
			function ($scope) {
				initAll($scope[0]);
			}
		);
	});
})();
