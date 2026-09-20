<?php
$peran = $_SESSION['user']['peran'] ?? '';
$path  = '/' . trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen(BASE_PATH)), '/');
$nav   = function (string $href, string $label, bool $exact = false) use ($path) {
    $on = $exact ? $path === $href : str_starts_with($path, $href);
    return '<a href="' . url($href) . '"' . ($on ? ' class="on"' : '') . '>' . e($label) . '</a>';
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Aplikasi') ?> - Perpustakaan</title>
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <link rel="stylesheet" href="<?= url('assets/style.css') ?>">
</head>
<body>
  <header class="top">
    <div class="in">
      <a class="brand" href="<?= url('/') ?>">Perpustakaan</a>
      <nav>
        <?php if ($peran === 'admin'): ?>
          <?= $nav('/admin', 'Ringkasan', true) ?>
          <?= $nav('/admin/books', 'Buku') ?>
          <?= $nav('/admin/users', 'User') ?>
          <?= $nav('/admin/borrowings', 'Riwayat') ?>
        <?php else: ?>
          <?= $nav('/dashboard', 'Dashboard') ?>
        <?php endif; ?>
        <span class="who"><?= e($_SESSION['user']['nama'] ?? '') ?> (<?= e($peran) ?>)</span>
        <a href="<?= url('/logout') ?>">Keluar</a>
      </nav>
    </div>
  </header>
  <main>
    <?php if ($m = flash('success')): ?><div class="alert ok"><?= e($m) ?></div><?php endif; ?>
    <?php if ($m = flash('error')): ?><div class="alert err"><?= e($m) ?></div><?php endif; ?>
    <?= $content ?>
  </main>
  <script>
    // Konfirmasi sebelum aksi yang menghapus / menolak
    document.addEventListener('submit', function (ev) {
      var msg = ev.target.getAttribute('data-confirm');
      if (msg && !confirm(msg)) ev.preventDefault();
    });
  </script>
</body>
</html>
