<?php $title = $s['id'] ? 'Ubah soal' : 'Soal baru'; ?>
<a href="<?= h(url('admin/soal')) ?>" class="mute" style="text-decoration:none">&larr; Bank soal</a>
<h1 style="margin-top:12px"><?= h($title) ?></h1>
<?= flash_html($err) ?>
<form class="box" method="post" action="<?= h(url('admin/soal/' . ($s['id'] ?: 'baru'))) ?>" style="max-width:720px">
  <?= csrf_field() ?>
  <p class="hint">Rumus ditulis di antara tanda dolar, misalnya <code>$\frac{2}{3}+x^2$</code>.</p>
  <div class="field"><label for="topik_id">Topik</label><select id="topik_id" name="topik_id"><?php foreach ($topiks as $t): ?><option value="<?= (int)$t['id'] ?>" <?= (int)$t['id'] === (int)$s['topik_id'] ? 'selected' : '' ?>><?= h($t['nama']) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="tingkat">Tingkat kesulitan</label><select id="tingkat" name="tingkat"><?php foreach ([1 => 'Mudah', 2 => 'Sedang', 3 => 'Sulit'] as $k => $l): ?><option value="<?= $k ?>" <?= (int)$s['tingkat'] === $k ? 'selected' : '' ?>><?= h($l) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="teks">Soal</label><textarea id="teks" name="teks" rows="4" required><?= h($s['teks']) ?></textarea></div>
  <?php foreach (['a', 'b', 'c', 'd'] as $o): ?>
    <div class="field"><label for="o<?= $o ?>">Pilihan <?= strtoupper($o) ?></label><input type="text" id="o<?= $o ?>" name="<?= $o ?>" value="<?= h($s[$o]) ?>" required></div>
  <?php endforeach; ?>
  <div class="field"><label for="kunci">Kunci jawaban</label><select id="kunci" name="kunci"><?php foreach (['A', 'B', 'C', 'D'] as $k): ?><option <?= $s['kunci'] === $k ? 'selected' : '' ?>><?= $k ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="pembahasan">Pembahasan</label><textarea id="pembahasan" name="pembahasan" rows="3"><?= h($s['pembahasan']) ?></textarea></div>
  <div class="field"><label class="check"><input type="checkbox" name="aktif" value="1" <?= (int)$s['aktif'] ? 'checked' : '' ?>><span>Soal aktif</span></label></div>
  <div class="row"><button class="btn" type="submit">Simpan</button></div>
</form>
<?php if ($s['id']): ?>
<form method="post" action="<?= h(url('admin/soal/' . $s['id'] . '/hapus')) ?>" data-confirm="Hapus soal ini?" style="margin-top:16px"><?= csrf_field() ?><button class="btn danger sm" type="submit">Hapus soal</button></form>
<?php endif; ?>
