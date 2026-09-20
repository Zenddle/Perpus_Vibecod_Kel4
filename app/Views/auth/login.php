<?php $sebagai = old('sebagai', 'siswa'); ?>
<h1>Perpustakaan</h1>
<p class="muted" style="margin:0">Masuk untuk melihat dan meminjam buku.</p>

<?php if ($m = flash('error')): ?><div class="alert err" style="margin-top:14px"><?= e($m) ?></div><?php endif; ?>
<?php if ($m = flash('success')): ?><div class="alert ok" style="margin-top:14px"><?= e($m) ?></div><?php endif; ?>

<form method="post" action="<?= url('/login') ?>">
  <?= csrf_field() ?>
  <div class="tabs" role="radiogroup" aria-label="Masuk sebagai">
    <input type="radio" name="sebagai" id="r-siswa" value="siswa" <?= $sebagai === 'siswa' ? 'checked' : '' ?>>
    <label for="r-siswa">Siswa</label>
    <input type="radio" name="sebagai" id="r-admin" value="admin" <?= $sebagai === 'admin' ? 'checked' : '' ?>>
    <label for="r-admin">Admin</label>
  </div>

  <label for="nim">NIS / NIM / ID</label>
  <input id="nim" type="text" name="nim" value="<?= e(old('nim')) ?>" required autocomplete="username" <?= old('nim') === '' ? 'autofocus' : '' ?>>

  <label for="pw">Password</label>
  <input id="pw" type="password" name="password" required autocomplete="current-password" <?= old('nim') !== '' ? 'autofocus' : '' ?>>

  <div class="form-actions"><button class="btn block" type="submit">Masuk</button></div>
</form>
