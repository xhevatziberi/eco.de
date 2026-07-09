(function () {
	function initEcoFeaturedSliderV2(scope) {
		const root = scope || document;
		const sliders = root.querySelectorAll('.eco-featured-slider-v2:not([data-eco-featured-slider-v2-ready="yes"])');

		if (!sliders.length) {
			return;
		}

		sliders.forEach((slider) => {
			slider.dataset.ecoFeaturedSliderV2Ready = 'yes';

			const panels = Array.from(slider.querySelectorAll('.eco-featured-slider-v2__panel'));
			const dots = Array.from(slider.querySelectorAll('.eco-featured-slider-v2__dot'));
			const autoplay = slider.dataset.autoplay === 'yes';
			const interval = Math.max(1500, parseInt(slider.dataset.interval || '6000', 10));

			if (!panels.length) {
				return;
			}

			let activeIndex = Math.max(0, panels.findIndex((panel) => panel.classList.contains('is-active')));
			let timer = null;

			function goTo(index) {
				if (index < 0 || index >= panels.length) {
					return;
				}

				activeIndex = index;

				panels.forEach((panel, panelIndex) => {
					panel.classList.toggle('is-active', panelIndex === activeIndex);
				});

				dots.forEach((dot, dotIndex) => {
					dot.classList.toggle('is-active', dotIndex === activeIndex);
					dot.setAttribute('aria-current', dotIndex === activeIndex ? 'true' : 'false');
				});
			}

			function next() {
				const nextIndex = activeIndex + 1 >= panels.length ? 0 : activeIndex + 1;
				goTo(nextIndex);
			}

			function stop() {
				if (timer) {
					window.clearInterval(timer);
					timer = null;
				}
			}

			function start() {
				if (!autoplay || panels.length <= 1) {
					return;
				}

				stop();
				timer = window.setInterval(next, interval);
			}

			dots.forEach((dot) => {
				dot.addEventListener('click', () => {
					const index = parseInt(dot.dataset.index || '0', 10);
					goTo(index);
					start();
				});
			});

			slider.addEventListener('mouseenter', stop);
			slider.addEventListener('mouseleave', start);
			slider.addEventListener('focusin', stop);
			slider.addEventListener('focusout', start);

			goTo(activeIndex);
			start();
		});
	}

	document.addEventListener('DOMContentLoaded', () => initEcoFeaturedSliderV2(document));

	window.ecoFeaturedSliderV2Init = initEcoFeaturedSliderV2;

	if (window.jQuery && window.elementorFrontend) {
		window.jQuery(window).on('elementor/frontend/init', () => {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/eco-featured-slider-v2.default', ($scope) => {
				const node = $scope && $scope[0] ? $scope[0] : document;
				initEcoFeaturedSliderV2(node);
			});
		});
	}
})();
