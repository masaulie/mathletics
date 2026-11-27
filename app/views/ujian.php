<?php $title = $pc['judul']; ?>
<header class="examtop">
  <div class="in">
    <div><b><?= h($pc['judul']) ?></b> <span style="color:#94a3b8;font-size:14px">&middot; <span id="prog"></span></span></div>
    <div class="timer" id="timer" role="timer" aria-live="off">--:--</div>
  </div>
</header>
<main class="exmain">
  <div>
    <div class="qbox" id="qbox">
      <div class="qno"><span id="qno"></span><span id="qtopik"></span></div>
      <div class="qtext" id="qtext"></div>
      <ul class="opsi" id="opsi"></ul>
      <label class="check" style="margin-top:14px"><input type="checkbox" id="ragu"><span>Ragu-ragu, tandai untuk dicek lagi</span></label>
    </div>
    <div class="peta" id="peta" aria-label="Peta nomor soal"></div>
    <p class="saved" id="saved" role="status" style="margin-top:12px"></p>
  </div>
</main>
<div class="examnav">
  <div class="in">
    <button class="btn ghost" type="button" id="prev">Sebelumnya</button>
    <span class="grow"></span>
    <button class="btn ghost" type="button" id="next">Berikutnya</button>
    <button class="btn dark" type="button" id="selesai">Kumpulkan</button>
  </div>
</div>
<dialog class="pj" id="kdlg">
  <div class="in">
    <h2>Kumpulkan sekarang?</h2>
    <p id="kinfo" class="mute"></p>
    <div class="row"><button class="btn ghost grow" type="button" id="kbatal">Kembali</button><button class="btn grow" type="button" id="kyakin">Ya, kumpulkan</button></div>
  </div>
</dialog>
<form id="kumpulForm" method="post" action="<?= h(url('ujian/' . $pc['token'] . '/kumpul')) ?>"><?= csrf_field() ?></form>
<script type="application/json" id="exdata"><?= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script defer src="<?= h(asset('js/ujian.js')) ?>"></script>
