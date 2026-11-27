<?php
/** @var array $menu @var string $panel @var string $variant */
$cp = current_path();
$u = me();
$ini = strtoupper(mb_substr(trim($u['nama'] ?? 'M'), 0, 1));
$aktif = function (string $p) use ($cp): bool {
    return in_array($p, ['/admin', '/belajar'], true) ? $cp === $p : str_starts_with($cp, $p);
};
?>
<aside class="sidebar <?= h($variant) ?><?= $variant === 'admin' ? ' drawer' : '' ?>" id="sidebar">
  <div class="sb-top">
    <div class="sb-head">
      <div class="sb-badge">M</div>
      <div class="sb-logo"><span class="logo">Math<b>letics</b></span><small><?= h($panel) ?></small></div>
      <button type="button" class="sb-toggle" id="sbToggle" aria-label="Ciutkan atau lebarkan menu"><?= icon('chev') ?></button>
    </div>
    <div class="sb-label">Menu utama</div>
    <nav class="sb-nav" aria-label="<?= h($panel) ?>">
      <?php foreach ($menu as [$p, $l, $i]): ?>
        <a class="navi" href="<?= h(url($p)) ?>" title="<?= h($l) ?>"<?= $aktif($p) ? ' aria-current="page"' : '' ?>><span class="iw"><?= icon($i) ?></span><span class="tx"><?= h($l) ?></span></a>
      <?php endforeach; ?>
    </nav>
    <div class="sb-label">Akun</div>
    <div class="sb-nav">
      <a class="navi" href="<?= h(url('akun')) ?>" title="Akun"<?= $cp === '/akun' ? ' aria-current="page"' : '' ?>><span class="iw"><?= icon('lock') ?></span><span class="tx">Akun dan password</span></a>
      <a class="navi" href="<?= h(url('')) ?>" title="Ke beranda"><span class="iw"><?= icon('home') ?></span><span class="tx">Ke beranda</span></a>
      <form method="post" action="<?= h(url('keluar')) ?>"><?= csrf_field() ?><button class="navi" type="submit" title="Keluar"><span class="iw"><?= icon('out') ?></span><span class="tx">Keluar</span></button></form>
    </div>
  </div>
  <div class="sb-foot">
    <div class="usercard"><div class="avatar"><?= h($ini) ?></div><div><b><?= h($u['nama'] ?? '') ?></b><small><?= $variant === 'admin' ? 'Administrator' : 'Siswa' ?></small></div></div>
  </div>
</aside>
