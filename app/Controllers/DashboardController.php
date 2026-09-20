<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Borrowing;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = $_SESSION['user'];
        $this->view('dashboard/index', [
            'title'      => 'Dashboard',
            'user'       => $user,
            'books'      => Book::allActive(),
            'borrowings' => Borrowing::byUser($user['nim']),
        ]);
    }
}
