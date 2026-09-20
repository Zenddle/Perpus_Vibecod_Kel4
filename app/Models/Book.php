<?php
namespace App\Models;

use App\Core\Database;

class Book
{
    // Daftar buku + kategori + jumlah eksemplar yang sedang dipinjam.
    // tersedia = stok - dipinjam. Buku "Dipinjam" bila tersedia = 0.
    private const SELECT = "SELECT b.*, c.nama_kategori,
        (SELECT COUNT(*) FROM borrowings br WHERE br.book_id = b.id AND br.status = 'approved') AS dipinjam
        FROM books b LEFT JOIN categories c ON c.id = b.category_id";

    public static function all(?int $categoryId = null, string $q = ''): array
    {
        $where = [];
        $args  = [];
        if ($categoryId) {
            $where[] = 'b.category_id = ?';
            $args[]  = $categoryId;
        }
        if ($q !== '') {
            $where[] = '(b.judul LIKE ? OR b.penulis LIKE ?)';
            $args[]  = "%{$q}%";
            $args[]  = "%{$q}%";
        }
        $sql = self::SELECT . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY b.judul';
        $st = Database::connect()->prepare($sql);
        $st->execute($args);
        return array_map([self::class, 'withAvailability'], $st->fetchAll());
    }

    public static function find(int $id): ?array
    {
        $st = Database::connect()->prepare(self::SELECT . ' WHERE b.id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ? self::withAvailability($row) : null;
    }

    private static function withAvailability(array $b): array
    {
        $b['tersedia'] = max(0, (int)$b['stok'] - (int)$b['dipinjam']);
        return $b;
    }

    public static function create(array $d): void
    {
        Database::connect()->prepare(
            'INSERT INTO books (judul, penulis, penerbit, tahun, sinopsis, cover, category_id, stok)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([$d['judul'], $d['penulis'], $d['penerbit'], $d['tahun'], $d['sinopsis'],
                    $d['cover'], $d['category_id'], $d['stok']]);
    }

    public static function update(int $id, array $d): void
    {
        Database::connect()->prepare(
            'UPDATE books SET judul = ?, penulis = ?, penerbit = ?, tahun = ?, sinopsis = ?,
                              cover = ?, category_id = ?, stok = ? WHERE id = ?'
        )->execute([$d['judul'], $d['penulis'], $d['penerbit'], $d['tahun'], $d['sinopsis'],
                    $d['cover'], $d['category_id'], $d['stok'], $id]);
    }

    public static function delete(int $id): void
    {
        Database::connect()->prepare('DELETE FROM books WHERE id = ?')->execute([$id]);
    }
}
