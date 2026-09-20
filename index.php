<?php
// Tampilkan error untuk proses debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

<<<<<<< HEAD
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
=======
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/app/Core/Router.php';

use App\Core\Router;

$router = new Router();

require_once BASE_PATH . '/app/Routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
>>>>>>> d80d0347eedc134252bcd32e4dd4b3210c948e17
