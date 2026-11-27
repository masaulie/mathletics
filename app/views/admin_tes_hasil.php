<?php $title = 'Hasil tes'; ?>
<a href="<?= h(url('admin/tes')) ?>" class="mute" style="text-decoration:none">&larr; Semua tes</a>
<h1 style="margin-top:12px">Hasil: <?= h($t['judul']) ?></h1>
<div class="box scroll">
<table class="tbl res">
  <thead><tr><th>Nama</th><th>WhatsApp</th><th>Mulai</th><th>Skor</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td data-l="Nama"><?= h($r['nama']) ?></td>
      <td data-l="WhatsApp"><?php if ($r['hp']): ?><a href="<?= h(wa_link($r['hp'])) ?>" target="_blank" rel="noopener"><?= h($r['hp']) ?></a><?php else: ?>-<?php endif; ?></td>
      <td data-l="Mulai"><?= h(tgl($r['mulai'], true)) ?></td>
      <td data-l="Skor" class="num"><?= $r['selesai'] ? (int)$r['skor'] : '<span class="badge kuning">belum selesai</span>' ?></td>
      <td><a href="<?= h(url('ujian/' . $r['token'] . ($r['selesai'] ? '/hasil' : ''))) ?>">Buka</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if (!$rows): ?><p class="mute">Belum ada yang mengerjakan.</p><?php endif; ?>
</div>
