<h2>Dashboard <?= e($_SESSION['user']['peran']) ?></h2>
<p>
  Buku: <strong><?= $stats['buku'] ?></strong> &middot;
  Anggota: <strong><?= $stats['anggota'] ?></strong> &middot;
  Pengajuan pending: <strong><?= $stats['pengajuan'] ?></strong> &middot;
  Sedang dipinjam: <strong><?= $stats['dipinjam'] ?></strong>
</p>

<h3>Pengguna</h3>
<?php if ($_SESSION['user']['peran'] === 'admin'): ?>
  <p><a href="<?= url('/admin/users/create') ?>">+ Tambah user</a></p>
<?php endif; ?>

<table>
  <tr><th>NIM</th><th>Nama</th><th>Peran</th></tr>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= e($u['nim']) ?></td>
      <td><?= e($u['nama']) ?></td>
      <td><?= e($u['peran']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>
