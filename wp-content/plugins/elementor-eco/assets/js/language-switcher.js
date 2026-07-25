(function () {
	'use strict';

	function closeSwitcher(switcher) {
		var trigger = switcher.querySelector('.eco-language-switcher__trigger');
		var dropdown = switcher.querySelector('.eco-language-switcher__dropdown');
		if (!trigger || !dropdown) return;
		switcher.classList.remove('is-open');
		trigger.setAttribute('aria-expanded', 'false');
		dropdown.hidden = true;
	}

	function closeAll(except) {
		document.querySelectorAll('.eco-language-switcher.is-open').forEach(function (item) {
			if (item !== except) closeSwitcher(item);
		});
	}

	function initSwitcher(switcher) {
		if (!switcher || switcher.dataset.ecoLanguageSwitcherReady === '1') return;

		var trigger = switcher.querySelector('.eco-language-switcher__trigger');
		var dropdown = switcher.querySelector('.eco-language-switcher__dropdown');
		var select = switcher.querySelector('.eco-language-switcher__native-select');
		if (!trigger || !dropdown || !select) return;

		switcher.dataset.ecoLanguageSwitcherReady = '1';

		var breakpoint = parseInt(switcher.dataset.mobileBreakpoint || '767', 10);
		var mediaQuery = window.matchMedia('(max-width: ' + breakpoint + 'px)');
		var updateMode = function () {
			switcher.classList.toggle('is-native-mode', mediaQuery.matches);
			if (mediaQuery.matches) closeSwitcher(switcher);
		};
		updateMode();
		if (mediaQuery.addEventListener) mediaQuery.addEventListener('change', updateMode);
		else mediaQuery.addListener(updateMode);

		trigger.addEventListener('click', function () {
			if (mediaQuery.matches) {
				select.focus();
				select.click();
				return;
			}

			var willOpen = !switcher.classList.contains('is-open');
			closeAll(switcher);
			switcher.classList.toggle('is-open', willOpen);
			trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
			dropdown.hidden = !willOpen;
		});

		select.addEventListener('change', function () {
			if (this.value) window.location.href = this.value;
		});

		switcher.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeSwitcher(switcher);
				trigger.focus();
			}
		});
	}

	function initAll(scope) {
		(scope || document).querySelectorAll('.eco-language-switcher').forEach(initSwitcher);
	}

	document.addEventListener('click', function (event) {
		if (!event.target.closest('.eco-language-switcher')) closeAll();
	});

	document.addEventListener('DOMContentLoaded', function () {
		initAll(document);
	});

	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/eco-language-switcher.default', function ($scope) {
				initAll($scope[0]);
			});
		}
	});
})();
