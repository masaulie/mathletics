<?php
declare(strict_types=1);

require __DIR__ . '/app/lib/core.php';

if (!is_file(APP . '/config.php')) {
    header('Location: install.php');
    exit;
}

set_exception_handler(function (Throwable $e): void {
    error_log('[mathletics] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) http_response_code(500);
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Terjadi masalah</title>'
        . '<body style="font-family:system-ui,sans-serif;max-width:480px;margin:12vh auto;padding:0 20px;color:#0f172a">'
        . '<h1 style="font-size:22px">Terjadi masalah di server</h1><p>Coba muat ulang beberapa saat lagi. Kalau berulang, hubungi admin.</p></body>';
});

boot();
foreach (['halaman', 'auth', 'ujian', 'siswa', 'admin'] as $f) require APP . '/lib/' . $f . '.php';
dispatch();
