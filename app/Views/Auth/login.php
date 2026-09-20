<h2>Masuk</h2>
<?php if ($m = flash('error')): ?>

<<<<<<< HEAD
        <!-- Form mengarah ke rute POST /login -->
        <form action="<?= BASE_URL ?>/login" method="POST">
            <div class="input-group">
                <label for="username">Username / NIM</label>
                <!-- Tambahkan atribut name agar data bisa ditangkap oleh $_POST -->
                <input type="text" id="username" name="username" required autocomplete="off">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>
=======
<p class="err">
<?= e($m) ?></p>
<?php endif; ?>
<?php if ($m = flash('success')): ?>
    
<p class="ok"
><?= e($m) ?></p>
<?php endif; ?>

<form method="post" action="<?= url('/login') ?>">
    <input name="nim" placeholder="NIM" required autofocus>
    <input name="password" type="password" placeholder="Password" required>
    <button type="submit">Masuk</button>
</form>
>>>>>>> d80d0347eedc134252bcd32e4dd4b3210c948e17
