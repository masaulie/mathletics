<?php $title = 'Jadwal'; ?>
<h1>Jadwal kelas</h1>
<?php if (!$rows): ?>
  <div class="box"><p class="mute" style="margin:0">Jadwal belum diisi. Kamu akan dikabari lewat WhatsApp.</p></div>
<?php else: ?>
  <div class="box">
    <?php foreach ($rows as $r): $lalu = $r['tanggal'] < $hariIni; ?>
      <div class="item <?= $lalu ? 'lock' : '' ?>">
        <span class="n" style="width:auto;min-width:64px;font-size:15px"><?= h(tgl($r['tanggal'])) ?></span>
        <span class="t"><b><?= h($r['judul']) ?></b><small><?= h(trim(($r['jam'] ? $r['jam'] . ' WIB' : '') . ($r['topik'] ? ' · ' . $r['topik'] : ''), ' ·')) ?></small></span>
        <?php if (!$lalu && $r['tautan']): ?><a class="btn sm" href="<?= h($r['tautan']) ?>" target="_blank" rel="noopener">Buka kelas</a><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
