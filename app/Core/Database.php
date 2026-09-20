<?php
namespace App\Core;

use PDO;
use App\Config\Database as DbConfig;

// Koneksi PDO tunggal (singleton)
class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $c = DbConfig::settings();
            self::$pdo = new PDO(
                "mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4",
                $c['user'],
                $c['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        return self::$pdo;
    }
}
