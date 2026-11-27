<?php $title = $p['id'] ? 'Ubah paket' : 'Paket baru'; ?>
<a href="<?= h(url('admin/paket')) ?>" class="mute" style="text-decoration:none">&larr; Semua paket</a>
<h1 style="margin-top:12px"><?= h($title) ?></h1>
<?= flash_html($err) ?>
<form class="box" method="post" action="<?= h(url('admin/paket/' . ($p['id'] ?: 'baru'))) ?>" style="max-width:640px">
  <?= csrf_field() ?>
  <div class="field"><label for="nama">Nama paket</label><input type="text" id="nama" name="nama" value="<?= h($p['nama']) ?>" required></div>
  <div class="field"><label for="slug">Alamat (slug)</label><input type="text" id="slug" name="slug" value="<?= h($p['slug']) ?>"><div class="hint">Kosongkan untuk dibuat otomatis dari nama.</div></div>
  <div class="field"><label for="jenjang_id">Jenjang</label><select id="jenjang_id" name="jenjang_id"><?php foreach ($jenjang as $j): ?><option value="<?= (int)$j['id'] ?>" <?= (int)$j['id'] === (int)$p['jenjang_id'] ? 'selected' : '' ?>><?= h($j['nama']) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="tipe">Tipe</label><select id="tipe" name="tipe"><?php foreach (['kelas' => 'Kelas', 'tes' => 'Tes dan try-out', 'mandiri' => 'Belajar mandiri'] as $k => $l): ?><option value="<?= h($k) ?>" <?= $p['tipe'] === $k ? 'selected' : '' ?>><?= h($l) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="ringkas">Ringkasan satu kalimat</label><input type="text" id="ringkas" name="ringkas" value="<?= h($p['ringkas']) ?>"></div>
  <div class="field"><label for="deskripsi">Isi paket (satu poin per baris)</label><textarea id="deskripsi" name="deskripsi" rows="6"><?= h($p['deskripsi']) ?></textarea></div>
  <div class="field"><label for="harga">Harga (Rp)</label><input type="text" id="harga" name="harga" inputmode="numeric" value="<?= h($p['harga']) ?>"></div>
  <div class="field"><label for="harga_normal">Harga normal (dicoret, opsional)</label><input type="text" id="harga_normal" name="harga_normal" inputmode="numeric" value="<?= h($p['harga_normal']) ?>"></div>
  <div class="field"><label for="kuota">Kuota kursi (kosong = tanpa batas)</label><input type="number" id="kuota" name="kuota" min="0" value="<?= h($p['kuota']) ?>"></div>
  <div class="field"><label for="jadwal">Jadwal (teks tampil di situs)</label><input type="text" id="jadwal" name="jadwal" value="<?= h($p['jadwal']) ?>"></div>
  <div class="field"><label for="status">Status</label><select id="status" name="status"><option value="draft" <?= $p['status'] === 'draft' ? 'selected' : '' ?>>Draft (tidak tampil)</option><option value="aktif" <?= $p['status'] === 'aktif' ? 'selected' : '' ?>>Aktif (tampil dan bisa didaftar)</option></select></div>
  <button class="btn" type="submit">Simpan</button>
</form>
