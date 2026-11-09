(function () {
    'use strict';
    const SITE = window.SITE || {};
    const launcher = document.getElementById('chat-launcher');
    const panel = document.getElementById('chat-panel');
    if (!launcher || !panel) return;

    const list = document.getElementById('chat-messages');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send');
    const chips = document.getElementById('chat-chips');
    let loaded = false, busy = false;

    const WELCOME = '¡Hola! 👋 Soy el ' + (SITE.chatbotName || 'asistente virtual') +
        '. Puedo ayudarte con **horarios, servicios, doctores y solicitudes de cita**. ¿En qué te ayudo?';

    /* ---------- Formato seguro del texto (escapa HTML y aplica un markdown mínimo) ---------- */
    function esc(s) {
        return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
    function format(text) {
        let h = esc(text);
        h = h.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        h = h.replace(/\b(https?:\/\/[^\s<]+[^\s<.,;:)])/g, '<a href="$1" target="_blank" rel="noopener" class="underline">$1</a>');
        h = h.replace(/(^|[\s(])((?:\+?1[\s-]?)?8[024]9[\s-]?\d{3}[\s-]?\d{4})\b/g, (m, pre, num) =>
            pre + '<a href="tel:' + num.replace(/[^\d+]/g, '') + '" class="underline font-semibold">' + num + '</a>');
        h = h.replace(/\b9-1-1\b|\b911\b/g, '<a href="tel:911" class="underline font-semibold text-red-600">$&</a>');
        // Listas con "- " o "• " y saltos de línea
        const lines = h.split(/\n/);
        let out = '', inList = false;
        for (const ln of lines) {
            const m = ln.match(/^\s*(?:[-•*]|\d+\.)\s+(.*)/);
            if (m) { if (!inList) { out += '<ul class="list-disc pl-5 my-1 space-y-0.5">'; inList = true; } out += '<li>' + m[1] + '</li>'; }
            else { if (inList) { out += '</ul>'; inList = false; } out += ln ? ln + '<br>' : '<br>'; }
        }
        if (inList) out += '</ul>';
        return out.replace(/(<br>)+$/, '');
    }

    /* ---------- Burbujas ---------- */
    function bubble(role, text, extraClass) {
        const row = document.createElement('div');
        row.className = 'flex ' + (role === 'user' ? 'justify-end' : 'justify-start');
        const b = document.createElement('div');
        b.className = (role === 'user'
            ? 'bg-teal-600 text-white rounded-2xl rounded-br-md'
            : 'bg-white text-slate-800 border border-slate-200 rounded-2xl rounded-bl-md') +
            ' px-4 py-2.5 max-w-[85%] text-[15px] leading-relaxed shadow-sm break-words ' + (extraClass || '');
        b.innerHTML = role === 'user' ? esc(text).replace(/\n/g, '<br>') : format(text);
        row.appendChild(b);
        list.appendChild(row);
        list.scrollTop = list.scrollHeight;
        return row;
    }
    function typing() {
        const row = document.createElement('div');
        row.className = 'flex justify-start';
        row.innerHTML = '<div class="bg-white border border-slate-200 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm" aria-label="El asistente está escribiendo">' +
            '<span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>';
        list.appendChild(row);
        list.scrollTop = list.scrollHeight;
        return row;
    }
    function notice(html) {
        const row = document.createElement('div');
        row.className = 'flex justify-center';
        row.innerHTML = '<div class="text-xs bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl px-3 py-2 text-center">' + html + '</div>';
        list.appendChild(row);
        list.scrollTop = list.scrollHeight;
    }

    /* ---------- Servidor ---------- */
    async function api(body) {
        const res = await fetch('chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(Object.assign({ csrf: SITE.csrf }, body)),
            credentials: 'same-origin'
        });
        let data = {};
        try { data = await res.json(); } catch (e) { /* respuesta no JSON */ }
        return data;
    }

    async function loadHistory() {
        loaded = true;
        bubble('assistant', WELCOME);
        try {
            const data = await api({ action: 'history' });
            (data.messages || []).forEach(m => bubble(m.role === 'user' ? 'user' : 'assistant', m.content));
        } catch (e) { /* sin historial */ }
    }

    async function send(text) {
        text = (text || '').trim();
        if (!text || busy) return;
        busy = true;
        sendBtn.disabled = true;
        bubble('user', text);
        input.value = '';
        autoGrow();
        const t = typing();
        try {
            const data = await api({ action: 'message', message: text });
            t.remove();
            bubble('assistant', data.reply || 'Lo siento, ocurrió un error. Intenta de nuevo.', data.ok === false ? 'border-amber-300 bg-amber-50' : '');
            if (data.cita && data.cita.numero) {
                notice('✅ Solicitud <strong>' + esc(data.cita.numero) + '</strong> registrada. Te llamaremos para confirmar.');
            }
        } catch (e) {
            t.remove();
            bubble('assistant', 'No hay conexión en este momento. Revisa tu internet o llámanos.', 'border-amber-300 bg-amber-50');
        } finally {
            busy = false;
            sendBtn.disabled = false;
            if (window.matchMedia('(min-width: 640px)').matches) input.focus();
        }
    }

    /* ---------- Abrir / cerrar ---------- */
    function open(prefill) {
        panel.hidden = false;
        panel.classList.add('flex');
        launcher.setAttribute('aria-expanded', 'true');
        launcher.classList.add('chat-launcher-hidden');
        document.body.classList.add('chat-open');
        if (!loaded) loadHistory();
        if (prefill) setTimeout(() => send(prefill), loaded ? 0 : 150);
        else setTimeout(() => input.focus(), 50);
    }
    function close() {
        panel.hidden = true;
        panel.classList.remove('flex');
        launcher.setAttribute('aria-expanded', 'false');
        launcher.classList.remove('chat-launcher-hidden');
        document.body.classList.remove('chat-open');
        launcher.focus();
    }

    launcher.addEventListener('click', () => panel.hidden ? open() : close());
    document.getElementById('chat-close').addEventListener('click', close);
    document.getElementById('chat-reset').addEventListener('click', async () => {
        if (busy) return;
        try { await api({ action: 'reset' }); } catch (e) {}
        list.innerHTML = '';
        bubble('assistant', WELCOME);
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !panel.hidden) close(); });

    // Cualquier enlace o botón con data-open-chat abre el chat con ese mensaje
    document.addEventListener('click', e => {
        const el = e.target.closest('[data-open-chat]');
        if (!el) return;
        e.preventDefault();
        const modalClose = document.getElementById('modal-close');
        const modal = document.getElementById('serviceModal');
        if (modal && !modal.classList.contains('hidden') && modalClose) modalClose.click();
        open(el.getAttribute('data-open-chat'));
    });

    chips.addEventListener('click', e => {
        const c = e.target.closest('.chat-chip');
        if (c) send(c.dataset.q);
    });

    form.addEventListener('submit', e => { e.preventDefault(); send(input.value); });
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(input.value); }
    });
    function autoGrow() { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 112) + 'px'; }
    input.addEventListener('input', autoGrow);
})();
