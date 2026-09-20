<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\User;

class AuthController extends Controller
{
    // Hash palsu agar waktu respons sama, baik NIM ada maupun tidak
    private const DUMMY_HASH = '$2y$10$X6Gz3qM5Rj0IoaWJbZFfzOnN5mKvxZvip7smzCskfjbEtvG.7BvKm';

    private function startSessionIfNeeded(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showLogin(): void
    {
        $this->startSessionIfNeeded();
        $this->view('auth/login', ['title' => 'Masuk'], 'guest');
        unset($_SESSION['old']); // input lama hanya dipakai sekali
    }

    public function login(): void
    {
        $this->startSessionIfNeeded();

        $nim      = trim($_POST['nim'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($nim === '' || $password === '') {
            $_SESSION['old'] = ['nim' => $nim];
            $this->flash('error', 'NIM dan password wajib diisi.');
            $this->redirect('/login');
        }

        $user = User::findByNim($nim);
        $hash = $user['password'] ?? self::DUMMY_HASH;

        // password_verify selalu dijalankan supaya tidak bisa menebak NIM lewat waktu respons
        if (!password_verify($password, $hash) || !$user) {
            $_SESSION['old'] = ['nim' => $nim]; // password tidak diisi ulang
            $this->flash('error', 'NIM atau password salah.');
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        unset($user['password'], $_SESSION['old']);
        $_SESSION['user'] = $user;

        AuthMiddleware::redirectHome();
    }

    public function logout(): void
    {
        $this->startSessionIfNeeded();

        $_SESSION = [];
        session_destroy();

        // Sesi baru untuk menampung pesan logout
        session_start();
        $this->flash('success', 'Anda sudah keluar.');
        $this->redirect('/login');
    }
}
