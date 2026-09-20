<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Borrowing;

// Ringkasan, persetujuan, dan riwayat peminjaman
class AdminController extends Controller
{
    public function index(): void
    {
        $this->view('admin/index', [
            'title'    => 'Dashboard Admin',
            'stats'    => Borrowing::stats(),
            'pending'  => Borrowing::pending(),
            'approved' => Borrowing::approvedList(),
            'durasi'   => (int)config('durasi_default', 7),
        ]);
    }

    public function approve(string $id): void
    {
        $hari = (int)($_POST['hari'] ?? 0);
        if ($hari < 1 || $hari > 60) {
            $this->flash('error', 'Lama peminjaman harus 1-60 hari.');
            $this->redirect('/admin');
        }

        $error = Borrowing::approve((int)$id, $hari, $_SESSION['user']['nim']);
        $error ? $this->flash('error', $error) : $this->flash('success', "Peminjaman disetujui ({$hari} hari).");
        $this->redirect('/admin');
    }

    public function reject(string $id): void
    {
        Borrowing::reject((int)$id, $_SESSION['user']['nim'])
            ? $this->flash('success', 'Pengajuan ditolak.')
            : $this->flash('error', 'Pengajuan tidak ditemukan atau sudah diproses.');
        $this->redirect('/admin');
    }

    public function returned(string $id): void
    {
        Borrowing::markReturned((int)$id, $_SESSION['user']['nim'])
            ? $this->flash('success', 'Buku ditandai sudah dikembalikan.')
            : $this->flash('error', 'Data tidak ditemukan atau sudah dikembalikan.');
        $this->redirect('/admin');
    }

    public function history(): void
    {
        $status = $_GET['status'] ?? '';
        $this->view('admin/borrowings', [
            'title'   => 'Riwayat Peminjaman',
            'rows'    => Borrowing::history($status),
            'status'  => $status,
        ]);
    }
}
