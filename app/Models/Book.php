<?php
namespace App\Models;

use App\Core\Database;

class Book
{
    // Buku aktif beserta nama kategorinya
    public static function allActive(): array
    {
        return Database::connect()->query(
            "SELECT b.id, b.judul, b.stok, b.status, c.nama_kategori
             FROM books b
             LEFT JOIN categories c ON c.id = b.category_id
             WHERE b.status <> 'nonaktif'
             ORDER BY b.judul"
        )->fetchAll();
    }
}
