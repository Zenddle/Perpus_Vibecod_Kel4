<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function findByNim(string $nim): ?array
    {
        $st = Database::connect()->prepare('SELECT * FROM users WHERE nim = ? LIMIT 1');
        $st->execute([trim($nim)]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function all(): array
    {
        return Database::connect()
            ->query("SELECT u.nim, u.nama, u.peran,
                            (SELECT COUNT(*) FROM borrowings b
                              WHERE b.user_id = u.nim AND b.status IN ('pending','approved')) AS aktif
                     FROM users u ORDER BY u.peran, u.nama")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(string $nim, string $nama, string $password, string $peran): void
    {
        $st = Database::connect()->prepare(
            'INSERT INTO users (nim, nama, password, peran) VALUES (?, ?, ?, ?)'
        );
        $st->execute([trim($nim), trim($nama), password_hash($password, PASSWORD_DEFAULT), $peran]);
    }

    // Password hanya diganti jika $password tidak kosong
    public static function update(string $nim, string $nama, string $peran, string $password = ''): void
    {
        $db = Database::connect();
        if ($password !== '') {
            $db->prepare('UPDATE users SET nama = ?, peran = ?, password = ? WHERE nim = ?')
               ->execute([trim($nama), $peran, password_hash($password, PASSWORD_DEFAULT), $nim]);
        } else {
            $db->prepare('UPDATE users SET nama = ?, peran = ? WHERE nim = ?')
               ->execute([trim($nama), $peran, $nim]);
        }
    }

    public static function delete(string $nim): void
    {
        Database::connect()->prepare('DELETE FROM users WHERE nim = ?')->execute([$nim]);
    }

    public static function countAdmins(): int
    {
        return (int)Database::connect()->query("SELECT COUNT(*) FROM users WHERE peran = 'admin'")->fetchColumn();
    }
}
