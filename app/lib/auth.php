<?php
declare(strict_types=1);

function h_masuk(): void
{
    if (me()) redirect(home_for(me()));
    view('masuk', ['mode' => qs('sebagai') === 'admin' ? 'admin' : 'siswa', 'ident' => '', 'err' => flashes()], 'bare');
}

function h_masuk_post(): void
{
    $mode = post('mode') === 'admin' ? 'admin' : 'siswa';
    $ident = mb_substr(post('ident'), 0, 160);
    $pw = (string)($_POST['sandi'] ?? '');
    $key = $mode === 'siswa' ? normalize_hp($ident) : strtolower($ident);
    $ip = client_ip();
    $since = date('Y-m-d H:i:s', time() - 900);

    $gagalIdent = (int)val('SELECT COUNT(*) FROM login_gagal WHERE ident=? AND waktu > ?', [$key, $since]);
    $gagalIp = (int)val('SELECT COUNT(*) FROM login_gagal WHERE ip=? AND waktu > ?', [$ip, $since]);
    $kembali = function (string $msg) use ($mode, $ident): void {
        flash('error', $msg);
        view('masuk', ['mode' => $mode, 'ident' => $ident, 'err' => flashes()], 'bare');
    };
    if ($gagalIdent >= 5 || $gagalIp >= 20) {
        http_response_code(429);
        $kembali('Terlalu banyak percobaan. Tunggu 15 menit lalu coba lagi.');
        return;
    }

    $u = $key === '' ? null : one('SELECT * FROM users WHERE (hp=? OR email=?) AND aktif=1', [$key, $key]);
    // verifikasi tetap dijalankan agar waktu respons tidak membocorkan akun mana yang ada
    $hash = $u['sandi'] ?? '$2y$10$abcdefghijklmnopqrstuuYQ0mH8bB0eM7r3yq1Z5sQ6K1m3k9bKe';
    $ok = password_verify($pw, $hash) && $u;

    if (!$ok) {
        q('INSERT INTO login_gagal (ident, ip, waktu) VALUES (?,?,?)', [$key, $ip, now()]);
        $kembali($mode === 'siswa' ? 'Nomor HP atau password salah.' : 'Email atau password salah.');
        return;
    }
    if (($mode === 'admin') !== ($u['peran'] === 'admin')) {
        $kembali($u['peran'] === 'admin' ? 'Akun ini akun admin. Pilih tab Admin.' : 'Akun ini akun siswa. Pilih tab Siswa.');
        return;
    }
    if (password_needs_rehash($u['sandi'], PASSWORD_DEFAULT)) q('UPDATE users SET sandi=? WHERE id=?', [password_hash($pw, PASSWORD_DEFAULT), $u['id']]);
    q('DELETE FROM login_gagal WHERE ident=?', [$key]);
    session_regenerate_id(true);
    $_SESSION['uid'] = (int)$u['id'];
    q('UPDATE users SET terakhir_masuk=? WHERE id=?', [now(), $u['id']]);
    $next = $_SESSION['next'] ?? null;
    unset($_SESSION['next']);
    $dest = ($next && ($u['peran'] === 'admin') === str_starts_with($next, '/admin')) ? ltrim($next, '/') : home_for($u);
    redirect($dest);
}

function h_keluar(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $c = session_get_cookie_params();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $c['path'], 'secure' => $c['secure'], 'httponly' => true, 'samesite' => 'Lax']);
    }
    session_destroy();
    session_start();
    flash('ok', 'Kamu sudah keluar.');
    redirect('masuk');
}

function h_akun(): void
{
    $u = me();
    view('akun', ['u' => $u, 'err' => flashes()], $u['peran'] === 'admin' ? 'admin' : 'siswa');
}

function h_akun_post(): void
{
    $u = me();
    $lama = (string)($_POST['lama'] ?? '');
    $baru = (string)($_POST['baru'] ?? '');
    $ulang = (string)($_POST['ulang'] ?? '');
    if (!password_verify($lama, $u['sandi'])) flash('error', 'Password saat ini salah.');
    elseif (strlen($baru) < 8) flash('error', 'Password baru minimal 8 karakter.');
    elseif ($baru === $lama) flash('error', 'Password baru harus berbeda dari yang lama.');
    elseif ($baru !== $ulang) flash('error', 'Ulangi password baru dengan sama persis.');
    else {
        q('UPDATE users SET sandi=?, harus_ganti=0 WHERE id=?', [password_hash($baru, PASSWORD_DEFAULT), $u['id']]);
        session_regenerate_id(true);
        flash('ok', 'Password diganti.');
        redirect(home_for($u));
    }
    redirect('akun');
}

route('GET', '/masuk', 'h_masuk');
route('POST', '/masuk', 'h_masuk_post');
route('POST', '/keluar', 'h_keluar');
route('GET', '/akun', 'h_akun', ['mw_login']);
route('POST', '/akun', 'h_akun_post', ['mw_login']);
