<?php $title = $t['id'] ? 'Ubah tes' : 'Tes baru'; ?>
<a href="<?= h(url('admin/tes')) ?>" class="mute" style="text-decoration:none">&larr; Semua tes</a>
<h1 style="margin-top:12px"><?= h($title) ?></h1>
<?= flash_html($err) ?>
<form method="post" action="<?= h(url('admin/tes/' . ($t['id'] ?: 'baru'))) ?>" style="max-width:720px" class="stack">
  <?= csrf_field() ?>
  <div class="box">
    <div class="field"><label for="judul">Judul</label><input type="text" id="judul" name="judul" value="<?= h($t['judul']) ?>" required></div>
    <div class="field"><label for="tipe">Tipe</label><select id="tipe" name="tipe"><option value="tryout" <?= $t['tipe'] === 'tryout' ? 'selected' : '' ?>>Try-out (untuk pemilik paket)</option><option value="diagnostik" <?= $t['tipe'] === 'diagnostik' ? 'selected' : '' ?>>Diagnostik</option></select></div>
    <div class="field"><label for="durasi">Durasi (menit)</label><input type="number" id="durasi" name="durasi" min="5" max="240" value="<?= (int)$t['durasi'] ?>"></div>
    <div class="field"><label for="paket_id">Paket pemilik akses</label><select id="paket_id" name="paket_id"><option value="">Tidak terikat paket</option><?php foreach ($pakets as $p): ?><option value="<?= (int)$p['id'] ?>" <?= (int)$t['paket_id'] === (int)$p['id'] ? 'selected' : '' ?>><?= h($p['nama']) ?></option><?php endforeach; ?></select></div>
    <div class="field"><label class="check"><input type="checkbox" name="publik" value="1" <?= (int)$t['publik'] ? 'checked' : '' ?>><span>Jadikan tes diagnostik gratis di situs publik (hanya satu yang bisa publik, khusus tipe diagnostik)</span></label></div>
    <div class="field"><label for="status">Status</label><select id="status" name="status"><option value="draft" <?= $t['status'] === 'draft' ? 'selected' : '' ?>>Draft</option><option value="aktif" <?= $t['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option></select></div>
  </div>
  <div class="box">
    <h3>Soal (<span id="jml"><?= count($dipilih) ?></span> dipilih)</h3>
    <div class="pick">
      <?php $last = null; foreach ($soal as $s): if ($last !== $s['topik_nama']) { echo '<h4>' . h($s['topik_nama']) . '</h4>'; $last = $s['topik_nama']; } ?>
        <label class="check"><input type="checkbox" name="soal[]" value="<?= (int)$s['id'] ?>" <?= in_array((int)$s['id'], $dipilih, true) ? 'checked' : '' ?>><span><?= h(mb_strimwidth($s['teks'], 0, 120, '...')) ?><?= (int)$s['contoh'] ? ' <span class="badge kuning">contoh</span>' : '' ?></span></label>
      <?php endforeach; ?>
      <?php if (!$soal): ?><p class="mute">Bank soal masih kosong.</p><?php endif; ?>
    </div>
  </div>
  <button class="btn" type="submit">Simpan tes</button>
</form>
<script>
(function(){var c=document.querySelectorAll('.pick input[type=checkbox]'),j=document.getElementById('jml');
function u(){j.textContent=document.querySelectorAll('.pick input:checked').length}c.forEach(function(x){x.addEventListener('change',u)});})();
</script>
