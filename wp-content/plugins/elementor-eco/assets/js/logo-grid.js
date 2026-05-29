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

		const displayCount = Math.max(1, parseInt(grid.dataset.displayCount || '6', 10));
        const rows = Math.max(1, parseInt(grid.dataset.rows || '3', 10));
		const interval = Math.max(1500, parseInt(grid.dataset.interval || '6000', 10));
		const duration = Math.max(100, parseInt(grid.dataset.duration || '550', 10));
		const openNewTab = grid.dataset.openNewTab === 'yes';

		let pool = shuffle([...logos]);
		let pointer = 0;

		grid.style.setProperty('--eco-logo-grid-duration', `${duration}ms`);

        function updateGridMinHeight() {
            const firstItem = grid.querySelector('.eco-logo-grid__item');

            if (!firstItem) {
                return;
            }

            const styles = window.getComputedStyle(grid);
            const rowGap = parseFloat(styles.rowGap || styles.gap || 0);
            const itemHeight = firstItem.offsetHeight;

            const minHeight = (itemHeight * rows) + (rowGap * (rows - 1));

            grid.style.setProperty('--eco-logo-grid-min-height', `${minHeight}px`);
        }

		function shuffle(array) {
			const copied = [...array];

			for (let i = copied.length - 1; i > 0; i--) {
				const j = Math.floor(Math.random() * (i + 1));
				[copied[i], copied[j]] = [copied[j], copied[i]];
			}

			return copied;
		}

		function getNextSet() {
			if (pool.length <= displayCount) {
				return shuffle([...pool]).slice(0, displayCount);
			}

			if (pointer + displayCount > pool.length) {
				pool = shuffle([...logos]);
				pointer = 0;
			}

			const set = pool.slice(pointer, pointer + displayCount);
			pointer += displayCount;

			return set;
		}

		function createLogoItem(item, index) {
			const hasUrl = item.url && item.url !== '#';
			const element = document.createElement(hasUrl ? 'a' : 'div');

			element.className = 'eco-logo-grid__item';

			if (hasUrl) {
				element.href = item.url;

				if (openNewTab) {
					element.target = '_blank';
					element.rel = 'noopener';
				}
			}

			const img = document.createElement('img');

			img.className = 'eco-logo-grid__logo';
			img.src = item.logo;
			img.alt = item.title || '';
			img.loading = 'lazy';

			element.appendChild(img);

			setTimeout(() => {
				element.classList.add('is-visible');
			}, 70 * index);

			return element;
		}

		function renderSet() {
			const currentItems = grid.querySelectorAll('.eco-logo-grid__item');

			currentItems.forEach((item, index) => {
				setTimeout(() => {
					item.classList.remove('is-visible');
					item.classList.add('is-leaving');
				}, 45 * index);
			});

			const waitTime = currentItems.length ? duration + currentItems.length * 45 : 0;

			setTimeout(() => {
				const nextSet = getNextSet();

				grid.innerHTML = '';

				nextSet.forEach((item, index) => {
					grid.appendChild(createLogoItem(item, index));
				});

                requestAnimationFrame(updateGridMinHeight);
			}, waitTime);
		}

		renderSet();

		if (logos.length > displayCount) {
			setInterval(renderSet, interval);
		}

        window.addEventListener('resize', updateGridMinHeight);
	});
});