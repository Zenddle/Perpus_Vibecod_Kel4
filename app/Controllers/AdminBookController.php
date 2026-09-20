<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;

// CRUD buku (admin)
class AdminBookController extends Controller
{
    private const MAX_COVER = 2 * 1024 * 1024; // 2 MB
    private const COVER_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public function index(): void
    {
        $this->view('admin/books', ['title' => 'Kelola Buku', 'books' => Book::all()]);
    }

    public function create(): void
    {
        $this->view('admin/book_form', [
            'title' => 'Tambah Buku', 'edit' => null, 'categories' => Category::all(),
        ]);
        unset($_SESSION['old']);
    }

    public function store(): void
    {
        [$data, $error] = $this->collect(null);
        if ($error) {
            $this->failForm($error, '/admin/books/create');
        }
        Book::create($data);
        $this->flash('success', "Buku \"{$data['judul']}\" ditambahkan.");
        $this->redirect('/admin/books');
    }

    public function edit(string $id): void
    {
        $book = Book::find((int)$id) ?? $this->notFound();
        $this->view('admin/book_form', [
            'title' => 'Edit Buku', 'edit' => $book, 'categories' => Category::all(),
        ]);
        unset($_SESSION['old']);
    }

    public function update(string $id): void
    {
        $book = Book::find((int)$id) ?? $this->notFound();
        [$data, $error] = $this->collect($book);
        if ($error) {
            $this->failForm($error, "/admin/books/{$book['id']}/edit");
        }

        // stok tidak boleh lebih kecil dari eksemplar yang sedang dipinjam
        if ($data['stok'] < (int)$book['dipinjam']) {
            $this->deleteCover($data['cover'] !== $book['cover'] ? $data['cover'] : null);
            $this->failForm("Stok tidak boleh di bawah jumlah yang sedang dipinjam ({$book['dipinjam']}).",
                            "/admin/books/{$book['id']}/edit");
        }

        Book::update((int)$book['id'], $data);
        if ($data['cover'] !== $book['cover']) {
            $this->deleteCover($book['cover']); // cover lama diganti
        }
        $this->flash('success', "Buku \"{$data['judul']}\" diperbarui.");
        $this->redirect('/admin/books');
    }

    public function delete(string $id): void
    {
        $book = Book::find((int)$id) ?? $this->notFound();

        if (Borrowing::activeCountForBook((int)$book['id']) > 0) {
            $this->flash('error', 'Buku masih diajukan/dipinjam, tidak bisa dihapus.');
        } else {
            Book::delete((int)$book['id']);
            $this->deleteCover($book['cover']);
            $this->flash('success', "Buku \"{$book['judul']}\" dihapus.");
        }
        $this->redirect('/admin/books');
    }

    // ---------- Helper ----------

    private function failForm(string $error, string $back): never
    {
        $_SESSION['old'] = array_intersect_key($_POST, array_flip(
            ['judul', 'penulis', 'penerbit', 'tahun', 'sinopsis', 'category_id', 'kategori_baru', 'stok']
        ));
        $this->flash('error', $error);
        $this->redirect($back);
    }

    // Validasi input + upload cover. Return [data, error].
    private function collect(?array $existing): array
    {
        $judul    = trim($_POST['judul'] ?? '');
        $penulis  = trim($_POST['penulis'] ?? '');
        $penerbit = trim($_POST['penerbit'] ?? '');
        $tahun    = trim($_POST['tahun'] ?? '');
        $sinopsis = trim($_POST['sinopsis'] ?? '');
        $stok     = trim($_POST['stok'] ?? '');
        $baru     = trim($_POST['kategori_baru'] ?? '');
        $catId    = ctype_digit($_POST['category_id'] ?? '') ? (int)$_POST['category_id'] : null;

        $err = fn(string $m) => [null, $m];

        if ($judul === '' || $penulis === '') return $err('Judul dan penulis wajib diisi.');
        if (len($judul) > 200 || len($penulis) > 150 || len($penerbit) > 150) {
            return $err('Judul maksimal 200 karakter; penulis dan penerbit maksimal 150.');
        }
        if ($tahun !== '' && (!ctype_digit($tahun) || (int)$tahun < 1000 || (int)$tahun > (int)date('Y') + 1)) {
            return $err('Tahun terbit tidak valid.');
        }
        if (!ctype_digit($stok) || (int)$stok < 1 || (int)$stok > 999) {
            return $err('Stok harus angka 1-999.');
        }
        if ($baru !== '') {
            if (len($baru) > 100) return $err('Nama kategori baru maksimal 100 karakter.');
            $catId = Category::findOrCreate($baru);
        }

        [$cover, $coverErr] = $this->handleCover($existing['cover'] ?? null);
        if ($coverErr) return $err($coverErr);

        return [[
            'judul' => $judul, 'penulis' => $penulis, 'penerbit' => $penerbit,
            'tahun' => $tahun === '' ? null : (int)$tahun,
            'sinopsis' => $sinopsis, 'cover' => $cover,
            'category_id' => $catId, 'stok' => (int)$stok,
        ], null];
    }

    // Return [nama file cover, error]. Tanpa upload baru, cover lama dipertahankan.
    private function handleCover(?string $current): array
    {
        $f = $_FILES['cover'] ?? null;
        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) return [$current, null];
        if ($f['error'] !== UPLOAD_ERR_OK)              return [null, 'Upload cover gagal. Coba lagi.'];
        if ($f['size'] > self::MAX_COVER)               return [null, 'Ukuran cover maksimal 2 MB.'];

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        if (!isset(self::COVER_TYPES[$mime]))           return [null, 'Cover harus berformat JPG, PNG, atau WebP.'];

        $dir = ROOT_PATH . '/uploads/covers';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = bin2hex(random_bytes(10)) . '.' . self::COVER_TYPES[$mime];
        if (!move_uploaded_file($f['tmp_name'], "{$dir}/{$name}")) {
            return [null, 'Cover tidak bisa disimpan. Periksa izin folder uploads/covers.'];
        }
        return [$name, null];
    }

    private function deleteCover(?string $file): void
    {
        if ($file && preg_match('/^[a-f0-9]+\.(jpg|png|webp)$/', $file)) {
            @unlink(ROOT_PATH . '/uploads/covers/' . $file);
        }
    }
}
