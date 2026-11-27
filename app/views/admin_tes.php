<?php $title = 'Tes'; ?>
<div class="row" style="justify-content:space-between"><h1>Tes</h1><a class="btn" href="<?= h(url('admin/tes/baru')) ?>">Tes baru</a></div>
<div class="box scroll">
<table class="tbl res">
  <thead><tr><th>Judul</th><th>Tipe</th><th>Soal</th><th>Durasi</th><th>Status</th><th>Selesai</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $t): ?>
    <tr>
      <td data-l="Judul"><a href="<?= h(url('admin/tes/' . $t['id'])) ?>"><?= h($t['judul']) ?></a><?= (int)$t['publik'] ? ' <span class="badge">Publik</span>' : '' ?></td>
      <td data-l="Tipe"><?= h($t['tipe']) ?></td>
      <td data-l="Soal" class="num"><?= (int)$t['jumlah'] ?></td>
      <td data-l="Durasi"><?= (int)$t['durasi'] ?> mnt</td>
      <td data-l="Status"><?= $t['status'] === 'aktif' ? '<span class="badge hijau">Aktif</span>' : '<span class="badge">Draft</span>' ?></td>
      <td data-l="Selesai"><a href="<?= h(url('admin/tes/' . $t['id'] . '/hasil')) ?>"><?= (int)$t['selesai'] ?> hasil</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
