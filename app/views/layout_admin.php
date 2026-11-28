<?php
$title = $title ?? '';
$noindex = true;
require __DIR__ . '/_head.php';
$variant = 'admin';
$panel = 'Panel admin';
$menu = [
  ['/admin', 'Ringkasan', 'grid'], ['/admin/pendaftaran', 'Pembayaran', 'wallet'], ['/admin/siswa', 'Siswa', 'users'],
  ['/admin/paket', 'Paket', 'box'], ['/admin/pertemuan', 'Materi', 'book'], ['/admin/soal', 'Bank soal', 'list'],
  ['/admin/tes', 'Tes', 'test'], ['/admin/tunggu', 'Daftar tunggu', 'clock'], ['/admin/pengaturan', 'Pengaturan', 'gear'],
];
?>
<body class="shell-b">
<?php require __DIR__ . '/_bg.php'; ?>
<div class="scrim" id="scrim"></div>
<div class="shell">
  <?php require __DIR__ . '/_sidebar.php'; ?>
  <div class="main">
    <?php require __DIR__ . '/_topbar.php'; ?>
    <main class="content">
      <?= flash_html(flashes()) ?>
      <?= $content ?>
    </main>
  </div>
</div>
</body>
</html>
