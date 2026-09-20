<?php
namespace App\Models;

use App\Core\Database;

class User
{
    public static function findByNim(string $nim): ?array
    {
        $st = Database::connect()->prepare('SELECT * FROM users WHERE nim = ? LIMIT 1');
        $st->execute([$nim]);
        return $st->fetch() ?: null;
    }

    public static function all(): array
    {
        return Database::connect()->query('SELECT nim, nama, peran FROM users ORDER BY nama')->fetchAll();
    }
}
