<?php
$wa = setting('wa');
$tkaLabel = setting('tka_label', 'TKA SMP 2027');
$tkaTs = strtotime($tka . ' 00:00:00 +0700');
$hari = $tkaTs ? (int)ceil(($tkaTs - time()) / 86400) : 0;
$tkaMasihAda = $hari > 0;
$tkaTeks = tgl($tka);
$jmlPertemuan = count($silabus);
$punyaSilabus = false;
foreach ($silabus as $s) if (trim((string)$s['topik']) !== '') { $punyaSilabus = true; break; }
$jadwalPendek = $kelas ? trim((string)preg_replace('/\s*\(.*?\)\s*/u', ' ', (string)$kelas['jadwal'])) : '';
$sisaKelas = $kelas ? kuota_sisa($kelas) : null;
$maks = $kelas && $kelas['kuota'] !== null && $kelas['kuota'] !== '' ? (int)$kelas['kuota'] : 0;
$pengajar = trim(setting('pengajar_nama'));
$bioPengajar = trim(setting('pengajar_bio'));
$garansi = trim(setting('garansi'));
$testi = [];
foreach (preg_split('/\R/', trim(setting('testimoni'))) ?: [] as $baris) {
    if (!str_contains($baris, '|')) continue;
    [$tn, $ti] = array_map('trim', explode('|', $baris, 2));
    if ($tn !== '' && $ti !== '') $testi[] = [$tn, $ti];
}

$title = 'Bimbel Matematika Online ' . $tkaLabel;
$desc = 'Kelas Matematika online untuk ' . $tkaLabel . ($maks ? ', maksimal ' . $maks . ' siswa per kelas' : '') . '. Mulai dari tes diagnostik gratis, lalu belajar sesuai topik yang masih lemah.';

$faq = [
    ['Apa itu TKA SMP?', 'Tes Kemampuan Akademik untuk kelas 9. Mata pelajaran yang diuji adalah Matematika dan Bahasa Indonesia, masing-masing 30 soal. TKA bukan ujian kelulusan.'],
    ['Kapan ' . $tkaLabel . '?', setting('tka_info', 'Pelaksanaan dimulai ' . $tkaTeks . '. Pastikan jadwal terbaru di sekolah anak.')],
    ['Bagaimana kelasnya berjalan?', 'Lewat video call' . ($maks ? ', maksimal ' . $maks . ' siswa' : '') . '. Tautan kelas dan jadwal muncul di akun siswa setelah pendaftaran dikonfirmasi.'],
    ['Bagaimana cara bayar?', 'Setelah mengisi pendaftaran, Anda mendapat petunjuk QRIS atau transfer. Unggah foto bukti, lalu kami konfirmasi dan akun siswa dikirim lewat WhatsApp.'],
    ['Perlu alat apa?', 'HP atau laptop dengan internet yang stabil, buku tulis, dan alat tulis. Materi dan worksheet bisa dibuka dari akun siswa.'],
];

$schema = ['@context' => 'https://schema.org', '@graph' => [
    ['@type' => 'EducationalOrganization', 'name' => setting('nama_usaha', 'Mathletics'), 'url' => abs_url(''), 'description' => $desc],
    ['@type' => 'FAQPage', 'mainEntity' => array_map(fn($f) => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], $faq)],
]];
?>
<section class="hero">
  <div class="wrap">
    <div>
      <span class="eyebrow"><?= h($tkaLabel) ?> &middot; Matematika kelas 9</span>
      <h1>Kelas Matematika TKA<?= $maks ? ', maksimal ' . $maks . ' siswa' : '' ?>.</h1>
      <p class="lead"><?= $jmlPertemuan ? h($jmlPertemuan) . ' pertemuan online lewat video call.' : 'Kelas online lewat video call.' ?> Anak dites dulu, jadi pembahasan mulai dari topik yang memang lemah.</p>
      <?php if ($kelas): ?>
      <p class="hero-harga">
        <span class="mute">Mulai dari</span>
        <b class="num"><?= h(rupiah($kelas['harga'])) ?></b>
        <?php if ($kelas['harga_normal']): ?><s class="mute"><?= h(rupiah($kelas['harga_normal'])) ?></s><?php endif; ?>
      </p>
      <?php endif; ?>
      <div class="cta">
        <?php if ($diag && $diag['jumlah'] > 0): ?>
          <a class="btn" href="<?= h(url('tes')) ?>">Coba tes diagnostik gratis <?= icon('arrow') ?></a>
          <a class="btn ghost" href="#paket">Lihat paket dan harga</a>
        <?php else: ?>
          <a class="btn" href="#paket">Lihat paket dan harga <?= icon('arrow') ?></a>
        <?php endif; ?>
      </div>
      <div class="chips">
        <?php if ($jmlPertemuan): ?><span class="chip"><?= icon('cal') ?><?= h($jmlPertemuan) ?> pertemuan</span><?php endif; ?>
        <?php if ($maks): ?><span class="chip"><?= icon('users') ?>Maks. <?= h($maks) ?> siswa</span><?php endif; ?>
        <?php if ($jadwalPendek !== ''): ?><span class="chip"><?= icon('clock') ?><?= h($jadwalPendek) ?></span><?php endif; ?>
        <?php if ($sisaKelas !== null && $sisaKelas > 0): ?><span class="chip hot"><?= icon('star') ?>Sisa <?= (int)$sisaKelas ?> kursi</span><?php endif; ?>
      </div>
    </div>
    <div class="stack">
    <?php if ($tkaMasihAda): ?>
    <div class="cdhero" id="cd" data-target="<?= h(date('c', $tkaTs)) ?>">
      <span class="eyebrow eyebrow-w">Hitung mundur</span>
      <h2><?= h($tkaLabel) ?> dimulai <?= h($tkaTeks) ?></h2>
      <p class="sub">Waktu persiapan tinggal:</p>
      <div class="cd-grid">
        <div class="cd-box"><div class="cd-num" id="cdD"><?= (int)$hari ?></div><div class="cd-unit">Hari</div></div>
        <div class="cd-box"><div class="cd-num" id="cdH">00</div><div class="cd-unit">Jam</div></div>
        <div class="cd-box"><div class="cd-num" id="cdM">00</div><div class="cd-unit">Menit</div></div>
      </div>
    </div>
    <?php endif; ?>
    <div class="soalcard" id="contoh" aria-label="Contoh soal">
      <span class="tag">Contoh soal &middot; Aljabar</span>
      <p class="q">Jika 3<i>x</i> &minus; 7 = 11, maka nilai 2<i>x</i> + 1 adalah ...</p>
      <ul class="opsi">
        <li><button type="button" data-k="A"><span class="k">A</span>11</button></li>
        <li><button type="button" data-k="B"><span class="k">B</span>12</button></li>
        <li><button type="button" data-k="C"><span class="k">C</span>13</button></li>
        <li><button type="button" data-k="D"><span class="k">D</span>15</button></li>
      </ul>
      <div class="bahas" id="bahas"><b>Pembahasan.</b> 3<i>x</i> = 18, jadi <i>x</i> = 6. Maka 2<i>x</i> + 1 = 2(6) + 1 = 13.</div>
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

