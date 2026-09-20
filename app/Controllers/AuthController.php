<?php
<<<<<<< HEAD
require_once __DIR__ . '/../Core/Controller.php';

class AuthController extends Controller {
    
    // Method default yang dipanggil saat pertama kali membuka web
    public function index() {
        // Mengarahkan ke file tampilan login di app/Views/Auth/login.php
        $this->view('Auth/login');
    }

    // Method untuk memproses data dari form login
    public function loginProcess() {
        // Logika pengecekan username dan password ke database akan diisi di sini nanti
    }

    // Method untuk keluar dari akun
    public function logout() {
        if (!session_id()) session_start();
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }
}
=======
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
>>>>>>> d80d0347eedc134252bcd32e4dd4b3210c948e17
