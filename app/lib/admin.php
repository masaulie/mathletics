<?php
declare(strict_types=1);

function abs_url(string $p = ''): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url($p);
}
function not_found_admin(): void
{
    http_response_code(404);
    view('pesan', ['judul' => 'Data tidak ditemukan', 'isi' => 'Data yang kamu cari tidak ada atau sudah dihapus.'], 'admin');
}

/* ---------- ringkasan ---------- */
function h_admin(): void
{
    $tiga = date('Y-m-d H:i:s', time() - 3 * 86400);
    $s = [
        'menunggu' => (int)val("SELECT COUNT(*) FROM pendaftaran WHERE status='menunggu_konfirmasi'"),
        'belum_bayar' => (int)val("SELECT COUNT(*) FROM pendaftaran WHERE status='menunggu_bayar'"),
        'siswa' => (int)val("SELECT COUNT(*) FROM users WHERE peran='siswa' AND aktif=1"),
        'diag7' => (int)val('SELECT COUNT(*) FROM percobaan pc JOIN tes t ON t.id=pc.tes_id WHERE t.publik=1 AND pc.selesai IS NOT NULL AND pc.selesai > ?', [date('Y-m-d H:i:s', time() - 7 * 86400)]),
        'tunggu' => (int)val('SELECT COUNT(*) FROM tunggu'),
        'omzet' => (int)val("SELECT COALESCE(SUM(p.harga),0) FROM pendaftaran d JOIN paket p ON p.id=d.paket_id WHERE d.status='lunas'"),
    ];
    $pasif = all("SELECT id, nama, hp, terakhir_masuk FROM users WHERE peran='siswa' AND aktif=1 AND (terakhir_masuk IS NULL OR terakhir_masuk < ?) ORDER BY terakhir_masuk LIMIT 8", [$tiga]);
    $baru = all("SELECT d.*, p.nama AS paket_nama FROM pendaftaran d JOIN paket p ON p.id=d.paket_id WHERE d.status='menunggu_konfirmasi' ORDER BY d.diperbarui LIMIT 8");
    $paket = all("SELECT * FROM paket WHERE status='aktif' AND kuota IS NOT NULL");
    foreach ($paket as &$p) $p['terpakai'] = kuota_terpakai((int)$p['id']);
    unset($p);
    view('admin_ringkasan', compact('s', 'pasif', 'baru', 'paket'), 'admin');
}

/* ---------- pendaftaran dan pembayaran ---------- */
function h_admin_pendaftaran(): void
{
    $st = qs('status', 'menunggu_konfirmasi');
    $valid = ['menunggu_konfirmasi', 'menunggu_bayar', 'lunas', 'ditolak', 'semua'];
    if (!in_array($st, $valid, true)) $st = 'menunggu_konfirmasi';
    $rows = $st === 'semua'
        ? all('SELECT d.*, p.nama AS paket_nama FROM pendaftaran d JOIN paket p ON p.id=d.paket_id ORDER BY d.id DESC LIMIT 200')
        : all('SELECT d.*, p.nama AS paket_nama FROM pendaftaran d JOIN paket p ON p.id=d.paket_id WHERE d.status=? ORDER BY d.id DESC LIMIT 200', [$st]);
    view('admin_pendaftaran', ['rows' => $rows, 'st' => $st], 'admin');
}

function h_admin_pendaftaran_detail(array $a): void
{
    $r = one('SELECT d.*, p.nama AS paket_nama, p.harga FROM pendaftaran d JOIN paket p ON p.id=d.paket_id WHERE d.id=?', [(int)$a['id']]);
    if (!$r) { not_found_admin(); return; }
    $kred = $_SESSION['kred'] ?? null;
    unset($_SESSION['kred']);
    view('admin_pendaftaran_detail', ['r' => $r, 'kred' => $kred, 'err' => flashes()], 'admin');
}

function h_admin_bukti(array $a): void
{
    $r = one('SELECT bukti FROM pendaftaran WHERE id=?', [(int)$a['id']]);
    if (!$r || !$r['bukti']) { http_response_code(404); exit('Tidak ada bukti.'); }
    send_file($r['bukti']);
}

function buat_kredensial(string $nama, string $hp, string $pw): array
{
    $teks = "Halo, akun belajar {$nama} di Mathletics sudah aktif.\nMasuk di " . abs_url('masuk') . "\nNomor HP: {$hp}\nPassword awal: {$pw}\n(Kamu akan diminta menggantinya saat masuk pertama.)";
    return ['nama' => $nama, 'hp' => $hp, 'pw' => $pw, 'wa' => wa_link($hp, $teks)];
}

