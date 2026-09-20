<p><a href="<?= url('/dashboard') ?>">&larr; Kembali ke katalog</a></p>

<div class="detail">
  <div class="cover"><?php $cb = $book; require APP_PATH . '/Views/books/_cover.php'; ?></div>

  <div>
    <span class="muted"><?= e($book['nama_kategori'] ?? 'Tanpa kategori') ?></span>
    <h2><?= e($book['judul']) ?></h2>

    <div>
      <?php if ($book['tersedia'] > 0): ?>
        <span class="badge ok">Tersedia</span>
      <?php else: ?>
        <span class="badge err">Dipinjam</span>
      <?php endif; ?>
      <span class="muted">&nbsp;<?= (int)$book['tersedia'] ?> dari <?= (int)$book['stok'] ?> eksemplar tersedia</span>
    </div>

    <dl class="meta">
      <dt>Penulis</dt><dd><?= e($book['penulis']) ?></dd>
      <dt>Penerbit</dt><dd><?= e($book['penerbit'] ?: '-') ?></dd>
      <dt>Tahun terbit</dt><dd><?= e((string)($book['tahun'] ?: '-')) ?></dd>
      <dt>Kategori</dt><dd><?= e($book['nama_kategori'] ?? '-') ?></dd>
    </dl>

    <?php if ($book['sinopsis']): ?>
      <h3 style="margin-top:8px">Sinopsis</h3>
      <p class="sinopsis"><?= e($book['sinopsis']) ?></p>
    <?php endif; ?>

    <div class="panel" style="margin-top:18px;max-width:420px">
      <?php if ($pinjaman): [$teks, $kelas] = sisa_info($pinjaman['tanggal_kembali']); ?>
        <strong>Sedang Anda pinjam</strong>
        <p class="muted" style="margin:4px 0 8px">
          Dipinjam <?= e(tgl($pinjaman['tanggal_pinjam'])) ?><br>
          Kembali paling lambat <strong><?= e(tgl($pinjaman['tanggal_kembali'])) ?></strong>
        </p>
        <span class="badge <?= $kelas === 'err' ? 'err' : ($kelas === 'warn' ? 'warn' : 'ok') ?>"><?= e($teks) ?></span>
      <?php elseif ($bisa): ?>
        <form method="post" action="<?= url('/books/' . (int)$book['id'] . '/borrow') ?>">
          <?= csrf_field() ?>
          <button class="btn block" type="submit">Pinjam buku</button>
        </form>
        <p class="note">Pengajuan perlu disetujui admin. Kuota Anda: <?= $aktif ?>/<?= $max ?>.</p>
      <?php else: ?>
        <button class="btn block" type="button" disabled><?= e($label) ?></button>
        <?php if ($myStatus === 'pending'): ?>
          <p class="note">Admin akan memproses pengajuan Anda.</p>
        <?php elseif ($aktif >= $max): ?>
          <p class="note">Kembalikan salah satu buku untuk meminjam lagi.</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
