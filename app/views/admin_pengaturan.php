<?php $title = 'Pengaturan'; ?>
<h1>Pengaturan</h1>
<?= flash_html($err) ?>
<form class="box" method="post" action="<?= h(url('admin/pengaturan')) ?>" enctype="multipart/form-data" style="max-width:640px">
  <?= csrf_field() ?>
  <div class="field"><label for="nama_usaha">Nama usaha</label><input type="text" id="nama_usaha" name="nama_usaha" value="<?= h(setting('nama_usaha', 'Mathletics')) ?>"></div>
  <div class="field"><label for="wa">Nomor WhatsApp Business</label><input type="tel" id="wa" name="wa" inputmode="tel" value="<?= h(setting('wa')) ?>" placeholder="6281234567890"></div>
  <div class="field"><label for="instruksi_bayar">Petunjuk pembayaran (tampil di halaman pendaftaran)</label><textarea id="instruksi_bayar" name="instruksi_bayar" rows="5"><?= h(setting('instruksi_bayar')) ?></textarea><div class="hint">Tulis nama bank, nomor rekening, dan atas nama.</div></div>
  <div class="field"><label for="qris">Gambar QRIS (PNG atau JPG)</label><input type="file" id="qris" name="qris" accept="image/png,image/jpeg"><?php if (setting('qris')): ?><div class="hint">QRIS sudah terpasang. Pilih berkas baru untuk menggantinya.</div><?php endif; ?></div>
  <h2>Beranda</h2>
  <div class="field"><label for="tka_label">Nama ujian (judul beranda)</label><input type="text" id="tka_label" name="tka_label" value="<?= h(setting('tka_label', 'TKA SMP 2027')) ?>"></div>
  <div class="field"><label for="tka_tanggal">Tanggal ujian (hitung mundur)</label><input type="date" id="tka_tanggal" name="tka_tanggal" value="<?= h(setting('tka_tanggal', '2027-03-29')) ?>"><div class="hint">Setelah tanggal ini lewat, kartu hitung mundur otomatis hilang.</div></div>
  <div class="field"><label for="tka_info">Jawaban FAQ "Kapan ujiannya?"</label><textarea id="tka_info" name="tka_info" rows="3"><?= h(setting('tka_info')) ?></textarea><div class="hint">Kosongkan untuk memakai kalimat otomatis dari tanggal di atas. Cocokkan dengan sumber resmi sebelum diisi.</div></div>
  <div class="field"><label for="pengajar_nama">Nama pengajar</label><input type="text" id="pengajar_nama" name="pengajar_nama" value="<?= h(setting('pengajar_nama')) ?>"></div>
  <div class="field"><label for="pengajar_bio">Profil pengajar</label><textarea id="pengajar_bio" name="pengajar_bio" rows="5"><?= h(setting('pengajar_bio')) ?></textarea><div class="hint">Latar belakang dan pengalaman mengajar. Bagian "Pengajar" baru tampil di beranda kalau nama dan profil sama-sama terisi.</div></div>
  <div class="field"><label for="testimoni">Testimoni</label><textarea id="testimoni" name="testimoni" rows="5" placeholder="Ibu Rina, orang tua siswa | Anak saya jadi berani bertanya."><?= h(setting('testimoni')) ?></textarea><div class="hint">Satu testimoni per baris, format: <code>Nama | isi testimoni</code>. Pakai hanya testimoni asli dan sudah diizinkan. Kosong berarti bagian ini tidak tampil.</div></div>
  <div class="field"><label for="garansi">Jaminan atau kebijakan pengembalian</label><textarea id="garansi" name="garansi" rows="4"><?= h(setting('garansi')) ?></textarea><div class="hint">Tampil di beranda dan di halaman Syarat. Tulis hanya yang benar-benar Anda berikan. Kosong berarti tidak tampil.</div></div>
  <div class="field"><label for="analitik">Kode analitik (opsional)</label><textarea id="analitik" name="analitik" rows="3" spellcheck="false"><?= h(setting('analitik')) ?></textarea><div class="hint">Tempel snippet dari Google Analytics, Plausible, atau Meta Pixel. Dipasang di halaman publik saja, bukan di panel admin atau siswa. Hanya tempel kode dari sumber yang Anda percaya.</div></div>
  <button class="btn" type="submit">Simpan</button>
</form>
