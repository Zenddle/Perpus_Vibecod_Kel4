<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\User;

class AuthController extends Controller
{
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
    }

    public function login(): void
    {
        $this->startSessionIfNeeded();

        $nim      = trim($_POST['nim'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByNim($nim);

        // Verify user exists and password matches BCRYPT hash
        if (!$user || !password_verify($password, $user['password'])) {
            $this->flash('error', 'NIM atau password salah.');
            $this->redirect('/login');
            exit; // Ensure script stops after redirect
        }

        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;

        AuthMiddleware::redirectHome();
    }

    public function logout(): void
    {
        $this->startSessionIfNeeded();
        
        $_SESSION = [];
        session_destroy();

        // Start new session to store the logout flash message
        session_start();
        $this->flash('success', 'Anda sudah keluar.');
        $this->redirect('/login');
        exit;
    }
}