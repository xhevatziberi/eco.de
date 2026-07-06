(function () {
	"use strict";

	const SELECTORS = {
		root: ".eco-member-list",
		filter: ".member-filter-item[data-letter]",
		grid: ".member-grid",
		status: ".eco-members-status",
		loadMore: ".eco-load-more-btn",
		detailButton: ".member-description-link",
		modal: ".eco-modal",
		modalTitle: ".eco-modal-title",
		modalBody: ".eco-modal-body",
		modalClose: "[data-modal-close]"
	};

	function initMembersWidget(root) {
		if (!root || root.dataset.ecoMembersInitialized === "true") {
			return;
		}

		if (root.classList.contains("eco-member-list--editor")) {
			return;
		}

		root.dataset.ecoMembersInitialized = "true";

		const ajaxUrl = root.dataset.ajaxUrl;
		const nonce = root.dataset.nonce;
		const initialLetter = root.dataset.initialLetter || "";

		const grid = root.querySelector(SELECTORS.grid);
		const status = root.querySelector(SELECTORS.status);
		const loadMoreButton = root.querySelector(SELECTORS.loadMore);
		const filterButtons = Array.from(root.querySelectorAll(SELECTORS.filter));

		const modal = root.querySelector(SELECTORS.modal);
		const modalTitle = root.querySelector(SELECTORS.modalTitle);
		const modalBody = root.querySelector(SELECTORS.modalBody);
		const modalCloseElements = Array.from(
			root.querySelectorAll(SELECTORS.modalClose)
		);

		let currentLetter = initialLetter;
		let currentRequest = null;
		let lastFocusedElement = null;

		if (!ajaxUrl || !nonce || !grid) {
			return;
		}

		filterButtons.forEach((button) => {
			if (button.disabled) {
				return;
			}

			button.addEventListener("click", () => {
				const letter = button.dataset.letter || "";

				if (!letter || letter === currentLetter) {
					return;
				}

				setActiveFilter(filterButtons, button);
				currentLetter = letter;

				loadMembers({
					root,
					grid,
					status,
					loadMoreButton,
					ajaxUrl,
					nonce,
					letter,
					loadAll: false,
					getCurrentRequest: () => currentRequest,
					setCurrentRequest: (request) => {
						currentRequest = request;
					}
				});
			});
		});

		loadMoreButton?.addEventListener("click", () => {
			if (!currentLetter) {
				return;
			}

			loadMembers({
				root,
				grid,
				status,
				loadMoreButton,
				ajaxUrl,
				nonce,
				letter: currentLetter,
				loadAll: true,
				getCurrentRequest: () => currentRequest,
				setCurrentRequest: (request) => {
					currentRequest = request;
				}
			});
		});

		root.addEventListener("click", (event) => {
			const detailButton = event.target.closest(SELECTORS.detailButton);

			if (!detailButton || !root.contains(detailButton)) {
				return;
			}

			event.preventDefault();

			const memberId = detailButton.dataset.memberId;

			if (!memberId) {
				return;
			}

			lastFocusedElement = detailButton;

			openModal(modal, modalTitle, modalBody);
			loadMemberDetails({
				ajaxUrl,
				nonce,
				memberId,
				modalTitle,
				modalBody
			});
		});

		modalCloseElements.forEach((element) => {
			element.addEventListener("click", () => {
				closeModal(modal, modalTitle, modalBody, lastFocusedElement);
			});
		});

		document.addEventListener("keydown", (event) => {
			if (
				event.key === "Escape" &&
				modal &&
				modal.getAttribute("aria-hidden") === "false"
			) {
				closeModal(modal, modalTitle, modalBody, lastFocusedElement);
			}
		});

		if (initialLetter) {
			loadMembers({
				root,
				grid,
				status,
				loadMoreButton,
				ajaxUrl,
				nonce,
				letter: initialLetter,
				loadAll: false,
				getCurrentRequest: () => currentRequest,
				setCurrentRequest: (request) => {
					currentRequest = request;
				}
			});
		} else {
			grid.innerHTML =
				`<p class="eco-members-empty">${ecoMembersL10n.noMembers}</p>`;
			grid.setAttribute("aria-busy", "false");
		}
	}

	async function loadMembers(options) {
		const {
			root,
			grid,
			status,
			loadMoreButton,
			ajaxUrl,
			nonce,
			letter,
			loadAll,
			getCurrentRequest,
			setCurrentRequest
		} = options;

		const previousRequest = getCurrentRequest();

		if (previousRequest) {
			previousRequest.abort();
		}

		const controller = new AbortController();
		setCurrentRequest(controller);

		setLoadingState(root, grid, status, loadMoreButton, true, loadAll);

		const formData = new FormData();
		formData.append("action", "eco_load_members");
		formData.append("nonce", nonce);
		formData.append("letter", letter);
		formData.append("load_all", loadAll ? "1" : "0");

		try {
			const response = await fetch(ajaxUrl, {
				method: "POST",
				credentials: "same-origin",
				body: formData,
				signal: controller.signal
			});

			if (!response.ok) {
				throw new Error(`HTTP ${response.status}`);
			}

			const result = await response.json();

			if (!result.success || !result.data) {
				throw new Error(
					result?.data?.message || ecoMembersL10n.membersLoadError
				);
			}

			grid.innerHTML = result.data.html || "";
			grid.setAttribute("aria-busy", "false");

			if (status) {
				const total = Number(result.data.total || 0);

				status.textContent =
					total === 1
						? ecoMembersL10n.oneMemberFound
						: ecoMembersL10n.membersFound.replace("%d", total);
			}

			if (loadMoreButton) {
				loadMoreButton.hidden = !result.data.has_more;
				loadMoreButton.disabled = false;
				loadMoreButton.removeAttribute("aria-busy");
			}
		} catch (error) {
			if (error.name === "AbortError") {
				return;
			}

			grid.innerHTML =
				`<p class="eco-members-error">${ecoMembersL10n.membersLoadError}</p>`;

			grid.setAttribute("aria-busy", "false");

			if (status) {
				status.textContent = "";
			}

			if (loadMoreButton) {
				loadMoreButton.hidden = true;
				loadMoreButton.disabled = false;
				loadMoreButton.removeAttribute("aria-busy");
			}

			console.error("ECO Members:", error);
		} finally {
			if (getCurrentRequest() === controller) {
				setCurrentRequest(null);
			}

			root.classList.remove("is-loading");
		}
	}

	async function loadMemberDetails(options) {
		const {
			ajaxUrl,
			nonce,
			memberId,
			modalTitle,
			modalBody
		} = options;

		const formData = new FormData();
		formData.append("action", "eco_load_member_details");
		formData.append("nonce", nonce);
		formData.append("member_id", memberId);

		try {
			const response = await fetch(ajaxUrl, {
				method: "POST",
				credentials: "same-origin",
				body: formData
			});

			if (!response.ok) {
				throw new Error(`HTTP ${response.status}`);
			}

			const result = await response.json();

			if (!result.success || !result.data) {
				throw new Error(
					result?.data?.message || ecoMembersL10n.memberDetailsLoadError
				);
			}

			if (modalTitle) {
				modalTitle.textContent = result.data.title || "";
			}

			if (modalBody) {
				modalBody.innerHTML = result.data.html || "";
			}
		} catch (error) {
			if (modalBody) {
				modalBody.innerHTML =
					`<p class="eco-members-error">${ecoMembersL10n.memberDetailsLoadError}</p>`;
			}

			console.error("ECO Member details:", error);
		}
	}

	function setActiveFilter(buttons, activeButton) {
		buttons.forEach((button) => {
			const isActive = button === activeButton;

			button.classList.toggle("active", isActive);
			button.setAttribute("aria-pressed", isActive ? "true" : "false");
		});
	}

	function setLoadingState(
		root,
		grid,
		status,
		loadMoreButton,
		isLoading,
		loadAll
	) {
		root.classList.toggle("is-loading", isLoading);
		grid.setAttribute("aria-busy", isLoading ? "true" : "false");

		if (status) {
			status.textContent = loadAll
				? ecoMembersL10n.loadingAllMembers
				: ecoMembersL10n.loadingMembers;
		}

		if (loadMoreButton) {
			loadMoreButton.disabled = isLoading;
			loadMoreButton.setAttribute(
				"aria-busy",
				isLoading ? "true" : "false"
			);

			if (!loadAll) {
				loadMoreButton.hidden = true;
			}
		}

		if (!loadAll) {
			grid.innerHTML = createSkeletons(8);
		}
	}

	function createSkeletons(count) {
		let html = "";

		for (let index = 0; index < count; index += 1) {
			html += `
				<div class="eco-member-skeleton" aria-hidden="true">
					<div class="eco-member-skeleton__visual"></div>
					<div class="eco-member-skeleton__line eco-member-skeleton__line--title"></div>
					<div class="eco-member-skeleton__line"></div>
				</div>
			`;
		}

		return html;
	}

	function openModal(modal, modalTitle, modalBody) {
		if (!modal) {
			return;
		}

		if (modalTitle) {
			modalTitle.textContent = "";
		}

		if (modalBody) {
			modalBody.innerHTML =
				`<div class="eco-member-modal-loading">${ecoMembersL10n.loadingInformation}</div>`;
		}

		modal.classList.add("is-open");
		modal.setAttribute("aria-hidden", "false");
		document.documentElement.classList.add("eco-modal-open");

		const closeButton = modal.querySelector(".eco-modal-close");
		closeButton?.focus();
	}

	function closeModal(modal, modalTitle, modalBody, lastFocusedElement) {
		if (!modal) {
			return;
		}

		modal.classList.remove("is-open");
		modal.setAttribute("aria-hidden", "true");
		document.documentElement.classList.remove("eco-modal-open");

		if (modalTitle) {
			modalTitle.textContent = "";
		}

		if (modalBody) {
			modalBody.innerHTML = "";
		}

		lastFocusedElement?.focus();
	}

	function initializeAllMembersWidgets(scope) {
		const context = scope || document;

		if (context.matches?.(SELECTORS.root)) {
			initMembersWidget(context);
		}

		context.querySelectorAll?.(SELECTORS.root).forEach(initMembersWidget);
	}

	document.addEventListener("DOMContentLoaded", () => {
		initializeAllMembersWidgets(document);
	});

	/*
	 * Elementor frontend support.
	 */
	window.addEventListener("elementor/frontend/init", () => {
		if (!window.elementorFrontend?.hooks) {
			return;
		}

		window.elementorFrontend.hooks.addAction(
			"frontend/element_ready/members.default",
			($scope) => {
				const scopeElement = $scope?.[0] || $scope;
				initializeAllMembersWidgets(scopeElement);
			}
		);
	});
})();