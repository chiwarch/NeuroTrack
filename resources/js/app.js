// ===== Landing: menu mobile + penanda menu aktif =====
const toggle = document.getElementById('nav-toggle');
const links = document.getElementById('nav-links');

if (toggle && links) {
    toggle.addEventListener('click', () => {
        const open = links.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(open));
    });
    links.querySelectorAll('a').forEach((a) =>
        a.addEventListener('click', () => {
            links.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        })
    );
}

const sections = [...document.querySelectorAll('main section[id]')];
const anchors = [...document.querySelectorAll('.nav__links a')];
if (anchors.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(
        (entries) =>
            entries.forEach((e) => {
                if (!e.isIntersecting) return;
                anchors.forEach((a) => a.classList.toggle('is-active', a.getAttribute('href') === '#' + e.target.id));
            }),
        { rootMargin: '-45% 0px -50% 0px' }
    );
    sections.forEach((s) => io.observe(s));
}

// ===== Panel: sidebar mobile =====
const side = document.getElementById('side');
const overlay = document.getElementById('side-overlay');
const sideToggle = document.getElementById('side-toggle');
if (side && sideToggle) {
    const setOpen = (open) => {
        side.classList.toggle('-translate-x-full', !open);
        overlay?.classList.toggle('hidden', !open);
    };
    sideToggle.addEventListener('click', () => setOpen(side.classList.contains('-translate-x-full')));
    overlay?.addEventListener('click', () => setOpen(false));
}

// ===== Form soal: tampilkan bagian sesuai jenis soal =====
const typeSelect = document.getElementById('q-type');
if (typeSelect) {
    const sync = () =>
        document.querySelectorAll('[data-section]').forEach((el) => {
            el.classList.toggle('hidden', el.dataset.section !== typeSelect.value);
        });
    typeSelect.addEventListener('change', sync);
    sync();
}

// ===== Kuis: hitung mundur, kirim otomatis saat waktu habis =====
const quizForm = document.getElementById('quiz-form');
const timerEl = document.getElementById('timer');
if (quizForm && timerEl) {
    let left = parseInt(quizForm.dataset.remaining, 10) || 0;
    const fmt = (s) => String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
    const tick = () => {
        timerEl.textContent = fmt(Math.max(left, 0));
        timerEl.classList.toggle('text-rose-600', left <= 60);
        if (left <= 0) {
            clearInterval(id);
            quizForm.requestSubmit();
        }
        left--;
    };
    const id = setInterval(tick, 1000);
    tick();
}

// ===== Absensi guru: QR yang berganti + status siswa langsung =====
const qrBox = document.getElementById('qr-box');
if (qrBox && qrBox.dataset.liveUrl) {
    const canvas = document.getElementById('qr-canvas');
    const bar = document.getElementById('qr-bar');
    const countEl = document.getElementById('count-in');
    const IN = ['present', 'late'];
    // offset = selisih jam server dan jam perangkat ini; window = nomor jendela waktu token yang dikirim server
    const state = { offset: 0, step: 7, window: null, url: null, nextUrl: null, shown: null };
    let QR = null;

    if (bar) bar.style.transition = 'none';

    document.querySelectorAll('[data-select]').forEach((sel) =>
        sel.addEventListener('change', () => (sel.dataset.dirty = '1'))
    );

    const paint = (records) => {
        let masuk = 0;
        records.forEach((r) => {
            if (IN.includes(r.status)) masuk++;
            const row = document.querySelector(`[data-student="${r.id}"]`);
            if (!row) return;
            const badge = row.querySelector('[data-badge]');
            badge.className = 't-badge ' + r.tone;
            badge.textContent = r.label;
            row.querySelector('[data-time]').textContent = r.time || '–';
            const sel = row.querySelector('[data-select]');
            if (sel && sel.dataset.dirty !== '1') sel.value = r.status;
        });
        if (countEl) countEl.textContent = masuk;
    };

    const drawQr = async (url) => {
        QR = QR || (await import('qrcode')).default;
        await QR.toCanvas(canvas, url, { width: 256, margin: 1 });
    };

    // Dijalankan tiap frame: bilah selalu mulai penuh di awal jendela waktu, QR diganti tepat di batas jendela.
    const tick = () => {
        if (canvas && state.url) {
            const now = Date.now() / 1000 + state.offset;
            if (bar) bar.style.transform = `scaleX(${1 - (now % state.step) / state.step})`;
            const w = Math.floor(now / state.step);
            if (w !== state.shown) {
                const url = w === state.window ? state.url : w === state.window + 1 ? state.nextUrl : null;
                if (url) {
                    state.shown = w;
                    drawQr(url);
                }
            }
        }
        requestAnimationFrame(tick);
    };

    const poll = async () => {
        const t0 = Date.now();
        try {
            const res = await fetch(qrBox.dataset.liveUrl, { headers: { Accept: 'application/json' } });
            const d = await res.json();
            const t1 = Date.now();
            state.offset = d.server_time + (t1 - t0) / 2000 - t1 / 1000; // koreksi separuh waktu tempuh
            state.step = d.step;
            paint(d.records);
            if (canvas) {
                if (d.url) {
                    state.window = Math.floor(d.server_time / d.step);
                    state.url = d.url;
                    state.nextUrl = d.next_url;
                } else if (!d.open) {
                    location.reload(); // sesi ditutup dari tempat lain
                    return;
                }
            }
        } catch (e) {
            /* jaringan putus: coba lagi */
        }
        setTimeout(poll, Math.max(1, Math.min(3, state.step - 1)) * 1000);
    };

    poll();
    requestAnimationFrame(tick);
}
