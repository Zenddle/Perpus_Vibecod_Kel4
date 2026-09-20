<?php
namespace App\Models;

use App\Core\Database;

class Borrowing
{
    // Ringkasan angka untuk dashboard admin/petugas
    public static function stats(): array
    {
        $db = Database::connect();
        return [
            'buku'        => (int)$db->query('SELECT COUNT(*) FROM books')->fetchColumn(),
            'anggota'     => (int)$db->query("SELECT COUNT(*) FROM users WHERE peran = 'anggota'")->fetchColumn(),
            'pengajuan'   => (int)$db->query("SELECT COUNT(*) FROM borrow_requests WHERE status = 'pending'")->fetchColumn(),
            'dipinjam'    => (int)$db->query("SELECT COUNT(*) FROM borrowings WHERE status = 'approved'")->fetchColumn(),
        ];
    }

    // Peminjaman milik satu anggota
    public static function byUser(string $nim): array
    {
        $st = Database::connect()->prepare(
            'SELECT br.*, b.judul FROM borrowings br
             JOIN books b ON b.id = br.book_id
             WHERE br.user_id = ? ORDER BY br.tanggal_pinjam DESC'
        );
        $st->execute([$nim]);
        return $st->fetchAll();
    }
}
