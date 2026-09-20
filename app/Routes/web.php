<?php
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\AdminController;


$router->get('/', [DashboardController::class, 'index'], ['auth']);

$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'],     ['guest']);
$router->get('/logout', [AuthController::class, 'logout'],    ['auth']);

$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

$router->get('/admin', [AdminController::class, 'index'], ['auth', 'staff']);

// Tambah user: hanya admin (petugas tidak boleh)
$router->get('/admin/users/create', [AdminController::class, 'createUser'], ['auth', 'admin']);
$router->post('/admin/users',       [AdminController::class, 'storeUser'],  ['auth', 'admin']);