<?php
$nama = setting('nama_usaha', 'Mathletics');
$wa = setting('wa');
$garansi = trim(setting('garansi'));
$desc = 'Syarat dan ketentuan penggunaan layanan ' . $nama . ': pendaftaran, pembayaran, akun, dan materi belajar.';
?>
<main class="wrap legal">
  <h1>Syarat dan Ketentuan</h1>
  <p class="mute">Terakhir diperbarui: <?= h(tgl(date('Y-m-d'))) ?>.</p>

  <h2>Pendaftaran</h2>
  <p>Pendaftaran dilakukan oleh orang tua atau wali dengan data yang benar. Kursi dihitung terisi setelah pendaftaran dibuat dan dilepas kembali bila pembayaran tidak diselesaikan dalam waktu yang ditentukan. Jumlah kursi per kelas terbatas.</p>

  <h2>Pembayaran</h2>
  <p>Pembayaran lewat QRIS atau transfer sesuai petunjuk di halaman pendaftaran. Akun siswa dibuat setelah kami memastikan pembayaran diterima, dan kredensialnya dikirim lewat WhatsApp. Pastikan nominal sesuai dengan harga paket.</p>
<?php if ($garansi !== ''): ?>
  <h2>Jaminan dan kebijakan pengembalian</h2>
  <p><?= nl2br(h($garansi)) ?></p>
<?php endif; ?>

  <h2>Akun siswa</h2>
  <p>Akun bersifat pribadi dan tidak boleh dibagikan atau dijual. Segera ganti kata sandi awal saat masuk pertama. Kami dapat menonaktifkan akun yang disalahgunakan.</p>

  <h2>Materi belajar</h2>
  <p>Modul, worksheet, kunci, slide, dan soal adalah milik <?= h($nama) ?> dan hanya untuk dipakai belajar oleh siswa terdaftar. Dilarang menyalin, menyebarkan ulang, atau memperjualbelikan tanpa izin tertulis.</p>

  <h2>Kelas dan jadwal</h2>
  <p>Jadwal dan tautan kelas diumumkan lewat akun siswa dan WhatsApp. Kelas berjalan online, sehingga siswa perlu menyiapkan perangkat dan koneksi internet sendiri.</p>

  <h2>Hasil belajar</h2>
  <p>Kami berusaha membantu siswa menguasai materi, tetapi hasil akhir ujian bergantung pada banyak hal, termasuk usaha siswa, dan tidak dapat dijamin.</p>

  <h2>Kontak</h2>
  <p>Pertanyaan tentang ketentuan ini bisa disampaikan<?php if ($wa): ?> lewat <a href="<?= h(wa_link($wa, 'Halo, saya mau tanya soal syarat dan ketentuan.')) ?>" rel="noopener">WhatsApp</a><?php else: ?> kepada kami<?php endif; ?>. Lihat juga <a href="<?= h(url('privasi')) ?>">Kebijakan Privasi</a>.</p>
</main>
