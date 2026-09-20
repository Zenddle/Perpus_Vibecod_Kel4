<?php
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