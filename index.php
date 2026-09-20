<?php
// Tampilkan error untuk proses debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mulai sesi
if (!session_id()) session_start();

// 1. Muat Konfigurasi
require_once 'config/config.php';

// 2. Muat Core System (Urutan sangat penting)
require_once 'app/Core/Database.php';
require_once 'app/Core/Controller.php';
require_once 'app/Core/Router.php'; 

// 3. Muat Definisi Rute
require_once 'app/Routes/web.php';

// 4. Jalankan Aplikasi
Route::run();