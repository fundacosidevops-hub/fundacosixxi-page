(function () {
    'use strict';
    const SITE = window.SITE || { slides: [], services: {} };
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Menú móvil ---------- */
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    function setMenu(open) {
        menu.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
        toggle.querySelector('i').className = 'fa-solid ' + (open ? 'fa-xmark' : 'fa-bars') + ' text-lg';
    }
    if (toggle && menu) {
        toggle.addEventListener('click', () => setMenu(menu.classList.contains('hidden')));
        menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
        window.addEventListener('resize', () => { if (window.innerWidth >= 1024) setMenu(false); });
    }

    /* ---------- Slider ---------- */
    const imgs = document.querySelectorAll('.slide-img');
    const dots = document.querySelectorAll('.slider-dot');
    const textBox = document.getElementById('slider-text');
    const tagEl = document.getElementById('slider-tag');
    const titleEl = document.getElementById('slider-title');
    const descEl = document.getElementById('slider-desc');
    const pauseBtn = document.getElementById('slide-pause');
    let current = 0, timer = null, paused = reduceMotion;

    function show(i) {
        if (!imgs.length) return;
        current = (i + imgs.length) % imgs.length;
        imgs.forEach((img, k) => {
            if (k === current) img.removeAttribute('loading');
            img.classList.toggle('opacity-40', k === current);
            img.classList.toggle('opacity-0', k !== current);
        });
        dots.forEach((d, k) => {
            d.classList.toggle('is-active', k === current);
            if (k === current) d.setAttribute('aria-current', 'true'); else d.removeAttribute('aria-current');
        });
        const s = SITE.slides[current];
        textBox.style.opacity = 0;
        setTimeout(() => {
            tagEl.textContent = s.tag;
            titleEl.innerHTML = s.title; // contenido propio y estático
            descEl.textContent = s.desc;
            textBox.style.opacity = 1;
        }, reduceMotion ? 0 : 300);
    }
    function start() { stop(); if (!paused) timer = setInterval(() => show(current + 1), 7000); }
    function stop() { if (timer) clearInterval(timer); timer = null; }
    function setPaused(p) {
        paused = p;
        if (pauseBtn) {
            pauseBtn.setAttribute('aria-label', p ? 'Reanudar presentación' : 'Pausar presentación');
            pauseBtn.querySelector('i').className = 'fa-solid ' + (p ? 'fa-play' : 'fa-pause');
        }
        start();
    }

    if (imgs.length) {
        document.getElementById('slide-prev').addEventListener('click', () => { show(current - 1); start(); });
        document.getElementById('slide-next').addEventListener('click', () => { show(current + 1); start(); });
        dots.forEach(d => d.addEventListener('click', () => { show(+d.dataset.index); start(); }));
        pauseBtn && pauseBtn.addEventListener('click', () => setPaused(!paused));
        const hero = document.getElementById('inicio');
        hero.addEventListener('mouseenter', stop);
        hero.addEventListener('mouseleave', start);
        hero.addEventListener('focusin', stop);
        hero.addEventListener('focusout', start);
        document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
        // Deslizar en móvil
        let x0 = null;
        hero.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
        hero.addEventListener('touchend', e => {
            if (x0 === null) return;
            const dx = e.changedTouches[0].clientX - x0;
            if (Math.abs(dx) > 50) { show(current + (dx < 0 ? 1 : -1)); start(); }
            x0 = null;
        });
        setPaused(paused);
    }

    /* ---------- Ventana de servicios ---------- */
    const modal = document.getElementById('serviceModal');
    const modalContent = document.getElementById('modal-content');
    const closeBtn = document.getElementById('modal-close');
    let lastFocus = null;

    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function openModal(key) {
        const d = SITE.services[key];
        if (!d) return;
        const c = d.classes;
        const wa = SITE.whatsapp + '?text=' + encodeURIComponent('Hola, deseo información sobre el servicio de ' + d.title + '.');
        modalContent.innerHTML = `
            <div class="relative h-44 sm:h-48 overflow-hidden">
                <img src="${esc(d.image)}" class="w-full h-full object-cover" alt="">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-16 flex items-center gap-3 text-white">
                    <div class="w-12 h-12 rounded-xl ${c.bg} flex items-center justify-center text-xl shadow-lg flex-shrink-0"><i class="fa-solid ${esc(d.icon)}" aria-hidden="true"></i></div>
                    <h3 id="modal-title" class="text-2xl font-bold font-serif">${esc(d.title)}</h3>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <p class="text-slate-600 mb-6 leading-relaxed">${esc(d.desc)}</p>
                <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-circle-check ${c.text}" aria-hidden="true"></i> Servicios incluidos</h4>
                <ul class="space-y-3 mb-8">
                    ${d.items.map(i => `<li class="flex items-center gap-3 text-slate-600 text-sm"><span class="w-1.5 h-1.5 rounded-full ${c.bg}"></span>${esc(i)}</li>`).join('')}
                </ul>
                <a href="${esc(wa)}" target="_blank" rel="noopener" data-open-chat="${esc('Quiero agendar una cita de ' + d.title)}" class="flex items-center justify-center gap-2 w-full ${c.bg} ${c.hover} text-white py-4 rounded-xl font-semibold transition-colors">
                    <i class="fa-solid fa-calendar-check text-lg" aria-hidden="true"></i> Solicitar consulta
                </a>
            </div>`;
        lastFocus = document.activeElement;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        closeBtn.focus();
    }
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        if (lastFocus) lastFocus.focus();
    }
    document.querySelectorAll('.open-service').forEach(b => b.addEventListener('click', () => openModal(b.dataset.service)));
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeModal();
        if (e.key === 'Tab') { // mantener el foco dentro de la ventana
            const f = modal.querySelectorAll('a[href], button');
            const first = f[0], last = f[f.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
    });

    /* ---------- Gráfico (se carga al hacerse visible) ---------- */
    const canvas = document.getElementById('impactChart');
    function drawChart() {
        if (!window.Chart || canvas.dataset.done) return;
        canvas.dataset.done = '1';
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4'],
                datasets: [{
                    label: 'Pacientes atendidos',
                    data: [2600, 3100, 2900, 3400], // TODO: actualizar con datos reales
                    borderColor: '#14b8a6', backgroundColor: 'rgba(20,184,166,0.12)',
                    borderWidth: 3, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#14b8a6'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: reduceMotion ? false : undefined,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.06)' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    }
    if (canvas) {
        if ('IntersectionObserver' in window) {
            new IntersectionObserver((entries, obs) => {
                if (entries[0].isIntersecting) { drawChart(); obs.disconnect(); }
            }, { rootMargin: '200px' }).observe(canvas);
        } else { window.addEventListener('load', drawChart); }
    }

    /* ---------- Formulario ---------- */
    const form = document.getElementById('feedback-form');
    const status = document.getElementById('form-status');
    function showStatus(ok, msg) {
        status.className = 'rounded-xl px-4 py-3 text-sm ' + (ok ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200');
        status.textContent = msg;
    }
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Enviando…';
            try {
                const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json' } });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.ok) {
                    showStatus(true, data.message || '¡Gracias! Recibimos tu mensaje.');
                    form.reset();
                } else {
                    showStatus(false, data.message || 'No pudimos enviar tu mensaje. Intenta de nuevo o escríbenos por WhatsApp.');
                }
            } catch (err) {
                showStatus(false, 'Error de conexión. Intenta de nuevo o escríbenos por WhatsApp.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Enviar mensaje';
            }
        });
    }
})();
