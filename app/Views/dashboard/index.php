<h2>Halo, <?= e($user['nama']) ?></h2>
<p class="muted">NIS/NIM <?= e($user['nim']) ?></p>

<div class="quota">
  <span>Kuota pinjam:</span>
  <span class="dots" aria-hidden="true">
    <?php for ($i = 0; $i < $max; $i++): ?><i class="<?= $i < $aktif ? 'f' : '' ?>"></i><?php endfor; ?>
  </span>
  <strong><?= $aktif ?>/<?= $max ?></strong>
  <?php if ($aktif >= $max): ?><span class="badge err">Penuh, opsi pinjam terkunci</span><?php endif; ?>
</div>
<p class="note" style="margin-top:2px">Pengajuan yang menunggu persetujuan juga dihitung dalam kuota.</p>

<h3>Buku yang sedang saya pinjam</h3>
<?php if (!$dipinjam): ?>
  <div class="panel empty">Belum ada buku yang disetujui admin.</div>
<?php else: ?>
  <div class="loans">
    <?php foreach ($dipinjam as $l): [$teks, $kelas] = sisa_info($l['tanggal_kembali']); ?>
      <div class="loan">
        <div class="cv"><?php $cb = ['cover' => $l['cover'], 'judul' => $l['judul_buku'], 'id' => $l['book_id']]; require APP_PATH . '/Views/books/_cover.php'; ?></div>
        <div>
          <b><?= $l['book_id'] ? '<a href="' . url('/books/' . (int)$l['book_id']) . '">' . e($l['judul_buku']) . '</a>' : e($l['judul_buku']) ?></b>
          <span class="muted">Dipinjam <?= e(tgl($l['tanggal_pinjam'])) ?><br>Kembali paling lambat <strong><?= e(tgl($l['tanggal_kembali'])) ?></strong></span><br>
          <span class="badge <?= $kelas === 'err' ? 'err' : ($kelas === 'warn' ? 'warn' : 'ok') ?>"><?= e($teks) ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($menunggu): ?>
  <h3>Menunggu persetujuan admin</h3>
  <div class="table-wrap"><table>
    <tr><th>Buku</th><th>Diajukan</th></tr>
    <?php foreach ($menunggu as $m): ?>
      <tr><td><?= e($m['judul_buku']) ?></td><td><?= e(tgl($m['tanggal_pengajuan'])) ?></td></tr>
    <?php endforeach; ?>
  </table></div>
<?php endif; ?>

<h3>Katalog buku</h3>
<form method="get" action="<?= url('/dashboard') ?>" class="search">
  <?php if ($kategori): ?><input type="hidden" name="kategori" value="<?= (int)$kategori ?>"><?php endif; ?>
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari judul atau penulis">
  <button class="btn" type="submit">Cari</button>
</form>

<div class="filters">
  <a class="chip <?= !$kategori ? 'on' : '' ?>" href="<?= url('/dashboard' . ($q !== '' ? '?q=' . urlencode($q) : '')) ?>">Semua</a>
  <?php foreach ($categories as $c): ?>
    <a class="chip <?= $kategori === (int)$c['id'] ? 'on' : '' ?>"
       href="<?= url('/dashboard?kategori=' . (int)$c['id'] . ($q !== '' ? '&q=' . urlencode($q) : '')) ?>"><?= e($c['nama_kategori']) ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$books): ?>
  <div class="panel empty">Tidak ada buku yang cocok.</div>
<?php else: ?>
  <div class="books">
    <?php foreach ($books as $b): ?>
      <article class="book">
        <a class="cv" href="<?= url('/books/' . (int)$b['id']) ?>"><?php $cb = $b; require APP_PATH . '/Views/books/_cover.php'; ?></a>
        <div class="body">
          <span class="muted"><?= e($b['nama_kategori'] ?? 'Tanpa kategori') ?></span>
          <h4><a href="<?= url('/books/' . (int)$b['id']) ?>"><?= e($b['judul']) ?></a></h4>
          <span class="by"><?= e($b['penulis']) ?></span>
          <div>
            <?php if ($b['tersedia'] > 0): ?>
              <span class="badge ok">Tersedia (<?= (int)$b['tersedia'] ?>)</span>
            <?php else: ?>
              <span class="badge err">Dipinjam</span>
            <?php endif; ?>
            <?php if ($b['status_saya'] === 'approved'): ?><span class="badge info">Anda pinjam</span>
            <?php elseif ($b['status_saya'] === 'pending'): ?><span class="badge warn">Diajukan</span><?php endif; ?>
          </div>
          <div class="foot">
            <?php if ($b['bisa_pinjam']): ?>
              <form method="post" action="<?= url('/books/' . (int)$b['id'] . '/borrow') ?>">
                <?= csrf_field() ?>
                <button class="btn block sm" type="submit">Pinjam</button>
              </form>
            <?php else: ?>
              <button class="btn block sm" type="button" disabled title="<?= e($b['label']) ?>"><?= e($b['label']) ?></button>
            <?php endif; ?>
            <a class="btn ghost sm" href="<?= url('/books/' . (int)$b['id']) ?>">Detail</a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
