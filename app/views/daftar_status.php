<?php
$title = 'Pendaftaran';
$wa = setting('wa');
$bayar = $r['status'] === 'menunggu_bayar' || $r['status'] === 'ditolak';
?>
<section class="wrap narrow" style="padding:36px 20px 64px">
  <span class="eyebrow">Pendaftaran</span>
  <h1 style="font-size:28px"><?= h($r['nama_anak']) ?></h1>
  <p><?= h($r['paket_nama']) ?> &middot; <?= badge($r['status']) ?></p>
  <?= flash_html(flashes()) ?>

  <?php if ($r['status'] === 'ditolak' && $r['catatan']): ?>
    <div class="flash error"><?= h($r['catatan']) ?> Unggah ulang bukti di bawah.</div>
  <?php endif; ?>

  <?php if ($r['status'] === 'lunas'): ?>
    <div class="box"><b>Pembayaran sudah dikonfirmasi.</b><p class="mute" style="margin:6px 0 14px">Akun siswa dikirim lewat WhatsApp ke <?= h($r['hp']) ?>. Belum menerima? Hubungi kami.</p>
    <a class="btn" href="<?= h(url('masuk')) ?>">Masuk ke akun siswa</a></div>
  <?php else: ?>
    <div class="box stack">
      <div>
        <div class="mute" style="font-size:13px">Total yang dibayar</div>
        <div class="num" style="font-size:34px"><?= h(rupiah($r['harga'])) ?></div>
      </div>
      <div style="white-space:pre-line"><?= h(setting('instruksi_bayar')) ?></div>
      <?php if (setting('qris')): ?><img src="<?= h(url('qris')) ?>" alt="Kode QRIS pembayaran" style="max-width:280px;border:1px solid var(--line);border-radius:8px"><?php endif; ?>
    </div>

    <form class="box" method="post" action="<?= h(url('daftar/' . $r['token'] . '/bukti')) ?>" enctype="multipart/form-data" style="margin-top:16px">
      <?= csrf_field() ?>
      <h3><?= $r['bukti'] ? 'Ganti bukti pembayaran' : 'Unggah bukti pembayaran' ?></h3>
      <div class="field"><label for="bukti">Foto bukti (JPG atau PNG, maks. 3 MB)</label><input type="file" id="bukti" name="bukti" accept="image/jpeg,image/png" required></div>
      <button class="btn block" type="submit">Kirim bukti</button>
      <?php if ($r['status'] === 'menunggu_konfirmasi'): ?><p class="hint" style="margin-top:10px">Bukti sudah masuk dan menunggu dicek admin.</p><?php endif; ?>
    </form>
  <?php endif; ?>

  <p class="hint" style="margin-top:20px">Simpan halaman ini untuk memantau status. Kalau ada kendala<?= $wa ? ', <a href="' . h(wa_link($wa, 'Halo, saya mau tanya pendaftaran ' . $r['nama_anak'] . '.')) . '">hubungi kami lewat WhatsApp</a>' : ', hubungi admin' ?>.</p>
</section>
