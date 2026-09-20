<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Borrowing;

// Detail buku & pengajuan pinjam (siswa)
class BookController extends Controller
{
    public function show(string $id): void
    {
        $book = Book::find((int)$id) ?? $this->notFound();
        $nim  = $_SESSION['user']['nim'];

        $statusMap = Borrowing::statusMap($nim);
        $aktif     = Borrowing::activeCount($nim);
        $myStatus  = $statusMap[$book['id']] ?? null;
        [$bisa, $label] = Borrowing::state($book, $myStatus, $aktif);

        // Jika sedang dipinjam siswa ini, tampilkan jatuh temponya
        $pinjaman = null;
        if ($myStatus === 'approved') {
            foreach (Borrowing::forUser($nim, 'approved') as $p) {
                if ((int)$p['book_id'] === (int)$book['id']) { $pinjaman = $p; break; }
            }
        }

        $this->view('books/show', [
            'title'     => $book['judul'],
            'book'      => $book,
            'bisa'      => $bisa,
            'label'     => $label,
            'myStatus'  => $myStatus,
            'pinjaman'  => $pinjaman,
            'aktif'     => $aktif,
            'max'       => (int)config('max_pinjam', 3),
        ]);
    }

    public function borrow(string $id): void
    {
        $error = Borrowing::request($_SESSION['user']['nim'], (int)$id);

        if ($error) {
            $this->flash('error', $error);
        } else {
            $this->flash('success', 'Pengajuan terkirim. Tunggu persetujuan admin.');
        }
        $this->redirect('/books/' . (int)$id);
    }
}
