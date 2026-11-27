<?php $title = 'Pengaturan'; ?>
<h1>Pengaturan</h1>
<?= flash_html($err) ?>
<form class="box" method="post" action="<?= h(url('admin/pengaturan')) ?>" enctype="multipart/form-data" style="max-width:640px">
  <?= csrf_field() ?>
  <div class="field"><label for="nama_usaha">Nama usaha</label><input type="text" id="nama_usaha" name="nama_usaha" value="<?= h(setting('nama_usaha', 'Mathletics')) ?>"></div>
  <div class="field"><label for="wa">Nomor WhatsApp Business</label><input type="tel" id="wa" name="wa" inputmode="tel" value="<?= h(setting('wa')) ?>" placeholder="6281234567890"></div>
  <div class="field"><label for="instruksi_bayar">Petunjuk pembayaran (tampil di halaman pendaftaran)</label><textarea id="instruksi_bayar" name="instruksi_bayar" rows="5"><?= h(setting('instruksi_bayar')) ?></textarea><div class="hint">Tulis nama bank, nomor rekening, dan atas nama.</div></div>
  <div class="field"><label for="qris">Gambar QRIS (PNG atau JPG)</label><input type="file" id="qris" name="qris" accept="image/png,image/jpeg"><?php if (setting('qris')): ?><div class="hint">QRIS sudah terpasang. Pilih berkas baru untuk menggantinya.</div><?php endif; ?></div>
  <button class="btn" type="submit">Simpan</button>
</form>
