(function( $ ) {
	'use strict';

	$(document).ready(function(){
		if($('.flyout-menus')[0]) {
			$('.flyout-menus .djan a').on('click', function(event){
				event.preventDefault();
			})

			$('.flyout-menus .djan span').on('click', function(){
				var spanLink = $(this).attr('href');

				$(location).attr('href', spanLink);
			})
		}
	});

	$(document).ready(function() {
		if($('.menu-item-has-children')[0]) {
			$('.flyout-menus .djan a').on('click', function(){
				var iconIco = $(this).find('i'),
					htmlImg = iconIco.html(),
					theIcon = iconIco.attr('class');

				if($('.icon-display i').hasClass("act")) {
					$('.icon-display i').empty();
					$('.icon-display i').removeClass();

					if(theIcon != undefined) {
						$('.icon-display i').addClass(theIcon);
					} else {
						$('.icon-display i').append(htmlImg);
					}
					$('.icon-display i').addClass('act');
				}
				else {
					if(theIcon != undefined) {
						$('.icon-display i').addClass(theIcon);
					} else {
						$('.icon-display i').append(htmlImg);
					}
					$('.icon-display i').addClass('act');
				}
			});
			$('#mp-pusher').on('click', function(){
				$('.icon-display i').removeClass();
				$('.icon-display i').empty();
			});
		}

		// in another js file, far, far away
		if($('.fly-style-genep')[0]) {
			$('.low.menu-item-has-children .djan, .low.has-sub-menu .djan').on('click', function() {
			    $(this).parent().toggleClass('active-menu');
			});

			$('#mp-pusher').on('click', function(){
				$('.low').removeClass('active-menu');
			});
		}


		if($('.flyout-style5')[0]) {
			$('#trigger').on('click', function () {
				$('#flyout-overlay-wrap').css('visibility', 'visible');
			});

			$('#flyout-overlay-wrap').on('click', function () {
				$('.flyout-style5').removeClass('active');
				$('#flyout-overlay-wrap').css('visibility', 'hidden');
				$('.low').removeClass('active-menu');
				$('.mp-level').removeClass('active');
			});

			$('.close-menu-wrapper').on('click', function () {
				$('.flyout-style5').removeClass('active');
				$('#flyout-overlay-wrap').css('visibility', 'hidden');
			});
		}

		$(document).ready(function() {
			if($('.menu-item-has-children')[0]) {
				$('.flyout-menus .menu-item-has-children > .djan a').on('click', function() {
					$(this).parent().siblings('.mp-level').toggleClass('active');
					$(this).parent().parent().toggleClass('active-menu');
				});
			}
			
			if($('.has-sub-menu')[0]) {
				$('.flyout-menus .has-sub-menu > .djan a').on('click', function() {
					$(this).parent().siblings('.mp-level').toggleClass('active');
					$(this).parent().parent().toggleClass('active-menu');
				});
			}
		});
	});

})( jQuery );