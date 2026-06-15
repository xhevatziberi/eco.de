(function () {
	'use strict';

	const SELECTOR = '.eco-testimonial-showcase';

	function parseSettings(root) {
		const raw = root.getAttribute('data-eco-testimonial-settings');

		if (!raw) {
			return {};
		}

		try {
			return JSON.parse(raw);
		} catch (error) {
			console.warn(
				'ECO Testimonial Showcase: invalid settings.',
				error
			);

			return {};
		}
	}

	function updateAutoLayout(root) {
		if (!root.classList.contains('eco-testimonial-showcase--auto')) {
			root.classList.remove('eco-testimonial-showcase--narrow');
			return;
		}

		root.classList.toggle(
			'eco-testimonial-showcase--narrow',
			root.getBoundingClientRect().width <= 720
		);
	}

	function buildConfig(root, settings) {
		const previousButton = root.querySelector(
			'.eco-testimonial-showcase__prev'
		);

		const nextButton = root.querySelector(
			'.eco-testimonial-showcase__next'
		);

		const pagination = root.querySelector(
			'.eco-testimonial-showcase__pagination'
		);

		const config = {
			slidesPerView: 1,
			spaceBetween: 0,
			speed: Number(settings.speed) || 550,
			loop: Boolean(settings.loop),
			autoHeight: true,
			observer: true,
			observeParents: true,
			watchOverflow: true,
			allowTouchMove: true,
			effect: 'fade',
			fadeEffect: {
				crossFade: true,
			},
			a11y: {
				enabled: true,
			},
		};

		if (settings.showArrows && previousButton && nextButton) {
			config.navigation = {
				prevEl: previousButton,
				nextEl: nextButton,
			};
		}

		if (settings.showDots && pagination) {
			config.pagination = {
				el: pagination,
				clickable: true,
			};
		}

		if (settings.autoplay) {
			config.autoplay = {
				delay: Number(settings.autoplayDelay) || 6000,
				disableOnInteraction: false,
				pauseOnMouseEnter: Boolean(settings.pauseOnHover),
			};
		}

		return config;
	}

	function isUsableInstance(instance) {
		return Boolean(
			instance &&
			!instance.destroyed &&
			typeof instance.update === 'function'
		);
	}

	function updateInstanceHeight(instance) {
		if (!isUsableInstance(instance)) {
			return;
		}

		instance.update();

		if (
			!instance.destroyed &&
			typeof instance.updateAutoHeight === 'function'
		) {
			instance.updateAutoHeight(0);
		}
	}

	function storeInstance(root, instance) {
		if (!instance) {
			root.ecoTestimonialShowcaseInitializing = false;
			return;
		}

		root.ecoTestimonialShowcaseSwiper = instance;
		root.ecoTestimonialShowcaseInitializing = false;

		root.querySelectorAll('img').forEach(function (image) {
			if (image.complete) {
				return;
			}

			image.addEventListener(
				'load',
				function () {
					if (
						instance.destroyed ||
						root.ecoTestimonialShowcaseSwiper !== instance
					) {
						return;
					}

					updateInstanceHeight(instance);
				},
				{ once: true }
			);
		});
	}

	function createSwiper(root, config) {
		const currentInstance =
			root.ecoTestimonialShowcaseSwiper;

		if (
			root.ecoTestimonialShowcaseInitializing ||
			(
				currentInstance &&
				!currentInstance.destroyed
			)
		) {
			return;
		}

		root.ecoTestimonialShowcaseInitializing = true;

		if (typeof window.Swiper !== 'undefined') {
			try {
				storeInstance(
					root,
					new window.Swiper(root, config)
				);
			} catch (error) {
				root.ecoTestimonialShowcaseInitializing = false;

				console.error(
					'ECO Testimonial Showcase: Swiper initialization failed.',
					error
				);
			}

			return;
		}

		if (
			typeof window.elementorFrontend === 'undefined' ||
			!window.elementorFrontend.utils ||
			!window.elementorFrontend.utils.swiper
		) {
			root.ecoTestimonialShowcaseInitializing = false;
			return;
		}

		const AsyncSwiper =
			window.elementorFrontend.utils.swiper;

		try {
			const result = new AsyncSwiper(root, config);

			if (result && typeof result.then === 'function') {
				result
					.then(function (instance) {
						storeInstance(root, instance);
					})
					.catch(function (error) {
						root.ecoTestimonialShowcaseInitializing = false;

						console.error(
							'ECO Testimonial Showcase: Swiper initialization failed.',
							error
						);
					});

				return;
			}

			storeInstance(root, result);
		} catch (error) {
			root.ecoTestimonialShowcaseInitializing = false;

			console.error(
				'ECO Testimonial Showcase: Swiper initialization failed.',
				error
			);
		}
	}

	function attachResizeObserver(root) {
		if (!('ResizeObserver' in window)) {
			return;
		}

		if (root.ecoTestimonialResizeObserver) {
			root.ecoTestimonialResizeObserver.disconnect();
		}

		root.ecoTestimonialResizeObserver =
			new ResizeObserver(function () {
				updateAutoLayout(root);

				const instance =
					root.ecoTestimonialShowcaseSwiper;

				if (!isUsableInstance(instance)) {
					return;
				}

				updateInstanceHeight(instance);
			});

		root.ecoTestimonialResizeObserver.observe(root);
	}

	function initWidget(root) {
		if (!root) {
			return;
		}

		const slides = root.querySelectorAll(
			'.eco-testimonial-showcase__slide'
		);

		if (!slides.length) {
			return;
		}

		updateAutoLayout(root);

		createSwiper(
			root,
			buildConfig(root, parseSettings(root))
		);

		attachResizeObserver(root);
	}

	function initAll(context) {
		const scope = context || document;

		scope.querySelectorAll(SELECTOR).forEach(initWidget);
	}

	function initElementor() {
		if (
			typeof window.elementorFrontend === 'undefined'
		) {
			return;
		}

		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/eco_testimonial_showcase.default',
			function ($scope) {
				const scopeElement =
					$scope && $scope[0] ? $scope[0] : $scope;

				if (!scopeElement) {
					return;
				}

				const root = scopeElement.matches &&
					scopeElement.matches(SELECTOR)
					? scopeElement
					: scopeElement.querySelector(SELECTOR);

				initWidget(root);
			}
		);
	}

	if (window.jQuery) {
		window.jQuery(window).on(
			'elementor/frontend/init',
			initElementor
		);
	}

	if (typeof window.elementorFrontend === 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener(
				'DOMContentLoaded',
				function () {
					initAll(document);
				}
			);
		} else {
			initAll(document);
		}
	}
})();
