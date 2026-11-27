<?php $title = 'Akun'; ?>
<h1>Akun</h1>
<div class="box" style="max-width:480px">
  <p><b><?= h($u['nama']) ?></b><br><span class="mute"><?= h($u['peran'] === 'admin' ? ($u['email'] ?? '') : ($u['hp'] ?? '')) ?></span></p>
  <?= flash_html($err) ?>
  <form method="post" action="<?= h(url('akun')) ?>">
    <?= csrf_field() ?>
    <h3>Ganti password</h3>
    <div class="field"><label for="lama">Password saat ini</label><input type="password" id="lama" name="lama" autocomplete="current-password" required></div>
    <div class="field"><label for="baru">Password baru (min. 8 karakter)</label><input type="password" id="baru" name="baru" autocomplete="new-password" required></div>
    <div class="field"><label for="ulang">Ulangi password baru</label><input type="password" id="ulang" name="ulang" autocomplete="new-password" required></div>
    <button class="btn" type="submit">Simpan password</button>
  </form>
  <form method="post" action="<?= h(url('keluar')) ?>" style="margin-top:20px"><?= csrf_field() ?><button class="btn ghost" type="submit">Keluar</button></form>
</div>
