<?php
$title = '';
$popup = true;
$math = true;
$wa = setting('wa');
$hari = max(0, (int)ceil((strtotime('2027-03-29') - time()) / 86400));
?>
<section class="hero">
  <div class="wrap">
    <div>
      <span class="eyebrow">TKA SMP 2027 &middot; Matematika kelas 9</span>
      <h1>Kelas Matematika TKA, maksimal 8 siswa.</h1>
      <p class="lead">14 pertemuan online lewat video call, 90 menit tiap pertemuan, di malam hari. Anak dites dulu, jadi pembahasan mulai dari topik yang memang lemah.</p>
      <div class="cta">
        <?php if ($diag && $diag['jumlah'] > 0): ?><a class="btn" href="<?= h(url('tes')) ?>">Coba tes diagnostik gratis <?= icon('arrow') ?></a><?php endif; ?>
        <a class="btn ghost" href="#paket">Lihat paket</a>
      </div>
      <div class="chips">
        <span class="chip"><?= icon('cal') ?>14 pertemuan</span>
        <span class="chip"><?= icon('users') ?>Maks. 8 siswa</span>
        <span class="chip"><?= icon('clock') ?>Senin-Jumat malam</span>
      </div>
    </div>
    <div class="stack">
    <div class="cdhero" id="cd" data-target="2027-03-29T00:00:00+07:00">
      <span class="eyebrow" style="background:rgba(255,255,255,.14);border-color:rgba(255,255,255,.3);color:#fff">Hitung mundur</span>
      <h2>TKA SMP dimulai 29 Maret 2027</h2>
      <p class="sub">Waktu persiapanmu tinggal:</p>
      <div class="cd-grid">
        <div class="cd-box"><div class="cd-num" id="cdD"><?= (int)$hari ?></div><div class="cd-unit">Hari</div></div>
        <div class="cd-box"><div class="cd-num" id="cdH">00</div><div class="cd-unit">Jam</div></div>
        <div class="cd-box"><div class="cd-num" id="cdM">00</div><div class="cd-unit">Menit</div></div>
        <div class="cd-box"><div class="cd-num" id="cdS">00</div><div class="cd-unit">Detik</div></div>
      </div>
      <a class="btn white" href="#paket">Amankan kursi</a>
    </div>
    <div class="soalcard" id="contoh" aria-label="Contoh soal">
      <span class="tag">Contoh soal &middot; Aljabar</span>
      <p class="q">Jika $3x-7=11$, maka nilai $2x+1$ adalah ...</p>
      <ul class="opsi">
        <li><button type="button" data-k="A"><span class="k">A</span>11</button></li>
        <li><button type="button" data-k="B"><span class="k">B</span>12</button></li>
        <li><button type="button" data-k="C"><span class="k">C</span>13</button></li>
        <li><button type="button" data-k="D"><span class="k">D</span>15</button></li>
      </ul>
      <div class="bahas" id="bahas"><b>Pembahasan.</b> $3x=18$, jadi $x=6$. Maka $2x+1=2(6)+1=13$.</div>
    </div>
    </div>
  </div>
</section>

<section class="sec" id="cara">
  <div class="wrap">
    <span class="eyebrow">Cara kerja</span>
    <h2>Tiga langkah dari coba sampai belajar.</h2>
    <ol class="steps">
      <li><div><b>Kerjakan tes diagnostik</b><span class="mute">Gratis, tanpa akun. Hasilnya menunjukkan topik mana yang kuat dan mana yang perlu dilatih.</span></div></li>
      <li><div><b>Pilih paket dan daftar</b><span class="mute">Isi data, bayar lewat QRIS atau transfer, lalu unggah bukti. Kami konfirmasi lewat WhatsApp.</span></div></li>
      <li><div><b>Belajar di akun siswa</b><span class="mute">Jadwal, tautan kelas, modul, worksheet, dan slide ada di satu tempat. Tinggal masuk dari HP.</span></div></li>
    </ol>
  </div>
</section>

<section class="sec" id="paket">
  <div class="wrap">
    <span class="eyebrow">Paket</span>
    <h2>Paket yang sedang dibuka.</h2>
    <?php if ($paket): ?>
      <div class="paketlist">
        <?php foreach ($paket as $p): $sisa = kuota_sisa($p); ?>
          <article class="paketcard">
            <div><h3 style="margin:0"><?= h($p['nama']) ?></h3><p class="mute" style="margin:4px 0 0"><?= h($p['ringkas']) ?></p></div>
            <div><span class="harga"><?= h(rupiah($p['harga'])) ?></span><?php if ($p['harga_normal']): ?><span class="normal"><?= h(rupiah($p['harga_normal'])) ?></span><?php endif; ?></div>
            <div class="meta">
              <?php if ($p['jadwal']): ?><span><?= icon('clock') ?><?= h($p['jadwal']) ?></span><?php endif; ?>
              <?php if ($sisa !== null): ?><span><?= icon('users') ?>Sisa <?= (int)$sisa ?> kursi</span><?php endif; ?>
            </div>
            <a class="btn <?= $sisa === 0 ? 'ghost' : '' ?>" href="<?= h(url('paket/' . $p['slug'])) ?>"><?= $sisa === 0 ? 'Kuota penuh, lihat detail' : 'Lihat dan daftar' ?></a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="mute">Paket belum dibuka. Coba tes diagnostik dulu<?= $wa ? ' atau tanya kami lewat WhatsApp' : '' ?>.</p>
    <?php endif; ?>
  </div>
</section>

<section class="sec" id="tka">
  <div class="wrap">
    <span class="eyebrow">Tentang TKA SMP</span>
    <h2>Yang perlu orang tua tahu.</h2>
    <div class="faq">
      <details><summary>Apa itu TKA SMP?</summary><p>Tes Kemampuan Akademik untuk kelas 9. Mata pelajaran yang diuji adalah Matematika dan Bahasa Indonesia, masing-masing 30 soal. TKA bukan ujian kelulusan.</p></details>
      <details><summary>Kapan TKA SMP 2027?</summary><p>Pelaksanaan dimulai 29 Maret 2027, dengan pendaftaran 6 Januari sampai 7 Februari 2027. Pastikan jadwal terbaru di sekolah anak.</p></details>
      <details><summary>Bagaimana kelasnya berjalan?</summary><p>Lewat video call, 90 menit per pertemuan, maksimal 8 siswa. Tautan kelas dan jadwal muncul di akun siswa setelah pendaftaran dikonfirmasi.</p></details>
      <details><summary>Bagaimana cara bayar?</summary><p>Setelah mengisi pendaftaran, kamu mendapat petunjuk QRIS atau transfer. Unggah foto bukti, lalu kami konfirmasi dan akun siswa dikirim lewat WhatsApp.</p></details>
      <details><summary>Perlu alat apa?</summary><p>HP atau laptop dengan internet yang stabil, buku tulis, dan alat tulis. Materi dan worksheet bisa dibuka dari akun siswa.</p></details>
    </div>
  </div>
</section>
