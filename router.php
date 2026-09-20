<?php
// Hanya untuk server bawaan PHP:  php -S localhost:8000 router.php
// (Di Apache/XAMPP tidak dipakai; .htaccess yang mengatur.)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/(app|config|database)/#', $path)) {
    http_response_code(403);
    exit('403');
}
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file) && !str_ends_with($file, '.php')) {
    return false; // sajikan file statis (css, cover)
}
require __DIR__ . '/index.php';
