<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Masuk'], 'guest');
    }

    public function login(): void
    {
        $nim      = trim($_POST['nim'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByNim($nim);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->flash('error', 'NIM atau password salah.');
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user; // nim, nama, peran

        AuthMiddleware::redirectHome();
    }

    public function logout(): void
    {
        session_destroy();
        session_start();
        $this->flash('success', 'Anda sudah keluar.');
        $this->redirect('/login');
    }
}
