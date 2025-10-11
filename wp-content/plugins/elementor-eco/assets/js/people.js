document.addEventListener("DOMContentLoaded", () => {
	const modal = document.getElementById("eco-biography-modal");
	if (!modal) return;
	const titleEl = modal.querySelector(".eco-modal-title");
	const bodyEl = modal.querySelector(".eco-modal-body");
	const closeBtn = modal.querySelector(".eco-modal-close");

	document.querySelectorAll(".eco-biography-link").forEach(link => {
		link.addEventListener("click", (e) => {
			e.preventDefault();

			const name = link.dataset.name || "";
			const position = link.dataset.position || "";
			const company = link.dataset.company || "";
			const address = link.dataset.address || "";
			const phone = link.dataset.phone || "";
			const email = link.dataset.email || "";
			const bio = link.dataset.biography || "";

			const socials = {
				facebook: link.dataset.facebook,
				twitter: link.dataset.twitter,
				linkedin: link.dataset.linkedin,
				xing: link.dataset.xing
			};

			let socialLinks = "";
			for (const [key, value] of Object.entries(socials)) {
				if (value && value !== "undefined") {
					socialLinks += `<a href="${value}" target="_blank" style="margin-right:10px;"><i class="fab fa-${key}"></i></a>`;
				}
			}

			let modalHtml = `
				${position ? `<p><strong>${position}</strong></p>` : ""}
				${company ? `<p>${company}</p>` : ""}
				${address ? `<p>${address}</p>` : ""}
				${phone ? `<p>Tel.: ${phone}</p>` : ""}
				${email ? `<p>Email: <a href="mailto:${email}">${email}</a></p>` : ""}
				${socialLinks ? `<div>${socialLinks}</div>` : ""}
				${bio ? `<hr><p>${bio}</p>` : ""}
			`;

			titleEl.textContent = name;
			bodyEl.innerHTML = modalHtml;
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
});
