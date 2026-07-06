(function () {
	'use strict';

	function serialize(data) {
		var params = new URLSearchParams();
		Object.keys(data).forEach(function (key) {
			params.append(key, data[key]);
		});
		return params;
	}

	function getAjaxUrl() {
		if (window.elementorEcoEventCalendar && window.elementorEcoEventCalendar.ajaxUrl) {
			return window.elementorEcoEventCalendar.ajaxUrl;
		}

		if (window.ajaxurl) {
			return window.ajaxurl;
		}

		return '/wp-admin/admin-ajax.php';
	}

	function loadCalendar(root, options) {
		if (!root || root.classList.contains('is-loading')) return;

		var year = parseInt(root.getAttribute('data-year'), 10) || new Date().getFullYear();
		var month = parseInt(root.getAttribute('data-month'), 10) || (new Date().getMonth() + 1);
		var day = parseInt(root.getAttribute('data-day'), 10) || 0;
		var source = root.getAttribute('data-source') || 'all';
		var page = parseInt(root.getAttribute('data-page'), 10) || 1;

		options = options || {};

		if (options.direction === 'prev') {
			month -= 1;
			if (month < 1) {
				month = 12;
				year -= 1;
			}
			day = 0;
			page = 1;
		}

		if (options.direction === 'next') {
			month += 1;
			if (month > 12) {
				month = 1;
				year += 1;
			}
			day = 0;
			page = 1;
		}

		if (Object.prototype.hasOwnProperty.call(options, 'day')) {
			day = parseInt(options.day, 10) || 0;
			page = 1;
		}

		if (Object.prototype.hasOwnProperty.call(options, 'source')) {
			source = options.source || 'all';
			day = 0;
			page = 1;
		}

		if (options.loadMore) {
			page += 1;
		}

		root.classList.add('is-loading');

		fetch(getAjaxUrl(), {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: serialize({
				action: 'eco_event_calendar_load',
				nonce: root.getAttribute('data-nonce') || '',
				settings: root.getAttribute('data-settings') || '{}',
				year: year,
				month: month,
				day: day,
				source: source,
				page: page
			})
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (json) {
				if (!json || !json.success || !json.data) {
					throw new Error('Invalid response');
				}

				var calendar = root.querySelector('.eco-event-calendar__calendar');
				var cards = root.querySelector('.eco-event-calendar__cards');
				var loadMoreWrap = root.querySelector('.eco-event-calendar__load-more-wrap');

				if (calendar && !options.loadMore) {
					calendar.innerHTML = json.data.calendar_html || '';
				}

				if (cards) {
					if (options.loadMore) {
						cards.insertAdjacentHTML('beforeend', json.data.cards_html || '');
					} else {
						cards.innerHTML = json.data.cards_html || '';
					}
				}

				if (loadMoreWrap) {
					loadMoreWrap.hidden = !json.data.has_more;
				}

				root.setAttribute('data-year', year);
				root.setAttribute('data-month', month);
				root.setAttribute('data-day', day);
				root.setAttribute('data-source', source);
				root.setAttribute('data-page', page);
			})
			.catch(function (error) {
				console.error('eco Event Calendar:', error);
			})
			.finally(function () {
				root.classList.remove('is-loading');
			});
	}

	function init(root) {
		if (!root || root.dataset.ecoEventCalendarInit === 'yes') return;
		root.dataset.ecoEventCalendarInit = 'yes';

		root.addEventListener('click', function (event) {
			var nav = event.target.closest('.eco-event-calendar__nav');
			if (nav && root.contains(nav)) {
				event.preventDefault();
				loadCalendar(root, { direction: nav.getAttribute('data-direction') });
				return;
			}

			var day = event.target.closest('.eco-event-calendar__day');
			if (day && root.contains(day) && !day.classList.contains('eco-event-calendar__day--blank')) {
				event.preventDefault();
				var selectedDay = parseInt(day.getAttribute('data-day'), 10) || 0;
				var currentDay = parseInt(root.getAttribute('data-day'), 10) || 0;
				loadCalendar(root, { day: selectedDay === currentDay ? 0 : selectedDay });
				return;
			}

			var loadMore = event.target.closest('.eco-event-calendar__load-more');
			if (loadMore && root.contains(loadMore)) {
				event.preventDefault();
				loadCalendar(root, { loadMore: true });
			}
		});

		var select = root.querySelector('.eco-event-calendar__filter-select');
		if (select) {
			select.addEventListener('change', function () {
				loadCalendar(root, { source: select.value || 'all' });
			});
		}
	}

	function initAll(scope) {
		(scope || document).querySelectorAll('.eco-event-calendar').forEach(init);
	}

	document.addEventListener('DOMContentLoaded', function () {
		initAll(document);
	});

	window.addEventListener('elementor/frontend/init', function () {
		if (window.elementorFrontend && window.elementorFrontend.hooks) {
			window.elementorFrontend.hooks.addAction('frontend/element_ready/eco-event-calendar.default', function ($scope) {
				var scope = $scope && $scope[0] ? $scope[0] : document;
				initAll(scope);
			});
		}
	});
})();