function h_admin_konfirmasi(array $a): void
{
    $r = one('SELECT * FROM pendaftaran WHERE id=?', [(int)$a['id']]);
    if (!$r || !in_array($r['status'], ['menunggu_konfirmasi', 'menunggu_bayar', 'ditolak'], true)) redirect('admin/pendaftaran');
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $baru = false;
        $pw = '';
        $user = one('SELECT * FROM users WHERE hp=?', [$r['hp']]);
        if ($user && $user['peran'] !== 'siswa') throw new RuntimeException('Nomor ini dipakai akun non-siswa.');
        if (!$user) {
            $pw = temp_password();
            $uid = ins('INSERT INTO users (peran, nama, hp, email, sandi, harus_ganti, aktif, dibuat) VALUES (?,?,?,?,?,1,1,?)',
                ['siswa', $r['nama_anak'], $r['hp'], null, password_hash($pw, PASSWORD_DEFAULT), now()]);
            $baru = true;
        } else {
            $uid = (int)$user['id'];
            q('UPDATE users SET aktif=1 WHERE id=?', [$uid]);
        }
        if (!val('SELECT COUNT(*) FROM akses WHERE user_id=? AND paket_id=?', [$uid, $r['paket_id']])) {
            ins('INSERT INTO akses (user_id, paket_id, dibuat) VALUES (?,?,?)', [$uid, $r['paket_id'], now()]);
        }
        q("UPDATE pendaftaran SET status='lunas', user_id=?, catatan=NULL, diperbarui=? WHERE id=?", [$uid, now(), $r['id']]);
        audit('konfirmasi_bayar', '#' . $r['id'] . ' ' . $r['nama_anak']);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash('error', 'Gagal: ' . $e->getMessage());
        redirect('admin/pendaftaran/' . $r['id']);
    }
    if ($baru) $_SESSION['kred'] = buat_kredensial($r['nama_anak'], $r['hp'], $pw);
    else flash('ok', 'Pembayaran dikonfirmasi. Nomor ini sudah punya akun, akses paket ditambahkan ke akun yang sama.');
    redirect('admin/pendaftaran/' . $r['id']);
}

function h_admin_tolak(array $a): void
{
    $r = one('SELECT * FROM pendaftaran WHERE id=?', [(int)$a['id']]);
    if (!$r) redirect('admin/pendaftaran');
    $cat = mb_substr(post('catatan', 'Bukti pembayaran belum sesuai.'), 0, 255);
    q("UPDATE pendaftaran SET status='ditolak', catatan=?, diperbarui=? WHERE id=?", [$cat, now(), $r['id']]);
    audit('tolak_bayar', '#' . $r['id']);
    flash('ok', 'Pendaftaran ditandai ditolak. Orang tua bisa mengunggah ulang bukti lewat tautan pendaftarannya.');
    redirect('admin/pendaftaran/' . $r['id']);
}

/* ---------- siswa ---------- */
function h_admin_siswa(): void
{
    $cari = qs('q');
    $rows = $cari !== ''
        ? all("SELECT * FROM users WHERE peran='siswa' AND (nama LIKE ? OR hp LIKE ?) ORDER BY id DESC LIMIT 200", ['%' . $cari . '%', '%' . $cari . '%'])
        : all("SELECT * FROM users WHERE peran='siswa' ORDER BY id DESC LIMIT 200");
    view('admin_siswa', ['rows' => $rows, 'cari' => $cari], 'admin');
}

function h_admin_siswa_detail(array $a): void
{
    $u = one("SELECT * FROM users WHERE id=? AND peran='siswa'", [(int)$a['id']]);
    if (!$u) { not_found_admin(); return; }
    $kred = $_SESSION['kred'] ?? null;
    unset($_SESSION['kred']);
    view('admin_siswa_detail', [
        'u' => $u, 'kred' => $kred, 'err' => flashes(),
        'paket' => all('SELECT p.nama FROM akses a JOIN paket p ON p.id=a.paket_id WHERE a.user_id=?', [$u['id']]),
        'percobaan' => all('SELECT pc.*, t.judul FROM percobaan pc JOIN tes t ON t.id=pc.tes_id WHERE pc.user_id=? ORDER BY pc.id DESC LIMIT 20', [$u['id']]),
    ], 'admin');
}

