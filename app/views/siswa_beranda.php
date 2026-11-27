<?php
$title = 'Hari ini';
$pctBuka = $total > 0 ? (int)round($buka / $total * 100) : 0;
$sapa = (int)date('G') < 11 ? 'Selamat pagi' : ((int)date('G') < 15 ? 'Selamat siang' : ((int)date('G') < 18 ? 'Selamat sore' : 'Selamat malam'));
?>
<h1><?= h($sapa) ?>, <?= h(explode(' ', trim($u['nama']))[0]) ?>.</h1>
<div class="stack">
  <section class="hari">
    <?php if ($jalan): ?>
      <span class="eyebrow">Tes belum selesai</span>
      <h2><?= h($jalan['judul']) ?></h2>
      <p>Pengerjaanmu masih berjalan. Lanjutkan sebelum waktunya habis.</p>
      <a class="btn" href="<?= h(url('ujian/' . $jalan['token'])) ?>">Lanjutkan tes <?= icon('arrow') ?></a>
    <?php elseif ($berikut): ?>
      <span class="eyebrow">Kelas berikutnya</span>
      <h2><?= h($berikut['judul']) ?></h2>
      <p><?= h(tgl($berikut['tanggal'])) ?><?= $berikut['jam'] ? ', ' . h($berikut['jam']) . ' WIB' : '' ?><?= $berikut['topik'] ? ' &middot; ' . h($berikut['topik']) : '' ?></p>
      <?php if ($berikut['tautan']): ?><a class="btn" href="<?= h($berikut['tautan']) ?>" rel="noopener" target="_blank">Buka kelas <?= icon('arrow') ?></a><?php else: ?><a class="btn" href="<?= h(url('belajar/jadwal')) ?>">Lihat jadwal</a><?php endif; ?>
    <?php elseif ($terbuka): ?>
      <span class="eyebrow">Lanjut belajar</span>
      <h2><?= h($terbuka[0]['judul']) ?></h2>
      <p><?= h($terbuka[0]['topik'] ?: 'Buka modul dan worksheet pertemuan ini.') ?></p>
      <a class="btn" href="<?= h(url('belajar/materi/' . $terbuka[0]['id'])) ?>">Buka materi <?= icon('arrow') ?></a>
    <?php else: ?>
      <span class="eyebrow">Selamat datang</span>
      <h2>Belum ada materi yang dibuka.</h2>
      <p>Jadwal dan materi akan muncul di sini begitu kelas dimulai. Sambil menunggu, kerjakan tes diagnostik.</p>
      <a class="btn" href="<?= h(url('belajar/tes')) ?>">Ke halaman tes <?= icon('arrow') ?></a>
    <?php endif; ?>
  </section>

  <?php if ($total > 0): ?>
  <section class="box">
    <div class="row" style="justify-content:space-between"><h3 style="margin:0">Materi terbuka</h3><span class="num"><?= (int)$buka ?>/<?= (int)$total ?></span></div>
    <div class="bar" style="margin:12px 0"><i style="width:<?= $pctBuka ?>%"></i></div>
    <?php foreach ($terbuka as $t): ?>
      <a class="item" href="<?= h(url('belajar/materi/' . $t['id'])) ?>"><span class="n"><?= (int)$t['urut'] ?></span><span class="t"><b><?= h($t['judul']) ?></b><small><?= h($t['topik'] ?: 'Modul, worksheet, kunci, slide') ?> &middot; <?= (int)$t['jml'] ?> berkas</small></span><?= icon('arrow') ?></a>
    <?php endforeach; ?>
    <?php if (!$terbuka): ?><p class="mute" style="margin:0">Belum ada pertemuan yang dibuka.</p><?php endif; ?>
  </section>
  <?php endif; ?>

  <?php if ($terakhir): ?>
  <section class="box">
    <h3>Tes terakhir</h3>
    <p style="margin:0"><?= h($terakhir['judul']) ?> &middot; skor <span class="num"><?= (int)$terakhir['skor'] ?></span> <a href="<?= h(url('ujian/' . $terakhir['token'] . '/hasil')) ?>">Lihat hasil</a></p>
  </section>
  <?php endif; ?>
</div>
