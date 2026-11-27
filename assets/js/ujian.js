(function () {
  'use strict';
  var el = document.getElementById('exdata');
  if (!el) return;
  var d = JSON.parse(el.textContent);
  var S = d.soal, A = d.jawab || {}, cur = 0;
  var end = Date.now() + d.sisa * 1000;
  var dirty = {}, sending = false, submitted = false;
  var LS = 'mx_' + d.token;

  function $(id) { return document.getElementById(id); }
  function store() { try { localStorage.setItem(LS, JSON.stringify(A)); } catch (e) { } }
  try {
    var loc = JSON.parse(localStorage.getItem(LS) || '{}');
    Object.keys(loc).forEach(function (k) { if (!A[k] && loc[k] && loc[k].p) { A[k] = loc[k]; dirty[k] = true; } });
  } catch (e) { }

  var saved = $('saved');
  function say(t) { saved.textContent = t; }

  function flush() {
    if (sending) return Promise.resolve();
    var ids = Object.keys(dirty);
    if (!ids.length) return Promise.resolve();
    sending = true;
    say('Menyimpan...');
    return Promise.all(ids.map(function (id) {
      var a = A[id] || { p: '', r: 0 };
      var fd = new FormData();
      fd.append('soal', id); fd.append('pilihan', a.p || ''); fd.append('ragu', a.r ? '1' : '0');
      return fetch(d.base + '/ujian/' + d.token + '/simpan', { method: 'POST', body: fd, headers: { 'X-CSRF': window.ML.csrf }, credentials: 'same-origin' })
        .then(function (r) {
          if (r.status === 409) { doSubmit(); return; }
          if (r.ok) delete dirty[id];
        });
    })).then(function () {
      sending = false;
      say(Object.keys(dirty).length ? 'Belum tersimpan, mencoba lagi...' : 'Tersimpan');
    }).catch(function () {
      sending = false;
      say('Koneksi putus. Jawabanmu aman di perangkat dan dikirim ulang otomatis.');
    });
  }
  setInterval(flush, 4000);

  function mark(id, patch) {
    var a = A[id] || { p: '', r: 0 };
    if ('p' in patch) a.p = patch.p;
    if ('r' in patch) a.r = patch.r;
    A[id] = a; dirty[id] = true; store(); paintMap(); flush();
  }

  var peta = $('peta');
  S.forEach(function (s, i) {
    var b = document.createElement('button');
    b.type = 'button'; b.textContent = String(i + 1);
    b.setAttribute('aria-label', 'Soal ' + (i + 1));
    b.addEventListener('click', function () { go(i); });
    peta.appendChild(b);
  });
  function paintMap() {
    Array.prototype.forEach.call(peta.children, function (b, i) {
      var a = A[S[i].id];
      b.className = (a && a.r ? 'rg' : (a && a.p ? 'dij' : '')) + (i === cur ? ' cur' : '');
    });
  }

  function render() {
    var s = S[cur], a = A[s.id] || { p: '', r: 0 };
    $('qno').textContent = 'Soal ' + (cur + 1) + ' dari ' + S.length;
    $('qtopik').textContent = s.topik;
    $('prog').textContent = (cur + 1) + '/' + S.length;
    $('qtext').textContent = s.teks;
    var ul = $('opsi'); ul.innerHTML = '';
    ['A', 'B', 'C', 'D'].forEach(function (k) {
      var li = document.createElement('li'), b = document.createElement('button');
      b.type = 'button';
      if (a.p === k) b.className = 'on';
      var kk = document.createElement('span'); kk.className = 'k'; kk.textContent = k;
      var tx = document.createElement('span'); tx.textContent = s.opsi[k];
      b.appendChild(kk); b.appendChild(tx);
      b.addEventListener('click', function () { mark(s.id, { p: a.p === k ? '' : k }); render(); });
      li.appendChild(b); ul.appendChild(li);
    });
    $('ragu').checked = !!a.r;
    $('prev').disabled = cur === 0;
    $('next').disabled = cur === S.length - 1;
    paintMap();
    if (window.ML && window.ML.math) window.ML.math($('qbox'));
  }
  function go(i) { cur = Math.max(0, Math.min(S.length - 1, i)); render(); window.scrollTo(0, 0); }

  $('ragu').addEventListener('change', function () { mark(S[cur].id, { r: this.checked ? 1 : 0 }); });
  $('prev').addEventListener('click', function () { go(cur - 1); });
  $('next').addEventListener('click', function () { go(cur + 1); });

  function doSubmit() {
    if (submitted) return;
    submitted = true;
    flush().then(function () { $('kumpulForm').submit(); });
    setTimeout(function () { $('kumpulForm').submit(); }, 2500);
  }
  var dlg = $('kdlg');
  $('selesai').addEventListener('click', function () {
    var kosong = 0, ragu = 0;
    S.forEach(function (s) { var a = A[s.id]; if (!a || !a.p) kosong++; if (a && a.r) ragu++; });
    $('kinfo').textContent = kosong + ' soal belum dijawab' + (ragu ? ' dan ' + ragu + ' ditandai ragu-ragu' : '') + '. Setelah dikumpulkan, jawaban tidak bisa diubah.';
    if (typeof dlg.showModal === 'function') dlg.showModal(); else if (window.confirm('Kumpulkan sekarang?')) doSubmit();
  });
  $('kbatal').addEventListener('click', function () { dlg.close(); });
  $('kyakin').addEventListener('click', doSubmit);

  var timer = $('timer');
  function tick() {
    var left = Math.max(0, Math.round((end - Date.now()) / 1000));
    var h = Math.floor(left / 3600), m = Math.floor((left % 3600) / 60), s = left % 60;
    timer.textContent = (h ? h + ':' + String(m).padStart(2, '0') : String(m).padStart(2, '0')) + ':' + String(s).padStart(2, '0');
    timer.classList.toggle('low', left <= 300);
    if (left === 0) doSubmit();
  }
  setInterval(tick, 1000); tick();

  document.addEventListener('keydown', function (e) {
    if (e.target.tagName === 'INPUT' || e.metaKey || e.ctrlKey || e.altKey) return;
    var k = e.key.toLowerCase();
    if (['a', 'b', 'c', 'd'].indexOf(k) > -1) { mark(S[cur].id, { p: k.toUpperCase() }); render(); }
    else if (e.key === 'ArrowRight') go(cur + 1);
    else if (e.key === 'ArrowLeft') go(cur - 1);
  });

  if (Object.keys(dirty).length) flush();
  render();
})();
