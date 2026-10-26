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
    let lastUrl = null;
    let QR = null;

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

    const refresh = async () => {
        const wait = 4; // status siswa diperbarui tiap 4 detik; QR digambar ulang hanya saat token berganti
        try {
            const res = await fetch(qrBox.dataset.liveUrl, { headers: { Accept: 'application/json' } });
            const d = await res.json();
            paint(d.records);
            if (d.url && canvas) {
                if (d.url !== lastUrl) {
                    QR = QR || (await import('qrcode')).default;
                    await QR.toCanvas(canvas, d.url, { width: 256, margin: 1 });
                    lastUrl = d.url;
                    if (bar) {
                        bar.style.transition = 'none';
                        bar.style.transform = `scaleX(${d.expires_in / 20})`;
                        void bar.offsetWidth;
                        bar.style.transition = `transform ${d.expires_in}s linear`;
                        bar.style.transform = 'scaleX(0)';
                    }
                }
            }
        } catch (e) {
            /* jaringan putus: coba lagi */
        }
        setTimeout(refresh, wait * 1000);
    };
    refresh();
}
