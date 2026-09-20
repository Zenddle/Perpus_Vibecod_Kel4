<!-- app/Views/auth/login.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpus Vibecod</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #0d0d0d; color: #f0f0f0; }
        .login-box { background-color: #1a1a1a; padding: 2.5rem; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .login-box h2 { margin-top: 0; text-align: center; color: #ffffff; margin-bottom: 1.5rem; }
        
        /* Notifikasi Error */
        .alert-error { background-color: #ff4d4d; color: white; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; font-size: 0.9rem; }
        
        .input-group { margin-bottom: 1.2rem; }
        .input-group label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; color: #aaaaaa; }
        .input-group input { width: 100%; padding: 0.8rem; border: 1px solid #333; background: #262626; color: #fff; border-radius: 6px; outline: none; }
        .input-group input:focus { border-color: #5b9dd9; }
        .btn-login { width: 100%; padding: 0.8rem; background-color: #ffffff; color: #000000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .btn-login:hover { background-color: #e0e0e0; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Masuk Perpustakaan</h2>
        
        <!-- Menampilkan pesan error dari controller jika ada -->
        <?php if (isset($data['error'])): ?>
            <div class="alert-error">
                <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Form mengarah ke rute POST /login -->
        <form action="/login" method="POST">
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