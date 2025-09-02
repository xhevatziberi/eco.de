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
		const currentSet = shuffle(logos).slice(0, 8);

		const center = currentSet[0];
		const rest = currentSet.slice(1);

		// Center logo
		const centerLink = document.createElement('a');
		centerLink.href = center.url || '#';
		centerLink.target = '_blank';
		centerLink.rel = 'noopener';

		const centerImg = document.createElement('img');
		centerImg.src = center.logo;
		centerImg.className = 'logo-center';

		centerLink.appendChild(centerImg);
		cloud.appendChild(centerLink);

		// Circle layout
		const radius = 150;
		const centerX = 200;
		const centerY = 200;

		rest.forEach((item, i) => {
			const angle = (i / rest.length) * 2 * Math.PI;
			const x = centerX + radius * Math.cos(angle) - 50;
			const y = centerY + radius * Math.sin(angle) - 50;

			const link = document.createElement('a');
			link.href = item.url || '#';
			link.target = '_blank';
			link.rel = 'noopener';

			const img = document.createElement('img');
			img.src = item.logo;
			img.className = 'logo-peripheral';
			img.style.left = `${x}px`;
			img.style.top = `${y}px`;

			link.appendChild(img);
			cloud.appendChild(link);
		});
	}



	displayLogos();

	setInterval(() => {
		const images = cloud.querySelectorAll('img');
		
		// Fade out existing images one by one
		images.forEach((img, i) => {
			setTimeout(() => {
				img.classList.remove('logo-fade-in');
				img.classList.add('logo-fade-out');
			}, i * 100);
		});

		// After all faded, replace with next set
		setTimeout(() => {
			displayLogos();
		}, images.length * 100 + 600); // wait for full fade out
	}, 6000);

});

