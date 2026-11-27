<?php /** @var string $title */ ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= h(($title ?? '') !== '' ? $title . ' · ' : '') ?><?= h(setting('nama_usaha', 'Mathletics')) ?></title>
<meta name="theme-color" content="#e11d48">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="<?= h(asset('css/app.css')) ?>">
<?php if (!empty($math)): ?>
<link rel="stylesheet" href="<?= h(asset('vendor/katex/katex.min.css')) ?>">
<script defer src="<?= h(asset('vendor/katex/katex.min.js')) ?>"></script>
<script defer src="<?= h(asset('vendor/katex/auto-render.min.js')) ?>"></script>
<?php endif; ?>
<script>window.ML={base:<?= json_encode(rtrim(url(''), '/')) ?>,csrf:<?= json_encode(csrf()) ?>};</script>
<script defer src="<?= h(asset('js/app.js')) ?>"></script>
</head>
