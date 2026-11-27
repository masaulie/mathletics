<?php $title = 'Siswa'; ?>
<h1>Siswa</h1>
<form method="get" class="row" style="margin-bottom:16px"><input type="search" name="q" value="<?= h($cari) ?>" placeholder="Cari nama atau nomor" style="max-width:320px"><button class="btn dark" type="submit">Cari</button></form>
<div class="box scroll">
<table class="tbl res">
  <thead><tr><th>Nama</th><th>Nomor HP</th><th>Status</th><th>Terakhir masuk</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $u): ?>
    <tr>
      <td data-l="Nama"><a href="<?= h(url('admin/siswa/' . $u['id'])) ?>"><?= h($u['nama']) ?></a></td>
      <td data-l="Nomor HP"><?= h($u['hp']) ?></td>
      <td data-l="Status"><?= (int)$u['aktif'] ? '<span class="badge hijau">Aktif</span>' : '<span class="badge">Nonaktif</span>' ?></td>
      <td data-l="Terakhir masuk"><?= h($u['terakhir_masuk'] ? tgl($u['terakhir_masuk'], true) : 'belum pernah') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if (!$rows): ?><p class="mute">Belum ada siswa. Akun dibuat otomatis saat kamu mengonfirmasi pembayaran.</p><?php endif; ?>
</div>
