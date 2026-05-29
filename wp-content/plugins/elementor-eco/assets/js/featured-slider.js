document.addEventListener('DOMContentLoaded', () => {
	const sliders = document.querySelectorAll('.eco-featured-slider');

	if (!sliders.length) {
		return;
	}

	sliders.forEach((slider) => {
		const panels = Array.from(slider.querySelectorAll('.eco-featured-slider__panel'));
		const dots = Array.from(slider.querySelectorAll('.eco-featured-slider__dot'));
		const autoplay = slider.dataset.autoplay === 'yes';
		const interval = Math.max(1500, parseInt(slider.dataset.interval || '6000', 10));

		if (panels.length <= 1) {
			return;
		}

		let activeIndex = 0;
		let timer = null;

		function goTo(index) {
			activeIndex = index;

			panels.forEach((panel, panelIndex) => {
				panel.classList.toggle('is-active', panelIndex === activeIndex);
			});

			dots.forEach((dot, dotIndex) => {
				dot.classList.toggle('is-active', dotIndex === activeIndex);
			});
		}

		function next() {
			const nextIndex = activeIndex + 1 >= panels.length ? 0 : activeIndex + 1;
			goTo(nextIndex);
		}

		function start() {
			if (!autoplay) {
				return;
			}

			stop();
			timer = window.setInterval(next, interval);
		}

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
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

		start();
	});
});