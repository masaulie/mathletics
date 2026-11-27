<?php
declare(strict_types=1);

/* ---------- pembantu paket ---------- */
function kuota_terpakai(int $paketId): int
{
    $batas = date('Y-m-d H:i:s', time() - 48 * 3600);
    return (int)val(
        "SELECT COUNT(*) FROM pendaftaran WHERE paket_id=? AND (status IN ('menunggu_konfirmasi','lunas') OR (status='menunggu_bayar' AND dibuat > ?))",
        [$paketId, $batas]
    );
}
function kuota_sisa(array $p): ?int
{
    if ($p['kuota'] === null || $p['kuota'] === '') return null;
    return max(0, (int)$p['kuota'] - kuota_terpakai((int)$p['id']));
}
function paket_aktif_smp(): array
{
    return all("SELECT p.* FROM paket p JOIN jenjang j ON j.id=p.jenjang_id WHERE j.kode='SMP' AND p.status='aktif' AND p.harga > 0 ORDER BY p.harga DESC");
}
function tes_diagnostik(): ?array
{
    $t = one("SELECT * FROM tes WHERE publik=1 AND status='aktif' AND tipe='diagnostik' ORDER BY id LIMIT 1");
    if ($t) $t['jumlah'] = (int)val('SELECT COUNT(*) FROM tes_soal ts JOIN soal s ON s.id=ts.soal_id WHERE ts.tes_id=? AND s.aktif=1', [$t['id']]);
    return $t;
}

/* ---------- halaman ---------- */
function h_home(): void
{
    view('beranda', ['paket' => paket_aktif_smp(), 'diag' => tes_diagnostik()]);
}

function h_paket_list(): void
{
    view('paket_daftar', ['paket' => paket_aktif_smp(), 'diag' => tes_diagnostik()]);
}

function h_paket_detail(array $a): void
{
    $p = one("SELECT * FROM paket WHERE slug=? AND status='aktif'", [$a['slug']]);
    if (!$p) {
        http_response_code(404);
        view('pesan', ['judul' => 'Paket tidak ditemukan', 'isi' => 'Paket ini tidak tersedia atau sudah ditutup.']);
        return;
    }
    view('paket_detail', ['p' => $p, 'sisa' => kuota_sisa($p), 'old' => $_SESSION['old'] ?? [], 'err' => flashes()]);
    unset($_SESSION['old']);
}

