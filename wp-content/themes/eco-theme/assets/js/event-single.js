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

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.eco-event-agenda').forEach(initAgendaTabs);
	});
})();
