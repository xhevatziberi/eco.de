(function ($) {
	'use strict';

	$(document).on('click', '.eco-content-cards__load-more', function () {
		const $button = $(this);
		const $widget = $button.closest('.eco-content-cards');
		const $grid = $widget.find('.eco-content-cards__grid');

		if ($widget.hasClass('is-loading')) {
			return;
		}

		const currentPage = parseInt($widget.attr('data-page') || '1', 10);
		const nextPage = currentPage + 1;
		const settings = $widget.attr('data-settings') || '{}';

		$widget.addClass('is-loading');

		$.ajax({
			url: ecoContentCards.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'eco_content_cards_load_more',
				nonce: ecoContentCards.nonce,
				page: nextPage,
				settings: settings
			}
		})
			.done(function (response) {
				if (!response || !response.success || !response.data) {
					return;
				}

				if (response.data.html) {
					const $items = $(response.data.html);

					$items.addClass('is-ajax-new');
					$grid.append($items);
					$widget.attr('data-page', nextPage);
				}

				if (!response.data.has_more) {
					$button.closest('.eco-content-cards__load-more-wrap').remove();
				}
			})
			.fail(function () {
				console.warn('eco Content Cards: Ajax load more failed.');
			})
			.always(function () {
				$widget.removeClass('is-loading');
			});
	});

})(jQuery);