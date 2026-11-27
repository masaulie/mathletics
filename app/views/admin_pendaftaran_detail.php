<?php $title = 'Pendaftaran'; ?>
<a href="<?= h(url('admin/pendaftaran')) ?>" class="mute" style="text-decoration:none">&larr; Semua pendaftaran</a>
<h1 style="margin-top:12px"><?= h($r['nama_anak']) ?> <?= badge($r['status']) ?></h1>
<?= flash_html($err) ?>
<?php if ($kred): ?>
<div class="kred">
  <b>Akun siswa dibuat.</b> Password awal hanya tampil sekali di halaman ini:
  <p style="margin:12px 0"><code><?= h($kred['pw']) ?></code></p>
  <p style="margin:0 0 14px;color:#cbd5e1">Nomor HP: <?= h($kred['hp']) ?>. Siswa wajib menggantinya saat masuk pertama.</p>
  <a class="btn" href="<?= h($kred['wa']) ?>" target="_blank" rel="noopener">Kirim lewat WhatsApp</a>
</div>
<?php endif; ?>
<div class="cols c2" style="align-items:start">
  <div class="box">
    <h3>Data</h3>
    <table class="tbl"><tbody>
      <tr><td class="mute">Paket</td><td><?= h($r['paket_nama']) ?></td></tr>
      <tr><td class="mute">Nominal</td><td class="num"><?= h(rupiah($r['harga'])) ?></td></tr>
      <tr><td class="mute">Kelas</td><td><?= h($r['kelas']) ?></td></tr>
      <tr><td class="mute">Orang tua</td><td><?= h($r['nama_ortu']) ?></td></tr>
      <tr><td class="mute">WhatsApp</td><td><a href="<?= h(wa_link($r['hp'])) ?>" target="_blank" rel="noopener"><?= h($r['hp']) ?></a></td></tr>
      <tr><td class="mute">Email</td><td><?= h($r['email'] ?: '-') ?></td></tr>
      <tr><td class="mute">Daftar</td><td><?= h(tgl($r['dibuat'], true)) ?></td></tr>
      <?php if ($r['catatan']): ?><tr><td class="mute">Catatan</td><td><?= h($r['catatan']) ?></td></tr><?php endif; ?>
      <?php if ($r['user_id']): ?><tr><td class="mute">Akun siswa</td><td><a href="<?= h(url('admin/siswa/' . $r['user_id'])) ?>">Buka akun</a></td></tr><?php endif; ?>
    </tbody></table>
  </div>
  <div class="box stack">
    <h3>Bukti pembayaran</h3>
    <?php if ($r['bukti']): ?>
      <a href="<?= h(url('admin/bukti/' . $r['id'])) ?>" target="_blank" rel="noopener"><img src="<?= h(url('admin/bukti/' . $r['id'])) ?>" alt="Bukti pembayaran" style="border:1px solid var(--line);border-radius:8px;max-height:420px"></a>
    <?php else: ?><p class="mute">Belum ada bukti diunggah.</p><?php endif; ?>
    <?php if ($r['status'] !== 'lunas'): ?>
      <form method="post" action="<?= h(url('admin/pendaftaran/' . $r['id'] . '/konfirmasi')) ?>" data-confirm="Konfirmasi pembayaran ini dan buat akun siswa?"><?= csrf_field() ?><button class="btn block" type="submit">Konfirmasi lunas dan buat akun</button></form>
      <form method="post" action="<?= h(url('admin/pendaftaran/' . $r['id'] . '/tolak')) ?>">
        <?= csrf_field() ?>
        <div class="field"><label for="catatan">Alasan penolakan</label><input type="text" id="catatan" name="catatan" value="Bukti pembayaran belum sesuai."></div>
        <button class="btn danger block" type="submit">Tolak bukti</button>
      </form>
    <?php endif; ?>
  </div>
</div>
