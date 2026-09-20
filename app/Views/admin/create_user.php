<h2>Tambah User</h2>
<?php if ($m = flash('error')): ?><p class="err"><?= e($m) ?></p><?php endif; ?>

<form method="post" action="<?= url('/admin/users') ?>" style="max-width:360px">
  <label>NIM</label>
  <input name="nim" value="<?= e(old('nim')) ?>" maxlength="20" required style="width:100%;padding:8px;margin:4px 0 12px">

  <label>Nama</label>
  <input name="nama" value="<?= e(old('nama')) ?>" maxlength="150" required style="width:100%;padding:8px;margin:4px 0 12px">

  <label>Password (minimal 6 karakter)</label>
  <input name="password" type="password" minlength="6" required style="width:100%;padding:8px;margin:4px 0 12px">

  <label>Peran</label>
  <select name="peran" style="width:100%;padding:8px;margin:4px 0 12px">
    <?php foreach (['anggota', 'petugas', 'admin'] as $p): ?>
      <option value="<?= $p ?>" <?= old('peran', 'anggota') === $p ? 'selected' : '' ?>><?= $p ?></option>
    <?php endforeach; ?>
  </select>

  <button type="submit" style="padding:8px 16px">Simpan</button>
  <a href="<?= url('/admin') ?>" style="margin-left:12px">Batal</a>
</form>