<?php
declare(strict_types=1);

/** Daftar perintah pembuatan tabel. Token {PK} dan {ENG} disesuaikan dengan MySQL atau SQLite. */
function schema_sql(bool $mysql): array
{
    $pk = $mysql ? 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $eng = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4' : '';
    $t = [
"CREATE TABLE users (
  id {PK},
  peran VARCHAR(10) NOT NULL,
  nama VARCHAR(120) NOT NULL,
  hp VARCHAR(20) NULL UNIQUE,
  email VARCHAR(160) NULL UNIQUE,
  sandi VARCHAR(255) NOT NULL,
  harus_ganti TINYINT NOT NULL DEFAULT 0,
  aktif TINYINT NOT NULL DEFAULT 1,
  dibuat DATETIME NOT NULL,
  terakhir_masuk DATETIME NULL
){ENG}",
"CREATE TABLE jenjang (
  id {PK},
  kode VARCHAR(10) NOT NULL UNIQUE,
  nama VARCHAR(60) NOT NULL,
  aktif TINYINT NOT NULL DEFAULT 0,
  urut INT NOT NULL DEFAULT 0
){ENG}",
"CREATE TABLE paket (
  id {PK},
  jenjang_id INT NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  nama VARCHAR(120) NOT NULL,
  ringkas VARCHAR(255) NOT NULL DEFAULT '',
  deskripsi TEXT NULL,
  tipe VARCHAR(12) NOT NULL DEFAULT 'kelas',
  harga INT NOT NULL DEFAULT 0,
  harga_normal INT NULL,
  kuota INT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'draft',
  jadwal VARCHAR(160) NOT NULL DEFAULT '',
  dibuat DATETIME NOT NULL
){ENG}",
"CREATE TABLE pendaftaran (
  id {PK},
  token VARCHAR(32) NOT NULL UNIQUE,
  paket_id INT NOT NULL,
  nama_anak VARCHAR(120) NOT NULL,
  kelas VARCHAR(20) NOT NULL DEFAULT '',
  nama_ortu VARCHAR(120) NOT NULL,
  hp VARCHAR(20) NOT NULL,
  email VARCHAR(160) NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'menunggu_bayar',
  bukti VARCHAR(200) NULL,
  catatan VARCHAR(255) NULL,
  user_id INT NULL,
  dibuat DATETIME NOT NULL,
  diperbarui DATETIME NOT NULL
){ENG}",
"CREATE INDEX idx_pendaftaran_status ON pendaftaran (status)",
"CREATE TABLE akses (
  id {PK},
  user_id INT NOT NULL,
  paket_id INT NOT NULL,
  dibuat DATETIME NOT NULL,
  UNIQUE (user_id, paket_id)
){ENG}",
"CREATE TABLE pertemuan (
  id {PK},
  paket_id INT NOT NULL,
  urut INT NOT NULL DEFAULT 0,
  judul VARCHAR(160) NOT NULL,
  topik VARCHAR(160) NOT NULL DEFAULT '',
  tanggal DATE NULL,
  jam VARCHAR(5) NULL,
  tautan VARCHAR(255) NULL,
  terbuka TINYINT NOT NULL DEFAULT 0
){ENG}",
"CREATE TABLE berkas (
  id {PK},
  pertemuan_id INT NOT NULL,
  jenis VARCHAR(12) NOT NULL,
  judul VARCHAR(160) NOT NULL,
  path VARCHAR(200) NULL,
  url VARCHAR(255) NULL,
  tampil TINYINT NOT NULL DEFAULT 1
){ENG}",
"CREATE TABLE topik (
  id {PK},
  jenjang_id INT NOT NULL,
  nama VARCHAR(80) NOT NULL
){ENG}",
"CREATE TABLE soal (
  id {PK},
  topik_id INT NOT NULL,
  tingkat TINYINT NOT NULL DEFAULT 2,
  teks TEXT NOT NULL,
  a TEXT NOT NULL,
  b TEXT NOT NULL,
  c TEXT NOT NULL,
  d TEXT NOT NULL,
  kunci CHAR(1) NOT NULL,
  pembahasan TEXT NULL,
  aktif TINYINT NOT NULL DEFAULT 1,
  contoh TINYINT NOT NULL DEFAULT 0
){ENG}",
"CREATE TABLE tes (
  id {PK},
  paket_id INT NULL,
  judul VARCHAR(160) NOT NULL,
  tipe VARCHAR(12) NOT NULL DEFAULT 'diagnostik',
  durasi INT NOT NULL DEFAULT 60,
  publik TINYINT NOT NULL DEFAULT 0,
  status VARCHAR(10) NOT NULL DEFAULT 'draft',
  dibuat DATETIME NOT NULL
){ENG}",
"CREATE TABLE tes_soal (
  tes_id INT NOT NULL,
  soal_id INT NOT NULL,
  urut INT NOT NULL DEFAULT 0,
  PRIMARY KEY (tes_id, soal_id)
){ENG}",
"CREATE TABLE percobaan (
  id {PK},
  token VARCHAR(32) NOT NULL UNIQUE,
  tes_id INT NOT NULL,
  user_id INT NULL,
  nama VARCHAR(120) NOT NULL,
  hp VARCHAR(20) NULL,
  mulai DATETIME NOT NULL,
  batas DATETIME NOT NULL,
  selesai DATETIME NULL,
  skor INT NULL,
  benar INT NULL,
  total INT NULL,
  rincian TEXT NULL
){ENG}",
"CREATE TABLE jawaban (
  percobaan_id INT NOT NULL,
  soal_id INT NOT NULL,
  pilihan CHAR(1) NULL,
  ragu TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (percobaan_id, soal_id)
){ENG}",
"CREATE TABLE tunggu (
  id {PK},
  jenjang_kode VARCHAR(10) NOT NULL,
  nama VARCHAR(120) NOT NULL,
  hp VARCHAR(20) NOT NULL,
  dibuat DATETIME NOT NULL
){ENG}",
"CREATE TABLE login_gagal (
  id {PK},
  ident VARCHAR(160) NOT NULL,
  ip VARCHAR(45) NOT NULL,
  waktu DATETIME NOT NULL
){ENG}",
"CREATE TABLE pengaturan (
  kunci VARCHAR(40) NOT NULL PRIMARY KEY,
  nilai TEXT NULL
){ENG}",
"CREATE TABLE audit (
  id {PK},
  user_id INT NULL,
  aksi VARCHAR(60) NOT NULL,
  objek VARCHAR(120) NOT NULL DEFAULT '',
  waktu DATETIME NOT NULL
){ENG}",
    ];
    return array_map(fn($s) => str_replace(['{PK}', '{ENG}'], [$pk, $eng], $s), $t);
}