function h_admin_siswa_reset(array $a): void
{
    $u = one("SELECT * FROM users WHERE id=? AND peran='siswa'", [(int)$a['id']]);
    if (!$u) redirect('admin/siswa');
    $pw = temp_password();
    q('UPDATE users SET sandi=?, harus_ganti=1 WHERE id=?', [password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
    audit('reset_password', $u['nama']);
    $_SESSION['kred'] = buat_kredensial($u['nama'], (string)$u['hp'], $pw);
    redirect('admin/siswa/' . $u['id']);
}

function h_admin_siswa_aktif(array $a): void
{
    $u = one("SELECT * FROM users WHERE id=? AND peran='siswa'", [(int)$a['id']]);
    if (!$u) redirect('admin/siswa');
    q('UPDATE users SET aktif=? WHERE id=?', [(int)$u['aktif'] === 1 ? 0 : 1, $u['id']]);
    audit('ubah_status_siswa', $u['nama']);
    redirect('admin/siswa/' . $u['id']);
}

/* ---------- paket ---------- */
function h_admin_paket(): void
{
    $rows = all('SELECT p.*, j.nama AS jenjang FROM paket p JOIN jenjang j ON j.id=p.jenjang_id ORDER BY p.id');
    foreach ($rows as &$r) $r['terpakai'] = kuota_terpakai((int)$r['id']);
    unset($r);
    view('admin_paket', ['rows' => $rows], 'admin');
}

function h_admin_paket_form(array $a): void
{
    $p = $a['id'] === 'baru' ? ['id' => 0, 'jenjang_id' => (int)val("SELECT id FROM jenjang WHERE kode='SMP'"), 'slug' => '', 'nama' => '', 'ringkas' => '', 'deskripsi' => '', 'tipe' => 'kelas', 'harga' => 0, 'harga_normal' => '', 'kuota' => '', 'status' => 'draft', 'jadwal' => '']
        : one('SELECT * FROM paket WHERE id=?', [(int)$a['id']]);
    if (!$p) { not_found_admin(); return; }
    view('admin_paket_form', ['p' => $p, 'jenjang' => all('SELECT * FROM jenjang ORDER BY urut'), 'err' => flashes()], 'admin');
}

function h_admin_paket_simpan(array $a): void
{
    $id = $a['id'] === 'baru' ? 0 : (int)$a['id'];
    $nama = mb_substr(post('nama'), 0, 120);
    $slug = slugify(post('slug') ?: $nama);
    $tipe = in_array(post('tipe'), ['kelas', 'tes', 'mandiri'], true) ? post('tipe') : 'kelas';
    $status = post('status') === 'aktif' ? 'aktif' : 'draft';
    $harga = max(0, (int)preg_replace('/\D/', '', post('harga')));
    $hn = post('harga_normal') === '' ? null : max(0, (int)preg_replace('/\D/', '', post('harga_normal')));
    $kuota = post('kuota') === '' ? null : max(0, (int)post('kuota'));
    $jid = (int)post('jenjang_id');
    if ($nama === '' || !val('SELECT COUNT(*) FROM jenjang WHERE id=?', [$jid])) {
        flash('error', 'Nama paket wajib diisi.');
        redirect('admin/paket/' . ($id ?: 'baru'));
    }
    if ($status === 'aktif' && $harga <= 0) {
        flash('error', 'Paket aktif harus punya harga lebih dari 0.');
        redirect('admin/paket/' . ($id ?: 'baru'));
    }
    if (val('SELECT COUNT(*) FROM paket WHERE slug=? AND id<>?', [$slug, $id])) $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 3);
    $f = [$jid, $slug, $nama, mb_substr(post('ringkas'), 0, 255), post('deskripsi'), $tipe, $harga, $hn, $kuota, $status, mb_substr(post('jadwal'), 0, 160)];
    if ($id) {
        q('UPDATE paket SET jenjang_id=?, slug=?, nama=?, ringkas=?, deskripsi=?, tipe=?, harga=?, harga_normal=?, kuota=?, status=?, jadwal=? WHERE id=?', [...$f, $id]);
    } else {
        $id = ins('INSERT INTO paket (jenjang_id, slug, nama, ringkas, deskripsi, tipe, harga, harga_normal, kuota, status, jadwal, dibuat) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', [...$f, now()]);
    }
    audit('simpan_paket', $nama);
    flash('ok', 'Paket disimpan.');
    redirect('admin/paket');
}

/* ---------- pertemuan dan berkas ---------- */
function h_admin_pertemuan(): void
{
    $paketId = (int)qs('paket');
    $pakets = all("SELECT id, nama FROM paket WHERE tipe='kelas' OR id IN (SELECT DISTINCT paket_id FROM pertemuan) ORDER BY id");
    if (!$paketId && $pakets) $paketId = (int)$pakets[0]['id'];
    $rows = $paketId ? all('SELECT pt.*, (SELECT COUNT(*) FROM berkas b WHERE b.pertemuan_id=pt.id) AS jml FROM pertemuan pt WHERE pt.paket_id=? ORDER BY pt.urut', [$paketId]) : [];
    view('admin_pertemuan', ['rows' => $rows, 'pakets' => $pakets, 'paketId' => $paketId, 'err' => flashes()], 'admin');
}

function h_admin_pertemuan_tambah(): void
{
    $pid = (int)post('paket_id');
    if (!val('SELECT COUNT(*) FROM paket WHERE id=?', [$pid])) redirect('admin/pertemuan');
    $urut = (int)val('SELECT COALESCE(MAX(urut),0) FROM pertemuan WHERE paket_id=?', [$pid]) + 1;
    $id = ins('INSERT INTO pertemuan (paket_id, urut, judul, topik, terbuka) VALUES (?,?,?,?,0)', [$pid, $urut, 'Pertemuan ' . $urut, '']);
    redirect('admin/pertemuan/' . $id);
}

function h_admin_pertemuan_form(array $a): void
{
    $pt = one('SELECT * FROM pertemuan WHERE id=?', [(int)$a['id']]);
    if (!$pt) { not_found_admin(); return; }
    view('admin_pertemuan_form', ['pt' => $pt, 'berkas' => all('SELECT * FROM berkas WHERE pertemuan_id=? ORDER BY id', [$pt['id']]), 'err' => flashes()], 'admin');
}

function tautan_valid(string $u): bool { return (bool)preg_match('#^https?://[^\s]+$#i', $u); }

function h_admin_pertemuan_simpan(array $a): void
{
    $pt = one('SELECT * FROM pertemuan WHERE id=?', [(int)$a['id']]);
    if (!$pt) redirect('admin/pertemuan');
    $tgl = post('tanggal');
    $jam = post('jam');
    $tautan = post('tautan');
    if ($tgl !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) { flash('error', 'Format tanggal tidak valid.'); redirect('admin/pertemuan/' . $pt['id']); }
    if ($jam !== '' && !preg_match('/^\d{2}:\d{2}$/', $jam)) { flash('error', 'Format jam tidak valid.'); redirect('admin/pertemuan/' . $pt['id']); }
    if ($tautan !== '' && !tautan_valid($tautan)) { flash('error', 'Tautan kelas harus diawali http:// atau https://'); redirect('admin/pertemuan/' . $pt['id']); }
    q('UPDATE pertemuan SET urut=?, judul=?, topik=?, tanggal=?, jam=?, tautan=?, terbuka=? WHERE id=?',
        [max(1, (int)post('urut', (string)$pt['urut'])), mb_substr(post('judul', $pt['judul']), 0, 160) ?: $pt['judul'], mb_substr(post('topik'), 0, 160), $tgl ?: null, $jam ?: null, $tautan ?: null, post('terbuka') === '1' ? 1 : 0, $pt['id']]);
    audit('simpan_pertemuan', $pt['judul']);
    flash('ok', 'Pertemuan disimpan.');
    redirect('admin/pertemuan/' . $pt['id']);
}

function h_admin_berkas_tambah(array $a): void
{
    $pt = one('SELECT * FROM pertemuan WHERE id=?', [(int)$a['id']]);
    if (!$pt) redirect('admin/pertemuan');
    $jenis = in_array(post('jenis'), ['modul', 'worksheet', 'kunci', 'slide', 'lain'], true) ? post('jenis') : 'lain';
    $judul = mb_substr(post('judul'), 0, 160) ?: ucfirst($jenis);
    $url = post('url');
    $path = null;
    if (!empty($_FILES['berkas']['name'])) {
        $path = save_upload($_FILES['berkas'], 'materi', ['application/pdf' => 'pdf', 'image/png' => 'png', 'image/jpeg' => 'jpg'], 25 * 1024 * 1024);
        if (!$path) { flash('error', 'Unggahan gagal. Pakai PDF, PNG, atau JPG maksimal 25 MB.'); redirect('admin/pertemuan/' . $pt['id']); }
    } elseif ($url === '' || !tautan_valid($url)) {
        flash('error', 'Pilih berkas, atau isi tautan yang diawali http:// atau https://');
        redirect('admin/pertemuan/' . $pt['id']);
    }
    ins('INSERT INTO berkas (pertemuan_id, jenis, judul, path, url, tampil) VALUES (?,?,?,?,?,1)', [$pt['id'], $jenis, $judul, $path, $path ? null : $url]);
    audit('tambah_berkas', $judul);
    flash('ok', 'Berkas ditambahkan.');
    redirect('admin/pertemuan/' . $pt['id']);
}

function h_admin_berkas_tampil(array $a): void
{
    $b = one('SELECT * FROM berkas WHERE id=?', [(int)$a['id']]);
    if (!$b) redirect('admin/pertemuan');
    q('UPDATE berkas SET tampil=? WHERE id=?', [(int)$b['tampil'] === 1 ? 0 : 1, $b['id']]);
    redirect('admin/pertemuan/' . $b['pertemuan_id']);
}

function h_admin_berkas_hapus(array $a): void
{
    $b = one('SELECT * FROM berkas WHERE id=?', [(int)$a['id']]);
    if (!$b) redirect('admin/pertemuan');
    if ($b['path']) {
        $p = realpath(STORAGE . '/' . $b['path']);
        if ($p && str_starts_with($p, (string)realpath(STORAGE) . DIRECTORY_SEPARATOR)) @unlink($p);
    }
    q('DELETE FROM berkas WHERE id=?', [$b['id']]);
    audit('hapus_berkas', $b['judul']);
    redirect('admin/pertemuan/' . $b['pertemuan_id']);
}

/* ---------- bank soal ---------- */
function h_admin_soal(): void
{
    $topik = (int)qs('topik');
    $cari = qs('q');
    $w = ['1=1'];
    $p = [];
    if ($topik) { $w[] = 's.topik_id=?'; $p[] = $topik; }
    if ($cari !== '') { $w[] = 's.teks LIKE ?'; $p[] = '%' . $cari . '%'; }
    $rows = all('SELECT s.*, t.nama AS topik_nama FROM soal s JOIN topik t ON t.id=s.topik_id WHERE ' . implode(' AND ', $w) . ' ORDER BY s.id DESC LIMIT 300', $p);
    view('admin_soal', ['rows' => $rows, 'topiks' => all('SELECT t.*, (SELECT COUNT(*) FROM soal s WHERE s.topik_id=t.id) AS jml FROM topik t ORDER BY t.id'), 'topik' => $topik, 'cari' => $cari, 'err' => flashes(), 'math' => true], 'admin');
}

function h_admin_soal_form(array $a): void
{
    $s = $a['id'] === 'baru' ? ['id' => 0, 'topik_id' => (int)qs('topik'), 'tingkat' => 2, 'teks' => '', 'a' => '', 'b' => '', 'c' => '', 'd' => '', 'kunci' => 'A', 'pembahasan' => '', 'aktif' => 1]
        : one('SELECT * FROM soal WHERE id=?', [(int)$a['id']]);
    if (!$s) { not_found_admin(); return; }
    view('admin_soal_form', ['s' => $s, 'topiks' => all('SELECT * FROM topik ORDER BY id'), 'err' => flashes(), 'math' => true], 'admin');
}

function h_admin_soal_simpan(array $a): void
{
    $id = $a['id'] === 'baru' ? 0 : (int)$a['id'];
    $f = [(int)post('topik_id'), min(3, max(1, (int)post('tingkat', '2'))), post('teks'), post('a'), post('b'), post('c'), post('d'), strtoupper(post('kunci')), post('pembahasan') ?: null, post('aktif') === '1' ? 1 : 0];
    if (!val('SELECT COUNT(*) FROM topik WHERE id=?', [$f[0]]) || $f[2] === '' || $f[3] === '' || $f[4] === '' || $f[5] === '' || $f[6] === '' || !in_array($f[7], ['A', 'B', 'C', 'D'], true)) {
        flash('error', 'Lengkapi topik, soal, keempat pilihan, dan kunci jawaban.');
        redirect('admin/soal/' . ($id ?: 'baru'));
    }
    if ($id) q('UPDATE soal SET topik_id=?, tingkat=?, teks=?, a=?, b=?, c=?, d=?, kunci=?, pembahasan=?, aktif=?, contoh=0 WHERE id=?', [...$f, $id]);
    else ins('INSERT INTO soal (topik_id, tingkat, teks, a, b, c, d, kunci, pembahasan, aktif, contoh) VALUES (?,?,?,?,?,?,?,?,?,?,0)', $f);
    audit('simpan_soal', '#' . $id);
    flash('ok', 'Soal disimpan.');
    redirect('admin/soal?topik=' . $f[0]);
}

function h_admin_soal_hapus(array $a): void
{
    $id = (int)$a['id'];
    if (val('SELECT COUNT(*) FROM jawaban WHERE soal_id=?', [$id])) {
        q('UPDATE soal SET aktif=0 WHERE id=?', [$id]);
        flash('ok', 'Soal sudah pernah dijawab siswa, jadi hanya dinonaktifkan agar riwayat tidak rusak.');
    } else {
        q('DELETE FROM tes_soal WHERE soal_id=?', [$id]);
        q('DELETE FROM soal WHERE id=?', [$id]);
        flash('ok', 'Soal dihapus.');
    }
    audit('hapus_soal', '#' . $id);
    redirect('admin/soal');
}

function h_admin_topik_tambah(): void
{
    $n = mb_substr(post('nama'), 0, 80);
    if ($n !== '') ins('INSERT INTO topik (jenjang_id, nama) VALUES (?,?)', [(int)val("SELECT id FROM jenjang WHERE kode='SMP'"), $n]);
    redirect('admin/soal');
}

function h_admin_soal_templat(): void
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="templat-soal.csv"');
    echo "\xEF\xBB\xBF";
    $o = fopen('php://output', 'w');
    fputcsv($o, ['topik', 'tingkat', 'soal', 'a', 'b', 'c', 'd', 'kunci', 'pembahasan'], ',', '"', '');
    fputcsv($o, ['Aljabar', '2', 'Jika $2x+3=11$, nilai $x$ adalah ...', '2', '3', '4', '5', 'C', '$2x=8$, jadi $x=4$.'], ',', '"', '');
    fclose($o);
    exit;
}

