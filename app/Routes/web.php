<?php
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\BookController;
use App\Controllers\AdminController;
use App\Controllers\AdminUserController;
use App\Controllers\AdminBookController;

$router->get('/', [DashboardController::class, 'home'], ['auth']);

// Login (siswa & admin memakai form yang sama, dipilih lewat tab)
$router->get('/login',  [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'],     ['guest']);
$router->get('/logout', [AuthController::class, 'logout'],    ['auth']);

// ---------- Siswa ----------
$router->get('/dashboard',           [DashboardController::class, 'index'], ['auth', 'siswa']);
$router->get('/books/{id}',          [BookController::class, 'show'],       ['auth', 'siswa']);
$router->post('/books/{id}/borrow',  [BookController::class, 'borrow'],     ['auth', 'siswa']);

// ---------- Admin ----------
$router->get('/admin', [AdminController::class, 'index'], ['auth', 'admin']);

// Persetujuan & riwayat peminjaman
$router->post('/admin/borrowings/{id}/approve', [AdminController::class, 'approve'],  ['auth', 'admin']);
$router->post('/admin/borrowings/{id}/reject',  [AdminController::class, 'reject'],   ['auth', 'admin']);
$router->post('/admin/borrowings/{id}/return',  [AdminController::class, 'returned'], ['auth', 'admin']);
$router->get('/admin/borrowings',               [AdminController::class, 'history'],  ['auth', 'admin']);

// Kelola user
$router->get('/admin/users',                  [AdminUserController::class, 'index'],  ['auth', 'admin']);
$router->get('/admin/users/create',           [AdminUserController::class, 'create'], ['auth', 'admin']);
$router->post('/admin/users',                 [AdminUserController::class, 'store'],  ['auth', 'admin']);
$router->get('/admin/users/{nim}/edit',       [AdminUserController::class, 'edit'],   ['auth', 'admin']);
$router->post('/admin/users/{nim}/update',    [AdminUserController::class, 'update'], ['auth', 'admin']);
$router->post('/admin/users/{nim}/delete',    [AdminUserController::class, 'delete'], ['auth', 'admin']);

// Kelola buku
$router->get('/admin/books',                  [AdminBookController::class, 'index'],  ['auth', 'admin']);
$router->get('/admin/books/create',           [AdminBookController::class, 'create'], ['auth', 'admin']);
$router->post('/admin/books',                 [AdminBookController::class, 'store'],  ['auth', 'admin']);
$router->get('/admin/books/{id}/edit',        [AdminBookController::class, 'edit'],   ['auth', 'admin']);
$router->post('/admin/books/{id}/update',     [AdminBookController::class, 'update'], ['auth', 'admin']);
$router->post('/admin/books/{id}/delete',     [AdminBookController::class, 'delete'], ['auth', 'admin']);
