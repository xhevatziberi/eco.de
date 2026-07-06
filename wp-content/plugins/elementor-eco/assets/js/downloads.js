(function ($) {
	'use strict';

	function getPageFromHref(href) {
		const fallback = 1;

		if (!href) {
			return fallback;
		}

		try {
			const url = new URL(href, window.location.href);

			for (const [key, value] of url.searchParams.entries()) {
				if (key.indexOf('eco_dl_page_') === 0) {
					return parseInt(value, 10) || fallback;
				}
			}
		} catch (error) {
			const match = href.match(/eco_dl_page_[^=]+=([0-9]+)/);

			if (match && match[1]) {
				return parseInt(match[1], 10) || fallback;
			}
		}

		return fallback;
	}

	function pushUrl($widget, page, filter) {
		if (!window.history || !window.history.replaceState) {
			return;
		}

		const href = window.location.href;
		const url = new URL(href);
		const widgetId = ($widget.attr('data-settings') || '').match(/"_widget_id":"([^\"]+)"/);
		const suffix = widgetId && widgetId[1] ? widgetId[1] : '';

		if (!suffix) {
			return;
		}

		const pageKey = 'eco_dl_page_' + suffix;
		const filterKey = 'eco_dl_filter_' + suffix;

		if (page && page > 1) {
			url.searchParams.set(pageKey, page);
		} else {
			url.searchParams.delete(pageKey);
		}

		if (filter) {
			url.searchParams.set(filterKey, filter);
		} else {
			url.searchParams.delete(filterKey);
		}

		window.history.replaceState({}, '', url.toString());
	}

	function scrollToWidgetTop($widget) {
		if (!$widget || !$widget.length) {
			return;
		}

		const offset = parseInt((window.ecoDownloads && ecoDownloads.scrollOffset) ? ecoDownloads.scrollOffset : 90, 10);
		const top = Math.max(0, $widget.offset().top - offset);

		$('html, body').stop(true).animate({
			scrollTop: top
		}, 300);
	}

	function loadDownloads($widget, page, filter, scrollToTop) {
		const settings = $widget.attr('data-settings') || '{}';
		const $results = $widget.find('.eco-downloads-results').first();
		const $list = $widget.find('.eco-downloads-list').first();

		if (!$results.length || !$list.length || $widget.hasClass('is-loading')) {
			return;
		}

		$widget.addClass('is-loading');

		$.ajax({
			url: ecoDownloads.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'eco_downloads_render',
				nonce: ecoDownloads.nonce,
				settings: settings,
				page: page,
				filter: filter || ''
			}
		})
			.done(function (response) {
				if (!response || !response.success || !response.data) {
					return;
				}

				$list.html(response.data.list_html || '');
				$widget.find('.eco-downloads-pagination').remove();

				if (response.data.pagination_html) {
					$list.after(response.data.pagination_html);
				}

				$widget.attr('data-page', response.data.page || page);
				$widget.attr('data-filter', response.data.active_filter || '');

				$widget.find('.eco-downloads-filter').removeClass('is-active');
				$widget
					.find('.eco-downloads-filter[data-filter="' + (response.data.active_filter || '') + '"]')
					.addClass('is-active');

				pushUrl($widget, response.data.page || page, response.data.active_filter || '');

				if (scrollToTop) {
					scrollToWidgetTop($widget);
				}
			})
			.fail(function () {
				if (window.console && console.warn) {
					console.warn(ecoDownloads.error || 'eco Downloads: Ajax request failed.');
				}
			})
			.always(function () {
				$widget.removeClass('is-loading');
			});
	}

	$(document).on('click', '.eco-downloads-widget .eco-downloads-pagination a', function (event) {
		event.preventDefault();

		const $link = $(this);
		const $widget = $link.closest('.eco-downloads-widget');
		const page = getPageFromHref($link.attr('href'));
		const filter = $widget.attr('data-filter') || '';

		loadDownloads($widget, page, filter, true);
	});

	$(document).on('click', '.eco-downloads-widget .eco-downloads-filter', function (event) {
		event.preventDefault();

		const $link = $(this);
		const $widget = $link.closest('.eco-downloads-widget');
		const filter = $link.attr('data-filter') || '';

		loadDownloads($widget, 1, filter, true);
	});

})(jQuery);
