<h2>Dashboard Admin</h2>

<div class="stats">
  <div class="stat"><b><?= $stats['buku'] ?></b><span>Judul buku</span></div>
  <div class="stat"><b><?= $stats['siswa'] ?></b><span>Siswa</span></div>
  <div class="stat"><b><?= $stats['pengajuan'] ?></b><span>Menunggu persetujuan</span></div>
  <div class="stat"><b><?= $stats['dipinjam'] ?></b><span>Sedang dipinjam</span></div>
</div>

<h3>Pengajuan peminjaman</h3>
<?php if (!$pending): ?>
  <div class="panel empty">Tidak ada pengajuan yang menunggu.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <tr><th>Siswa</th><th>Buku</th><th>Diajukan</th><th>Stok</th><th>Setujui &amp; lama pinjam</th><th></th></tr>
  <?php foreach ($pending as $p): $sisa = max(0, (int)$p['stok'] - (int)$p['dipinjam']); ?>
    <tr>
      <td><?= e($p['nama_peminjam']) ?><br><span class="muted"><?= e($p['nim_peminjam']) ?></span></td>
      <td><?= e($p['judul_buku']) ?></td>
      <td><?= e(tgl($p['tanggal_pengajuan'])) ?></td>
      <td><?= $sisa > 0 ? "{$sisa} tersedia" : '<span class="badge err">Habis</span>' ?></td>
      <td>
        <form method="post" action="<?= url('/admin/borrowings/' . (int)$p['id'] . '/approve') ?>" class="inline">
          <?= csrf_field() ?>
          <input class="mini days" type="number" name="hari" min="1" max="60" value="<?= (int)$durasi ?>" aria-label="Lama pinjam (hari)"> hari
          <button class="btn good sm" type="submit" <?= $sisa > 0 ? '' : 'disabled' ?>>Setujui</button>
        </form>
      </td>
      <td class="act">
        <form method="post" action="<?= url('/admin/borrowings/' . (int)$p['id'] . '/reject') ?>" class="inline"
              data-confirm="Tolak pengajuan ini?">
          <?= csrf_field() ?>
          <button class="btn danger sm" type="submit">Tolak</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table></div>
<?php endif; ?>

<h3>Sedang dipinjam &amp; jangka waktunya</h3>
<?php if (!$approved): ?>
  <div class="panel empty">Belum ada buku yang sedang dipinjam.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <tr><th>Siswa</th><th>Buku</th><th>Tgl pinjam</th><th>Jatuh tempo</th><th>Sisa waktu</th><th></th></tr>
  <?php foreach ($approved as $a): [$teks, $kelas] = sisa_info($a['tanggal_kembali']); ?>
    <tr>
      <td><?= e($a['nama_peminjam']) ?><br><span class="muted"><?= e($a['nim_peminjam']) ?></span></td>
      <td><?= e($a['judul_buku']) ?></td>
      <td><?= e(tgl($a['tanggal_pinjam'])) ?></td>
      <td><?= e(tgl($a['tanggal_kembali'])) ?></td>
      <td><span class="badge <?= $kelas === 'err' ? 'err' : ($kelas === 'warn' ? 'warn' : 'ok') ?>"><?= e($teks) ?></span></td>
      <td class="act">
        <form method="post" action="<?= url('/admin/borrowings/' . (int)$a['id'] . '/return') ?>" class="inline"
              data-confirm="Tandai buku ini sudah dikembalikan?">
          <?= csrf_field() ?>
          <button class="btn ghost sm" type="submit">Tandai dikembalikan</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table></div>
<?php endif; ?>
