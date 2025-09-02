document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll('.eco-podcast-player').forEach(player => {
		const audio = new Audio(player.dataset.audio);
		const playBtn = player.querySelector('.play-btn');
		const currentTimeEl = player.querySelector('.time.current');
		const durationEl = player.querySelector('.time.duration');
		const progressBar = player.querySelector('.progress-bar');
		const progress = player.querySelector('.progress');

		let isPlaying = false;

		playBtn.addEventListener('click', () => {
            if (isPlaying) {
                audio.pause();
                playBtn.innerHTML = '<i class="fas fa-play"></i>';
            } else {
                audio.play();
                playBtn.innerHTML = '<i class="fas fa-pause"></i>';
            }
            isPlaying = !isPlaying;
        });


		audio.addEventListener('loadedmetadata', () => {
			durationEl.textContent = formatTime(audio.duration);
		});

		audio.addEventListener('timeupdate', () => {
			currentTimeEl.textContent = formatTime(audio.currentTime);
			progress.style.width = `${(audio.currentTime / audio.duration) * 100}%`;
		});

		// ✅ CLICKABLE PROGRESS BAR
		progressBar.addEventListener('click', (e) => {
			const rect = progressBar.getBoundingClientRect();
			const clickX = e.clientX - rect.left;
			const width = rect.width;
			const percent = clickX / width;
			audio.currentTime = percent * audio.duration;
		});

		function formatTime(t) {
			const m = Math.floor(t / 60);
			const s = Math.floor(t % 60);
			return `${m}:${s.toString().padStart(2, '0')}`;
		}
	});
});
