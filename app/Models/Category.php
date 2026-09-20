<?php
namespace App\Models;

use App\Core\Database;

class Category
{
    public static function all(): array
    {
        return Database::connect()
            ->query('SELECT id, nama_kategori FROM categories ORDER BY nama_kategori')
            ->fetchAll();
    }

    // Ambil id kategori berdasarkan nama; buat baru jika belum ada
    public static function findOrCreate(string $nama): int
    {
        $db = Database::connect();
        $st = $db->prepare('SELECT id FROM categories WHERE nama_kategori = ?');
        $st->execute([$nama]);
        if ($id = $st->fetchColumn()) {
            return (int)$id;
        }
        $db->prepare('INSERT INTO categories (nama_kategori) VALUES (?)')->execute([$nama]);
        return (int)$db->lastInsertId();
    }
}
