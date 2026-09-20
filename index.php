<?php
// Front controller: semua request masuk lewat sini (lihat .htaccess)
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

$config = require ROOT_PATH . '/config/config.php';
if ($config['app']['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// Helper global
function url(string $path = ''): string { return BASE_PATH . '/' . ltrim($path, '/'); }
function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function flash(string $key): ?string {
    $m = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $m;
}

// Isi ulang form setelah gagal validasi (sekali pakai)
function old(string $key, string $default = ''): string {
    return (string)($_SESSION['old'][$key] ?? $default);
}

// Autoload: App\Core\Router => app/Core/Router.php
spl_autoload_register(function (string $class) {
    if (str_starts_with($class, 'App\\')) {
        $file = APP_PATH . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) require $file;
    }
});

$router = new App\Core\Router();
require APP_PATH . '/Routes/web.php';
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

//test kontribusi