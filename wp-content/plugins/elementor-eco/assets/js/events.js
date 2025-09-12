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
				const parts = date.split('.');
				if (parts.length !== 3) return;
				const isoDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
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
		eventsDiv.innerHTML = '<p>Lade Veranstaltungen...</p>';

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
					eventsDiv.innerHTML = '<p>Keine Veranstaltungen gefunden.</p>';
					return;
				}
				eventsDiv.innerHTML = data.map(event => _template(event)).join('');
			})
			.catch(err => {
				console.error('Error loading events:', err);
				eventsDiv.innerHTML = '<p>Error loading events. Please try again later.</p>';
			});
	}

	function _template(event) {
		console.log(event);
		let dateStr = event.start_date;
		// Format date from YYYYMMDD to DD.MM.YYYY
		// Example: 20240415 -> 15.04.2024
		// Ensure dateStr is 8 characters long
		let _date = '';
		if (dateStr.length !== 8) {
			_date = event.start_date; // Fallback to original if format is unexpected
		} else {
			const year = dateStr.substring(0, 4);
			// const month = parseInt(dateStr.substring(4, 6)) - 1; // Months are 0-indexed in JS
			const month = dateStr.substring(4, 6);
			const day = dateStr.substring(6, 8);
			_date = `${day}.${month}.${year}`;
		}
		
		
		return `
			<div class="event-item grid">
				<div class="event-datetime">
					<span class="event-date">${_date}</span>
					<span class="event-time">${event.time}</span>
					<span class="event-location">${event.location}</span>
				</div>
				<div class="event-details">
					<div class="event-category">
						${event.categories.map(cat => `<span class="event-category-item" data-category="${cat}">${cat}</span>`).join(' | ')}
					</div>
					<h4 class="event-title">${event.title}</h4>
					${event.teaser_short_description ? `<div class="event-teaser">${event.teaser_short_description}</div>` : ''}
					<div class="event-ticketshop">
						<a href="${event.link}#ticket">Link zum Ticketshop</a>
					</div>
					
					<div class="event-tags">
						${event.tags.map(tag => `<span class="event-tag-inside" data-tag="${tag}">${tag}</span>`).join(' ')}
					</div>
				</div>
				<div class="event-link">
					<a href="${event.link}" class="event-button">Mehr Infos</a>
				</div>
			</div>
		`;
	}

	thisMonthBtn?.addEventListener('click', () => {
		// thisMonthMode = true;
		// document.querySelectorAll('#calendar-days div').forEach(d => d.classList.remove('active'));
		// thisMonthBtn.classList.add('active');
		// loadEvents();
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

	document.querySelectorAll('.event-tag').forEach(tagElement => {
		tagElement.addEventListener('click', () => {
			selectedTag = tagElement.dataset.tag || '';
			document.querySelectorAll('.event-tag').forEach(el => el.classList.remove('active'));
			tagElement.classList.add('active');
			loadEvents();
		});
	});

	renderCalendar();
};
