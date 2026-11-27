<?php
$title = $p['nama'];
$baris = array_filter(array_map('trim', explode("\n", (string)$p['deskripsi'])));
$penuh = $sisa !== null && $sisa <= 0;
?>
<section class="wrap" style="padding:36px 20px 64px">
  <a href="<?= h(url('paket')) ?>" class="mute" style="text-decoration:none">&larr; Semua paket</a>
  <div class="cols c2" style="margin-top:16px;align-items:start;gap:32px">
    <div>
      <span class="eyebrow">Paket</span>
      <h1 style="font-size:clamp(26px,5vw,38px)"><?= h($p['nama']) ?></h1>
      <p class="mute"><?= h($p['ringkas']) ?></p>
      <p><span class="num" style="font-size:34px"><?= h(rupiah($p['harga'])) ?></span><?php if ($p['harga_normal']): ?> <span class="mute num" style="text-decoration:line-through"><?= h(rupiah($p['harga_normal'])) ?></span><?php endif; ?></p>
      <?php if ($baris): ?><ul class="bullets"><?php foreach ($baris as $b): ?><li><?= h($b) ?></li><?php endforeach; ?></ul><?php endif; ?>
      <div class="meta" style="margin-top:16px">
        <?php if ($p['jadwal']): ?><span><?= icon('clock') ?><?= h($p['jadwal']) ?></span><?php endif; ?>
        <?php if ($sisa !== null): ?><span><?= icon('users') ?>Sisa <?= (int)$sisa ?> dari <?= (int)$p['kuota'] ?> kursi</span><?php endif; ?>
      </div>
    </div>
    <div class="box">
      <h3>Daftar</h3>
      <?= flash_html($err) ?>
      <?php if ($penuh): ?>
        <p>Kuota paket ini sudah penuh. Hubungi kami lewat WhatsApp untuk masuk daftar tunggu.</p>
        <?php if (setting('wa')): ?><a class="btn" href="<?= h(wa_link(setting('wa'), 'Halo, kuota ' . $p['nama'] . ' penuh. Saya mau masuk daftar tunggu.')) ?>">Tulis lewat WhatsApp</a><?php endif; ?>
      <?php else: ?>
      <form method="post" action="<?= h(url('paket/' . $p['slug'])) ?>" novalidate>
        <?= csrf_field() ?>
        <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="field"><label for="nama_anak">Nama anak</label><input type="text" id="nama_anak" name="nama_anak" value="<?= h($old['nama_anak'] ?? '') ?>" required autocomplete="off"></div>
        <div class="field"><label for="kelas">Kelas</label><input type="text" id="kelas" name="kelas" value="<?= h($old['kelas'] ?? '9') ?>" inputmode="numeric"></div>
        <div class="field"><label for="nama_ortu">Nama orang tua</label><input type="text" id="nama_ortu" name="nama_ortu" value="<?= h($old['nama_ortu'] ?? '') ?>" required autocomplete="name"></div>
        <div class="field"><label for="hp">Nomor WhatsApp orang tua</label><input type="tel" id="hp" name="hp" value="<?= h($old['hp'] ?? '') ?>" inputmode="tel" placeholder="08xxxxxxxxxx" required autocomplete="tel"><div class="hint">Nomor ini juga menjadi nama pengguna akun siswa.</div></div>
        <div class="field"><label for="email">Email <span class="mute" style="font-weight:400">(opsional)</span></label><input type="email" id="email" name="email" value="<?= h($old['email'] ?? '') ?>" autocomplete="email"></div>
        <div class="field"><label class="check"><input type="checkbox" name="setuju" value="1"><span>Saya orang tua atau wali anak ini dan setuju datanya dipakai untuk keperluan belajar di Mathletics.</span></label></div>
        <button class="btn block" type="submit">Lanjut ke pembayaran</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</section>
