<?php $title = 'Paket'; ?>
<div class="row" style="justify-content:space-between"><h1>Paket</h1><a class="btn" href="<?= h(url('admin/paket/baru')) ?>">Paket baru</a></div>
<div class="box scroll">
<table class="tbl res">
  <thead><tr><th>Nama</th><th>Jenjang</th><th>Harga</th><th>Kuota</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $p): ?>
    <tr>
      <td data-l="Nama"><a href="<?= h(url('admin/paket/' . $p['id'])) ?>"><?= h($p['nama']) ?></a></td>
      <td data-l="Jenjang"><?= h($p['jenjang']) ?></td>
      <td data-l="Harga" class="num"><?= h(rupiah($p['harga'])) ?></td>
      <td data-l="Kuota"><?= $p['kuota'] === null ? 'tanpa batas' : (int)$p['terpakai'] . '/' . (int)$p['kuota'] ?></td>
      <td data-l="Status"><?= $p['status'] === 'aktif' ? '<span class="badge hijau">Aktif</span>' : '<span class="badge">Draft</span>' ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
