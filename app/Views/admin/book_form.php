<?php $isEdit = $edit !== null; $v = fn(string $k) => old($k, $isEdit ? (string)($edit[$k] ?? '') : ''); ?>
<h2><?= $isEdit ? 'Edit Buku' : 'Tambah Buku' ?></h2>

<form method="post" enctype="multipart/form-data" class="panel form-card" style="margin-top:14px"
      action="<?= url($isEdit ? '/admin/books/' . (int)$edit['id'] . '/update' : '/admin/books') ?>">
  <?= csrf_field() ?>

  <label for="judul">Judul</label>
  <input id="judul" type="text" name="judul" maxlength="200" required value="<?= e($v('judul')) ?>">

  <div class="grid2">
    <div>
      <label for="penulis">Penulis</label>
      <input id="penulis" type="text" name="penulis" maxlength="150" required value="<?= e($v('penulis')) ?>">
    </div>
    <div>
      <label for="penerbit">Penerbit</label>
      <input id="penerbit" type="text" name="penerbit" maxlength="150" value="<?= e($v('penerbit')) ?>">
    </div>
    <div>
      <label for="tahun">Tahun terbit</label>
      <input id="tahun" type="number" name="tahun" min="1000" max="<?= (int)date('Y') + 1 ?>" value="<?= e($v('tahun')) ?>">
    </div>
    <div>
      <label for="stok">Stok (eksemplar)</label>
      <input id="stok" type="number" name="stok" min="1" max="999" required value="<?= e(old('stok', $isEdit ? (string)$edit['stok'] : '1')) ?>">
    </div>
    <div>
      <label for="cat">Kategori</label>
      <select id="cat" name="category_id">
        <option value="">- Tanpa kategori -</option>
        <?php $cur = old('category_id', $isEdit ? (string)$edit['category_id'] : ''); foreach ($categories as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= (string)$c['id'] === $cur ? 'selected' : '' ?>><?= e($c['nama_kategori']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="kb">atau kategori baru</label>
      <input id="kb" type="text" name="kategori_baru" maxlength="100" placeholder="Opsional" value="<?= e(old('kategori_baru')) ?>">
    </div>
  </div>

  <label for="sinopsis">Sinopsis / detail</label>
  <textarea id="sinopsis" name="sinopsis"><?= e($v('sinopsis')) ?></textarea>

  <label for="cover">Cover buku (JPG/PNG/WebP, maks 2 MB)</label>
  <?php if ($isEdit && $edit['cover']): ?>
    <img class="thumb" style="width:72px;height:100px;margin-bottom:8px" src="<?= e(cover_url($edit['cover'])) ?>" alt="Cover saat ini">
  <?php endif; ?>
  <input id="cover" type="file" name="cover" accept="image/jpeg,image/png,image/webp">
  <?php if ($isEdit): ?><p class="note">Kosongkan jika tidak ingin mengganti cover.</p><?php endif; ?>

  <div class="form-actions">
    <button class="btn" type="submit">Simpan</button>
    <a href="<?= url('/admin/books') ?>">Batal</a>
  </div>
</form>
