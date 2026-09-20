<?php use App\Models\Borrowing; ?>
<div class="toolbar">
  <div>
    <h2 style="margin:0">Riwayat Peminjaman</h2>
    <span class="muted">Siapa saja yang meminjam buku, beserta jangka waktunya.</span>
  </div>
</div>

<div class="filters" style="margin-top:0">
  <a class="chip <?= $status === '' ? 'on' : '' ?>" href="<?= url('/admin/borrowings') ?>">Semua</a>
  <?php foreach (Borrowing::STATUS_LABEL as $k => $label): ?>
    <a class="chip <?= $status === $k ? 'on' : '' ?>" href="<?= url('/admin/borrowings?status=' . $k) ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$rows): ?>
  <div class="panel empty">Belum ada data.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <tr><th>Peminjam</th><th>Buku</th><th>Diajukan</th><th>Tgl pinjam</th><th>Jatuh tempo</th><th>Dikembalikan</th><th>Status</th></tr>
  <?php foreach ($rows as $r):
    $badge = ['pending' => 'warn', 'approved' => 'info', 'rejected' => 'err', 'returned' => 'ok'][$r['status']];
  ?>
    <tr>
      <td><?= e($r['nama_peminjam']) ?><br><span class="muted"><?= e($r['nim_peminjam']) ?></span></td>
      <td><?= e($r['judul_buku']) ?></td>
      <td><?= e(tgl($r['tanggal_pengajuan'])) ?></td>
      <td><?= e(tgl($r['tanggal_pinjam'])) ?></td>
      <td>
        <?= e(tgl($r['tanggal_kembali'])) ?>
        <?php if ($r['status'] === 'approved'): [$teks, $kelas] = sisa_info($r['tanggal_kembali']); ?>
          <br><span class="<?= $kelas ?>" style="font-size:.82rem"><?= e($teks) ?></span>
        <?php endif; ?>
      </td>
      <td><?= e(tgl($r['tanggal_dikembalikan'])) ?></td>
      <td><span class="badge <?= $badge ?>"><?= e(Borrowing::STATUS_LABEL[$r['status']]) ?></span></td>
    </tr>
  <?php endforeach; ?>
</table></div>
<?php endif; ?>
