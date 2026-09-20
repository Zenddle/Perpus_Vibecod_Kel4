<h2>Halo, <?= e($user['nama']) ?></h2>
<p>NIM <?= e($user['nim']) ?> &middot; peran <strong><?= e($user['peran']) ?></strong></p>

<h3>Peminjaman saya</h3>
<?php if (!$borrowings): ?>
  <p>Belum ada peminjaman.</p>
<?php else: ?>
<table>
  <tr><th>Buku</th><th>Pinjam</th><th>Kembali</th><th>Status</th></tr>
  <?php foreach ($borrowings as $b): ?>
    <tr>
      <td><?= e($b['judul']) ?></td>
      <td><?= e($b['tanggal_pinjam']) ?></td>
      <td><?= e($b['tanggal_kembali']) ?></td>
      <td><?= e($b['status']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<h3>Daftar buku</h3>
<table>
  <tr><th>Judul</th><th>Kategori</th><th>Stok</th><th>Status</th></tr>
  <?php foreach ($books as $b): ?>
    <tr>
      <td><?= e($b['judul']) ?></td>
      <td><?= e($b['nama_kategori'] ?? '-') ?></td>
      <td><?= (int)$b['stok'] ?></td>
      <td><?= e($b['status']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>
