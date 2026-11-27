<?php
$title = $title ?? '';
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
      <?php if (!empty($popup)): ?><button type="button" class="pillbtn" id="jenjangBtn" aria-haspopup="dialog">SMP</button><?php endif; ?>
      <a class="pillbtn opt" href="<?= h(url('paket')) ?>">Paket</a>
      <a class="pillbtn opt" href="<?= h(url('tes')) ?>">Tes gratis</a>
      <?php if ($u): ?><a class="pillbtn solid" href="<?= h(url(home_for($u))) ?>">Buka akun</a><?php else: ?><a class="pillbtn solid" href="<?= h(url('masuk')) ?>"><?= icon('out') ?>&nbsp;Masuk</a><?php endif; ?>
    </nav>
  </div>
</header>
<?php $fl = flash_html(flashes()); if ($fl !== '') echo '<div class="wrap" style="padding-top:1rem">' . $fl . '</div>'; ?>
<?= $content ?>
<footer class="foot">
  <div class="wrap">
    <span><?= h($nama) ?> &middot; bimbingan Matematika online &middot; <a href="<?= h(url('paket')) ?>">Paket</a> &middot; <a href="<?= h(url('tes')) ?>">Tes gratis</a></span>
    <?php if ($wa): ?><a href="<?= h(wa_link($wa, 'Halo, saya mau tanya soal kelas Mathletics.')) ?>" rel="noopener">WhatsApp <?= h($wa) ?></a><?php endif; ?>
  </div>
</footer>
<?php if (!empty($popup)) require __DIR__ . '/_popup.php'; ?>
</body>
</html>
