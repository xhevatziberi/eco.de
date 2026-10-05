(function () {
	function initAgendaTabs(root) {
		var buttons = root.querySelectorAll('[data-eco-agenda-tab]');
		if (!buttons.length) return;

		buttons.forEach(function (button) {
			button.addEventListener('click', function () {
				var targetId = button.getAttribute('data-eco-agenda-tab');
				var panel = targetId ? root.querySelector('#' + CSS.escape(targetId)) : null;
				if (!panel) return;

				buttons.forEach(function (item) {
					item.classList.remove('is-active');
					item.setAttribute('aria-selected', 'false');
				});

				root.querySelectorAll('.eco-event-agenda-panel').forEach(function (item) {
					item.classList.remove('is-active');
					item.hidden = true;
				});

				button.classList.add('is-active');
				button.setAttribute('aria-selected', 'true');
				panel.hidden = false;
				panel.classList.add('is-active');
			});
		});
	}

	function initSpeakerToggle(root) {
		var button = root.querySelector('[data-eco-speakers-toggle]');
		if (!button) return;

		var extraSpeakers = root.querySelectorAll('.eco-event-person--extra');
		if (!extraSpeakers.length) return;

		button.addEventListener('click', function () {
			var willExpand = button.getAttribute('aria-expanded') !== 'true';

			extraSpeakers.forEach(function (speaker) {
				speaker.hidden = !willExpand;
			});

			button.setAttribute('aria-expanded', willExpand ? 'true' : 'false');
			button.textContent = willExpand
				? button.getAttribute('data-less-label')
				: button.getAttribute('data-more-label');
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.eco-event-agenda').forEach(initAgendaTabs);
		document.querySelectorAll('.eco-event-people--speakers').forEach(initSpeakerToggle);
	});
})();
