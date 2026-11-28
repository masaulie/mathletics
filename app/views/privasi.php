<?php
$nama = setting('nama_usaha', 'Mathletics');
$wa = setting('wa');
$desc = 'Kebijakan privasi ' . $nama . ': data apa yang dikumpulkan, untuk apa, berapa lama disimpan, dan cara meminta penghapusan.';
?>
<main class="wrap legal">
  <h1>Kebijakan Privasi</h1>
  <p class="mute">Terakhir diperbarui: <?= h(tgl(date('Y-m-d'))) ?>. Pengguna layanan kami adalah anak-anak, jadi kami berusaha mengumpulkan data sesedikit mungkin.</p>

  <h2>Data yang kami kumpulkan</h2>
  <ul>
    <li><b>Saat mendaftar paket:</b> nama anak, kelas, nama orang tua atau wali, nomor WhatsApp, dan email (opsional).</li>
    <li><b>Saat membayar:</b> foto bukti pembayaran yang Anda unggah.</li>
    <li><b>Saat mengerjakan tes:</b> nama, nomor WhatsApp, jawaban, dan hasil tes.</li>
    <li><b>Saat memakai akun siswa:</b> waktu masuk terakhir dan riwayat tes.</li>
    <li><b>Untuk keamanan:</b> alamat IP dan percobaan masuk yang gagal, disimpan sementara untuk membatasi penyalahgunaan.</li>
  </ul>

  <h2>Untuk apa data dipakai</h2>
  <ul>
    <li>Membuat akun siswa dan memberikan akses ke kelas, materi, dan tes.</li>
    <li>Mengonfirmasi pembayaran dan menghubungi Anda lewat WhatsApp.</li>
    <li>Menyusun hasil tes per topik agar pembelajaran sesuai kebutuhan anak.</li>
    <li>Menjaga keamanan layanan.</li>
  </ul>
  <p>Kami tidak menjual data Anda dan tidak membagikannya untuk keperluan iklan pihak lain.</p>

  <h2>Persetujuan orang tua</h2>
  <p>Pendaftaran anak hanya boleh dilakukan oleh orang tua atau wali. Dengan mencentang persetujuan pada formulir pendaftaran, Anda menyatakan berwenang mewakili anak dan menyetujui pemrosesan data sesuai kebijakan ini.</p>

  <h2>Penyimpanan dan keamanan</h2>
  <p>Kata sandi disimpan dalam bentuk terenkripsi satu arah. Bukti pembayaran dan materi disimpan di folder yang tidak dapat dibuka langsung dari internet. Tidak ada sistem yang sepenuhnya kebal, tetapi kami menerapkan pengamanan yang wajar dan membatasi akses admin.</p>

  <h2>Pihak ketiga</h2>
  <p>Situs ini memuat font dari Google Fonts. Kami dapat memakai layanan statistik pengunjung yang bersifat agregat. Komunikasi dengan Anda berlangsung lewat WhatsApp, yang tunduk pada kebijakan WhatsApp sendiri.</p>

  <h2>Hak Anda</h2>
  <p>Anda dapat meminta untuk melihat, memperbaiki, atau menghapus data anak Anda kapan saja. Hubungi kami<?php if ($wa): ?> lewat <a href="<?= h(wa_link($wa, 'Halo, saya ingin meminta data/penghapusan data anak saya di ' . $nama . '.')) ?>" rel="noopener">WhatsApp</a><?php endif; ?> dan sebutkan nama anak serta nomor yang terdaftar. Kami akan memprosesnya setelah memastikan permintaan datang dari orang tua atau wali.</p>

  <h2>Perubahan kebijakan</h2>
  <p>Jika kebijakan ini berubah, versi terbaru akan ditampilkan di halaman ini beserta tanggal pembaruannya.</p>
</main>
