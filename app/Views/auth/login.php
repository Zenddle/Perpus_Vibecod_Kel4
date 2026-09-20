<h2>Masuk</h2>
<?php if ($m = flash('error')): ?><p class="err"><?= e($m) ?></p><?php endif; ?>
<?php if ($m = flash('success')): ?><p class="ok"><?= e($m) ?></p><?php endif; ?>
<form method="post" action="<?= url('/login') ?>">
    <input name="nim" placeholder="NIM" required autofocus>
    <input name="password" type="password" placeholder="Password" required>
    <button type="submit">Masuk</button>
</form>
