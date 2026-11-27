<?php $title = 'Materi'; ?>
<h1>Materi</h1>
<?php if (!$rows): ?>
  <div class="box"><p class="mute" style="margin:0">Belum ada paket aktif di akunmu.</p></div>
<?php else: ?>
  <div class="box">
    <?php foreach ($rows as $r): $buka = (int)$r['terbuka'] === 1; ?>
      <?php if ($buka): ?>
        <a class="item" href="<?= h(url('belajar/materi/' . $r['id'])) ?>"><span class="n"><?= (int)$r['urut'] ?></span><span class="t"><b><?= h($r['judul']) ?></b><small><?= h($r['topik'] ?: 'Modul, worksheet, kunci, slide') ?> &middot; <?= (int)$r['jml'] ?> berkas</small></span><?= icon('arrow') ?></a>
      <?php else: ?>
        <div class="item lock"><span class="n"><?= (int)$r['urut'] ?></span><span class="t"><b><?= h($r['judul']) ?></b><small>Belum dibuka</small></span><?= icon('lock') ?></div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
