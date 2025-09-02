document.addEventListener('DOMContentLoaded', () => {
	const buttons = document.querySelectorAll('.eco-podcast-play');
	let currentAudio = null;
	let currentButton = null;

	buttons.forEach(button => {
		button.addEventListener('click', () => {
			const audioSrc = button.dataset.audio;

			// Pause and reset previously playing audio
			if (currentAudio && currentAudio !== button.audioElement) {
				currentAudio.pause();
				currentAudio.currentTime = 0;
				currentButton.innerHTML = '<i class="fas fa-play"></i>';
			}

			if (!button.audioElement) {
				const audio = new Audio(audioSrc);
				button.audioElement = audio;

				audio.addEventListener('ended', () => {
					button.innerHTML = '<i class="fas fa-play"></i>';
				});
			}

			currentAudio = button.audioElement;
			currentButton = button;

			if (button.audioElement.paused) {
				button.audioElement.play();
				button.innerHTML = '<i class="fas fa-pause"></i>';
			} else {
				button.audioElement.pause();
				button.innerHTML = '<i class="fas fa-play"></i>';
			}
		});
	});
});