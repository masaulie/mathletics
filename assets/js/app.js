(function () {
  'use strict';
  var ML = window.ML || (window.ML = {});

  function math(el) {
    if (window.renderMathInElement) {
      try {
        window.renderMathInElement(el || document.body, {
          delimiters: [{ left: '$$', right: '$$', display: true }, { left: '$', right: '$', display: false }],
          throwOnError: false
        });
      } catch (e) { /* abaikan */ }
    }
  }
  ML.math = math;

  function ls(k, v) {
    try {
      if (v === undefined) return window.localStorage.getItem(k);
      window.localStorage.setItem(k, v);
    } catch (e) { return null; }
  }
  function $(id) { return document.getElementById(id); }

  /* simbol matematika melayang di latar */
  function floatMath(box, n, white) {
    if (!box || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var sym = ['+', '-', '×', '÷', '=', '∑', '∫', 'π', '√', '∞', 'x²', 'α', 'β', '∂', '≈'];
    var frag = document.createDocumentFragment();
    for (var i = 0; i < n; i++) {
      var el = document.createElement('div');
      el.className = 'math-item';
      el.textContent = sym[Math.floor(Math.random() * sym.length)];
      el.style.left = Math.random() * 100 + '%';
      el.style.fontSize = (Math.random() * 2 + 1.2) + 'rem';
      el.style.animationDuration = (Math.random() * 14 + 14) + 's';
      el.style.animationDelay = '-' + Math.random() * 20 + 's';
      frag.appendChild(el);
    }
    box.appendChild(frag);
  }

  document.addEventListener('DOMContentLoaded', function () {
    math(document.body);
    if (!document.querySelector('.login') && !document.body.classList.contains('pub')) floatMath($('mathCanvas'), 14);

    /* konfirmasi sebelum kirim form berbahaya */
    document.addEventListener('submit', function (e) {
      var t = e.target.getAttribute && e.target.getAttribute('data-confirm');
      if (t && !window.confirm(t)) e.preventDefault();
    });

    /* sidebar: ciutkan, laci, dropdown akun */
    var sb = $('sidebar'), tg = $('sbToggle');
    if (sb && ls('mj_sb') === '1' && window.innerWidth >= 900) sb.classList.add('collapsed');
    if (tg) tg.addEventListener('click', function () {
      if (window.innerWidth < 900) { sb.classList.remove('open'); var sc = $('scrim'); if (sc) sc.classList.remove('show'); return; }
      sb.classList.toggle('collapsed');
      ls('mj_sb', sb.classList.contains('collapsed') ? '1' : '0');
    });
    var mb = $('menuBtn'), scrim = $('scrim');
    if (mb && sb) {
      mb.addEventListener('click', function () { sb.classList.add('open'); if (scrim) scrim.classList.add('show'); });
      if (scrim) scrim.addEventListener('click', function () { sb.classList.remove('open'); scrim.classList.remove('show'); });
    }
    var av = $('avBtn'), dd = $('dd');
    if (av && dd) {
      av.addEventListener('click', function (e) {
        e.stopPropagation();
        var on = dd.classList.toggle('show');
        av.setAttribute('aria-expanded', on ? 'true' : 'false');
      });
      document.addEventListener('click', function (e) { if (!dd.contains(e.target)) { dd.classList.remove('show'); av.setAttribute('aria-expanded', 'false'); } });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape') dd.classList.remove('show'); });
    }

    /* hitung mundur TKA */
    var cd = $('cd');
    if (cd) {
      var target = new Date(cd.getAttribute('data-target')).getTime();
      var parts = { d: $('cdD'), h: $('cdH'), m: $('cdM') };
      var pad = function (n) { return String(n).padStart(2, '0'); };
      var tick = function () {
        var left = Math.max(0, Math.floor((target - Date.now()) / 1000));
        parts.d.textContent = String(Math.floor(left / 86400));
        parts.h.textContent = pad(Math.floor(left % 86400 / 3600));
        parts.m.textContent = pad(Math.floor(left % 3600 / 60));
      };
      tick(); setInterval(tick, 30000);
    }

    /* contoh soal di beranda */
    var card = $('contoh');
    if (card) {
      var kunci = 'C', bahas = $('bahas'), done = false;
      card.querySelectorAll('.opsi button').forEach(function (b) {
        b.addEventListener('click', function () {
          if (done) return;
          done = true;
          var k = b.getAttribute('data-k');
          card.querySelectorAll('.opsi button').forEach(function (x) {
            if (x.getAttribute('data-k') === kunci) x.classList.add('benar');
          });
          if (k !== kunci) b.classList.add('salah');
          if (bahas) bahas.style.display = 'block';
        });
      });
    }

    /* popup jenjang lain (tidak pernah terbuka otomatis) */
    var dlg = $('jenjangDlg');
    if (dlg && typeof dlg.showModal === 'function') {
      var form = $('tungguForm');
      document.querySelectorAll('[data-jenjang]').forEach(function (t) {
        t.addEventListener('click', function () { form.style.display = 'none'; dlg.showModal(); });
      });
      var tutup = $('jenjangTutup');
      if (tutup) tutup.addEventListener('click', function () { dlg.close(); });
      dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
      dlg.querySelectorAll('.pjl button').forEach(function (b) {
        b.addEventListener('click', function () {
          var kode = b.getAttribute('data-kode');
          if (b.getAttribute('data-aktif') === '1') { dlg.close(); }
          else {
            $('tungguJudul').textContent = b.getAttribute('data-nama');
            $('tungguKode').value = kode;
            $('tungguPesan').textContent = '';
            form.style.display = 'block';
            $('tNama').focus();
          }
        });
      });
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var msg = $('tungguPesan');
        msg.textContent = 'Mengirim...';
        fetch(ML.base + '/tunggu', { method: 'POST', body: new FormData(form), headers: { 'X-CSRF': ML.csrf }, credentials: 'same-origin' })
          .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
          .then(function (res) {
            if (res.ok && res.j.ok) { msg.textContent = 'Tercatat. Kami kabari lewat WhatsApp.'; form.reset(); }
            else msg.textContent = (res.j && res.j.pesan) || 'Gagal mengirim. Coba lagi.';
          })
          .catch(function () { msg.textContent = 'Koneksi bermasalah. Coba lagi.'; });
      });
    }

    /* halaman masuk */
    var lf = $('loginForm');
    if (lf) {
      floatMath($('loginMath'), 18);

      var msgs = {
        siswa: ['Siap lanjut belajar hari ini?', 'Materi dan jadwal kelasmu ada di sini', 'Masukkan nomor HP dan password kamu'],
        admin: ['Pantau pendaftaran dan pembayaran', 'Kelola materi, soal, dan tes', 'Masukkan email dan password admin']
      };
      var mode = (lf.querySelector('input[name=mode]:checked') || {}).value || 'siswa';
      var wt = $('welcomeText'), wi = 0, wtimer;
      var showW = function () {
        wt.classList.remove('show'); void wt.offsetWidth;
        wt.textContent = msgs[mode][wi % msgs[mode].length];
        wt.classList.add('show');
      };
      showW();
      wtimer = setInterval(function () { wi++; showW(); }, 3000);

      lf.querySelectorAll('input[name=mode]').forEach(function (r) {
        r.addEventListener('change', function () {
          mode = this.value; wi = 0; showW();
          var a = mode === 'admin';
          $('identL').textContent = a ? 'Email admin' : 'Nomor HP (contoh: 0812...)';
          $('ident').setAttribute('inputmode', a ? 'email' : 'tel');
          $('ident').value = '';
          $('lres').textContent = '';
        });
      });

      /* kutipan berputar */
      var quotes = [
        { t: 'Dalam matematika, seni merumuskan pertanyaan jauh lebih berharga daripada sekadar menemukan jawabannya.', a: 'Georg Cantor' },
        { t: 'Tanpa matematika, tidak ada yang bisa kamu lakukan. Segala hal di sekitarmu adalah matematika. Segala hal di sekitarmu adalah angka.', a: 'Shakuntala Devi' },
        { t: 'Matematika adalah tempat di mana kamu bisa melakukan hal-hal yang tidak bisa kamu lakukan di dunia nyata.', a: 'Marcus du Sautoy' },
        { t: 'Cara terbaik untuk mempelajari matematika adalah dengan melakukan matematika itu sendiri.', a: 'Paul Halmos' },
        { t: 'Banyak hal di dunia ini yang terlihat rumit, sampai akhirnya matematika berhasil menyederhanakannya.', a: 'John von Neumann' }
      ];
      var qi = 0, qt = $('quoteText'), qa = $('quoteAuthor'), qd = $('quoteDots');
      var showQ = function () {
        var q = quotes[qi];
        qt.classList.remove('qfade'); qa.classList.remove('qfade'); void qt.offsetWidth;
        qt.textContent = q.t; qa.textContent = '— ' + q.a;
        qt.classList.add('qfade'); qa.classList.add('qfade');
        qd.innerHTML = '';
        quotes.forEach(function (_, i) {
          var d = document.createElement('div'); d.className = 'qdot' + (i === qi ? ' on' : ''); qd.appendChild(d);
        });
      };
      showQ();
      setInterval(function () { qi = (qi + 1) % quotes.length; showQ(); }, 5000);

      /* kilau saat kursor mendekat */
      var groups = Array.prototype.slice.call(document.querySelectorAll('.fg'));
      window.addEventListener('mousemove', function (e) {
        groups.forEach(function (fg) {
          var r = fg.getBoundingClientRect();
          var d = Math.hypot(e.clientX - (r.left + r.width / 2), e.clientY - (r.top + r.height / 2));
          fg.style.setProperty('--glow', Math.max(0, 1 - d / 220).toFixed(2));
        });
      }, { passive: true });

      /* riak tombol dan status mengirim */
      var btn = $('btnLogin');
      btn.addEventListener('click', function (e) {
        var rect = btn.getBoundingClientRect(), size = Math.max(rect.width, rect.height) * 1.2;
        var rp = document.createElement('span');
        rp.className = 'ripple-el';
        rp.style.width = rp.style.height = size + 'px';
        rp.style.left = (e.clientX - rect.left - size / 2) + 'px';
        rp.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(rp);
        setTimeout(function () { rp.remove(); }, 650);
      });
      lf.addEventListener('submit', function () {
        $('btnLabel').textContent = 'MEMVERIFIKASI...';
        setTimeout(function () { btn.disabled = true; }, 0);
      });
    }
  });
})();
