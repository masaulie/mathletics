<?php
declare(strict_types=1);

function tes_soal_list(int $tesId): array
{
    return all(
        'SELECT s.*, t.nama AS topik_nama, ts.urut FROM tes_soal ts JOIN soal s ON s.id=ts.soal_id JOIN topik t ON t.id=s.topik_id WHERE ts.tes_id=? AND s.aktif=1 ORDER BY ts.urut, s.id',
        [$tesId]
    );
}

function percobaan_by_token(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) return null;
    return one('SELECT pc.*, t.judul, t.durasi, t.tipe, t.publik FROM percobaan pc JOIN tes t ON t.id=pc.tes_id WHERE pc.token=?', [$token]);
}

function nilai_percobaan(array $pc): void
{
    if ($pc['selesai']) return;
    $soal = tes_soal_list((int)$pc['tes_id']);
    $jaw = [];
    foreach (all('SELECT soal_id, pilihan FROM jawaban WHERE percobaan_id=?', [$pc['id']]) as $j) $jaw[$j['soal_id']] = $j['pilihan'];
    $benar = 0;
    $topik = [];
    foreach ($soal as $s) {
        $ok = isset($jaw[$s['id']]) && $jaw[$s['id']] === $s['kunci'];
        $benar += $ok ? 1 : 0;
        $topik[$s['topik_nama']]['benar'] = ($topik[$s['topik_nama']]['benar'] ?? 0) + ($ok ? 1 : 0);
        $topik[$s['topik_nama']]['total'] = ($topik[$s['topik_nama']]['total'] ?? 0) + 1;
    }
    $total = count($soal);
    $skor = $total > 0 ? (int)round($benar / $total * 100) : 0;
    q('UPDATE percobaan SET selesai=?, skor=?, benar=?, total=?, rincian=? WHERE id=? AND selesai IS NULL',
        [now(), $skor, $benar, $total, json_encode($topik, JSON_UNESCAPED_UNICODE), $pc['id']]);
}

function buat_percobaan(array $tes, ?int $userId, string $nama, ?string $hp): string
{
    $token = bin2hex(random_bytes(16));
    $mulai = time();
    ins('INSERT INTO percobaan (token, tes_id, user_id, nama, hp, mulai, batas) VALUES (?,?,?,?,?,?,?)',
        [$token, $tes['id'], $userId, $nama, $hp, date('Y-m-d H:i:s', $mulai), date('Y-m-d H:i:s', $mulai + (int)$tes['durasi'] * 60)]);
    return $token;
}

/* ---------- tes publik (tanpa akun) ---------- */
function h_tes_publik(): void
{
    view('tes_publik', ['diag' => tes_diagnostik(), 'err' => flashes(), 'old' => ['nama' => '', 'hp' => '']]);
}

function h_tes_mulai(): void
{
    $tes = tes_diagnostik();
    $nama = mb_substr(post('nama'), 0, 120);
    $hp = normalize_hp(post('hp'));
    if (post('website') !== '') redirect('tes');
    if (!$tes || $tes['jumlah'] < 1) {
        flash('error', 'Tes belum tersedia.');
        redirect('tes');
    }
    if ($nama === '' || !valid_hp($hp)) {
        flash('error', 'Isi nama dan nomor WhatsApp yang benar.');
        view('tes_publik', ['diag' => $tes, 'err' => flashes(), 'old' => ['nama' => $nama, 'hp' => post('hp')]]);
        return;
    }
    if ((int)val('SELECT COUNT(*) FROM percobaan WHERE hp=? AND mulai > ?', [$hp, date('Y-m-d H:i:s', time() - 3600)]) >= 5) {
        flash('error', 'Terlalu banyak percobaan dari nomor ini. Coba lagi nanti.');
        redirect('tes');
    }
    // lanjutkan percobaan yang belum selesai untuk nomor yang sama
    $ada = one('SELECT token FROM percobaan WHERE hp=? AND tes_id=? AND selesai IS NULL AND batas > ? ORDER BY id DESC', [$hp, $tes['id'], now()]);
    redirect('ujian/' . ($ada['token'] ?? buat_percobaan($tes, null, $nama, $hp)));
}

/* ---------- tes untuk siswa login ---------- */
function h_siswa_tes_mulai(array $a): void
{
    $u = me();
    $tes = one("SELECT * FROM tes WHERE id=? AND status='aktif'", [(int)$a['id']]);
    if (!$tes || !tes_boleh($tes, $u)) redirect('belajar/tes');
    $ada = one('SELECT token FROM percobaan WHERE user_id=? AND tes_id=? AND selesai IS NULL AND batas > ? ORDER BY id DESC', [$u['id'], $tes['id'], now()]);
    redirect('ujian/' . ($ada['token'] ?? buat_percobaan($tes, (int)$u['id'], $u['nama'], $u['hp'])));
}

