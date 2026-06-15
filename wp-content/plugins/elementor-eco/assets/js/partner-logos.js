(function () {
	'use strict';

	function getTabletMaxWidth() {
		var breakpoints = window.elementorFrontend &&
			window.elementorFrontend.config &&
			window.elementorFrontend.config.responsive &&
			window.elementorFrontend.config.responsive.activeBreakpoints;

		if (breakpoints && breakpoints.tablet && breakpoints.tablet.value) {
			return parseInt(breakpoints.tablet.value, 10);
		}

		return 1024;
	}

	function getColumns(grid) {
		var value = getComputedStyle(grid).getPropertyValue('--eco-partner-columns');
		return Math.max(1, parseInt(value, 10) || 1);
	}

	function init(scope) {
		var root = scope instanceof Element ? scope : document;
		var grid = root.querySelector('.eco-partner-logos');

		if (!grid || grid.dataset.readMore !== 'yes') {
			return;
		}

		var widget = grid.closest('.elementor-widget-eco-partner-logos') || grid.parentElement;
		var button = widget ? widget.querySelector('.eco-partner-logos__toggle') : null;
		var items = Array.prototype.slice.call(grid.querySelectorAll('.eco-partner-logos__item'));

		if (!button || !items.length) {
			return;
		}

		var expanded = false;

		function update() {
			var isTabletOrMobile = window.matchMedia('(max-width: ' + getTabletMaxWidth() + 'px)').matches;
			var columns = getColumns(grid);
			var visibleLimit = columns * 2;
			var needsToggle = isTabletOrMobile && items.length > visibleLimit;

			if (!needsToggle) {
				expanded = false;
				items.forEach(function (item) {
					item.hidden = false;
				});
				button.hidden = true;
				button.setAttribute('aria-expanded', 'false');
				return;
			}

			items.forEach(function (item, index) {
				item.hidden = !expanded && index >= visibleLimit;
			});

			button.hidden = false;
			button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			button.textContent = expanded
				? grid.dataset.showLessLabel
				: grid.dataset.readMoreLabel;
		}

		if (button.dataset.ecoPartnerLogosBound !== 'yes') {
			button.dataset.ecoPartnerLogosBound = 'yes';
			button.addEventListener('click', function () {
				expanded = !expanded;
				update();
			});
		}

		var resizeTimer;
		window.addEventListener('resize', function () {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(update, 100);
		});

		update();
	}

	window.addEventListener('elementor/frontend/init', function () {
		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/eco-partner-logos.default',
			function ($scope) {
				init($scope[0]);
			}
		);
	});

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.elementor-widget-eco-partner-logos').forEach(init);
	});
})();
