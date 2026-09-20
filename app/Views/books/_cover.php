<?php
// Butuh: $cb = ['cover' => ?string, 'judul' => string, 'id' => int]
if (!empty($cb['cover'])): ?>
  <img src="<?= e(cover_url($cb['cover'])) ?>" alt="Cover <?= e($cb['judul']) ?>" loading="lazy">
<?php else:
  // Placeholder: inisial 2 kata pertama judul
  $ini = '';
  foreach (array_slice(preg_split('/\s+/', trim($cb['judul'])), 0, 2) as $w) {
      if (preg_match('/./u', $w, $ch)) $ini .= $ch[0];
  }
  $ini = strtoupper($ini);
?>
  <div class="ph c<?= ((int)($cb['id'] ?? 0)) % 6 ?>"><?= e($ini) ?></div>
<?php endif; ?>
