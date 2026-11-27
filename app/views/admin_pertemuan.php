<?php $title = 'Materi'; ?>
<h1>Materi per pertemuan</h1>
<?= flash_html($err) ?>
<form method="get" class="row" style="margin-bottom:16px">
  <select name="paket" style="max-width:320px" onchange="this.form.submit()"><?php foreach ($pakets as $p): ?><option value="<?= (int)$p['id'] ?>" <?= (int)$p['id'] === $paketId ? 'selected' : '' ?>><?= h($p['nama']) ?></option><?php endforeach; ?></select>
</form>
<div class="box">
  <?php foreach ($rows as $r): ?>
    <a class="item" href="<?= h(url('admin/pertemuan/' . $r['id'])) ?>">
      <span class="n"><?= (int)$r['urut'] ?></span>
      <span class="t"><b><?= h($r['judul']) ?></b><small><?= h($r['topik'] ?: 'Topik belum diisi') ?> &middot; <?= (int)$r['jml'] ?> berkas<?= $r['tanggal'] ? ' &middot; ' . h(tgl($r['tanggal'])) : '' ?></small></span>
      <?= (int)$r['terbuka'] ? '<span class="badge hijau">Terbuka</span>' : '<span class="badge">Tertutup</span>' ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$rows): ?><p class="mute">Belum ada pertemuan untuk paket ini.</p><?php endif; ?>
  <?php if ($paketId): ?>
  <form method="post" action="<?= h(url('admin/pertemuan/tambah')) ?>" style="margin-top:12px"><?= csrf_field() ?><input type="hidden" name="paket_id" value="<?= (int)$paketId ?>"><button class="btn ghost sm" type="submit">Tambah pertemuan</button></form>
  <?php endif; ?>
</div>
