<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Aplikasi') ?></title>
  <style>
    body{font-family:system-ui,sans-serif;margin:0;color:#1f2933;background:#f5f6f8}
    header{display:flex;justify-content:space-between;align-items:center;padding:12px 24px;background:#1f3a5f;color:#fff}
    header a{color:#fff;margin-left:16px;text-decoration:none}
    main{max-width:860px;margin:24px auto;padding:0 16px}
    table{width:100%;border-collapse:collapse;background:#fff}
    th,td{padding:8px 12px;border-bottom:1px solid #e3e6ea;text-align:left}
    .ok{color:#1b7f3b}.err{color:#b42318}
  </style>
</head>
<body>
  <header>
    <strong>Perpustakaan</strong>
    <nav>
      <a href="<?= url('/dashboard') ?>">Dashboard</a>
      <?php if (in_array($_SESSION['user']['peran'] ?? '', ['admin','petugas'], true)): ?>
        <a href="<?= url('/admin') ?>">Admin</a>
      <?php endif; ?>
      <a href="<?= url('/logout') ?>">Keluar</a>
    </nav>
  </header>
  <main>
    <?php if ($m = flash('success')): ?><p class="ok"><?= e($m) ?></p><?php endif; ?>
    <?= $content ?>
  </main>
</body>
</html>
