<?php $title = 'Tes'; ?>
<h1>Tes</h1>
<div class="stack">
  <div class="box">
    <h3>Tes tersedia</h3>
    <?php foreach ($tes as $t): ?>
      <div class="item">
        <span class="t"><b><?= h($t['judul']) ?></b><small><?= (int)$t['jumlah'] ?> soal &middot; <?= (int)$t['durasi'] ?> menit</small></span>
        <?php if ((int)$t['jumlah'] > 0): ?>
        <form method="post" action="<?= h(url('belajar/tes/' . $t['id'] . '/mulai')) ?>"><?= csrf_field() ?><button class="btn sm" type="submit">Kerjakan</button></form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (!$tes): ?><p class="mute" style="margin:0">Belum ada tes yang dibuka.</p><?php endif; ?>
  </div>
  <div class="box">
    <h3>Riwayat</h3>
    <?php foreach ($riwayat as $r): ?>
      <a class="item" href="<?= h(url('ujian/' . $r['token'] . ($r['selesai'] ? '/hasil' : ''))) ?>">
        <span class="t"><b><?= h($r['judul']) ?></b><small><?= h(tgl($r['mulai'], true)) ?></small></span>
        <?php if ($r['selesai']): ?><span class="num"><?= (int)$r['skor'] ?></span><?php else: ?><span class="badge kuning">Belum selesai</span><?php endif; ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$riwayat): ?><p class="mute" style="margin:0">Belum ada riwayat.</p><?php endif; ?>
  </div>
</div>
