<?php
$title = $title ?? '';
$pakaiAnalitik = true;
$cp = current_path();
$noindex = $noindex ?? (http_response_code() !== 200 || preg_match('#^/(daftar|ujian|akun|masuk)(/|$)#', $cp) === 1);
require __DIR__ . '/_head.php';
$wa = setting('wa');
$u = me();
$nama = setting('nama_usaha', 'Mathletics');
?>
<body class="pub">
<?php require __DIR__ . '/_bg.php'; ?>
<header class="liquid">
  <div class="in">
    <a class="brand" href="<?= h(url('')) ?>" aria-label="<?= h($nama) ?>"><?= brand_mark() ?><span>Math<b>letics</b></span></a>
    <nav class="nav" aria-label="Menu utama">
      <a class="pillbtn opt" href="<?= h(url('paket')) ?>">Paket</a>
      <a class="pillbtn opt" href="<?= h(url('tes')) ?>">Tes gratis</a>
      <?php if ($u): ?><a class="pillbtn solid" href="<?= h(url(home_for($u))) ?>">Buka akun</a><?php else: ?><a class="pillbtn solid" href="<?= h(url('masuk')) ?>"><?= icon('out') ?>&nbsp;Masuk</a><?php endif; ?>
    </nav>
  </div>
</header>
<?php $fl = flash_html(flashes()); if ($fl !== '') echo '<div class="wrap flashwrap">' . $fl . '</div>'; ?>
<?= $content ?>
<footer class="foot">
  <div class="wrap">
    <div class="foot-grid">
      <div>
        <a class="brand" href="<?= h(url('')) ?>"><?= brand_mark() ?><span>Math<b>letics</b></span></a>
        <p class="mute foot-about">Bimbingan Matematika online dengan kelas kecil, tes diagnostik, dan materi yang bisa dibuka kapan saja.</p>
      </div>
      <nav aria-label="Tautan situs">
        <b>Jelajahi</b>
        <a href="<?= h(url('paket')) ?>">Paket dan harga</a>
        <a href="<?= h(url('tes')) ?>">Tes diagnostik gratis</a>
        <a href="<?= h(url('masuk')) ?>">Masuk akun</a>
        <button type="button" class="linkbtn" data-jenjang>Jenjang lain (SD, SMA, UTBK)</button>
      </nav>
      <nav aria-label="Informasi">
        <b>Informasi</b>
        <a href="<?= h(url('privasi')) ?>">Kebijakan privasi</a>
        <a href="<?= h(url('syarat')) ?>">Syarat dan ketentuan</a>
        <?php if ($wa): ?><a href="<?= h(wa_link($wa, 'Halo, saya mau tanya soal kelas Mathletics.')) ?>" rel="noopener">WhatsApp <?= h($wa) ?></a><?php endif; ?>
      </nav>
    </div>
    <div class="foot-copy">&copy; <?= date('Y') ?> <?= h($nama) ?>. Semua hak dilindungi.</div>
  </div>
</footer>
<?php if ($wa): ?>
<a class="wafab" href="<?= h(wa_link($wa, 'Halo, saya mau tanya soal kelas Mathletics.')) ?>" rel="noopener" aria-label="Chat WhatsApp"><?= icon('chat') ?><span>Tanya via WhatsApp</span></a>
<?php endif; ?>
<?php require __DIR__ . '/_popup.php'; ?>
</body>
</html>
