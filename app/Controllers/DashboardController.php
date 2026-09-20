<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;

// Dashboard siswa
class DashboardController extends Controller
{
    public function home(): void
    {
        AuthMiddleware::redirectHome();
    }

    public function index(): void
    {
        $user = $_SESSION['user'];
        $nim  = $user['nim'];

        $kategori = ctype_digit($_GET['kategori'] ?? '') ? (int)$_GET['kategori'] : null;
        $q        = trim($_GET['q'] ?? '');

        $statusMap = Borrowing::statusMap($nim);
        $aktif     = Borrowing::activeCount($nim);

        $books = Book::all($kategori, $q);
        foreach ($books as &$b) {
            [$b['bisa_pinjam'], $b['label']] = Borrowing::state($b, $statusMap[$b['id']] ?? null, $aktif);
            $b['status_saya'] = $statusMap[$b['id']] ?? null;
        }
        unset($b);

        $this->view('dashboard/index', [
            'title'      => 'Dashboard',
            'user'       => $user,
            'books'      => $books,
            'categories' => Category::all(),
            'kategori'   => $kategori,
            'q'          => $q,
            'aktif'      => $aktif,
            'max'        => (int)config('max_pinjam', 3),
            'dipinjam'   => Borrowing::forUser($nim, 'approved'),
            'menunggu'   => Borrowing::forUser($nim, 'pending'),
        ]);
    }
}
