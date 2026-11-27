# Platform Belajar Mathletics (rilis 1)

PHP 8.1 atau lebih baru + MySQL. Tanpa composer, tanpa proses build.

## Isi rilis 1
- Publik: beranda dengan popup pilih jenjang (hanya SMP aktif, jenjang lain masuk daftar tunggu), katalog dan detail paket, pendaftaran + unggah bukti bayar, tes diagnostik gratis dengan hasil per topik.
- Login satu halaman, dua peran (Siswa / Admin), ganti password, batas percobaan login.
- Panel siswa: Hari ini, Materi per pertemuan, Jadwal, Tes dan riwayat.
- Panel admin: ringkasan, konfirmasi pembayaran (membuat akun siswa otomatis), siswa, paket dan kuota, materi (unggah PDF atau tautan slide), bank soal (form + impor CSV), tes, daftar tunggu, pengaturan (WhatsApp, rekening, QRIS).
- Belum ada (rilis 2): XP, streak, level, lencana, peta belajar, laporan otomatis, akun orang tua.

## Cara pasang di cPanel
1. Cadangkan isi `public_html` lama (zip + ekspor database) dan pindahkan keluar dari `public_html`.
2. cPanel > "Select PHP Version": pilih 8.1 atau lebih baru. Pastikan ekstensi `pdo_mysql`, `mbstring`, `fileinfo` aktif.
3. cPanel > "MySQL Databases": buat database kosong baru, buat user baru dengan password kuat, hubungkan user ke database (ALL PRIVILEGES). Jangan pakai database dan user lama.
4. File Manager > `public_html` > Upload `mathletics-platform.zip` > klik kanan > Extract. Isi zip langsung berada di `public_html` (ada `index.php`, `install.php`, folder `app`, `assets`, `storage`).
5. Buka `https://mathletics.id/install.php`, isi data database dan akun admin, klik Pasang.
6. **Hapus `install.php`** dari File Manager segera setelah berhasil.
7. Masuk di `/masuk` tab Admin. Lengkapi: Pengaturan (WhatsApp, rekening, QRIS), ganti 7 soal contoh dengan bank soal sendiri (tes diagnostik publik memakai soal yang dipilih di menu Tes), isi judul/topik/tanggal/tautan/berkas tiap pertemuan lalu buka (centang "Materi terbuka").

## Keamanan yang sudah ada
Password di-hash, prepared statement, token CSRF di semua form, cookie sesi HttpOnly + SameSite, pengecekan peran di server, batas login gagal, unggahan divalidasi isi file (bukan ekstensi) dan disimpan di folder terkunci (`storage/`), kuota ditegakkan dalam transaksi, honeypot anti-bot. Folder `app/` dan `storage/` dilindungi `.htaccess` (Apache/LiteSpeed). Setelah pasang, buka `https://mathletics.id/app/config.php` dan `https://mathletics.id/storage/` untuk memastikan hasilnya "Forbidden".

## Catatan
- Backup: aktifkan backup otomatis cPanel dan unduh ekspor database + folder `storage` secara berkala.
- Data anak: kumpulkan seminimal mungkin, minta persetujuan orang tua (sudah ada di form), siapkan jalur penghapusan data atas permintaan. Tinjau aspek hukum perlindungan data pribadi dengan ahlinya.
- Soal contoh bertanda "soal contoh" di bank soal sampai kamu menyuntingnya.
