<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Borrowing;

class AdminController extends Controller
{
    // 1. Method index() yang sudah digabungkan secara utuh
    public function index(): void
    {
        $this->view('admin/index', [
            'title' => 'Dashboard Admin',
            'stats' => Borrowing::stats(),
            'users' => User::all(),
        ]);
    }

    // 2. Tampilkan form tambah user
    public function createUser(): void
    {
        $this->view('admin/create_user', ['title' => 'Tambah User']);
        unset($_SESSION['old']); // input lama hanya dipakai sekali
    }

    // 3. Proses form tambah user
    public function storeUser(): void
    {
        $nim      = trim($_POST['nim'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $password = $_POST['password'] ?? '';
        $peran    = $_POST['peran'] ?? '';

        $error = null;
        if ($nim === '' || $nama === '' || $password === '') {
            $error = 'NIM, nama, dan password wajib diisi.';
        } elseif (!preg_match('/^[A-Za-z0-9]{1,20}$/', $nim)) {
            $error = 'NIM hanya huruf/angka, maksimal 20 karakter.';
        } elseif (strlen($nama) > 150) {
            $error = 'Nama maksimal 150 karakter.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } elseif (!in_array($peran, ['admin', 'petugas', 'anggota'], true)) {
            $error = 'Peran tidak valid.';
        } elseif (User::findByNim($nim)) {
            $error = "NIM {$nim} sudah terdaftar.";
        }

        if ($error) {
            $_SESSION['old'] = ['nim' => $nim, 'nama' => $nama, 'peran' => $peran]; // password tidak diisi ulang
            $this->flash('error', $error);
            $this->redirect('/admin/users/create');
        }

        User::create($nim, $nama, $password, $peran);
        $this->flash('success', "User {$nama} ({$nim}) berhasil ditambahkan.");
        $this->redirect('/admin');
    }
}