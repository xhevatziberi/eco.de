document.addEventListener("DOMContentLoaded", () => {
	const cloud = document.querySelector(".eco-logo-cloud");
	if (!cloud) return;

	const logos = JSON.parse(cloud.dataset.logos);
	let index = 0;
	const displayCount = 8;

	function shuffle(array) {
		return array.sort(() => Math.random() - 0.5);
	}

	function displayLogos() {
		cloud.innerHTML = '';
		const currentSet = shuffle(logos).slice(0, displayCount);

		currentSet.forEach((logo, i) => {
			const img = document.createElement('img');
			img.src = logo;
			img.className = 'logo-fade-in';
			cloud.appendChild(img);
		});
	}

	displayLogos();

	setInterval(() => {
		const images = cloud.querySelectorAll('img');
		images.forEach((img, i) => {
			setTimeout(() => {
				img.classList.remove('logo-fade-in');
				img.classList.add('logo-fade-out');
			}, i * 100); // stagger fade
		});

		setTimeout(() => {
			displayLogos();
		}, 1200);
	}, 6000); // every 6 seconds
});
