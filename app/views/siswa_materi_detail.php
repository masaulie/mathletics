<?php $title = $pt['judul']; $jn = ['modul' => 'Modul', 'worksheet' => 'Worksheet', 'kunci' => 'Kunci', 'slide' => 'Slide', 'lain' => 'Lainnya']; ?>
<a href="<?= h(url('belajar/materi')) ?>" class="mute" style="text-decoration:none">&larr; Semua materi</a>
<span class="eyebrow" style="margin-top:14px">Pertemuan <?= (int)$pt['urut'] ?></span>
<h1><?= h($pt['judul']) ?></h1>
<?php if ($pt['topik']): ?><p class="mute"><?= h($pt['topik']) ?></p><?php endif; ?>
<div class="stack">
  <?php if ($pt['tanggal']): ?>
    <div class="box tight"><?= icon('cal') ?> <?= h(tgl($pt['tanggal'])) ?><?= $pt['jam'] ? ', ' . h($pt['jam']) . ' WIB' : '' ?>
      <?php if ($pt['tautan']): ?> &middot; <a href="<?= h($pt['tautan']) ?>" target="_blank" rel="noopener">Buka kelas</a><?php endif; ?></div>
  <?php endif; ?>
  <?php foreach ($berkas as $b): ?>
    <a class="dl" href="<?= h(url('berkas/' . $b['id'])) ?>" <?= $b['url'] ? 'target="_blank" rel="noopener"' : '' ?>>
      <span class="jenis"><?= h($jn[$b['jenis']] ?? 'Berkas') ?></span><span class="grow"><b><?= h($b['judul']) ?></b></span><?= icon($b['url'] ? 'link' : 'file') ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$berkas): ?><div class="box"><p class="mute" style="margin:0">Belum ada berkas untuk pertemuan ini.</p></div><?php endif; ?>
  <div class="row" style="justify-content:space-between">
    <?php if ($prev): ?><a class="btn ghost" href="<?= h(url('belajar/materi/' . $prev['id'])) ?>">&larr; Sebelumnya</a><?php else: ?><span></span><?php endif; ?>
    <?php if ($next): ?><a class="btn ghost" href="<?= h(url('belajar/materi/' . $next['id'])) ?>">Berikutnya &rarr;</a><?php endif; ?>
  </div>
</div>
