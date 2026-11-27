<?php
$title = 'Hasil tes';
$math = true;
$wa = setting('wa');
$pct = $pc['total'] > 0 ? (int)round($pc['benar'] / $pc['total'] * 100) : 0;
$baris = [];
foreach ($rincian as $nama => $r) {
    $p = $r['total'] > 0 ? (int)round($r['benar'] / $r['total'] * 100) : 0;
    $baris[] = ['nama' => $nama, 'benar' => $r['benar'], 'total' => $r['total'], 'pct' => $p, 'lbl' => $p >= 70 ? 'kuat' : ($p >= 40 ? 'cukup' : 'lemah')];
}
usort($baris, fn($a, $b) => $a['pct'] <=> $b['pct']);
$terlemah = $baris[0] ?? null;
?>
<div class="<?= me() ? '' : 'wrap narrow' ?>" style="<?= me() ? '' : 'padding:36px 20px 64px' ?>">
  <span class="eyebrow"><?= h($pc['judul']) ?></span>
  <h1 style="font-size:26px"><?= h($pc['nama']) ?></h1>
  <div class="box" style="margin-bottom:16px">
    <div class="skor"><b><?= (int)$pc['skor'] ?></b><span>/ 100</span></div>
    <p class="mute" style="margin:6px 0 0"><span class="num"><?= (int)$pc['benar'] ?></span> benar dari <span class="num"><?= (int)$pc['total'] ?></span> soal &middot; selesai <?= h(tgl($pc['selesai'], true)) ?></p>
  </div>

  <?php if ($baris): ?>
  <div class="box" style="margin-bottom:16px">
    <h3>Per topik</h3>
    <?php if ($terlemah && $terlemah['pct'] < 70): ?><p class="mute">Prioritas latihan: <b style="color:var(--ink)"><?= h($terlemah['nama']) ?></b> (<?= (int)$terlemah['benar'] ?> dari <?= (int)$terlemah['total'] ?> benar).</p><?php endif; ?>
    <?php foreach ($baris as $b): ?>
      <div class="topikrow">
        <b><?= h($b['nama']) ?> <span class="mute num" style="font-weight:500"><?= (int)$b['benar'] ?>/<?= (int)$b['total'] ?></span></b>
        <span class="lbl <?= h($b['lbl']) ?>"><?= $b['lbl'] === 'kuat' ? 'Kuat' : ($b['lbl'] === 'cukup' ? 'Cukup' : 'Perlu dilatih') ?></span>
        <div class="bar"><i style="width:<?= (int)$b['pct'] ?>%"></i></div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!me() && (int)$pc['publik'] === 1): ?>
  <div class="hari" style="margin-bottom:16px">
    <span class="eyebrow">Langkah berikutnya</span>
    <h2>Latih topik yang lemah bersama.</h2>
    <p>Kelas kecil, maksimal 8 siswa, membahas dari topik yang paling perlu dilatih.</p>
    <div class="row">
      <?php if ($paket): ?><a class="btn" href="<?= h(url('paket/' . $paket[0]['slug'])) ?>">Lihat paket</a><?php endif; ?>
      <?php if ($wa): ?><a class="btn ghost" href="<?= h(wa_link($wa, 'Halo, saya ' . ($pc['nama']) . ', baru mengerjakan tes diagnostik Mathletics dengan skor ' . (int)$pc['skor'] . '. Saya mau konsultasi.')) ?>">Konsultasi lewat WhatsApp</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="box">
    <h3>Pembahasan</h3>
    <?php foreach ($soal as $i => $s):
        $j = $jaw[(int)$s['id']] ?? null; $benar = $j === $s['kunci']; ?>
      <div class="review">
        <div class="qno"><span>Soal <?= $i + 1 ?> &middot; <?= h($s['topik_nama']) ?></span><span class="<?= $benar ? '' : 'mute' ?>"><?= $benar ? 'Benar' : ($j ? 'Salah' : 'Kosong') ?></span></div>
        <div class="qtext" style="font-size:17px"><?= h($s['teks']) ?></div>
        <div class="jwb">Jawabanmu: <b><?= h($j ?? '-') ?></b> &middot; Kunci: <b><?= h($s['kunci']) ?></b> (<?= h($s[strtolower($s['kunci'])]) ?>)</div>
        <?php if ($s['pembahasan']): ?><div class="bahas" style="display:block"><?= h($s['pembahasan']) ?></div><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<script>document.addEventListener('DOMContentLoaded',function(){});</script>
