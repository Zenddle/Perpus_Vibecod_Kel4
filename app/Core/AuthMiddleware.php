<?php
namespace App\Core;

class AuthMiddleware
{
    public static function handle(string $name): void
    {
        match ($name) {
            'guest' => self::guest(),
            'auth'  => self::auth(),
            'admin' => self::role(['admin']),
            'staff' => self::role(['admin', 'petugas']),
            default => null,
        };
    }

    // Hanya untuk yang belum login
    private static function guest(): void
    {
        if (isset($_SESSION['user'])) {
            self::redirectHome();
        }
    }

    // Harus sudah login
    private static function auth(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . url('/login'));
            exit;
        }
    }

    // Harus login dan punya salah satu peran
    private static function role(array $allowed): void
    {
        self::auth();
        if (!in_array($_SESSION['user']['peran'], $allowed, true)) {
            http_response_code(403);
            exit('403 - Akses ditolak');
        }
    }

    // Admin & petugas ke /admin, anggota ke /dashboard
    public static function redirectHome(): never
    {
        $staff = in_array($_SESSION['user']['peran'] ?? '', ['admin', 'petugas'], true);
        header('Location: ' . url($staff ? '/admin' : '/dashboard'));
        exit;
    }
}
