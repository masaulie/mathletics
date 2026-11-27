<?php $title = 'Paket'; ?>
<section class="wrap" style="padding:40px 20px 64px">
  <span class="eyebrow">Paket</span>
  <h1 style="font-size:clamp(26px,5vw,36px)">Pilih paket belajar.</h1>
  <?php if ($paket): ?>
    <div class="paketlist">
      <?php foreach ($paket as $p): $sisa = kuota_sisa($p); ?>
        <article class="paketcard">
          <div><h3 style="margin:0"><?= h($p['nama']) ?></h3><p class="mute" style="margin:4px 0 0"><?= h($p['ringkas']) ?></p></div>
          <div><span class="harga"><?= h(rupiah($p['harga'])) ?></span><?php if ($p['harga_normal']): ?><span class="normal"><?= h(rupiah($p['harga_normal'])) ?></span><?php endif; ?></div>
          <div class="meta">
            <?php if ($p['jadwal']): ?><span><?= icon('clock') ?><?= h($p['jadwal']) ?></span><?php endif; ?>
            <?php if ($sisa !== null): ?><span><?= icon('users') ?>Sisa <?= (int)$sisa ?> kursi</span><?php endif; ?>
          </div>
          <a class="btn" href="<?= h(url('paket/' . $p['slug'])) ?>">Lihat dan daftar</a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="mute">Belum ada paket yang dibuka.</p>
  <?php endif; ?>
  <?php if ($diag && $diag['jumlah'] > 0): ?>
    <div class="box" style="margin-top:28px"><b>Belum yakin?</b> <a href="<?= h(url('tes')) ?>">Coba tes diagnostik gratis</a> dulu, hasilnya menunjukkan topik yang perlu dilatih.</div>
  <?php endif; ?>
</section>
