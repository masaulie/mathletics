<?php $title = $pt['judul']; $jn = ['modul' => 'Modul', 'worksheet' => 'Worksheet', 'kunci' => 'Kunci', 'slide' => 'Slide', 'lain' => 'Lainnya']; ?>
<a href="<?= h(url('admin/pertemuan?paket=' . $pt['paket_id'])) ?>" class="mute" style="text-decoration:none">&larr; Semua pertemuan</a>
<h1 style="margin-top:12px"><?= h($pt['judul']) ?></h1>
<?= flash_html($err) ?>
<div class="cols c2" style="align-items:start">
  <form class="box" method="post" action="<?= h(url('admin/pertemuan/' . $pt['id'])) ?>">
    <?= csrf_field() ?>
    <h3>Detail</h3>
    <div class="field"><label for="urut">Urutan</label><input type="number" id="urut" name="urut" min="1" value="<?= (int)$pt['urut'] ?>"></div>
    <div class="field"><label for="judul">Judul</label><input type="text" id="judul" name="judul" value="<?= h($pt['judul']) ?>" required></div>
    <div class="field"><label for="topik">Topik (tampil di siswa)</label><input type="text" id="topik" name="topik" value="<?= h($pt['topik']) ?>"></div>
    <div class="field"><label for="tanggal">Tanggal kelas</label><input type="date" id="tanggal" name="tanggal" value="<?= h($pt['tanggal']) ?>"></div>
    <div class="field"><label for="jam">Jam mulai</label><input type="time" id="jam" name="jam" value="<?= h($pt['jam']) ?>"></div>
    <div class="field"><label for="tautan">Tautan kelas online</label><input type="url" id="tautan" name="tautan" value="<?= h($pt['tautan']) ?>" placeholder="https://"></div>
    <div class="field"><label class="check"><input type="checkbox" name="terbuka" value="1" <?= (int)$pt['terbuka'] ? 'checked' : '' ?>><span>Materi terbuka untuk siswa</span></label></div>
    <button class="btn" type="submit">Simpan</button>
  </form>
  <div class="stack">
    <div class="box">
      <h3>Berkas</h3>
      <?php foreach ($berkas as $b): ?>
        <div class="item" style="flex-wrap:wrap">
          <span class="t"><b><?= h($b['judul']) ?></b><small><?= h($jn[$b['jenis']] ?? '') ?> &middot; <?= $b['url'] ? 'tautan' : 'unggahan' ?> &middot; <?= (int)$b['tampil'] ? 'tampil' : 'disembunyikan' ?></small></span>
          <a class="btn ghost sm" href="<?= h(url('berkas/' . $b['id'])) ?>" target="_blank" rel="noopener">Buka</a>
          <form method="post" action="<?= h(url('admin/berkas/' . $b['id'] . '/tampil')) ?>"><?= csrf_field() ?><button class="btn ghost sm" type="submit"><?= (int)$b['tampil'] ? 'Sembunyikan' : 'Tampilkan' ?></button></form>
          <form method="post" action="<?= h(url('admin/berkas/' . $b['id'] . '/hapus')) ?>" data-confirm="Hapus berkas ini?"><?= csrf_field() ?><button class="btn danger sm" type="submit">Hapus</button></form>
        </div>
      <?php endforeach; ?>
      <?php if (!$berkas): ?><p class="mute" style="margin:0">Belum ada berkas.</p><?php endif; ?>
    </div>
    <form class="box" method="post" action="<?= h(url('admin/pertemuan/' . $pt['id'] . '/berkas')) ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <h3>Tambah berkas</h3>
      <div class="field"><label for="jenis">Jenis</label><select id="jenis" name="jenis"><?php foreach ($jn as $k => $l): ?><option value="<?= h($k) ?>"><?= h($l) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label for="bjudul">Judul tampilan</label><input type="text" id="bjudul" name="judul" placeholder="Contoh: Modul 3 Aljabar"></div>
      <div class="field"><label for="berkas">Unggah PDF atau gambar (maks. 25 MB)</label><input type="file" id="berkas" name="berkas" accept="application/pdf,image/png,image/jpeg"></div>
      <div class="field"><label for="url">atau tautan (misalnya slide)</label><input type="url" id="url" name="url" placeholder="https://"></div>
      <button class="btn dark" type="submit">Tambah</button>
    </form>
  </div>
</div>
