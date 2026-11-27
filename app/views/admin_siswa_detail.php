<?php $title = $u['nama']; ?>
<a href="<?= h(url('admin/siswa')) ?>" class="mute" style="text-decoration:none">&larr; Semua siswa</a>
<h1 style="margin-top:12px"><?= h($u['nama']) ?> <?= (int)$u['aktif'] ? '<span class="badge hijau">Aktif</span>' : '<span class="badge">Nonaktif</span>' ?></h1>
<?php if ($kred): ?>
<div class="kred"><b>Password baru.</b> Hanya tampil sekali:
  <p style="margin:12px 0"><code><?= h($kred['pw']) ?></code></p>
  <a class="btn" href="<?= h($kred['wa']) ?>" target="_blank" rel="noopener">Kirim lewat WhatsApp</a></div>
<?php endif; ?>
<div class="cols c2" style="align-items:start">
  <div class="box">
    <table class="tbl"><tbody>
      <tr><td class="mute">Nomor HP</td><td><?= h($u['hp']) ?></td></tr>
      <tr><td class="mute">Paket</td><td><?= $paket ? h(implode(', ', array_column($paket, 'nama'))) : '-' ?></td></tr>
      <tr><td class="mute">Dibuat</td><td><?= h(tgl($u['dibuat'])) ?></td></tr>
      <tr><td class="mute">Terakhir masuk</td><td><?= h($u['terakhir_masuk'] ? tgl($u['terakhir_masuk'], true) : 'belum pernah') ?></td></tr>
    </tbody></table>
    <div class="row" style="margin-top:16px">
      <form method="post" action="<?= h(url('admin/siswa/' . $u['id'] . '/reset')) ?>" data-confirm="Buat password baru untuk siswa ini?"><?= csrf_field() ?><button class="btn dark sm" type="submit">Reset password</button></form>
      <form method="post" action="<?= h(url('admin/siswa/' . $u['id'] . '/aktif')) ?>"><?= csrf_field() ?><button class="btn ghost sm" type="submit"><?= (int)$u['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
    </div>
  </div>
  <div class="box">
    <h3>Tes</h3>
    <?php foreach ($percobaan as $p): ?>
      <a class="item" href="<?= h(url('ujian/' . $p['token'] . ($p['selesai'] ? '/hasil' : ''))) ?>"><span class="t"><b><?= h($p['judul']) ?></b><small><?= h(tgl($p['mulai'], true)) ?></small></span><span class="num"><?= $p['selesai'] ? (int)$p['skor'] : '...' ?></span></a>
    <?php endforeach; ?>
    <?php if (!$percobaan): ?><p class="mute" style="margin:0">Belum ada tes.</p><?php endif; ?>
  </div>
</div>