function h_admin_soal_impor(): void
{
    $f = $_FILES['csv'] ?? null;
    if (!$f || $f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024 || !is_uploaded_file($f['tmp_name'])) {
        flash('error', 'Pilih berkas CSV (maksimal 2 MB).');
        redirect('admin/soal');
    }
    $isi = file_get_contents($f['tmp_name']);
    $isi = preg_replace('/^\xEF\xBB\xBF/', '', (string)$isi);
    $baris = preg_split('/\r\n|\n|\r/', (string)$isi);
    $delim = substr_count((string)($baris[0] ?? ''), ';') > substr_count((string)($baris[0] ?? ''), ',') ? ';' : ',';
    $h = fopen('php://temp', 'r+');
    fwrite($h, $isi);
    rewind($h);
    $head = fgetcsv($h, 0, $delim, '"', '');
    $ok = 0;
    $gagal = [];
    $n = 1;
    $smp = (int)val("SELECT id FROM jenjang WHERE kode='SMP'");
    while (($r = fgetcsv($h, 0, $delim, '"', '')) !== false) {
        $n++;
        if (count($r) === 1 && trim((string)$r[0]) === '') continue;
        $r = array_pad(array_map('trim', $r), 9, '');
        [$tp, $tg, $teks, $oa, $ob, $oc, $od, $k, $pb] = $r;
        $k = strtoupper($k);
        if ($tp === '' || $teks === '' || $oa === '' || $ob === '' || $oc === '' || $od === '' || !in_array($k, ['A', 'B', 'C', 'D'], true)) { $gagal[] = $n; continue; }
        $tid = (int)val('SELECT id FROM topik WHERE LOWER(nama)=LOWER(?)', [$tp]);
        if (!$tid) $tid = ins('INSERT INTO topik (jenjang_id, nama) VALUES (?,?)', [$smp, mb_substr($tp, 0, 80)]);
        ins('INSERT INTO soal (topik_id, tingkat, teks, a, b, c, d, kunci, pembahasan, aktif, contoh) VALUES (?,?,?,?,?,?,?,?,?,1,0)', [$tid, min(3, max(1, (int)$tg ?: 2)), $teks, $oa, $ob, $oc, $od, $k, $pb ?: null]);
        $ok++;
    }
    fclose($h);
    audit('impor_soal', $ok . ' soal');
    flash($gagal ? 'info' : 'ok', $ok . ' soal diimpor.' . ($gagal ? ' Baris dilewati karena tidak lengkap: ' . implode(', ', array_slice($gagal, 0, 20)) . '.' : ''));
    redirect('admin/soal');
}

