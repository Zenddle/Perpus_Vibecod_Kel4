<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

// CRUD user (admin)
class AdminUserController extends Controller
{
    public function index(): void
    {
        $this->view('admin/users', ['title' => 'Kelola User', 'users' => User::all()]);
    }

    public function create(): void
    {
        $this->view('admin/user_form', ['title' => 'Tambah User', 'edit' => null]);
        unset($_SESSION['old']); // input lama hanya dipakai sekali
    }

    public function store(): void
    {
        $nim      = trim($_POST['nim'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $password = $_POST['password'] ?? '';
        $peran    = $_POST['peran'] ?? '';

        $error = null;
        if ($nim === '' || $nama === '' || $password === '') {
            $error = 'ID (NIS/NIM), nama, dan password wajib diisi.';
        } elseif (!preg_match('/^[A-Za-z0-9]{1,20}$/', $nim)) {
            $error = 'ID hanya huruf/angka, maksimal 20 karakter.';
        } elseif (len($nama) > 150) {
            $error = 'Nama maksimal 150 karakter.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } elseif (!in_array($peran, ['admin', 'siswa'], true)) {
            $error = 'Peran tidak valid.';
        } elseif (User::findByNim($nim)) {
            $error = "ID {$nim} sudah terdaftar.";
        }

        if ($error) {
            $_SESSION['old'] = ['nim' => $nim, 'nama' => $nama, 'peran' => $peran]; // password tidak diisi ulang
            $this->flash('error', $error);
            $this->redirect('/admin/users/create');
        }

        User::create($nim, $nama, $password, $peran);
        $this->flash('success', "User {$nama} ({$nim}) berhasil ditambahkan.");
        $this->redirect('/admin/users');
    }

    public function edit(string $nim): void
    {
        $user = User::findByNim($nim) ?? $this->notFound();
        $this->view('admin/user_form', ['title' => 'Edit User', 'edit' => $user]);
        unset($_SESSION['old']);
    }

    public function update(string $nim): void
    {
        $user = User::findByNim($nim) ?? $this->notFound();

        $nama     = trim($_POST['nama'] ?? '');
        $password = $_POST['password'] ?? '';
        $peran    = $_POST['peran'] ?? '';

        $error = null;
        if ($nama === '') {
            $error = 'Nama wajib diisi.';
        } elseif (len($nama) > 150) {
            $error = 'Nama maksimal 150 karakter.';
        } elseif ($password !== '' && strlen($password) < 6) {
            $error = 'Password baru minimal 6 karakter.';
        } elseif (!in_array($peran, ['admin', 'siswa'], true)) {
            $error = 'Peran tidak valid.';
        } elseif ($user['peran'] === 'admin' && $peran !== 'admin' && User::countAdmins() <= 1) {
            $error = 'Tidak bisa mengubah admin terakhir menjadi siswa.';
        }

        if ($error) {
            $_SESSION['old'] = ['nama' => $nama, 'peran' => $peran];
            $this->flash('error', $error);
            $this->redirect('/admin/users/' . $user['nim'] . '/edit');
        }

        User::update($user['nim'], $nama, $peran, $password);

        // Jika mengedit akun sendiri, perbarui sesi
        if ($_SESSION['user']['nim'] === $user['nim']) {
            $_SESSION['user']['nama']  = $nama;
            $_SESSION['user']['peran'] = $peran;
        }

        $this->flash('success', "User {$nama} diperbarui.");
        $this->redirect('/admin/users');
    }

    public function delete(string $nim): void
    {
        $user = User::findByNim($nim) ?? $this->notFound();

        if ($user['nim'] === $_SESSION['user']['nim']) {
            $this->flash('error', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
        } elseif (\App\Models\Borrowing::activeCount($user['nim']) > 0) {
            $this->flash('error', "{$user['nama']} masih punya pengajuan/peminjaman aktif. Selesaikan dulu.");
        } else {
            User::delete($user['nim']);
            $this->flash('success', "User {$user['nama']} dihapus. Riwayat peminjamannya tetap tersimpan.");
        }
        $this->redirect('/admin/users');
    }
}
