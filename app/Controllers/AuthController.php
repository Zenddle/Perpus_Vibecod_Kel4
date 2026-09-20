<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\User;

class AuthController extends Controller
{
    // Hash palsu agar waktu respons sama, baik NIM ada maupun tidak
    private const DUMMY_HASH = '$2y$10$X6Gz3qM5Rj0IoaWJbZFfzOnN5mKvxZvip7smzCskfjbEtvG.7BvKm';

    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Masuk'], 'guest');
        unset($_SESSION['old']); // input lama hanya dipakai sekali
    }

    public function login(): void
    {
        $nim      = trim($_POST['nim'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $sebagai  = ($_POST['sebagai'] ?? '') === 'admin' ? 'admin' : 'siswa';

        if ($nim === '' || $password === '') {
            $_SESSION['old'] = ['nim' => $nim, 'sebagai' => $sebagai];
            $this->flash('error', 'ID dan password wajib diisi.');
            $this->redirect('/login');
        }

        $user = User::findByNim($nim);
        $hash = $user['password'] ?? self::DUMMY_HASH;

        // password_verify selalu dijalankan supaya tidak bisa menebak NIM lewat waktu respons
        if (!password_verify($password, $hash) || !$user) {
            $_SESSION['old'] = ['nim' => $nim, 'sebagai' => $sebagai]; // password tidak diisi ulang
            $this->flash('error', 'ID atau password salah.');
            $this->redirect('/login');
        }

        // Login harus sesuai pilihan: akun siswa tidak bisa masuk lewat tab admin, dan sebaliknya
        if ($user['peran'] !== $sebagai) {
            $_SESSION['old'] = ['nim' => $nim, 'sebagai' => $sebagai];
            $this->flash('error', $sebagai === 'admin'
                ? 'Akun ini bukan akun admin. Pilih "Siswa" untuk masuk.'
                : 'Akun ini adalah akun admin. Pilih "Admin" untuk masuk.');
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        unset($user['password'], $_SESSION['old']);
        $_SESSION['user'] = $user;

        AuthMiddleware::redirectHome();
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();

        // Sesi baru untuk menampung pesan logout
        session_start();
        $this->flash('success', 'Anda sudah keluar.');
        $this->redirect('/login');
    }
}
