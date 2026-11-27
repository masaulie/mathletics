<?php
$title = $title ?? '';
require __DIR__ . '/_head.php';
$variant = 'siswa';
$panel = 'Panel siswa';
$menu = [['/belajar', 'Hari ini', 'grid'], ['/belajar/materi', 'Materi', 'book'], ['/belajar/jadwal', 'Jadwal', 'cal'], ['/belajar/tes', 'Tes', 'test']];
$cp = current_path();
$on = fn(string $p) => ($p === '/belajar' ? $cp === '/belajar' : str_starts_with($cp, $p)) ? ' aria-current="page"' : '';
?>
<body class="shell-b">
<?php require __DIR__ . '/_bg.php'; ?>
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
<nav class="bnav" aria-label="Menu siswa">
  <a href="<?= h(url('belajar')) ?>"<?= $on('/belajar') ?>><?= icon('grid') ?>Hari ini</a>
  <a href="<?= h(url('belajar/materi')) ?>"<?= $on('/belajar/materi') ?>><?= icon('book') ?>Materi</a>
  <a href="<?= h(url('belajar/jadwal')) ?>"<?= $on('/belajar/jadwal') ?>><?= icon('cal') ?>Jadwal</a>
  <a href="<?= h(url('belajar/tes')) ?>"<?= $on('/belajar/tes') ?>><?= icon('test') ?>Tes</a>
  <a href="<?= h(url('akun')) ?>"<?= $on('/akun') ?>><?= icon('user') ?>Akun</a>
</nav>
</body>
</html>
