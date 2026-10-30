(function () {
    'use strict';
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    function toast(msg) {
        var t = $('#toast');
        t.textContent = msg;
        t.classList.add('show');
        clearTimeout(toast._t);
        toast._t = setTimeout(function () { t.classList.remove('show'); }, 2200);
    }

    /* ---------- Buka undangan ---------- */
    var cover = $('#cover'), bgm = $('#bgm'), musicBtn = $('#musicBtn');
    $('#openBtn').addEventListener('click', function () {
        cover.classList.add('open');
        document.body.classList.remove('locked');
        $('#bottomNav').classList.add('show');
        window.scrollTo(0, 0);
        if (bgm) {
            musicBtn.hidden = false;
            bgm.play().then(function () { musicBtn.classList.add('playing'); }).catch(function () {});
        }
        setTimeout(function () { cover.style.display = 'none'; }, 1100);
    });
    if (bgm) {
        musicBtn.addEventListener('click', function () {
            if (bgm.paused) { bgm.play(); musicBtn.classList.add('playing'); }
            else { bgm.pause(); musicBtn.classList.remove('playing'); }
        });
    }

    /* ---------- Animasi muncul saat scroll ---------- */
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            });
        }, { threshold: 0.12 });
        $$('.reveal').forEach(function (el) { io.observe(el); });

        var navLinks = $$('.bottom-nav a');
        var spy = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) {
                    navLinks.forEach(function (a) {
                        a.classList.toggle('active', a.getAttribute('href') === '#' + en.target.id);
                    });
                }
            });
        }, { rootMargin: '-45% 0px -50% 0px' });
        navLinks.forEach(function (a) {
            var s = $(a.getAttribute('href'));
            if (s) { spy.observe(s); }
        });
    } else {
        $$('.reveal').forEach(function (el) { el.classList.add('in'); });
    }

    /* ---------- Hitung mundur ---------- */
    var target = new Date(document.body.getAttribute('data-target')).getTime();
    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        var diff = target - Date.now();
        if (isNaN(target)) { return; }
        if (diff <= 0) {
            $('#countdown').hidden = true;
            $('#cd-done').hidden = false;
            return;
        }
        var s = Math.floor(diff / 1000);
        $('#cd-d').textContent = pad(Math.floor(s / 86400));
        $('#cd-h').textContent = pad(Math.floor(s % 86400 / 3600));
        $('#cd-m').textContent = pad(Math.floor(s % 3600 / 60));
        $('#cd-s').textContent = pad(s % 60);
    }
    tick();
    setInterval(tick, 1000);

    /* ---------- Simpan ke Google Calendar ---------- */
    if (!isNaN(target)) {
        var fmt = function (d) { return d.toISOString().replace(/[-:]|\.\d{3}/g, ''); };
        var start = new Date(target), end = new Date(target + 4 * 3600 * 1000);
        $('#saveDate').href = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            + '&text=' + encodeURIComponent(window.INV.title)
            + '&dates=' + fmt(start) + '/' + fmt(end)
            + '&location=' + encodeURIComponent(window.INV.place + ', ' + window.INV.address);
    }

    /* ---------- Salin nomor rekening ---------- */
    $$('.copy').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var txt = btn.getAttribute('data-copy');
            var done = function () { toast('Berhasil disalin'); };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(txt).then(done, fallback);
            } else { fallback(); }
            function fallback() {
                var ta = document.createElement('textarea');
                ta.value = txt; ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta); ta.select();
                try { document.execCommand('copy'); done(); } catch (e) { toast('Gagal menyalin'); }
                document.body.removeChild(ta);
            }
        });
    });

    /* ---------- Lightbox galeri ---------- */
    var lb = $('#lightbox'), lbImg = $('img', lb);
    $$('[data-lightbox]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            lbImg.src = a.href;
            lb.hidden = false;
        });
    });
    lb.addEventListener('click', function () { lb.hidden = true; lbImg.src = ''; });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { lb.hidden = true; } });

    /* ---------- RSVP & ucapan ---------- */
    var form = $('#rsvpForm'), msg = $('#rsvpMsg'), btn = $('#rsvpBtn'), wishes = $('#wishes');
    var labels = { hadir: 'Hadir', tidak: 'Tidak Hadir', ragu: 'Masih Ragu' };

    function ago(iso) {
        var d = new Date(iso), s = Math.max(0, (Date.now() - d.getTime()) / 1000);
        if (isNaN(s)) { return ''; }
        if (s < 60) { return 'baru saja'; }
        if (s < 3600) { return Math.floor(s / 60) + ' menit lalu'; }
        if (s < 86400) { return Math.floor(s / 3600) + ' jam lalu'; }
        if (s < 2592000) { return Math.floor(s / 86400) + ' hari lalu'; }
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }
    function wishEl(w) {
        var el = document.createElement('div'); el.className = 'wish';
        var head = document.createElement('div'); head.className = 'wish-head';
        var n = document.createElement('span'); n.className = 'wish-name'; n.textContent = w.name;
        var b = document.createElement('span'); b.className = 'badge ' + w.attendance; b.textContent = labels[w.attendance] || '';
        head.appendChild(n); head.appendChild(b);
        var p = document.createElement('p'); p.textContent = w.message;      // textContent => aman dari XSS
        var t = document.createElement('time'); t.textContent = ago(w.time);
        el.appendChild(head); el.appendChild(p); el.appendChild(t);
        return el;
    }
    function render(data) {
        var st = data.stats;
        $('#stats').hidden = false;
        $('#st-hadir').textContent = st.hadir;
        $('#st-ragu').textContent = st.ragu;
        $('#st-tidak').textContent = st.tidak;
        wishes.innerHTML = '';
        if (!data.wishes.length) {
            wishes.innerHTML = '<p class="muted center">Jadilah yang pertama memberi ucapan.</p>';
            return;
        }
        data.wishes.forEach(function (w) { wishes.appendChild(wishEl(w)); });
    }
    function load() {
        fetch('api.php?action=list', { cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (d.ok) { render(d); } })
            .catch(function () { wishes.innerHTML = '<p class="muted center">Ucapan belum dapat dimuat.</p>'; });
    }
    load();

    var guestsWrap = $('#guestsWrap');
    $$('input[name=attendance]', form).forEach(function (r) {
        r.addEventListener('change', function () {
            guestsWrap.style.display = form.attendance.value === 'hadir' ? '' : 'none';
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        msg.className = 'form-msg'; msg.textContent = '';
        if (form.name.value.trim().length < 2) {
            msg.className = 'form-msg err'; msg.textContent = 'Mohon isi nama Anda.'; form.name.focus(); return;
        }
        btn.disabled = true; btn.textContent = 'Mengirim...';
        fetch('api.php', { method: 'POST', body: new FormData(form) })
            .then(function (r) { return r.json().catch(function () { return { ok: false, error: 'Respons server tidak valid.' }; }); })
            .then(function (d) {
                if (d.ok) {
                    msg.className = 'form-msg ok';
                    msg.textContent = 'Terima kasih! Konfirmasi dan ucapan Anda sudah kami terima.';
                    form.message.value = '';
                    toast('Terkirim, terima kasih!');
                    load();
                } else {
                    msg.className = 'form-msg err'; msg.textContent = d.error || 'Gagal mengirim.';
                }
            })
            .catch(function () { msg.className = 'form-msg err'; msg.textContent = 'Koneksi bermasalah, coba lagi.'; })
            .then(function () { btn.disabled = false; btn.textContent = 'Kirim'; });
    });
})();
