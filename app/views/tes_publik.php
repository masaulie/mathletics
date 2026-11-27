<?php $title = 'Tes diagnostik'; $ada = $diag && $diag['jumlah'] > 0; ?>
<section class="authpage">
  <div class="authbox">
    <span class="eyebrow">Gratis, tanpa akun</span>
    <h1 style="font-size:26px">Tes diagnostik Matematika</h1>
    <?php if ($ada): ?>
      <p class="mute"><span class="num"><?= (int)$diag['jumlah'] ?></span> soal &middot; <span class="num"><?= (int)$diag['durasi'] ?></span> menit. Hasilnya menunjukkan topik yang sudah kuat dan yang perlu dilatih.</p>
      <?= flash_html($err) ?>
      <form method="post" action="<?= h(url('tes/mulai')) ?>" novalidate>
        <?= csrf_field() ?>
        <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="field"><label for="nama">Nama anak</label><input type="text" id="nama" name="nama" value="<?= h($old['nama']) ?>" autocomplete="off" required></div>
        <div class="field"><label for="hp">Nomor WhatsApp orang tua</label><input type="tel" id="hp" name="hp" value="<?= h($old['hp']) ?>" inputmode="tel" placeholder="08xxxxxxxxxx" required><div class="hint">Dipakai untuk mengirim hasil dan menghubungi kalau kamu mau konsultasi.</div></div>
        <button class="btn block" type="submit">Mulai tes</button>
      </form>
      <p class="hint" style="margin-top:14px">Jawaban tersimpan otomatis. Kalau sinyal putus, buka lagi tautan tes yang sama.</p>
    <?php else: ?>
      <p class="mute">Tes diagnostik belum dibuka. Coba lagi sebentar lagi.</p>
    <?php endif; ?>
  </div>
</section>
