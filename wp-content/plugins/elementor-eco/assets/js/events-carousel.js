(function(){
  const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

  // ---- tiny clone of your _template(event) so markup matches your list cards ----
  function eventTemplate(event){
    const isYmd = s => /^\d{8}$/.test(s||"");
    const isDot = s => /^\d{2}\.\d{2}\.\d{4}$/.test(s||"");
    const fmtDate = s => {
      if (!s) return "";
      if (isDot(s)) return s;
      if (isYmd(s)) return `${s.slice(6,8)}.${s.slice(4,6)}.${s.slice(0,4)}`;
      return s;
    };
    const fmtTime = t => {
      if (!t) return "";
      const s = String(t).trim();
      const m24 = /^(\d{1,2}):(\d{2})(?::\d{2})?$/.exec(s);
      if (m24 && !/[ap]m$/i.test(s)) return `${m24[1].padStart(2,"0")}:${m24[2]}`;
      const m12 = /^(\d{1,2}):(\d{2})\s*([ap]m)$/i.exec(s);
      if (m12){
        let h = parseInt(m12[1],10);
        const m = m12[2], ap = m12[3].toLowerCase();
        if (ap==='pm' && h!==12) h+=12;
        if (ap==='am' && h===12) h=0;
        return `${String(h).padStart(2,"0")}:${m}`;
      }
      return s;
    };
    const metaRow = (sd,ed,st,et,loc)=>`
      <div class="event-meta-row">
        <span class="meta meta-date"><span class="meta-icon" aria-hidden="true">📅</span>${fmtDate(sd)} – ${fmtDate(ed||sd)}</span>
        ${st?`<span class="meta meta-time"><span class="meta-icon" aria-hidden="true">⏰</span>${fmtTime(st)}${et?` – ${fmtTime(et)}`:''}</span>`:'<span></span>'}
        ${loc?`<span class="meta meta-loc"><span class="meta-icon" aria-hidden="true">📍</span>${loc}</span>`:'<span></span>'}
      </div>`;

    const primary = event.start_date||"";
    const day   = isYmd(primary) ? primary.slice(6,8) : (isDot(primary) ? primary.slice(0,2) : "");
    const month = isYmd(primary) ? primary.slice(4,6) : (isDot(primary) ? primary.slice(3,5) : "");
    const year  = isYmd(primary) ? primary.slice(0,4) : (isDot(primary) ? primary.slice(6,10) : "");

    let rows = metaRow(event.start_date, event.end_date, event.start_time, event.end_time, event.location);

    const others = Array.isArray(event.other_dates) ? event.other_dates : [];
    const toYmd  = s => isYmd(s) ? s : (isDot(s)? s.split('.').reverse().join('') : '');
    others.sort((a,b)=> (toYmd(a?.start_date)||'').localeCompare(toYmd(b?.start_date)||''));
    others.forEach(d => rows += metaRow(d.start_date, d.end_date||d.start_date, d.start_time, d.end_time, d.location));

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
            ${(event.categories||[]).map(cat=>`<span class="event-category-item" data-category="${cat}">${cat}</span>`).join(' | ')}
          </div>

          <h4 class="event-title"><a href="${event.link}">${event.title}</a></h4>

          <div class="event-meta-rows">${rows}</div>

          ${event.teaser_short_description ? `<div class="event-teaser">${event.teaser_short_description}</div>` : ''}

          <div class="event-actions">
            ${event.has_tickets ? `<a class="link-underline" data-no-anchor href="${event.link}#tickets">${ecoEventsCarousel.ticket_shop}</a>` : ''}
            <a class="link-underline" href="${event.link}">${ecoEventsCarousel.more_info}</a>
          </div>

          <div class="event-tags">
            ${(event.tags||[]).map(tag=>`<span class="event-tag-inside" data-tag="${tag}">${tag}</span>`).join(' ')}
          </div>
        </section>

        ${event.thumbnail ? `<aside class="event-media"><img src="${event.thumbnail}" alt="${event.teaser_title||event.title}"></aside>` : ''}
      </div>`;
  }

  function initOne(root){
    if (!root) return;

    const perPage = parseInt(root.getAttribute('data-count')||'6',10);
    const upcoming= root.getAttribute('data-upcoming')==='1';
    const orderby = root.getAttribute('data-orderby')||'start_date';
    const order   = root.getAttribute('data-order')||'ASC';
    const arrows  = root.getAttribute('data-arrows')==='1';
    const dots    = root.getAttribute('data-dots')==='1';
    const slides  = parseInt(root.getAttribute('data-slides')||'2',10);
    const space   = parseInt(root.getAttribute('data-space')||'24',10);

    const wrapper = root.querySelector('.swiper-wrapper');

    // Load events using your existing endpoint
    const params = new URLSearchParams({
      action: 'eco_load_events_carousel',
      per_page: String(perPage),
      orderby,
      order
    });
    if (upcoming) params.set('upcoming','1');
    const cats = (root.getAttribute('data-cats') || '').trim();
    if (cats) params.set('cats', cats);

    // initial loading state
    wrapper.innerHTML = `<div class="swiper-slide"><div class="event-item" style="padding:24px;text-align:center;"><span class="event-loading">${ecoEventsCarousel.loading||'Loading…'}</span></div></div>`;

    fetch(`${ecoEventsCarousel.ajaxurl}?${params.toString()}`)
      .then(r => r.json())
      .then(items => {
        if (!Array.isArray(items) || !items.length) {
          wrapper.innerHTML = `<div class="swiper-slide"><div class="event-item" style="padding:24px;text-align:center;"><span class="event-empty">${ecoEventsCarousel.empty||'No events.'}</span></div></div>`;
          return;
        }
        wrapper.innerHTML = items.map(ev => `<div class="swiper-slide">${eventTemplate(ev)}</div>`).join('');

        // Swiper init
        const swiper = new Swiper(root.querySelector('.swiper'), {
          slidesPerView: slides,
          spaceBetween: space,
          navigation: arrows ? { nextEl: root.querySelector('.swiper-button-next'), prevEl: root.querySelector('.swiper-button-prev') } : false,
          pagination: dots ? { el: root.querySelector('.swiper-pagination'), clickable: true } : false,
          loop: false,
          breakpoints: {
            0:   { slidesPerView: 1 },
            640: { slidesPerView: Math.min(2, slides) },
            1024:{ slidesPerView: slides }
          }
        });
      })
      .catch(e => {
        console.error(e);
        wrapper.innerHTML = `<div class="swiper-slide"><div class="event-item" style="padding:24px;text-align:center;">${ecoEventsCarousel.error||'Error.'}</div></div>`;
      });
  }

  document.addEventListener('DOMContentLoaded', () => {
    $$('.eco-events-carousel').forEach(initOne);
  });
})();