/* ---------- tes ---------- */
function h_admin_tes(): void
{
    $rows = all("SELECT t.*, p.nama AS paket_nama, (SELECT COUNT(*) FROM tes_soal ts WHERE ts.tes_id=t.id) AS jumlah, (SELECT COUNT(*) FROM percobaan pc WHERE pc.tes_id=t.id AND pc.selesai IS NOT NULL) AS selesai FROM tes t LEFT JOIN paket p ON p.id=t.paket_id ORDER BY t.id DESC");
    view('admin_tes', ['rows' => $rows], 'admin');
}

function h_admin_tes_form(array $a): void
{
    $t = $a['id'] === 'baru' ? ['id' => 0, 'paket_id' => null, 'judul' => '', 'tipe' => 'tryout', 'durasi' => 60, 'publik' => 0, 'status' => 'draft']
        : one('SELECT * FROM tes WHERE id=?', [(int)$a['id']]);
    if (!$t) { not_found_admin(); return; }
    $dipilih = array_map('intval', array_column(all('SELECT soal_id FROM tes_soal WHERE tes_id=?', [(int)$t['id']]), 'soal_id'));
    $soal = all('SELECT s.id, s.teks, s.tingkat, s.contoh, t.nama AS topik_nama FROM soal s JOIN topik t ON t.id=s.topik_id WHERE s.aktif=1 ORDER BY t.id, s.id');
    view('admin_tes_form', ['t' => $t, 'dipilih' => $dipilih, 'soal' => $soal, 'pakets' => all('SELECT id, nama FROM paket ORDER BY id'), 'err' => flashes(), 'math' => true], 'admin');
}

