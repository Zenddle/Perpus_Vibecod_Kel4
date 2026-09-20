<div class="toolbar">
  <h2 style="margin:0">Kelola User</h2>
  <a class="btn" href="<?= url('/admin/users/create') ?>">+ Tambah user</a>
</div>

<div class="table-wrap"><table>
  <tr><th>ID (NIS/NIM)</th><th>Nama</th><th>Peran</th><th>Pinjaman aktif</th><th></th></tr>
  <?php foreach ($users as $u): $self = $u['nim'] === $_SESSION['user']['nim']; ?>
    <tr>
      <td><?= e($u['nim']) ?></td>
      <td><?= e($u['nama']) ?><?= $self ? ' <span class="muted">(Anda)</span>' : '' ?></td>
      <td><span class="badge <?= $u['peran'] === 'admin' ? 'info' : 'gray' ?>"><?= e($u['peran']) ?></span></td>
      <td><?= (int)$u['aktif'] ?></td>
      <td class="act">
        <a class="btn ghost sm" href="<?= url('/admin/users/' . rawurlencode($u['nim']) . '/edit') ?>">Edit</a>
        <?php if (!$self): ?>
          <form method="post" action="<?= url('/admin/users/' . rawurlencode($u['nim']) . '/delete') ?>" class="inline"
                data-confirm="Hapus user <?= e($u['nama']) ?>? Riwayat peminjamannya tetap tersimpan.">
            <?= csrf_field() ?>
            <button class="btn danger sm" type="submit">Hapus</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table></div>
