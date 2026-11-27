<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__, 2));
define('APP', ROOT . '/app');
define('STORAGE', ROOT . '/storage');
date_default_timezone_set('Asia/Jakarta');

/* ---------- konfigurasi ---------- */
function cfg(?string $k = null, $d = null)
{
    static $c = null;
    if ($c === null) {
        $f = APP . '/config.php';
        $c = is_file($f) ? (require $f) : [];
    }
    return $k === null ? $c : ($c[$k] ?? $d);
}

/* ---------- alamat ---------- */
function base(): string
{
    static $b = null;
    if ($b === null) {
        $d = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $b = ($d === '/' || $d === '.' || $d === '') ? '' : rtrim($d, '/');
    }
    return $b;
}
function url(string $p = ''): string { return base() . '/' . ltrim($p, '/'); }
function asset(string $p): string
{
    $f = ROOT . '/assets/' . $p;
    return url('assets/' . $p) . '?v=' . (is_file($f) ? (string)filemtime($f) : '1');
}
function redirect(string $p): never
{
    header('Location: ' . (preg_match('#^https?://#', $p) ? $p : url($p)));
    exit;
}

/* ---------- basis data ---------- */
function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;
    if (cfg('db_driver') === 'sqlite') {
        $pdo = new PDO('sqlite:' . cfg('db_file'));
        $pdo->exec('PRAGMA foreign_keys=ON');
    } else {
        $pdo = new PDO(
            'mysql:host=' . cfg('db_host', 'localhost') . ';dbname=' . cfg('db_name') . ';charset=utf8mb4',
            (string)cfg('db_user'),
            (string)cfg('db_pass')
        );
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}
function is_mysql(): bool { return cfg('db_driver') !== 'sqlite'; }
function q(string $sql, array $p = []): PDOStatement { $s = db()->prepare($sql); $s->execute($p); return $s; }
function all(string $sql, array $p = []): array { return q($sql, $p)->fetchAll(); }
function one(string $sql, array $p = []): ?array { $r = q($sql, $p)->fetch(); return $r === false ? null : $r; }
function val(string $sql, array $p = []) { $r = q($sql, $p)->fetch(PDO::FETCH_NUM); return $r ? $r[0] : null; }
function ins(string $sql, array $p = []): int { q($sql, $p); return (int)db()->lastInsertId(); }
function now(): string { return date('Y-m-d H:i:s'); }

/* ---------- pengaturan ---------- */
function setting(string $k, string $d = ''): string
{
    static $all = null;
    if ($all === null) {
        $all = [];
        try { foreach (all('SELECT kunci, nilai FROM pengaturan') as $r) $all[$r['kunci']] = (string)$r['nilai']; } catch (Throwable $e) {}
    }
    return $all[$k] ?? $d;
}
function set_setting(string $k, string $v): void
{
    if (val('SELECT COUNT(*) FROM pengaturan WHERE kunci=?', [$k])) q('UPDATE pengaturan SET nilai=? WHERE kunci=?', [$v, $k]);
    else q('INSERT INTO pengaturan (kunci, nilai) VALUES (?,?)', [$k, $v]);
}

/* ---------- pembantu tampilan ---------- */
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function rupiah($n): string { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function bulan(int $m): string { return ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][$m - 1]; }
function tgl(?string $d, bool $jam = false): string
{
    if (!$d) return '-';
    $t = strtotime($d);
    if (!$t) return '-';
    return (int)date('j', $t) . ' ' . bulan((int)date('n', $t)) . ' ' . date('Y', $t) . ($jam ? ', ' . date('H.i', $t) : '');
}
function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-') ?: 'paket';
}
function normalize_hp(string $s): string
{
    $d = preg_replace('/\D+/', '', $s) ?? '';
    if ($d === '') return '';
    if (str_starts_with($d, '62')) return $d;
    if (str_starts_with($d, '0')) return '62' . substr($d, 1);
    if (str_starts_with($d, '8')) return '62' . $d;
    return $d;
}
function valid_hp(string $hp): bool { return (bool)preg_match('/^62\d{8,13}$/', $hp); }
function wa_link(string $hp, string $text = ''): string
{
    return 'https://wa.me/' . $hp . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}
function post(string $k, string $d = ''): string { $v = $_POST[$k] ?? $d; return is_string($v) ? trim($v) : $d; }
function qs(string $k, string $d = ''): string { $v = $_GET[$k] ?? $d; return is_string($v) ? trim($v) : $d; }

