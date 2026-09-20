<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function findByNim(string $nim): ?array
    {
        $db = Database::connect();
        $st = $db->prepare('SELECT * FROM users WHERE nim = ? LIMIT 1');
        $st->execute([trim($nim)]);
        
        $user = $st->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function all(): array
    {
        return Database::connect()
            ->query('SELECT nim, nama, peran FROM users ORDER BY nama')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(string $nim, string $nama, string $password, string $peran): void
    {
        $st = Database::connect()->prepare(
            'INSERT INTO users (nim, nama, password, peran) VALUES (?, ?, ?, ?)'
        );
        $st->execute([
            trim($nim), 
            trim($nama), 
            password_hash($password, PASSWORD_DEFAULT), 
            $peran
        ]);
    }
}