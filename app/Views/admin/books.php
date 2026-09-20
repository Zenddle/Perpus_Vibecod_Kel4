<div class="toolbar">
  <h2 style="margin:0">Kelola Buku</h2>
  <a class="btn" href="<?= url('/admin/books/create') ?>">+ Tambah buku</a>
</div>

<?php if (!$books): ?>
  <div class="panel empty">Belum ada buku.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <tr><th>Cover</th><th>Judul</th><th>Kategori</th><th>Stok</th><th>Status</th><th></th></tr>
  <?php foreach ($books as $b): ?>
    <tr>
      <td><?php if ($b['cover']): ?><img class="thumb" src="<?= e(cover_url($b['cover'])) ?>" alt=""><?php else: ?><div class="thumb"><?php $cb = $b; require APP_PATH . '/Views/books/_cover.php'; ?></div><?php endif; ?></td>
      <td><strong><?= e($b['judul']) ?></strong><br><span class="muted"><?= e($b['penulis']) ?></span></td>
      <td><?= e($b['nama_kategori'] ?? '-') ?></td>
      <td><?= (int)$b['tersedia'] ?> / <?= (int)$b['stok'] ?></td>
      <td><?= $b['tersedia'] > 0 ? '<span class="badge ok">Tersedia</span>' : '<span class="badge err">Dipinjam</span>' ?></td>
      <td class="act">
        <a class="btn ghost sm" href="<?= url('/admin/books/' . (int)$b['id'] . '/edit') ?>">Edit</a>
        <form method="post" action="<?= url('/admin/books/' . (int)$b['id'] . '/delete') ?>" class="inline"
              data-confirm="Hapus buku &quot;<?= e($b['judul']) ?>&quot;?">
          <?= csrf_field() ?>
          <button class="btn danger sm" type="submit">Hapus</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table></div>
<p class="note">Stok: tersedia / total eksemplar.</p>
<?php endif; ?>