/* ---------- sesi, flash, CSRF ---------- */
function boot(): void
{
    ini_set('session.use_strict_mode', '1');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('mlid');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
function flash(string $type, string $msg): void { $_SESSION['flash'][] = [$type, $msg]; }
function flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function csrf(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . h(csrf()) . '">'; }
function csrf_ok(): bool
{
    $t = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF'] ?? '');
    return is_string($t) && hash_equals(csrf(), $t);
}

/* ---------- router ---------- */
function route(string $method, string $pattern, string $handler, array $mw = []): void
{
    $GLOBALS['ROUTES'][] = [$method, $pattern, $handler, $mw];
}
function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $b = base();
    if ($b !== '' && str_starts_with($path, $b)) $path = substr($path, strlen($b));
    if (str_starts_with($path, '/index.php')) $path = substr($path, 10);
    return '/' . trim($path, '/');
}
function dispatch(): void
{
    $path = current_path();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    foreach ($GLOBALS['ROUTES'] ?? [] as [$m, $p, $handler, $mw]) {
        if ($m !== $method) continue;
        $re = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $p) . '$#';
        if (!preg_match($re, $path, $mt)) continue;
        $params = array_filter($mt, 'is_string', ARRAY_FILTER_USE_KEY);
        if ($method === 'POST' && !csrf_ok()) {
            http_response_code(419);
            view('pesan', ['judul' => 'Sesi habis', 'isi' => 'Halaman ini sudah kedaluwarsa. Muat ulang halaman sebelumnya lalu coba lagi.'], 'public');
            return;
        }
        foreach ($mw as $f) $f();
        $handler($params);
        return;
    }
    http_response_code(404);
    view('pesan', ['judul' => 'Halaman tidak ditemukan', 'isi' => 'Alamat yang kamu buka tidak ada atau sudah dipindah.'], 'public');
}
function view(string $name, array $d = [], string $layout = 'public'): void
{
    extract($d, EXTR_SKIP);
    ob_start();
    require APP . '/views/' . $name . '.php';
    $content = ob_get_clean();
    require APP . '/views/layout_' . $layout . '.php';
}
function json_out(array $d, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($d, JSON_UNESCAPED_UNICODE);
    exit;
}

/* ---------- autentikasi ---------- */
function me(): ?array
{
    static $u = false;
    if ($u === false) {
        $u = isset($_SESSION['uid']) ? one('SELECT * FROM users WHERE id=? AND aktif=1', [$_SESSION['uid']]) : null;
        if (!$u) unset($_SESSION['uid']);
    }
    return $u;
}
function home_for(array $u): string { return $u['peran'] === 'admin' ? 'admin' : 'belajar'; }
function mw_login(): void
{
    $u = me();
    if (!$u) {
        $_SESSION['next'] = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' ? current_path() : null;
        redirect('masuk');
    }
    if ((int)$u['harus_ganti'] === 1 && current_path() !== '/akun') {
        flash('info', 'Ganti password awalmu dulu sebelum lanjut.');
        redirect('akun');
    }
}
function mw_siswa(): void
{
    mw_login();
    if (me()['peran'] !== 'siswa') redirect(home_for(me()));
}
function mw_admin(): void
{
    mw_login();
    if (me()['peran'] !== 'admin') redirect(home_for(me()));
}
function audit(string $aksi, string $objek = ''): void
{
    q('INSERT INTO audit (user_id, aksi, objek, waktu) VALUES (?,?,?,?)', [me()['id'] ?? null, $aksi, mb_substr($objek, 0, 120), now()]);
}
function temp_password(int $n = 8): string
{
    $a = 'abcdefghjkmnpqrstuvwxyz23456789';
    $s = '';
    for ($i = 0; $i < $n; $i++) $s .= $a[random_int(0, strlen($a) - 1)];
    return $s;
}
function client_ip(): string { return substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45); }

