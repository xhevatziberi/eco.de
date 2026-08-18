document.addEventListener('DOMContentLoaded', () => {
	const grids = document.querySelectorAll('.eco-logo-grid');

	if (!grids.length) {
		return;
	}

	grids.forEach((grid) => {
		let logos = [];

		try {
			logos = JSON.parse(grid.dataset.logos || '[]');
		} catch (error) {
			logos = [];
		}

		if (!Array.isArray(logos) || !logos.length) {
			return;
		}

		const rows = Math.max(1, parseInt(grid.dataset.rows || '3', 10));
		const interval = Math.max(1500, parseInt(grid.dataset.interval || '6000', 10));
		const duration = Math.max(100, parseInt(grid.dataset.duration || '550', 10));
		const openNewTab = grid.dataset.openNewTab === 'yes';
		const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		const stagger = reducedMotion ? 0 : 45;
		const effectiveDuration = reducedMotion ? 0 : duration;

		let pool = shuffle(logos);
		let pointer = 0;
		let currentDisplayCount = 0;
		let isAnimating = false;
		let resizeTimer = null;

		grid.style.setProperty('--eco-logo-grid-duration', `${duration}ms`);
		grid.style.setProperty('--eco-logo-grid-rows', rows);

		function shuffle(array) {
			const copied = [...array];

			for (let i = copied.length - 1; i > 0; i--) {
				const j = Math.floor(Math.random() * (i + 1));
				[copied[i], copied[j]] = [copied[j], copied[i]];
			}

			return copied;
		}

		function getActiveColumns() {
			const styles = window.getComputedStyle(grid);
			const value = parseInt(styles.getPropertyValue('--eco-logo-grid-columns'), 10);

			return Number.isFinite(value) && value > 0 ? value : 1;
		}

		function getDisplayCount() {
			return Math.max(1, getActiveColumns() * rows);
		}

		function takeNextLogos(count) {
			const next = [];

			while (next.length < count && logos.length) {
				if (pointer >= pool.length) {
					pool = shuffle(logos);
					pointer = 0;
				}

				next.push(pool[pointer]);
				pointer += 1;
			}

			return next;
		}

		function createLogoItem(item) {
			const hasUrl = item.url && item.url !== '#';
			const element = document.createElement(hasUrl ? 'a' : 'div');
			const img = document.createElement('img');

			element.className = 'eco-logo-grid__item';

			if (hasUrl) {
				element.href = item.url;

				if (openNewTab) {
					element.target = '_blank';
					element.rel = 'noopener';
				}
			}

			img.className = 'eco-logo-grid__logo';
			img.src = item.logo;
			img.alt = item.title || '';
			img.decoding = 'async';

			element.appendChild(img);

			return element;
		}

		function ensureSlots(count) {
			const slots = Array.from(grid.querySelectorAll('.eco-logo-grid__slot'));

			if (slots.length < count) {
				for (let i = slots.length; i < count; i++) {
					const slot = document.createElement('div');
					slot.className = 'eco-logo-grid__slot';
					grid.appendChild(slot);
				}
			} else if (slots.length > count) {
				for (let i = slots.length - 1; i >= count; i--) {
					slots[i].remove();
				}
			}
		}

		function showItem(item) {
			if (reducedMotion) {
				item.classList.add('is-visible');
				return;
			}

			requestAnimationFrame(() => {
				requestAnimationFrame(() => {
					item.classList.add('is-visible');
				});
			});
		}

		function populateInitialSet() {
			const count = getDisplayCount();
			const nextSet = takeNextLogos(count);

			ensureSlots(count);
			currentDisplayCount = count;

			const slots = grid.querySelectorAll('.eco-logo-grid__slot');

			slots.forEach((slot, index) => {
				const itemData = nextSet[index];

				if (!itemData) {
					return;
				}

				const item = createLogoItem(itemData);
				slot.replaceChildren(item);

				setTimeout(() => showItem(item), stagger * index);
			});
		}

		function rotateSet() {
			if (isAnimating) {
				return;
			}

			const count = getDisplayCount();

			if (count !== currentDisplayCount) {
				populateInitialSet();
				return;
			}

			if (logos.length <= count) {
				return;
			}

			isAnimating = true;

			const nextSet = takeNextLogos(count);
			const slots = grid.querySelectorAll('.eco-logo-grid__slot');
			let lastCompletion = 0;

			slots.forEach((slot, index) => {
				const currentItem = slot.querySelector('.eco-logo-grid__item');
				const itemData = nextSet[index];
				const delay = stagger * index;
				const completion = delay + effectiveDuration;

				lastCompletion = Math.max(lastCompletion, completion);

				setTimeout(() => {
					if (currentItem) {
						currentItem.classList.remove('is-visible');
						currentItem.classList.add('is-leaving');
					}
				}, delay);

				setTimeout(() => {
					if (!itemData) {
						slot.replaceChildren();
						return;
					}

					const nextItem = createLogoItem(itemData);
					slot.replaceChildren(nextItem);
					showItem(nextItem);
				}, completion);
			});

			setTimeout(() => {
				isAnimating = false;
			}, lastCompletion + 50);
		}

		populateInitialSet();

		if (logos.length > 1) {
			window.setInterval(rotateSet, interval);
		}

		window.addEventListener('resize', () => {
			window.clearTimeout(resizeTimer);

			resizeTimer = window.setTimeout(() => {
				const count = getDisplayCount();

				if (count !== currentDisplayCount) {
					populateInitialSet();
				}
			}, 150);
		});
	});
});
