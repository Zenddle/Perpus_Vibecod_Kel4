<?php
// Front controller: semua request masuk lewat sini (lihat .htaccess)
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

$config = require ROOT_PATH . '/config/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Makassar');
if ($config['app']['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// ---------- Helper global ----------
function config(string $key, $default = null)
{
    global $config;
    return $config['app'][$key] ?? $default;
}
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

// CSRF: token per sesi, dicek Router untuk semua POST
function csrf_token(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrf_field(): string {
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

// Panjang teks (aman walau ekstensi mbstring tidak aktif)
function len(string $s): int {
    return function_exists('mb_strlen') ? mb_strlen($s) : strlen($s);
}

// Tanggal berbahasa Indonesia, mis. 12 Mar 2026
function tgl(?string $date): string {
    if (!$date) return '-';
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $t = strtotime($date);
    return date('j', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

// Sisa waktu peminjaman: [teks, kelas css]
function sisa_info(?string $jatuhTempo): array {
    if (!$jatuhTempo) return ['-', ''];
    $selisih = (int)((strtotime($jatuhTempo) - strtotime(date('Y-m-d'))) / 86400);
    if ($selisih > 1)  return ["Sisa {$selisih} hari", $selisih <= 2 ? 'warn' : 'ok'];
    if ($selisih === 1) return ['Sisa 1 hari', 'warn'];
    if ($selisih === 0) return ['Jatuh tempo hari ini', 'warn'];
    return ['Terlambat ' . abs($selisih) . ' hari', 'err'];
}

function cover_url(?string $file): ?string {
    return $file ? url('uploads/covers/' . $file) : null;
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