function h_admin_tes_simpan(array $a): void
{
    $id = $a['id'] === 'baru' ? 0 : (int)$a['id'];
    $judul = mb_substr(post('judul'), 0, 160);
    $tipe = post('tipe') === 'diagnostik' ? 'diagnostik' : 'tryout';
    $durasi = min(240, max(5, (int)post('durasi', '60')));
    $publik = ($tipe === 'diagnostik' && post('publik') === '1') ? 1 : 0;
    $status = post('status') === 'aktif' ? 'aktif' : 'draft';
    $paket = (int)post('paket_id') ?: null;
    $pilih = array_values(array_unique(array_map('intval', (array)($_POST['soal'] ?? []))));
    if ($judul === '') { flash('error', 'Judul tes wajib diisi.'); redirect('admin/tes/' . ($id ?: 'baru')); }
    if ($status === 'aktif' && !$pilih) { flash('error', 'Pilih minimal satu soal sebelum mengaktifkan tes.'); redirect('admin/tes/' . ($id ?: 'baru')); }
    $pdo = db();
    $pdo->beginTransaction();
    try {
        if ($publik) q('UPDATE tes SET publik=0 WHERE tipe=? AND id<>?', ['diagnostik', $id]);
        if ($id) q('UPDATE tes SET paket_id=?, judul=?, tipe=?, durasi=?, publik=?, status=? WHERE id=?', [$paket, $judul, $tipe, $durasi, $publik, $status, $id]);
        else $id = ins('INSERT INTO tes (paket_id, judul, tipe, durasi, publik, status, dibuat) VALUES (?,?,?,?,?,?,?)', [$paket, $judul, $tipe, $durasi, $publik, $status, now()]);
        q('DELETE FROM tes_soal WHERE tes_id=?', [$id]);
        // urutan mengikuti urutan topik lalu id, agar konsisten dengan daftar yang dilihat admin
        $urut = 0;
        foreach (all('SELECT s.id FROM soal s WHERE s.aktif=1 ORDER BY s.topik_id, s.id') as $s) {
            if (in_array((int)$s['id'], $pilih, true)) ins('INSERT INTO tes_soal (tes_id, soal_id, urut) VALUES (?,?,?)', [$id, $s['id'], ++$urut]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    audit('simpan_tes', $judul);
    flash('ok', 'Tes disimpan.');
    redirect('admin/tes');
}

function h_admin_tes_hasil(array $a): void
{
    $t = one('SELECT * FROM tes WHERE id=?', [(int)$a['id']]);
    if (!$t) { not_found_admin(); return; }
    view('admin_tes_hasil', ['t' => $t, 'rows' => all('SELECT * FROM percobaan WHERE tes_id=? ORDER BY id DESC LIMIT 300', [$t['id']])], 'admin');
}

/* ---------- daftar tunggu dan pengaturan ---------- */
function h_admin_tunggu(): void
{
    view('admin_tunggu', ['rows' => all('SELECT * FROM tunggu ORDER BY id DESC LIMIT 500')], 'admin');
}

function h_admin_pengaturan(): void
{
    view('admin_pengaturan', ['err' => flashes()], 'admin');
}

function h_admin_pengaturan_simpan(): void
{
    $wa = normalize_hp(post('wa'));
    if ($wa !== '' && !valid_hp($wa)) {
        flash('error', 'Nomor WhatsApp tidak valid. Contoh: 6281234567890.');
        redirect('admin/pengaturan');
    }
    set_setting('wa', $wa);
    set_setting('nama_usaha', mb_substr(post('nama_usaha', 'Mathletics'), 0, 60) ?: 'Mathletics');
    set_setting('instruksi_bayar', post('instruksi_bayar'));
    if (!empty($_FILES['qris']['name'])) {
        $p = save_upload($_FILES['qris'], 'uploads', ['image/png' => 'png', 'image/jpeg' => 'jpg'], 3 * 1024 * 1024);
        if ($p) set_setting('qris', $p);
        else flash('error', 'Gambar QRIS gagal diunggah (PNG atau JPG, maksimal 3 MB).');
    }
    audit('simpan_pengaturan');
    flash('ok', 'Pengaturan disimpan.');
    redirect('admin/pengaturan');
}

$A = ['mw_admin'];
route('GET', '/admin', 'h_admin', $A);
route('GET', '/admin/pendaftaran', 'h_admin_pendaftaran', $A);
route('GET', '/admin/pendaftaran/{id}', 'h_admin_pendaftaran_detail', $A);
route('POST', '/admin/pendaftaran/{id}/konfirmasi', 'h_admin_konfirmasi', $A);
route('POST', '/admin/pendaftaran/{id}/tolak', 'h_admin_tolak', $A);
route('GET', '/admin/bukti/{id}', 'h_admin_bukti', $A);
route('GET', '/admin/siswa', 'h_admin_siswa', $A);
route('GET', '/admin/siswa/{id}', 'h_admin_siswa_detail', $A);
route('POST', '/admin/siswa/{id}/reset', 'h_admin_siswa_reset', $A);
route('POST', '/admin/siswa/{id}/aktif', 'h_admin_siswa_aktif', $A);
route('GET', '/admin/paket', 'h_admin_paket', $A);
route('GET', '/admin/paket/{id}', 'h_admin_paket_form', $A);
route('POST', '/admin/paket/{id}', 'h_admin_paket_simpan', $A);
route('GET', '/admin/pertemuan', 'h_admin_pertemuan', $A);
route('POST', '/admin/pertemuan/tambah', 'h_admin_pertemuan_tambah', $A);
route('GET', '/admin/pertemuan/{id}', 'h_admin_pertemuan_form', $A);
route('POST', '/admin/pertemuan/{id}', 'h_admin_pertemuan_simpan', $A);
route('POST', '/admin/pertemuan/{id}/berkas', 'h_admin_berkas_tambah', $A);
route('POST', '/admin/berkas/{id}/tampil', 'h_admin_berkas_tampil', $A);
route('POST', '/admin/berkas/{id}/hapus', 'h_admin_berkas_hapus', $A);
route('GET', '/admin/soal', 'h_admin_soal', $A);
route('GET', '/admin/soal/templat.csv', 'h_admin_soal_templat', $A);
route('POST', '/admin/soal/impor', 'h_admin_soal_impor', $A);
route('POST', '/admin/topik', 'h_admin_topik_tambah', $A);
route('GET', '/admin/soal/{id}', 'h_admin_soal_form', $A);
route('POST', '/admin/soal/{id}', 'h_admin_soal_simpan', $A);
route('POST', '/admin/soal/{id}/hapus', 'h_admin_soal_hapus', $A);
route('GET', '/admin/tes', 'h_admin_tes', $A);
route('GET', '/admin/tes/{id}', 'h_admin_tes_form', $A);
route('POST', '/admin/tes/{id}', 'h_admin_tes_simpan', $A);
route('GET', '/admin/tes/{id}/hasil', 'h_admin_tes_hasil', $A);
route('GET', '/admin/tunggu', 'h_admin_tunggu', $A);
route('GET', '/admin/pengaturan', 'h_admin_pengaturan', $A);
route('POST', '/admin/pengaturan', 'h_admin_pengaturan_simpan', $A);
