document.addEventListener("DOMContentLoaded", () => {
	const letters = document.querySelectorAll(".member-filter-item");
	const items = document.querySelectorAll(".member-item");

	// Find first enabled letter
	let firstLetter = '#';
	for (const el of letters) {
		if (!el.classList.contains("disabled")) {
			firstLetter = el.dataset.letter;
			el.classList.add("active");
			break;
		}
	}

	showMembers(firstLetter);

	letters.forEach(letter => {
		if (letter.classList.contains("disabled")) return;

		letter.addEventListener("click", () => {
			letters.forEach(l => l.classList.remove("active"));
			letter.classList.add("active");
			showMembers(letter.dataset.letter);
		});
	});

	function showMembers(letter) {
		const maxVisible = 8;
		let visibleCount = 0;

		items.forEach(item => {
			if (item.dataset.letter === letter) {
				visibleCount++;
				item.style.display = visibleCount <= maxVisible ? "block" : "none";
			} else {
				item.style.display = "none";
			}
		});

		// Show/hide load more button
		const loadMoreBtn = document.getElementById("load-more-btn");
		if (visibleCount > maxVisible) {
			loadMoreBtn.style.display = "inline-block";
			loadMoreBtn.dataset.letter = letter;
		} else {
			loadMoreBtn.style.display = "none";
		}
	}


	// Modal handling
	const modal = document.getElementById("member-description-modal");
	const modalTitle = modal.querySelector(".eco-modal-title");
	const modalBody = modal.querySelector(".eco-modal-body");
	const closeBtn = modal.querySelector(".eco-modal-close");

	document.querySelectorAll(".member-description-link").forEach(link => {
		link.addEventListener("click", (e) => {
			e.preventDefault();

			const title = link.dataset.title || "";
			const website = link.dataset.website || "";
			const line1 = link.dataset.line1 || "";
			const line2 = link.dataset.line2 || "";
			const line3 = link.dataset.line3 || "";
			const zip = link.dataset.zip || "";
			const city = link.dataset.city || "";
			const country = link.dataset.country || "";
			const phone = link.dataset.phone || "";
			const fax = link.dataset.fax || "";
			const email = link.dataset.email || "";
			const description = link.dataset.description || "";

			let address = "";
			if (line1) address += `<p>${line1}</p>`;
			if (line2) address += `<p>${line2}</p>`;
			if (line3) address += `<p>${line3}</p>`;
			if (zip || city) address += `<p>${zip} ${city}</p>`;
			if (country) address += `<p>${country}</p>`;

			let modalHtml = "";
			if (website) modalHtml += `<p><a href="${website}" target="_blank">Website besuchen</a></p>`;
			if (address) modalHtml += address;
			if (phone) modalHtml += `<p>Tel.: ${phone}</p>`;
			if (fax) modalHtml += `<p>Fax: ${fax}</p>`;
			if (email) modalHtml += `<p>Email: <a href="mailto:${email}">${email}</a></p>`;
			if (description) modalHtml += `<hr><div>${description}</div>`;

			modalTitle.textContent = title;
			modalBody.innerHTML = modalHtml;
			modal.style.display = "block";
		});
	});


	closeBtn.addEventListener("click", () => {
		modal.style.display = "none";
	});

	window.addEventListener("click", (e) => {
		if (e.target === modal) {
			modal.style.display = "none";
		}
	});

	const loadMoreBtn = document.getElementById("load-more-btn");

	loadMoreBtn?.addEventListener("click", () => {
		const currentLetter = loadMoreBtn.dataset.letter;
		items.forEach(item => {
			if (item.dataset.letter === currentLetter) {
				item.style.display = "block";
			}
		});
		loadMoreBtn.style.display = "none";
	});

});