function tes_boleh(array $tes, array $u): bool
{
    if ((int)$tes['publik'] === 1) return true;
    if ($tes['paket_id'] === null) return false;
    return (bool)val('SELECT COUNT(*) FROM akses WHERE user_id=? AND paket_id=?', [$u['id'], $tes['paket_id']]);
}

/* ---------- pengerjaan ---------- */
function h_ujian(array $a): void
{
    $pc = percobaan_by_token($a['token']);
    if (!$pc) {
        http_response_code(404);
        view('pesan', ['judul' => 'Tes tidak ditemukan', 'isi' => 'Tautan tes ini tidak valid.']);
        return;
    }
    if (!$pc['selesai'] && strtotime($pc['batas']) < time()) {
        nilai_percobaan($pc);
        redirect('ujian/' . $pc['token'] . '/hasil');
    }
    if ($pc['selesai']) redirect('ujian/' . $pc['token'] . '/hasil');

    $soal = [];
    foreach (tes_soal_list((int)$pc['tes_id']) as $s) {
        $soal[] = ['id' => (int)$s['id'], 'teks' => $s['teks'], 'topik' => $s['topik_nama'], 'opsi' => ['A' => $s['a'], 'B' => $s['b'], 'C' => $s['c'], 'D' => $s['d']]];
    }
    $jaw = [];
    foreach (all('SELECT soal_id, pilihan, ragu FROM jawaban WHERE percobaan_id=?', [$pc['id']]) as $j) {
        $jaw[(int)$j['soal_id']] = ['p' => $j['pilihan'], 'r' => (int)$j['ragu']];
    }
    view('ujian', [
        'pc' => $pc,
        'data' => ['token' => $pc['token'], 'soal' => $soal, 'jawab' => (object)$jaw, 'sisa' => max(0, strtotime($pc['batas']) - time()), 'base' => rtrim(url(''), '/')],
    ], 'ujian');
}

function h_ujian_simpan(array $a): void
{
    $pc = percobaan_by_token($a['token']);
    if (!$pc || $pc['selesai'] || strtotime($pc['batas']) + 10 < time()) json_out(['ok' => false], 409);
    $sid = (int)post('soal');
    $pil = post('pilihan');
    $ragu = post('ragu') === '1' ? 1 : 0;
    if (!in_array($pil, ['A', 'B', 'C', 'D', ''], true)) json_out(['ok' => false], 422);
    if (!val('SELECT COUNT(*) FROM tes_soal WHERE tes_id=? AND soal_id=?', [$pc['tes_id'], $sid])) json_out(['ok' => false], 422);
    $p = $pil === '' ? null : $pil;
    if (val('SELECT COUNT(*) FROM jawaban WHERE percobaan_id=? AND soal_id=?', [$pc['id'], $sid])) {
        q('UPDATE jawaban SET pilihan=?, ragu=? WHERE percobaan_id=? AND soal_id=?', [$p, $ragu, $pc['id'], $sid]);
    } else {
        q('INSERT INTO jawaban (percobaan_id, soal_id, pilihan, ragu) VALUES (?,?,?,?)', [$pc['id'], $sid, $p, $ragu]);
    }
    json_out(['ok' => true]);
}

function h_ujian_kumpul(array $a): void
{
    $pc = percobaan_by_token($a['token']);
    if (!$pc) redirect('tes');
    nilai_percobaan($pc);
    redirect('ujian/' . $pc['token'] . '/hasil');
}

function h_ujian_hasil(array $a): void
{
    $pc = percobaan_by_token($a['token']);
    if (!$pc) {
        http_response_code(404);
        view('pesan', ['judul' => 'Hasil tidak ditemukan', 'isi' => 'Tautan hasil ini tidak valid.']);
        return;
    }
    if (!$pc['selesai']) redirect('ujian/' . $pc['token']);
    $jaw = [];
    foreach (all('SELECT soal_id, pilihan FROM jawaban WHERE percobaan_id=?', [$pc['id']]) as $j) $jaw[(int)$j['soal_id']] = $j['pilihan'];
    $u = me();
    $layout = $u ? ($u['peran'] === 'admin' ? 'admin' : 'siswa') : 'public';
    view('hasil', [
        'pc' => $pc,
        'rincian' => json_decode((string)$pc['rincian'], true) ?: [],
        'soal' => tes_soal_list((int)$pc['tes_id']),
        'jaw' => $jaw,
        'paket' => (int)$pc['publik'] === 1 ? paket_aktif_smp() : [],
    ], $layout);
}

route('GET', '/tes', 'h_tes_publik');
route('POST', '/tes/mulai', 'h_tes_mulai');
route('POST', '/belajar/tes/{id}/mulai', 'h_siswa_tes_mulai', ['mw_siswa']);
route('GET', '/ujian/{token}', 'h_ujian');
route('POST', '/ujian/{token}/simpan', 'h_ujian_simpan');
route('POST', '/ujian/{token}/kumpul', 'h_ujian_kumpul');
route('GET', '/ujian/{token}/hasil', 'h_ujian_hasil');
