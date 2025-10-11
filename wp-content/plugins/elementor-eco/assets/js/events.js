document.addEventListener("DOMContentLoaded", () => {
	calendar();	
});

let calendar = () => {
	let thisMonthMode = true; // Default mode

	const calendarMonthYear = document.getElementById('calendar-month-year');
	const calendarDays = document.getElementById('calendar-days');
	const eventsDiv = document.getElementById('events');
	const prevBtn = document.getElementById('prev-month');
	const nextBtn = document.getElementById('next-month');
	const categorySelect = document.getElementById('category-select');
	const thisMonthBtn = document.getElementById('this-month-button');

	if (!calendarMonthYear || !calendarDays || !eventsDiv || !prevBtn || !nextBtn) {
		console.error('Calendar elements not found');
		return;
	}

	let currentMonthOffset = 0;
	let selectedDate = new Date().toISOString().split('T')[0]; // Default to today
	// click today's date
	document.querySelectorAll('#calendar-days div').forEach(dayDiv => {
		console.log(dayDiv.dataset.date, selectedDate);
		
		if (dayDiv.dataset.date === selectedDate) {
			dayDiv.classList.add('active');
		}
	});
	let selectedTag = '';
	loadEvents();

	function renderCalendar() {
		const baseDate = new Date();
		baseDate.setMonth(baseDate.getMonth() + currentMonthOffset);
		const year = baseDate.getFullYear();
		const month = baseDate.getMonth();
		const daysInMonth = new Date(year, month + 1, 0).getDate();

		calendarMonthYear.textContent = `${baseDate.toLocaleString('default', { month: 'long' })} ${year}`;
		calendarDays.innerHTML = '';

		// Build array of all days in the month
		let monthDates = [];
		for (let day = 1; day <= daysInMonth; day++) {
			const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
			monthDates.push(dateStr);

			const dayDiv = document.createElement('div');
			dayDiv.textContent = day;
			dayDiv.dataset.date = dateStr;
			dayDiv.classList.toggle('active', dateStr === selectedDate);
			dayDiv.addEventListener('click', () => {
				selectedDate = dayDiv.dataset.date;
				thisMonthMode = false;
				document.querySelectorAll('#calendar-days div, #this-month-button').forEach(d => d.classList.remove('active'));
				dayDiv.classList.add('active');
				loadEvents();
			});

			calendarDays.appendChild(dayDiv);
		}

		// Fetch dates that have events
		fetch(`${ecoEvents.ajaxurl}?action=eco_get_event_days`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ dates: monthDates })
		})
		.then(res => res.json())
		.then(data => {
			data.forEach(date => {
				if (!/^\d{8}$/.test(date)) return; // basic check
				const year = date.substring(0, 4);
				const month = date.substring(4, 6);
				const day = date.substring(6, 8);
				const isoDate = `${year}-${month}-${day}`;
				const el = document.querySelector(`#calendar-days div[data-date="${isoDate}"]`);
				if (el) el.classList.add('has-news');
			});

		});
	}


	function loadEvents() {
		if (!selectedDate && !thisMonthMode) return;

		const category = categorySelect?.value || '';
		const tag = selectedTag || '';

		// show loading message
		eventsDiv.innerHTML = thisMonthMode ? `<p>${ecoEventsL10n.loading_month}</p>` : `<p>${ecoEventsL10n.loading_events}</p>`;

		let fetchUrl = `${ecoEvents.ajaxurl}?action=eco_load_events&category=${category}&tag=${tag}`;

		if (thisMonthMode) {
			// Send year & month instead of a specific date
			const baseDate = new Date();
			baseDate.setMonth(baseDate.getMonth() + currentMonthOffset);
			const year = baseDate.getFullYear();
			const month = (baseDate.getMonth() + 1).toString().padStart(2, '0');
			fetchUrl += `&month=${year}-${month}`;
		} else {
			fetchUrl += `&date=${selectedDate}`;
		}

		fetch(fetchUrl)
			.then(res => res.json())
			.then(data => {
				// if data is empty, show a message
				if (data.length === 0) {
					eventsDiv.innerHTML = `<p>${thisMonthMode ? ecoEventsL10n.no_events_month : ecoEventsL10n.no_events}</p>`;
					return;
				}
				eventsDiv.innerHTML = data.map(event => _template(event)).join('');
			})
			.catch(err => {
				console.error('Error loading events:', err);
				eventsDiv.innerHTML = `<p>${ecoEventsL10n.error_loading}</p>`;
			});
	}

	function _template(event) {
		console.log(event);
		
		// ---------------- helpers ----------------
		const isYmd = s => /^\d{8}$/.test(s || "");
		const isDotted = s => /^\d{2}\.\d{2}\.\d{4}$/.test(s || "");
		const fmtDate = s => {
			if (!s) return "";
			if (isDotted(s)) return s;                         // already DD.MM.YYYY
			if (isYmd(s)) return `${s.slice(6,8)}.${s.slice(4,6)}.${s.slice(0,4)}`;
			return s;                                          // fallback
		};
		const fmtTime = t => {
			if (!t) return "";
			const s = String(t).trim();
			// 24h with/without seconds
			const m24 = /^(\d{1,2}):(\d{2})(?::\d{2})?$/.exec(s);
			if (m24 && !/[ap]m$/i.test(s)) {
			return `${m24[1].padStart(2, "0")}:${m24[2]}`;
			}
			// 12h am/pm -> 24h
			const m12 = /^(\d{1,2}):(\d{2})\s*([ap]m)$/i.exec(s);
			if (m12) {
			let h = parseInt(m12[1], 10);
			const m = m12[2];
			const ampm = m12[3].toLowerCase();
			if (ampm === "pm" && h !== 12) h += 12;
			if (ampm === "am" && h === 12) h = 0;
			return `${String(h).padStart(2, "0")}:${m}`;
			}
			return s; // fallback
		};

		const metaRow = (sDate, eDate, sTime, eTime, location) => {
			const dateTxt = `${fmtDate(sDate)} – ${fmtDate(eDate || sDate)}`; // ALWAYS show range
			const timeTxt = sTime ? `${fmtTime(sTime)}${eTime ? ` – ${fmtTime(eTime)}` : ""}` : "";
			return `
			<div class="event-meta-row">
				<span class="meta meta-date"><span class="meta-icon" aria-hidden="true">📅</span>${dateTxt}</span>
				${timeTxt ? `<span class="meta meta-time"><span class="meta-icon" aria-hidden="true">⏰</span>${timeTxt}</span>` : `<span></span>`}
				${location ? `<span class="meta meta-loc"><span class="meta-icon" aria-hidden="true">📍</span>${location}</span>` : `<span></span>`}
			</div>
			`;
		};

		// --------------- left-rail badge ---------------
		const primary = event.start_date || "";
		const day   = isYmd(primary) ? primary.slice(6,8) : (isDotted(primary) ? primary.slice(0,2) : "");
		const month = isYmd(primary) ? primary.slice(4,6) : (isDotted(primary) ? primary.slice(3,5) : "");
		const year  = isYmd(primary) ? primary.slice(0,4) : (isDotted(primary) ? primary.slice(6,10) : "");

		// --------------- meta rows ---------------
		// Main row (always with range, even if same)
		let rowsHtml = metaRow(
			event.start_date,
			event.end_date, // your PHP already falls back to start_date
			event.start_time,
			event.end_time,
			event.location
		);

		// Additional rows (normalized/sorted in PHP ideally; we’ll still handle whatever comes)
		const others = Array.isArray(event.other_dates) ? event.other_dates : [];
		// sort by start_date if comparable
		const toYmd = (s) => {
			if (isYmd(s)) return s;
			if (isDotted(s)) { const [dd,mm,yy] = s.split('.'); return `${yy}${mm}${dd}`; }
			return '';
		};
		others.sort((a,b) => toYmd(a?.start_date).localeCompare(toYmd(b?.start_date)));

		others.forEach(d => {
			rowsHtml += metaRow(
			d.start_date,
			d.end_date || d.start_date,            // fallback here too
			d.start_time,
			d.end_time,
			d.location
			);
		});

		// --------------- template ----------------
		return `
			<div class="event-item">
			<aside class="event-rail">
				<div class="date-badge" aria-label="${fmtDate(primary)}">
				<span class="day">${day}</span>
				<span class="month-year">${month}.${year}</span>
				</div>
			</aside>

			<section class="event-main">
				<div class="event-category">
				${event.categories.map(cat => `<span class="event-category-item" data-category="${cat}">${cat}</span>`).join(' | ')}
				</div>

				<h4 class="event-title"><a href="${event.link}">${event.title}</a></h4>

				<div class="event-meta-rows">
				${rowsHtml}
				</div>

				${event.teaser_short_description ? `<div class="event-teaser">${event.teaser_short_description}</div>` : ''}

				<div class="event-actions">
				${event.has_tickets ? `<a class="link-underline" data-no-anchor href="${event.link}#tickets">${ecoEventsL10n.ticket_shop}</a>` : ''}
				<a class="link-underline" href="${event.link}">${ecoEventsL10n.more_info}</a>
				</div>

				<div class="event-tags">
				${event.tags.map(tag => `<span class="event-tag-inside" data-tag="${tag}">${tag}</span>`).join(' ')}
				</div>
			</section>

			${event.thumbnail ? `
			<aside class="event-media">
				<img src="${event.thumbnail}" alt="${event.teaser_title || event.title}">
			</aside>` : ''}
			</div>
		`;
	}




	thisMonthBtn?.addEventListener('click', () => {
		loadMonth();
	});

	function loadMonth() {
		thisMonthMode = true;
		document.querySelectorAll('#calendar-days div').forEach(d => d.classList.remove('active'));
		thisMonthBtn.classList.add('active');
		loadEvents();
	}


	prevBtn.addEventListener('click', () => {
		if (currentMonthOffset > -10) {
			currentMonthOffset--;
			renderCalendar();
			loadMonth();
		}
	});

	nextBtn.addEventListener('click', () => {
		if (currentMonthOffset < 10) {
			currentMonthOffset++;
			renderCalendar();
			loadMonth();
		}
	});

	categorySelect?.addEventListener('change', loadEvents);

	document.querySelector('.calendar-tags')?.addEventListener('click', e => {
		if (e.target.classList.contains('event-tag')) {
			selectedTag = e.target.dataset.tag || '';
			document.querySelectorAll('.event-tag').forEach(el => el.classList.remove('active'));
			e.target.classList.add('active');
			loadEvents();
		}
	});


	renderCalendar();
};
