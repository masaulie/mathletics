<?php
declare(strict_types=1);
/**
 * Installer sekali pakai. Setelah berhasil, HAPUS file ini dari hosting.
 */
require __DIR__ . '/app/lib/core.php';
require __DIR__ . '/app/lib/schema.php';

if (is_file(APP . '/config.php')) {
    http_response_code(403);
    exit('<!doctype html><meta charset="utf-8"><title>Sudah terpasang</title><body style="font-family:sans-serif;max-width:480px;margin:10vh auto;padding:0 20px"><h1>Sudah terpasang</h1><p>Platform ini sudah dipasang. Hapus file <code>install.php</code> dari hosting.</p>');
}

session_start();
if (empty($_SESSION['ic'])) $_SESSION['ic'] = bin2hex(random_bytes(16));

$err = '';
$done = false;
$v = ['driver' => 'mysql', 'host' => 'localhost', 'name' => '', 'user' => '', 'nama' => '', 'email' => '', 'hp' => '', 'wa' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim((string)($_POST[$k] ?? $v[$k]));
    $dbpass = (string)($_POST['dbpass'] ?? '');
    $pw = (string)($_POST['sandi'] ?? '');
    try {
        if (!hash_equals($_SESSION['ic'], (string)($_POST['ic'] ?? ''))) throw new RuntimeException('Sesi installer tidak valid, muat ulang halaman.');
        if ($v['nama'] === '' || $v['email'] === '' || !filter_var($v['email'], FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Nama dan email admin wajib diisi dengan benar.');
        if (strlen($pw) < 10) throw new RuntimeException('Password admin minimal 10 karakter.');
        $wa = normalize_hp($v['wa']);
        if ($wa !== '' && !valid_hp($wa)) throw new RuntimeException('Nomor WhatsApp tidak valid. Contoh: 6281234567890.');
        $hpAdmin = $v['hp'] !== '' ? normalize_hp($v['hp']) : null;

        $sqlite = $v['driver'] === 'sqlite';
        $conf = ['secret' => bin2hex(random_bytes(16))];
        if ($sqlite) {
            $conf += ['db_driver' => 'sqlite', 'db_file' => APP . '/data.sqlite'];
        } else {
            if ($v['name'] === '' || $v['user'] === '') throw new RuntimeException('Nama database dan user database wajib diisi.');
            $conf += ['db_driver' => 'mysql', 'db_host' => $v['host'], 'db_name' => $v['name'], 'db_user' => $v['user'], 'db_pass' => $dbpass];
            new PDO('mysql:host=' . $v['host'] . ';dbname=' . $v['name'] . ';charset=utf8mb4', $v['user'], $dbpass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        $file = APP . '/config.php';
        if (file_put_contents($file, "<?php\nreturn " . var_export($conf, true) . ";\n", LOCK_EX) === false) throw new RuntimeException('Tidak bisa menulis app/config.php. Periksa izin folder app.');
        @chmod($file, 0640);

        try {
            $existing = $sqlite ? [] : array_column(db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM), 0);
            if (in_array('users', $existing, true)) throw new RuntimeException('Database tidak kosong (sudah ada tabel users). Pakai database kosong baru.');
            foreach (schema_sql(!$sqlite) as $stmt) db()->exec($stmt);
            ins('INSERT INTO users (peran, nama, hp, email, sandi, harus_ganti, aktif, dibuat) VALUES (?,?,?,?,?,0,1,?)',
                ['admin', $v['nama'], $hpAdmin, $v['email'], password_hash($pw, PASSWORD_DEFAULT), now()]);
            seed_data($wa);
        } catch (Throwable $e) {
            @unlink($file);
            throw $e;
        }
        $done = true;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}
?><!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pasang Mathletics</title>
<style>
body{font-family:system-ui,sans-serif;background:#f1f5f9;color:#0f172a;margin:0;padding:24px 16px}
main{max-width:520px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:24px}
h1{font-size:22px;margin:0 0 4px}p.s{color:#64748b;margin:0 0 20px}
label{display:block;font-weight:600;font-size:14px;margin:14px 0 4px}
input,select{width:100%;box-sizing:border-box;padding:11px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:16px}
button{margin-top:20px;width:100%;padding:13px;background:#e11d48;color:#fff;border:0;border-radius:8px;font-size:16px;font-weight:700}
.e{background:#fff1f2;border:1px solid #fda4af;color:#9f1239;padding:10px 12px;border-radius:8px;margin-bottom:12px}
h2{font-size:15px;margin:24px 0 0;padding-top:16px;border-top:1px solid #e2e8f0}
small{color:#64748b}
</style></head><body><main>
<?php if ($done): ?>
<h1>Terpasang</h1>
<p class="s">Database dan akun admin sudah dibuat.</p>
<p><b>Langkah terakhir yang wajib:</b> hapus file <code>install.php</code> dari hosting, lalu <a href="<?= h(url('masuk')) ?>">masuk sebagai admin</a>.</p>
<p>Setelah masuk: isi rekening atau QRIS di menu Pengaturan, ganti soal contoh dengan bank soal kamu, dan isi judul serta berkas tiap pertemuan.</p>
<?php else: ?>
<h1>Pasang Mathletics</h1>
<p class="s">Isi sekali, lalu hapus file ini.</p>
<?php if ($err): ?><div class="e"><?= h($err) ?></div><?php endif; ?>
<form method="post" autocomplete="off">
<input type="hidden" name="ic" value="<?= h($_SESSION['ic']) ?>">
<label>Jenis database</label>
<select name="driver"><option value="mysql" <?= $v['driver'] === 'mysql' ? 'selected' : '' ?>>MySQL (hosting)</option><option value="sqlite" <?= $v['driver'] === 'sqlite' ? 'selected' : '' ?>>SQLite (hanya untuk uji)</option></select>
<label>Host database</label><input name="host" value="<?= h($v['host']) ?>">
<label>Nama database</label><input name="name" value="<?= h($v['name']) ?>">
<label>User database</label><input name="user" value="<?= h($v['user']) ?>">
<label>Password database</label><input name="dbpass" type="password">
<h2>Akun admin</h2>
<label>Nama</label><input name="nama" value="<?= h($v['nama']) ?>">
<label>Email (dipakai untuk masuk)</label><input name="email" type="email" value="<?= h($v['email']) ?>">
<label>Nomor HP admin <small>(opsional)</small></label><input name="hp" inputmode="tel" value="<?= h($v['hp']) ?>">
<label>Password (min. 10 karakter)</label><input name="sandi" type="password">
<h2>Kontak usaha</h2>
<label>Nomor WhatsApp Business</label><input name="wa" inputmode="tel" placeholder="6281234567890" value="<?= h($v['wa']) ?>">
<button type="submit">Pasang</button>
</form>
<?php endif; ?>
</main></body></html>
