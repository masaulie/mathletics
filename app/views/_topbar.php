<?php $u = me(); $ini = strtoupper(mb_substr(trim($u['nama'] ?? 'M'), 0, 1)); ?>
<div class="topbar">
  <div class="row" style="gap:.7rem;flex-wrap:nowrap">
    <?php if ($variant === 'admin'): ?><button type="button" class="ibtn menu" id="menuBtn" aria-label="Buka menu"><?= icon('menu') ?></button><?php endif; ?>
    <a class="brand" href="<?= h(url($variant === 'admin' ? 'admin' : 'belajar')) ?>"><?= brand_mark() ?><span>Math<b>letics</b></span></a>
    <span class="where"><?= h($panel) ?></span>
  </div>
  <div class="avwrap">
    <button type="button" class="avatar" id="avBtn" aria-haspopup="true" aria-expanded="false" aria-label="Menu akun"><?= h($ini) ?></button>
    <div class="dd" id="dd" role="menu">
      <div class="who"><b><?= h($u['nama'] ?? '') ?></b><small><?= h($variant === 'admin' ? ($u['email'] ?? '') : ($u['hp'] ?? '')) ?></small></div>
      <a href="<?= h(url('akun')) ?>"><?= icon('lock') ?>Ganti password</a>
      <a href="<?= h(url('')) ?>"><?= icon('home') ?>Ke beranda</a>
      <form method="post" action="<?= h(url('keluar')) ?>"><?= csrf_field() ?><button type="submit"><?= icon('out') ?>Keluar</button></form>
    </div>
  </div>
</div>