function h_daftar_post(array $a): void
{
    $p = one("SELECT * FROM paket WHERE slug=? AND status='aktif'", [$a['slug']]);
    if (!$p) redirect('paket');
    if (post('website') !== '') redirect('paket');   // jebakan bot

    $d = [
        'nama_anak' => mb_substr(post('nama_anak'), 0, 120),
        'kelas' => mb_substr(post('kelas', '9'), 0, 20),
        'nama_ortu' => mb_substr(post('nama_ortu'), 0, 120),
        'hp' => normalize_hp(post('hp')),
        'email' => mb_substr(post('email'), 0, 160),
    ];
    $_SESSION['old'] = $d;
    $err = [];
    if ($d['nama_anak'] === '') $err[] = 'Nama anak wajib diisi.';
    if ($d['nama_ortu'] === '') $err[] = 'Nama orang tua wajib diisi.';
    if (!valid_hp($d['hp'])) $err[] = 'Nomor WhatsApp tidak valid. Contoh: 081234567890.';
    if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $err[] = 'Format email tidak benar.';
    if (post('setuju') !== '1') $err[] = 'Centang persetujuan orang tua untuk melanjutkan.';
    if ((int)val("SELECT COUNT(*) FROM pendaftaran WHERE hp=? AND dibuat > ?", [$d['hp'], date('Y-m-d H:i:s', time() - 3600)]) >= 3) {
        $err[] = 'Terlalu banyak pendaftaran dari nomor ini dalam satu jam. Coba lagi nanti atau hubungi kami lewat WhatsApp.';
    }
    if ($err) {
        foreach ($err as $e) flash('error', $e);
        redirect('paket/' . $p['slug']);
    }

    $token = bin2hex(random_bytes(16));
    $pdo = db();
    $pdo->beginTransaction();
    try {
        if (is_mysql()) q('SELECT id FROM paket WHERE id=? FOR UPDATE', [$p['id']]);
        $sisa = kuota_sisa($p);
        if ($sisa !== null && $sisa <= 0) {
            $pdo->rollBack();
            flash('error', 'Maaf, kuota paket ini sudah penuh.');
            redirect('paket/' . $p['slug']);
        }
        $n = now();
        ins('INSERT INTO pendaftaran (token, paket_id, nama_anak, kelas, nama_ortu, hp, email, status, dibuat, diperbarui) VALUES (?,?,?,?,?,?,?,?,?,?)',
            [$token, $p['id'], $d['nama_anak'], $d['kelas'], $d['nama_ortu'], $d['hp'], $d['email'] ?: null, 'menunggu_bayar', $n, $n]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    unset($_SESSION['old']);
    redirect('daftar/' . $token);
}

function h_daftar_status(array $a): void
{
    $r = one('SELECT d.*, p.nama AS paket_nama, p.harga FROM pendaftaran d JOIN paket p ON p.id=d.paket_id WHERE d.token=?', [$a['token']]);
    if (!$r) {
        http_response_code(404);
        view('pesan', ['judul' => 'Pendaftaran tidak ditemukan', 'isi' => 'Tautan ini tidak valid.']);
        return;
    }
    view('daftar_status', ['r' => $r]);
}

function h_bukti_post(array $a): void
{
    $r = one('SELECT * FROM pendaftaran WHERE token=?', [$a['token']]);
    if (!$r || !in_array($r['status'], ['menunggu_bayar', 'menunggu_konfirmasi', 'ditolak'], true)) redirect('paket');
    $path = save_upload($_FILES['bukti'] ?? [], 'uploads', ['image/jpeg' => 'jpg', 'image/png' => 'png'], 3 * 1024 * 1024);
    if (!$path) {
        flash('error', 'Unggahan gagal. Pakai foto JPG atau PNG, maksimal 3 MB.');
        redirect('daftar/' . $r['token']);
    }
    q("UPDATE pendaftaran SET bukti=?, status='menunggu_konfirmasi', catatan=NULL, diperbarui=? WHERE id=?", [$path, now(), $r['id']]);
    flash('ok', 'Bukti pembayaran terkirim. Kami konfirmasi secepatnya lewat WhatsApp.');
    redirect('daftar/' . $r['token']);
}

function h_tunggu(): void
{
    $kode = strtoupper(post('jenjang'));
    $nama = mb_substr(post('nama'), 0, 120);
    $hp = normalize_hp(post('hp'));
    if (!in_array($kode, ['SD', 'SMA', 'UMUM'], true) || $nama === '' || !valid_hp($hp)) {
        json_out(['ok' => false, 'pesan' => 'Isi nama dan nomor WhatsApp yang benar.'], 422);
    }
    if (!val('SELECT COUNT(*) FROM tunggu WHERE jenjang_kode=? AND hp=?', [$kode, $hp])) {
        ins('INSERT INTO tunggu (jenjang_kode, nama, hp, dibuat) VALUES (?,?,?,?)', [$kode, $nama, $hp, now()]);
    }
    json_out(['ok' => true]);
}

function h_qris(): void
{
    $p = setting('qris');
    if ($p === '') {
        http_response_code(404);
        exit;
    }
    send_file($p);
}

route('GET', '/', 'h_home');
route('GET', '/paket', 'h_paket_list');
route('GET', '/paket/{slug}', 'h_paket_detail');
route('POST', '/paket/{slug}', 'h_daftar_post');
route('GET', '/daftar/{token}', 'h_daftar_status');
route('POST', '/daftar/{token}/bukti', 'h_bukti_post');
route('POST', '/tunggu', 'h_tunggu');
route('GET', '/qris', 'h_qris');
