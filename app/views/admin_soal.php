<?php $title = 'Bank soal'; ?>
<div class="row" style="justify-content:space-between"><h1>Bank soal</h1><a class="btn" href="<?= h(url('admin/soal/baru' . ($topik ? '?topik=' . $topik : ''))) ?>">Soal baru</a></div>
<?= flash_html($err) ?>
<div class="tabs">
  <a href="<?= h(url('admin/soal')) ?>"<?= !$topik ? ' aria-current="page"' : '' ?>>Semua</a>
  <?php foreach ($topiks as $t): ?><a href="<?= h(url('admin/soal?topik=' . $t['id'])) ?>"<?= $topik === (int)$t['id'] ? ' aria-current="page"' : '' ?>><?= h($t['nama']) ?> (<?= (int)$t['jml'] ?>)</a><?php endforeach; ?>
</div>
<div class="cols c2" style="margin-bottom:16px">
  <form class="box tight row" method="post" action="<?= h(url('admin/topik')) ?>"><?= csrf_field() ?><input type="text" name="nama" placeholder="Topik baru" class="grow" style="min-width:140px"><button class="btn dark sm" type="submit">Tambah topik</button></form>
  <form class="box tight row" method="post" action="<?= h(url('admin/soal/impor')) ?>" enctype="multipart/form-data"><?= csrf_field() ?><input type="file" name="csv" accept=".csv,text/csv" class="grow" style="min-width:140px"><button class="btn dark sm" type="submit">Impor CSV</button><a href="<?= h(url('admin/soal/templat.csv')) ?>" class="hint">Unduh templat</a></form>
</div>
<form method="get" class="row" style="margin-bottom:16px"><?php if ($topik): ?><input type="hidden" name="topik" value="<?= (int)$topik ?>"><?php endif; ?><input type="search" name="q" value="<?= h($cari) ?>" placeholder="Cari teks soal" style="max-width:320px"><button class="btn ghost" type="submit">Cari</button></form>
<div class="box">
  <?php foreach ($rows as $s): ?>
    <a class="item" href="<?= h(url('admin/soal/' . $s['id'])) ?>">
      <span class="t"><b style="font-weight:600"><?= h(mb_strimwidth(strip_tags($s['teks']), 0, 140, '...')) ?></b><small><?= h($s['topik_nama']) ?> &middot; tingkat <?= (int)$s['tingkat'] ?> &middot; kunci <?= h($s['kunci']) ?><?= (int)$s['contoh'] ? ' &middot; <span class="badge kuning">soal contoh</span>' : '' ?><?= (int)$s['aktif'] ? '' : ' &middot; nonaktif' ?></small></span>
    </a>
  <?php endforeach; ?>
  <?php if (!$rows): ?><p class="mute" style="margin:0">Tidak ada soal.</p><?php endif; ?>
</div>
