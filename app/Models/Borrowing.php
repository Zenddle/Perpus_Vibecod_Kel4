<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Borrowing
{
    public const STATUS_LABEL = [
        'pending'  => 'Menunggu persetujuan',
        'approved' => 'Dipinjam',
        'rejected' => 'Ditolak',
        'returned' => 'Dikembalikan',
    ];

    // ---------- Ringkasan admin ----------
    public static function stats(): array
    {
        $db = Database::connect();
        return [
            'buku'      => (int)$db->query('SELECT COUNT(*) FROM books')->fetchColumn(),
            'siswa'     => (int)$db->query("SELECT COUNT(*) FROM users WHERE peran = 'siswa'")->fetchColumn(),
            'pengajuan' => (int)$db->query("SELECT COUNT(*) FROM borrowings WHERE status = 'pending'")->fetchColumn(),
            'dipinjam'  => (int)$db->query("SELECT COUNT(*) FROM borrowings WHERE status = 'approved'")->fetchColumn(),
        ];
    }

    // ---------- Sisi siswa ----------

    // Jumlah buku yang "memakai jatah" siswa: menunggu persetujuan + sedang dipinjam
    public static function activeCount(string $nim): int
    {
        $st = Database::connect()->prepare(
            "SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND status IN ('pending','approved')"
        );
        $st->execute([$nim]);
        return (int)$st->fetchColumn();
    }

    // [book_id => status] untuk buku yang sedang diajukan/dipinjam siswa
    public static function statusMap(string $nim): array
    {
        $st = Database::connect()->prepare(
            "SELECT book_id, status FROM borrowings WHERE user_id = ? AND status IN ('pending','approved')"
        );
        $st->execute([$nim]);
        return $st->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function forUser(string $nim, string $status): array
    {
        $st = Database::connect()->prepare(
            'SELECT br.*, b.cover FROM borrowings br
             LEFT JOIN books b ON b.id = br.book_id
             WHERE br.user_id = ? AND br.status = ?
             ORDER BY br.tanggal_kembali, br.tanggal_pengajuan DESC'
        );
        $st->execute([$nim, $status]);
        return $st->fetchAll();
    }

    // Boleh pinjam? Mengembalikan [bisa, alasan/label untuk tombol]
    public static function state(array $book, ?string $myStatus, int $activeCount): array
    {
        if ($myStatus === 'approved') return [false, 'Sedang Anda pinjam'];
        if ($myStatus === 'pending')  return [false, 'Menunggu persetujuan'];
        if ($activeCount >= (int)config('max_pinjam', 3)) {
            return [false, 'Batas ' . config('max_pinjam', 3) . ' buku tercapai'];
        }
        if ($book['tersedia'] <= 0) return [false, 'Sedang dipinjam'];
        return [true, 'Pinjam buku'];
    }

    // Ajukan peminjaman. Return pesan error, atau null jika berhasil.
    // Semua aturan dicek ulang di sini supaya tidak bisa ditembus lewat request manual.
    public static function request(string $nim, int $bookId): ?string
    {
        $db = Database::connect();
        $db->beginTransaction();
        try {
            $st = $db->prepare('SELECT nim, nama FROM users WHERE nim = ? FOR UPDATE'); // kunci per siswa
            $st->execute([$nim]);
            $user = $st->fetch();
            if (!$user) { $db->rollBack(); return 'Akun tidak ditemukan.'; }

            $st = $db->prepare('SELECT * FROM books WHERE id = ? FOR UPDATE');
            $st->execute([$bookId]);
            $book = $st->fetch();
            if (!$book) { $db->rollBack(); return 'Buku tidak ditemukan.'; }

            if (self::activeCount($nim) >= (int)config('max_pinjam', 3)) {
                $db->rollBack();
                return 'Batas maksimal ' . config('max_pinjam', 3) . ' buku sudah tercapai.';
            }

            $st = $db->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND book_id = ? AND status IN ('pending','approved')");
            $st->execute([$nim, $bookId]);
            if ((int)$st->fetchColumn() > 0) { $db->rollBack(); return 'Buku ini sudah Anda ajukan atau pinjam.'; }

            $st = $db->prepare("SELECT COUNT(*) FROM borrowings WHERE book_id = ? AND status = 'approved'");
            $st->execute([$bookId]);
            if ((int)$book['stok'] - (int)$st->fetchColumn() <= 0) { $db->rollBack(); return 'Buku sedang dipinjam.'; }

            $db->prepare(
                "INSERT INTO borrowings (user_id, book_id, nim_peminjam, nama_peminjam, judul_buku, status)
                 VALUES (?, ?, ?, ?, ?, 'pending')"
            )->execute([$nim, $bookId, $user['nim'], $user['nama'], $book['judul']]);

            $db->commit();
            return null;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    // ---------- Sisi admin ----------

    public static function pending(): array
    {
        return Database::connect()->query(
            "SELECT br.*, b.stok,
                    (SELECT COUNT(*) FROM borrowings x WHERE x.book_id = br.book_id AND x.status = 'approved') AS dipinjam
             FROM borrowings br LEFT JOIN books b ON b.id = br.book_id
             WHERE br.status = 'pending' ORDER BY br.tanggal_pengajuan"
        )->fetchAll();
    }

    public static function approvedList(): array
    {
        return Database::connect()->query(
            "SELECT * FROM borrowings WHERE status = 'approved' ORDER BY tanggal_kembali, id"
        )->fetchAll();
    }

    public static function history(string $status = ''): array
    {
        $db = Database::connect();
        if (isset(self::STATUS_LABEL[$status])) {
            $st = $db->prepare('SELECT * FROM borrowings WHERE status = ? ORDER BY tanggal_pengajuan DESC, id DESC');
            $st->execute([$status]);
            return $st->fetchAll();
        }
        return $db->query('SELECT * FROM borrowings ORDER BY tanggal_pengajuan DESC, id DESC')->fetchAll();
    }

    // Setujui pengajuan dan tetapkan jatuh tempo. Return pesan error atau null.
    public static function approve(int $id, int $hari, string $adminNim): ?string
    {
        $db = Database::connect();
        $db->beginTransaction();
        try {
            $st = $db->prepare("SELECT * FROM borrowings WHERE id = ? AND status = 'pending' FOR UPDATE");
            $st->execute([$id]);
            $br = $st->fetch();
            if (!$br) { $db->rollBack(); return 'Pengajuan tidak ditemukan atau sudah diproses.'; }
            if (!$br['book_id']) { $db->rollBack(); return 'Buku sudah dihapus.'; }

            $st = $db->prepare('SELECT stok FROM books WHERE id = ? FOR UPDATE');
            $st->execute([$br['book_id']]);
            $stok = (int)$st->fetchColumn();

            $st = $db->prepare("SELECT COUNT(*) FROM borrowings WHERE book_id = ? AND status = 'approved'");
            $st->execute([$br['book_id']]);
            if ($stok - (int)$st->fetchColumn() <= 0) {
                $db->rollBack();
                return 'Stok buku habis, tidak bisa disetujui.';
            }

            $db->prepare(
                "UPDATE borrowings SET status = 'approved', tanggal_pinjam = CURDATE(),
                        tanggal_kembali = DATE_ADD(CURDATE(), INTERVAL ? DAY), diproses_oleh = ?
                 WHERE id = ?"
            )->execute([$hari, $adminNim, $id]);

            $db->commit();
            return null;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
    }

    public static function reject(int $id, string $adminNim): bool
    {
        $st = Database::connect()->prepare(
            "UPDATE borrowings SET status = 'rejected', diproses_oleh = ? WHERE id = ? AND status = 'pending'"
        );
        $st->execute([$adminNim, $id]);
        return $st->rowCount() > 0;
    }

    public static function markReturned(int $id, string $adminNim): bool
    {
        $st = Database::connect()->prepare(
            "UPDATE borrowings SET status = 'returned', tanggal_dikembalikan = CURDATE(), diproses_oleh = ?
             WHERE id = ? AND status = 'approved'"
        );
        $st->execute([$adminNim, $id]);
        return $st->rowCount() > 0;
    }

    public static function activeCountForBook(int $bookId): int
    {
        $st = Database::connect()->prepare(
            "SELECT COUNT(*) FROM borrowings WHERE book_id = ? AND status IN ('pending','approved')"
        );
        $st->execute([$bookId]);
        return (int)$st->fetchColumn();
    }
}
