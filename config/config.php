<?php
<<<<<<< HEAD
// Sesuaikan nama folder di bawah ini dengan nama folder proyek Anda di htdocs
define('BASE_URL', 'http://localhost/Perpus_Vibecod_Kel4');

// Biarkan define BASE_PATH jika sebelumnya sudah ada
define('BASE_PATH', dirname(__DIR__)); 

// Konfigurasi Database (Jika belum ada)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpustakaan');
=======
namespace App\Config;

// Membaca bagian 'db' dari config/config.php
class Database
{
    public static function settings(): array
    {
        return (require dirname(__DIR__, 2) . '/config/config.php')['db'];
    }
}
>>>>>>> d80d0347eedc134252bcd32e4dd4b3210c948e17
