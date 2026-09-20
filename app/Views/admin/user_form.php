<?php $isEdit = $edit !== null; ?>
<h2><?= $isEdit ? 'Edit User' : 'Tambah User' ?></h2>

<form method="post" class="panel form-card" style="margin-top:14px"
      action="<?= url($isEdit ? '/admin/users/' . rawurlencode($edit['nim']) . '/update' : '/admin/users') ?>">
  <?= csrf_field() ?>

  <label for="nim">ID (NIS/NIM/username)</label>
  <input id="nim" type="text" name="nim" maxlength="20" required
         value="<?= e($isEdit ? $edit['nim'] : old('nim')) ?>" <?= $isEdit ? 'readonly' : '' ?>>

  <label for="nama">Nama</label>
  <input id="nama" type="text" name="nama" maxlength="150" required value="<?= e(old('nama', $isEdit ? $edit['nama'] : '')) ?>">

  <label for="pw"><?= $isEdit ? 'Password baru (kosongkan jika tidak diganti)' : 'Password (minimal 6 karakter)' ?></label>
  <input id="pw" type="password" name="password" minlength="6" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>

  <label for="peran">Peran</label>
  <select id="peran" name="peran">
    <?php $cur = old('peran', $isEdit ? $edit['peran'] : 'siswa'); foreach (['siswa', 'admin'] as $p): ?>
      <option value="<?= $p ?>" <?= $cur === $p ? 'selected' : '' ?>><?= $p ?></option>
    <?php endforeach; ?>
  </select>

  <div class="form-actions">
    <button class="btn" type="submit">Simpan</button>
    <a href="<?= url('/admin/users') ?>">Batal</a>
  </div>
</form>
