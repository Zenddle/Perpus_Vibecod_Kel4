<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Borrowing;

class AdminController extends Controller
{
    public function index(): void
    {
        $this->view('admin/index', [
            'title' => 'Dashboard Admin',
            'stats' => Borrowing::stats(),
            'users' => User::all(),
        ]);
    }
}