/* ---------- berkas unggahan ---------- */
function save_upload(array $f, string $dir, array $mimes, int $max): ?string
{
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
    if (!is_uploaded_file($f['tmp_name']) || $f['size'] > $max || $f['size'] <= 0) return null;
    $fi = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$fi->file($f['tmp_name']);
    if (!isset($mimes[$mime])) return null;
    $dirPath = STORAGE . '/' . $dir;
    if (!is_dir($dirPath)) mkdir($dirPath, 0755, true);
    $name = bin2hex(random_bytes(12)) . '.' . $mimes[$mime];
    if (!move_uploaded_file($f['tmp_name'], $dirPath . '/' . $name)) return null;
    return $dir . '/' . $name;
}
function send_file(string $rel, string $downloadName = ''): never
{
    $path = realpath(STORAGE . '/' . $rel);
    $root = realpath(STORAGE);
    if (!$path || !$root || !str_starts_with($path, $root . DIRECTORY_SEPARATOR) || !is_file($path)) {
        http_response_code(404);
        exit('Berkas tidak ditemukan.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($path));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, max-age=0');
    if ($downloadName !== '') header('Content-Disposition: inline; filename="' . preg_replace('/[^A-Za-z0-9._-]/', '_', $downloadName) . '"');
    readfile($path);
    exit;
}

/* ---------- komponen kecil ---------- */
function flash_html(array $f): string
{
    $o = '';
    foreach ($f as [$t, $m]) $o .= '<div class="flash ' . h($t) . '" role="' . ($t === 'error' ? 'alert' : 'status') . '">' . h($m) . '</div>';
    return $o;
}
function icon(string $n): string
{
    static $p = [
        'home' => '<path d="M4 11l8-7 8 7"/><path d="M6 10v10h12V10"/>',
        'book' => '<path d="M5 4h11a3 3 0 013 3v13H8a3 3 0 01-3-3V4z"/><path d="M5 17a3 3 0 013-3h11"/>',
        'cal' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M9 3v4M15 3v4"/>',
        'test' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4v2h6V4M9 12l2 2 4-4"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-3.5 3-5.5 7-5.5s7 2 7 5.5M16 4.5a3.5 3.5 0 010 7M18 14.5c2.5.5 4 2 4 5"/>',
        'wallet' => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18M16 15h2"/>',
        'box' => '<path d="M4 8l8-4 8 4v8l-8 4-8-4V8z"/><path d="M4 8l8 4 8-4M12 12v8"/>',
        'list' => '<path d="M9 6h11M9 12h11M9 18h11M4 6h.01M4 12h.01M4 18h.01"/>',
        'gear' => '<circle cx="12" cy="12" r="3"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/>',
        'out' => '<path d="M14 4h5v16h-5M10 8l-4 4 4 4M6 12h10"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'check' => '<path d="M5 12l4 4 10-10"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'file' => '<path d="M6 3h8l4 4v14H6V3z"/><path d="M14 3v4h4"/>',
        'link' => '<path d="M10 14a4 4 0 005.7 0l3-3a4 4 0 00-5.7-5.7l-1 1M14 10a4 4 0 00-5.7 0l-3 3a4 4 0 005.7 5.7l1-1"/>',
        'lock' => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/>',
        'chev' => '<path d="M15 18l-6-6 6-6"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'chat' => '<path d="M21 12a8 8 0 01-11.5 7.2L4 20l1-4.5A8 8 0 1121 12z"/>',
        'grid' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'star' => '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z"/>',
        'shield' => '<path d="M12 3l8 3v6c0 4.5-3.2 8-8 9-4.8-1-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
    ];
    return '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($p[$n] ?? '') . '</svg>';
}
function brand_mark(): string
{
    return '<svg class="mark" viewBox="0 0 28 28" fill="none" aria-hidden="true"><rect x="1" y="1" width="26" height="26" rx="6" fill="#0f172a"/><path d="M7 21V7M7 21h14" stroke="#64748b" stroke-width="1.4" stroke-linecap="round"/><path d="M8 17c3.5 0 3.5-9 7-9s3 8 6 8" stroke="#fb7185" stroke-width="2" stroke-linecap="round"/></svg>';
}
function status_label(string $s): array
{
    return [
        'menunggu_bayar' => ['Belum bayar', 'abu'],
        'menunggu_konfirmasi' => ['Menunggu konfirmasi', 'kuning'],
        'lunas' => ['Lunas', 'hijau'],
        'ditolak' => ['Bukti ditolak', 'merah'],
    ][$s] ?? [$s, 'abu'];
}
function badge(string $s): string { [$t, $c] = status_label($s); return '<span class="badge ' . $c . '">' . h($t) . '</span>'; }
