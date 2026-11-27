<dialog class="pj" id="jenjangDlg" aria-labelledby="pjH">
  <div class="in">
    <h2 id="pjH">Kamu di jenjang mana?</h2>
    <p class="mute">Pilih supaya paket dan tes yang tampil sesuai. Bisa diganti kapan saja dari tombol di pojok atas.</p>
    <ul class="pjl">
      <?php foreach (all('SELECT * FROM jenjang ORDER BY urut') as $j): ?>
        <li><button type="button" <?= $j['aktif'] ? 'autofocus' : '' ?> data-kode="<?= h($j['kode']) ?>" data-aktif="<?= (int)$j['aktif'] ?>" data-nama="<?= h($j['nama']) ?>">
          <span><?= h($j['nama']) ?><?php if ($j['aktif']): ?><small>Persiapan TKA SMP 2027</small><?php endif; ?></span>
          <?= $j['aktif'] ? icon('arrow') : '<span class="soon">Segera hadir</span>' ?>
        </button></li>
      <?php endforeach; ?>
    </ul>
    <form class="tungguform" id="tungguForm" novalidate>
      <p><b id="tungguJudul"></b> belum dibuka. Tinggalkan nomor WhatsApp, kami kabari begitu siap.</p>
      <input type="hidden" name="jenjang" id="tungguKode">
      <div class="field"><label for="tNama">Nama</label><input type="text" id="tNama" name="nama" autocomplete="name"></div>
      <div class="field"><label for="tHp">Nomor WhatsApp</label><input type="tel" id="tHp" name="hp" inputmode="tel" autocomplete="tel" placeholder="08xxxxxxxxxx"></div>
      <p class="mute" id="tungguPesan" role="status"></p>
      <button class="btn block" type="submit">Kabari saya</button>
    </form>
  </div>
</dialog>