<?php if ($punyaSilabus): ?>
<section class="sec" id="silabus">
  <div class="wrap">
    <span class="eyebrow">Isi kelas</span>
    <h2>Yang dipelajari di <?= h($jmlPertemuan) ?> pertemuan.</h2>
    <ol class="silabus">
      <?php foreach ($silabus as $s): ?>
        <li><span class="num"><?= (int)$s['urut'] ?></span><div><b><?= h($s['judul']) ?></b><?php if (trim((string)$s['topik']) !== ''): ?><span class="mute"><?= h($s['topik']) ?></span><?php endif; ?></div></li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<section class="sec" id="paket">
  <div class="wrap">
    <span class="eyebrow">Paket</span>
    <h2>Paket yang sedang dibuka.</h2>
    <?php if ($paket): ?>
      <div class="paketlist">
        <?php foreach ($paket as $p): $sisa = kuota_sisa($p); ?>
          <article class="paketcard">
            <div class="pc-head"><h3><?= h($p['nama']) ?></h3><p class="mute"><?= h($p['ringkas']) ?></p></div>
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
    <p class="mute jenjang-lain">Anak di SD, SMA, atau persiapan UTBK? <button type="button" class="linkbtn" data-jenjang>Kabari saya saat dibuka</button></p>
  </div>
</section>

<?php if ($garansi !== ''): ?>
<section class="sec" id="garansi">
  <div class="wrap">
    <div class="callout">
      <span class="icon-badge"><?= icon('shield') ?></span>
      <div><h2>Jaminan dan kebijakan</h2><p><?= nl2br(h($garansi)) ?></p></div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($pengajar !== '' && $bioPengajar !== ''): ?>
<section class="sec" id="pengajar">
  <div class="wrap">
    <span class="eyebrow">Pengajar</span>
    <h2><?= h($pengajar) ?></h2>
    <p class="bio"><?= nl2br(h($bioPengajar)) ?></p>
  </div>
</section>
<?php endif; ?>

<?php if ($testi): ?>
<section class="sec" id="testimoni">
  <div class="wrap">
    <span class="eyebrow">Kata orang tua dan siswa</span>
    <h2>Pengalaman mereka.</h2>
    <div class="testi">
      <?php foreach ($testi as [$tn, $ti]): ?>
        <figure class="box"><blockquote>&ldquo;<?= h($ti) ?>&rdquo;</blockquote><figcaption><?= h($tn) ?></figcaption></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="sec" id="tka">
  <div class="wrap">
    <span class="eyebrow">Tentang TKA SMP</span>
    <h2>Yang perlu orang tua tahu.</h2>
    <div class="faq">
      <?php foreach ($faq as $f): ?>
        <details><summary><?= h($f[0]) ?></summary><p><?= h($f[1]) ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="sec" id="mulai">
  <div class="wrap">
    <div class="akhir">
      <h2>Mulai dari yang gratis.</h2>
      <p>Tes diagnostik<?= $diag ? ' (' . (int)$diag['durasi'] . ' menit)' : '' ?> menunjukkan topik mana yang perlu diperkuat, tanpa perlu membuat akun.</p>
      <div class="cta">
        <?php if ($diag && $diag['jumlah'] > 0): ?><a class="btn" href="<?= h(url('tes')) ?>">Coba tes diagnostik gratis <?= icon('arrow') ?></a><?php endif; ?>
        <?php if ($wa): ?><a class="btn ghost" href="<?= h(wa_link($wa, 'Halo, saya mau tanya soal kelas Mathletics.')) ?>" rel="noopener">Tanya lewat WhatsApp</a><?php endif; ?>
      </div>
    </div>
  </div>
</section>
