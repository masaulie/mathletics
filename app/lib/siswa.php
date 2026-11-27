<?php
declare(strict_types=1);

function paket_ids(int $uid): array
{
    return array_map('intval', array_column(all('SELECT paket_id FROM akses WHERE user_id=?', [$uid]), 'paket_id'));
}
function in_list(array $ids): string { return $ids ? implode(',', array_map('intval', $ids)) : '0'; }

function h_belajar(): void
{
    $u = me();
    $ids = paket_ids((int)$u['id']);
    $in = in_list($ids);
    $hariIni = date('Y-m-d');
    $berikut = one("SELECT pt.*, p.nama AS paket_nama FROM pertemuan pt JOIN paket p ON p.id=pt.paket_id WHERE pt.paket_id IN ($in) AND pt.tanggal IS NOT NULL AND pt.tanggal >= ? ORDER BY pt.tanggal, pt.jam LIMIT 1", [$hariIni]);
    $terbuka = all("SELECT pt.*, (SELECT COUNT(*) FROM berkas b WHERE b.pertemuan_id=pt.id AND b.tampil=1) AS jml FROM pertemuan pt WHERE pt.paket_id IN ($in) AND pt.terbuka=1 ORDER BY pt.urut DESC LIMIT 3");
    $total = (int)val("SELECT COUNT(*) FROM pertemuan WHERE paket_id IN ($in)");
    $buka = (int)val("SELECT COUNT(*) FROM pertemuan WHERE paket_id IN ($in) AND terbuka=1");
    $terakhir = one('SELECT pc.*, t.judul FROM percobaan pc JOIN tes t ON t.id=pc.tes_id WHERE pc.user_id=? AND pc.selesai IS NOT NULL ORDER BY pc.id DESC LIMIT 1', [$u['id']]);
    $jalan = one('SELECT pc.token, t.judul FROM percobaan pc JOIN tes t ON t.id=pc.tes_id WHERE pc.user_id=? AND pc.selesai IS NULL AND pc.batas > ? ORDER BY pc.id DESC LIMIT 1', [$u['id'], now()]);
    view('siswa_beranda', compact('u', 'berikut', 'terbuka', 'total', 'buka', 'terakhir', 'jalan'), 'siswa');
}

function h_materi(): void
{
    $u = me();
    $in = in_list(paket_ids((int)$u['id']));
    $rows = all("SELECT pt.*, p.nama AS paket_nama, (SELECT COUNT(*) FROM berkas b WHERE b.pertemuan_id=pt.id AND b.tampil=1) AS jml FROM pertemuan pt JOIN paket p ON p.id=pt.paket_id WHERE pt.paket_id IN ($in) ORDER BY pt.paket_id, pt.urut");
    view('siswa_materi', ['rows' => $rows], 'siswa');
}

function h_materi_detail(array $a): void
{
    $u = me();
    $in = in_list(paket_ids((int)$u['id']));
    $pt = one("SELECT pt.*, p.nama AS paket_nama FROM pertemuan pt JOIN paket p ON p.id=pt.paket_id WHERE pt.id=? AND pt.paket_id IN ($in) AND pt.terbuka=1", [(int)$a['id']]);
    if (!$pt) {
        http_response_code(404);
        view('pesan', ['judul' => 'Materi belum dibuka', 'isi' => 'Pertemuan ini belum dibuka atau bukan bagian dari paketmu.'], 'siswa');
        return;
    }
    $prev = one("SELECT id FROM pertemuan WHERE paket_id=? AND terbuka=1 AND urut < ? ORDER BY urut DESC LIMIT 1", [$pt['paket_id'], $pt['urut']]);
    $next = one("SELECT id FROM pertemuan WHERE paket_id=? AND terbuka=1 AND urut > ? ORDER BY urut LIMIT 1", [$pt['paket_id'], $pt['urut']]);
    view('siswa_materi_detail', ['pt' => $pt, 'berkas' => all('SELECT * FROM berkas WHERE pertemuan_id=? AND tampil=1 ORDER BY id', [$pt['id']]), 'prev' => $prev, 'next' => $next], 'siswa');
}

function h_berkas(array $a): void
{
    $u = me();
    $b = one('SELECT b.*, pt.paket_id, pt.terbuka FROM berkas b JOIN pertemuan pt ON pt.id=b.pertemuan_id WHERE b.id=?', [(int)$a['id']]);
    if (!$b) {
        http_response_code(404);
        exit('Berkas tidak ditemukan.');
    }
    if ($u['peran'] !== 'admin') {
        $boleh = (int)$b['tampil'] === 1 && (int)$b['terbuka'] === 1 && in_array((int)$b['paket_id'], paket_ids((int)$u['id']), true);
        if (!$boleh) {
            http_response_code(403);
            exit('Kamu tidak punya akses ke berkas ini.');
        }
    }
    if ($b['url']) redirect($b['url']);
    if ($b['path']) send_file($b['path'], $b['judul'] . '.' . pathinfo($b['path'], PATHINFO_EXTENSION));
    http_response_code(404);
    exit('Berkas kosong.');
}

function h_jadwal(): void
{
    $u = me();
    $in = in_list(paket_ids((int)$u['id']));
    $rows = all("SELECT pt.*, p.nama AS paket_nama FROM pertemuan pt JOIN paket p ON p.id=pt.paket_id WHERE pt.paket_id IN ($in) AND pt.tanggal IS NOT NULL ORDER BY pt.tanggal, pt.jam");
    view('siswa_jadwal', ['rows' => $rows, 'hariIni' => date('Y-m-d')], 'siswa');
}

function h_siswa_tes(): void
{
    $u = me();
    $in = in_list(paket_ids((int)$u['id']));
    $tes = all("SELECT t.*, (SELECT COUNT(*) FROM tes_soal ts JOIN soal s ON s.id=ts.soal_id WHERE ts.tes_id=t.id AND s.aktif=1) AS jumlah FROM tes t WHERE t.status='aktif' AND (t.publik=1 OR t.paket_id IN ($in)) ORDER BY t.id");
    $riwayat = all('SELECT pc.*, t.judul FROM percobaan pc JOIN tes t ON t.id=pc.tes_id WHERE pc.user_id=? ORDER BY pc.id DESC LIMIT 20', [$u['id']]);
    view('siswa_tes', ['tes' => $tes, 'riwayat' => $riwayat], 'siswa');
}

route('GET', '/belajar', 'h_belajar', ['mw_siswa']);
route('GET', '/belajar/materi', 'h_materi', ['mw_siswa']);
route('GET', '/belajar/materi/{id}', 'h_materi_detail', ['mw_siswa']);
route('GET', '/belajar/jadwal', 'h_jadwal', ['mw_siswa']);
route('GET', '/belajar/tes', 'h_siswa_tes', ['mw_siswa']);
route('GET', '/berkas/{id}', 'h_berkas', ['mw_login']);
