<?php $title = 'Ringkasan'; ?>
<h1>Ringkasan</h1>
<div class="stack">
  <div class="stats">
    <a class="stat" href="<?= h(url('admin/pendaftaran')) ?>" style="text-decoration:none;color:inherit"><b><?= (int)$s['menunggu'] ?></b><span>menunggu konfirmasi</span></a>
    <div class="stat"><b><?= (int)$s['belum_bayar'] ?></b><span>belum bayar</span></div>
    <div class="stat"><b><?= (int)$s['siswa'] ?></b><span>siswa aktif</span></div>
    <div class="stat"><b><?= (int)$s['diag7'] ?></b><span>tes diagnostik 7 hari</span></div>
  </div>
  <div class="stats">
    <div class="stat"><b style="font-size:22px"><?= h(rupiah($s['omzet'])) ?></b><span>pemasukan terkonfirmasi</span></div>
    <a class="stat" href="<?= h(url('admin/tunggu')) ?>" style="text-decoration:none;color:inherit"><b><?= (int)$s['tunggu'] ?></b><span>daftar tunggu jenjang lain</span></a>
  </div>

  <div class="box">
    <h3>Perlu dikonfirmasi</h3>
    <?php foreach ($baru as $r): ?>
      <a class="item" href="<?= h(url('admin/pendaftaran/' . $r['id'])) ?>"><span class="t"><b><?= h($r['nama_anak']) ?></b><small><?= h($r['paket_nama']) ?> &middot; <?= h(tgl($r['diperbarui'], true)) ?></small></span><?= icon('arrow') ?></a>
    <?php endforeach; ?>
    <?php if (!$baru): ?><p class="mute" style="margin:0">Tidak ada yang menunggu.</p><?php endif; ?>
  </div>

  <?php if ($paket): ?>
  <div class="box">
    <h3>Kuota paket</h3>
    <?php foreach ($paket as $p): $pc = (int)$p['kuota'] > 0 ? min(100, (int)round($p['terpakai'] / $p['kuota'] * 100)) : 0; ?>
      <div style="margin:10px 0"><div class="row" style="justify-content:space-between"><b><?= h($p['nama']) ?></b><span class="num"><?= (int)$p['terpakai'] ?>/<?= (int)$p['kuota'] ?></span></div><div class="bar" style="margin-top:6px"><i style="width:<?= $pc ?>%"></i></div></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="box">
    <h3>Siswa tidak masuk 3 hari</h3>
    <?php foreach ($pasif as $u): ?>
      <a class="item" href="<?= h(url('admin/siswa/' . $u['id'])) ?>"><span class="t"><b><?= h($u['nama']) ?></b><small>Terakhir masuk: <?= h($u['terakhir_masuk'] ? tgl($u['terakhir_masuk']) : 'belum pernah') ?></small></span><?= icon('arrow') ?></a>
    <?php endforeach; ?>
    <?php if (!$pasif): ?><p class="mute" style="margin:0">Semua siswa aktif.</p><?php endif; ?>
  </div>
</div>
