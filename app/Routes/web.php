<?php
<<<<<<< HEAD
// --- RUTE AUTENTIKASI ---
Route::get('', ['AuthController', 'index']);           // Halaman utama (Login)
Route::post('login', ['AuthController', 'loginProcess']); // Proses form login
Route::get('logout', ['AuthController', 'logout']);       // Proses logout

// --- RUTE DASHBOARD ---
Route::get('dashboard', ['DashboardController', 'index']);
=======
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\AdminController;


$router->get('/', [DashboardController::class, 'index'], ['auth']);

$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'],     ['guest']);
$router->get('/logout', [AuthController::class, 'logout'],    ['auth']);

$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

$router->get('/admin', [AdminController::class, 'index'], ['auth', 'staff']);

>>>>>>> d80d0347eedc134252bcd32e4dd4b3210c948e17
