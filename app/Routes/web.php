<?php
// --- RUTE AUTENTIKASI ---
Route::get('', ['AuthController', 'index']);           // Halaman utama (Login)
Route::post('login', ['AuthController', 'loginProcess']); // Proses form login
Route::get('logout', ['AuthController', 'logout']);       // Proses logout

// --- RUTE DASHBOARD ---
Route::get('dashboard', ['DashboardController', 'index']);