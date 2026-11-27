<?php $title = 'Pembayaran'; $tab = ['menunggu_konfirmasi' => 'Menunggu konfirmasi', 'menunggu_bayar' => 'Belum bayar', 'lunas' => 'Lunas', 'ditolak' => 'Ditolak', 'semua' => 'Semua']; ?>
<h1>Pembayaran</h1>
<div class="tabs"><?php foreach ($tab as $k => $l): ?><a href="<?= h(url('admin/pendaftaran?status=' . $k)) ?>"<?= $st === $k ? ' aria-current="page"' : '' ?>><?= h($l) ?></a><?php endforeach; ?></div>
<div class="box scroll">
<table class="tbl res">
  <thead><tr><th>Anak</th><th>Orang tua</th><th>Paket</th><th>Status</th><th>Terakhir</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td data-l="Anak"><a href="<?= h(url('admin/pendaftaran/' . $r['id'])) ?>"><?= h($r['nama_anak']) ?></a></td>
      <td data-l="Orang tua"><?= h($r['nama_ortu']) ?><br><span class="mute"><?= h($r['hp']) ?></span></td>
      <td data-l="Paket"><?= h($r['paket_nama']) ?></td>
      <td data-l="Status"><?= badge($r['status']) ?></td>
      <td data-l="Terakhir"><?= h(tgl($r['diperbarui'], true)) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if (!$rows): ?><p class="mute">Tidak ada data.</p><?php endif; ?>
</div>
