<h2>Masuk</h2>
<?php if ($m = flash('error')): ?>
<p class="err"><?= e($m) ?></p>
<?php endif; ?>
<?php if ($m = flash('success')): ?>
<p class="ok"><?= e($m) ?></p>
<?php endif; ?>

<form method="post" action="<?= url('/login') ?>">
    <input name="nim" placeholder="NIM" value="<?= e(old('nim')) ?>" required <?= old('nim') === '' ? 'autofocus' : '' ?>>
    <input name="password" type="password" placeholder="Password" required <?= old('nim') !== '' ? 'autofocus' : '' ?>>
    <button type="submit">Masuk</button>
</form>
