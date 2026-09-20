<?php
namespace App\Config;

// Membaca bagian 'db' dari config/config.php
class Database
{
    public static function settings(): array
    {
        return (require dirname(__DIR__, 2) . '/config/config.php')['db'];
    }
}
