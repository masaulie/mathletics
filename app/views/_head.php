<?php
/** @var string $title */
$namaUsaha = setting('nama_usaha', 'Mathletics');
$noindex = $noindex ?? (http_response_code() !== 200);
$desc = $desc ?? 'Bimbingan Matematika online untuk siswa Indonesia: kelas kecil, tes diagnostik gratis, materi dan latihan soal.';
$kanonik = $kanonik ?? abs_url(ltrim(current_path(), '/'));
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= h(($title ?? '') !== '' ? $title . ' · ' : '') ?><?= h($namaUsaha) ?></title>
<meta name="description" content="<?= h($desc) ?>">
<meta name="theme-color" content="#e11d48">
<?php if ($noindex): ?>
<meta name="robots" content="noindex,nofollow">
<?php else: ?>
<link rel="canonical" href="<?= h($kanonik) ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="<?= h($namaUsaha) ?>">
<meta property="og:title" content="<?= h(($title ?? '') !== '' ? $title . ' · ' . $namaUsaha : $namaUsaha) ?>">
<meta property="og:description" content="<?= h($desc) ?>">
<meta property="og:url" content="<?= h($kanonik) ?>">
<meta name="twitter:card" content="summary">
<?php endif; ?>
<?php if (!empty($schema)): ?>
<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap">
<link rel="stylesheet" href="<?= h(asset('css/app.css')) ?>">
<?php if (!empty($math)): ?>
<link rel="stylesheet" href="<?= h(asset('vendor/katex/katex.min.css')) ?>">
<script defer src="<?= h(asset('vendor/katex/katex.min.js')) ?>"></script>
<script defer src="<?= h(asset('vendor/katex/auto-render.min.js')) ?>"></script>
<?php endif; ?>
<script>window.ML={base:<?= json_encode(rtrim(url(''), '/')) ?>,csrf:<?= json_encode(csrf()) ?>};</script>
<script defer src="<?= h(asset('js/app.js')) ?>"></script>
<?php if (!empty($pakaiAnalitik) && setting('analitik') !== '') echo setting('analitik'), "\n"; ?>
</head>