function seed_data(string $waNomor): void
{
    $now = now();
    foreach ([['SD', 'SD', 0, 1], ['SMP', 'SMP', 1, 2], ['SMA', 'SMA', 0, 3], ['UMUM', 'Umum (SNBT, CPNS)', 0, 4]] as [$kode, $nama, $aktif, $urut]) {
        ins('INSERT INTO jenjang (kode, nama, aktif, urut) VALUES (?,?,?,?)', [$kode, $nama, $aktif, $urut]);
    }
    $smp = (int)val("SELECT id FROM jenjang WHERE kode='SMP'");

    $paketKelas = ins(
        'INSERT INTO paket (jenjang_id, slug, nama, ringkas, deskripsi, tipe, harga, harga_normal, kuota, status, jadwal, dibuat) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
        [$smp, 'kelas-kolektif-tka-smp', 'Kelas Kolektif TKA SMP',
         'Kelas online kecil, maksimal 8 siswa, 14 pertemuan Matematika kelas 9.',
         "14 pertemuan online, masing-masing 90 menit.\nMaksimal 8 siswa per kelas supaya setiap anak sempat bertanya.\nSetiap pertemuan punya modul, worksheet, kunci, dan slide yang bisa dibuka kapan saja di akun siswa.\nTes diagnostik di awal dan laporan perkembangan per topik.",
         'kelas', 799000, 999000, 8, 'aktif', 'Malam hari, 18.30-20.00 (jadwal pasti dikirim setelah pendaftaran)', $now]
    );
    ins("INSERT INTO paket (jenjang_id, slug, nama, ringkas, deskripsi, tipe, harga, kuota, status, jadwal, dibuat) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
        [$smp, 'tes-dan-tryout', 'Tes dan Try-out', 'Paket try-out saja, tanpa kelas.', '', 'tes', 0, null, 'draft', '', $now]);
    ins("INSERT INTO paket (jenjang_id, slug, nama, ringkas, deskripsi, tipe, harga, kuota, status, jadwal, dibuat) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
        [$smp, 'belajar-mandiri', 'Belajar Mandiri', 'Materi saja, belajar sendiri.', '', 'mandiri', 0, null, 'draft', '', $now]);

    for ($i = 1; $i <= 14; $i++) {
        ins('INSERT INTO pertemuan (paket_id, urut, judul, topik, terbuka) VALUES (?,?,?,?,0)', [$paketKelas, $i, 'Pertemuan ' . $i, '']);
    }

    $topik = [];
    foreach (['Bilangan', 'Aljabar', 'Geometri dan pengukuran', 'Data dan peluang'] as $n) {
        $topik[$n] = ins('INSERT INTO topik (jenjang_id, nama) VALUES (?,?)', [$smp, $n]);
    }
    $contoh = [
        ['Bilangan', 2, 'Hasil dari $\frac{2}{3}+\frac{3}{4}$ adalah ...', '$\frac{5}{7}$', '$\frac{17}{12}$', '$\frac{5}{12}$', '$\frac{6}{7}$', 'B', 'Samakan penyebut: $\frac{8}{12}+\frac{9}{12}=\frac{17}{12}$.'],
        ['Aljabar', 2, 'Jika $3x-7=11$, maka nilai $2x+1$ adalah ...', '11', '12', '13', '15', 'C', '$3x=18$, jadi $x=6$ dan $2x+1=13$.'],
        ['Aljabar', 2, 'Himpunan penyelesaian dari $2x+5<13$ adalah ...', '$x<4$', '$x>4$', '$x<9$', '$x>9$', 'A', '$2x<8$, jadi $x<4$.'],
        ['Geometri dan pengukuran', 2, 'Luas lingkaran dengan jari-jari 7 cm (gunakan $\pi=\frac{22}{7}$) adalah ... cm$^2$', '44', '154', '308', '49', 'B', '$L=\pi r^2=\frac{22}{7}\times 49=154$.'],
        ['Geometri dan pengukuran', 1, 'Balok berukuran 5 cm $\times$ 4 cm $\times$ 3 cm. Volumenya adalah ... cm$^3$', '12', '47', '60', '94', 'C', '$V=5\times4\times3=60$.'],
        ['Data dan peluang', 1, 'Nilai ulangan lima siswa: 6, 7, 8, 9, 10. Rata-ratanya adalah ...', '7', '8', '9', '40', 'B', 'Jumlah 40 dibagi 5 siswa = 8.'],
        ['Data dan peluang', 2, 'Sebuah dadu dilempar sekali. Peluang muncul mata dadu genap adalah ...', '$\frac{1}{6}$', '$\frac{1}{3}$', '$\frac{1}{2}$', '$\frac{2}{3}$', 'C', 'Mata genap: 2, 4, 6 (3 dari 6), jadi $\frac{3}{6}=\frac{1}{2}$.'],
    ];
    $tes = ins("INSERT INTO tes (paket_id, judul, tipe, durasi, publik, status, dibuat) VALUES (NULL,?,?,?,1,'aktif',?)", ['Tes Diagnostik TKA SMP', 'diagnostik', 60, $now]);
    $u = 0;
    foreach ($contoh as [$tp, $tg, $teks, $a, $b, $c, $d, $k, $pb]) {
        $sid = ins('INSERT INTO soal (topik_id, tingkat, teks, a, b, c, d, kunci, pembahasan, aktif, contoh) VALUES (?,?,?,?,?,?,?,?,?,1,1)', [$topik[$tp], $tg, $teks, $a, $b, $c, $d, $k, $pb]);
        ins('INSERT INTO tes_soal (tes_id, soal_id, urut) VALUES (?,?,?)', [$tes, $sid, ++$u]);
    }

    set_setting('nama_usaha', 'Mathletics');
    set_setting('wa', $waNomor);
    set_setting('instruksi_bayar', "Transfer sesuai nominal ke rekening atau QRIS yang tertera, lalu unggah foto bukti pembayaran.\nIsi rekening: (ubah di menu Pengaturan admin)");
}
