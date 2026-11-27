<?php
$title = 'Masuk';
$pesan = '';
$tipe = 'error';
foreach ($err as [$t, $m]) { $pesan = $m; $tipe = $t; break; }
?>
<div class="split">
  <div class="lpane">
    <div class="lcard">
      <div style="text-align:center">
        <h1 class="logo-t" style="font-size:2.6rem;margin-bottom:.6rem">Math<b>letics</b></h1>
        <div class="welcome">
          <h1 style="font-size:1.05rem;margin:0 0 .35rem">Selamat Datang!</h1>
          <div class="carousel"><p id="welcomeText"></p></div>
        </div>
      </div>

      <form method="post" action="<?= h(url('masuk')) ?>" id="loginForm">
        <?= csrf_field() ?>
        <div class="seg" role="radiogroup" aria-label="Masuk sebagai">
          <label><input type="radio" name="mode" value="siswa" <?= $mode === 'siswa' ? 'checked' : '' ?>><span>Siswa</span></label>
          <label><input type="radio" name="mode" value="admin" <?= $mode === 'admin' ? 'checked' : '' ?>><span>Admin</span></label>
        </div>

        <div class="fg">
          <input type="text" id="ident" name="ident" placeholder=" " value="<?= h($ident) ?>" required autocomplete="username" inputmode="<?= $mode === 'admin' ? 'email' : 'tel' ?>">
          <label for="ident" id="identL"><?= $mode === 'admin' ? 'Email admin' : 'Nomor HP (contoh: 0812...)' ?></label>
          <span class="fi" id="identI"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2.5"/><circle cx="8.5" cy="11" r="2"/><path d="M6 16c.5-1.6 1.8-2.5 2.5-2.5s2 .9 2.5 2.5"/><line x1="14" y1="9" x2="18" y2="9"/><line x1="14" y1="13" x2="18" y2="13"/></svg></span>
        </div>
        <div class="fg">
          <input type="password" id="sandi" name="sandi" placeholder=" " required autocomplete="current-password">
          <label for="sandi">Password</label>
          <span class="fi"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2"/><path d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5"/><circle cx="12" cy="15.2" r="1.4"/></svg></span>
        </div>

        <button type="submit" id="btnLogin" class="btn-login"><span id="btnLabel"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>&nbsp;MASUK</span></button>
      </form>

      <div class="lres" role="alert" id="lres"<?= $tipe === 'ok' ? ' style="color:#10b981"' : '' ?>><?= h($pesan) ?></div>

      <p class="hint2">Belum mempunyai akun? <a href="<?= h(url('paket')) ?>">lihat paket di sini</a></p>
      <a href="<?= h(url('')) ?>" class="btn-back"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg><span>HALAMAN UTAMA</span></a>
    </div>
  </div>

  <div class="rpane" aria-hidden="true">
    <div class="mc" id="loginMath"></div>
    <div class="qstage">
      <div class="qmark">&ldquo;</div>
      <div class="lq-text" id="quoteText"></div>
      <div class="qauthor" id="quoteAuthor"></div>
      <div class="qdots" id="quoteDots"></div>
      <div class="qlogo">Math<span>letics</span></div>
    </div>
  </div>
</div>
