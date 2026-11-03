document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.eco-podcast-rss').forEach(root => {
		const buttons = root.querySelectorAll('.eco-podcast-play');

		if (!buttons.length) {
			return;
		}

		// Keep one reusable Audio instance per RSS widget.
		// This avoids leaving multiple MP3 range requests open when switching episodes.
		const audio = new Audio();
		audio.preload = 'none';

		let currentButton = null;
		let currentSrc = '';

		const setButtonState = (button, state) => {
			if (!button) {
				return;
			}

			button.innerHTML = state === 'pause'
				? '<i class="fas fa-pause"></i>'
				: '<i class="fas fa-play"></i>';
		};

		const resetCurrentButton = () => {
			setButtonState(currentButton, 'play');
		};

		audio.addEventListener('ended', () => {
			resetCurrentButton();
			audio.currentTime = 0;
		});

		audio.addEventListener('error', () => {
			resetCurrentButton();

			// Release a failed source so the next click starts with a clean request.
			audio.removeAttribute('src');
			audio.load();

			currentButton = null;
			currentSrc = '';
		});

		buttons.forEach(button => {
			button.addEventListener('click', async () => {
				const audioSrc = button.dataset.audio;

				if (!audioSrc) {
					return;
				}

				const isCurrentEpisode = currentButton === button && currentSrc === audioSrc;

				if (isCurrentEpisode && !audio.paused) {
					audio.pause();
					setButtonState(button, 'play');
					return;
				}

				if (!isCurrentEpisode) {
					audio.pause();
					resetCurrentButton();

					currentButton = button;
					currentSrc = audioSrc;

					// Replacing the source and calling load() aborts the previous
					// media request before starting the newly selected episode.
					audio.src = audioSrc;
					audio.load();
				}

				try {
					await audio.play();

					if (currentButton === button && currentSrc === audioSrc) {
						setButtonState(button, 'pause');
					}
				} catch (error) {
					if (currentButton === button && currentSrc === audioSrc) {
						setButtonState(button, 'play');
					}

					// AbortError is expected when the user quickly switches episodes.
					if (!error || error.name !== 'AbortError') {
						console.warn('Podcast playback failed.', error);
					}
				}
			});
		});
	});
});
