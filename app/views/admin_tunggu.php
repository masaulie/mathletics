<?php $title = 'Daftar tunggu'; ?>
<h1>Daftar tunggu jenjang lain</h1>
<p class="mute">Orang yang memilih SD, SMA, atau Umum di popup dan meninggalkan nomor. Ini ukuran minat sebelum kamu membuka jenjang baru.</p>
<div class="box scroll">
<table class="tbl res">
  <thead><tr><th>Jenjang</th><th>Nama</th><th>WhatsApp</th><th>Tanggal</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr><td data-l="Jenjang"><?= h($r['jenjang_kode']) ?></td><td data-l="Nama"><?= h($r['nama']) ?></td><td data-l="WhatsApp"><a href="<?= h(wa_link($r['hp'])) ?>" target="_blank" rel="noopener"><?= h($r['hp']) ?></a></td><td data-l="Tanggal"><?= h(tgl($r['dibuat'], true)) ?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if (!$rows): ?><p class="mute">Belum ada.</p><?php endif; ?>
</div>
