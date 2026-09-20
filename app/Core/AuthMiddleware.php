<?php
namespace App\Core;

class AuthMiddleware
{
    private static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function handle(string $name): void
    {
        self::initSession();

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
        
        // Checks both 'peran' and 'role' to avoid database column mismatch
        $userRole = $_SESSION['user']['peran'] ?? $_SESSION['user']['role'] ?? '';

        if (!in_array($userRole, $allowed, true)) {
            http_response_code(403);
            exit('403 - Akses ditolak');
        }
    }

    // Admin & petugas ke /admin, anggota ke /dashboard
    public static function redirectHome(): never
    {
        self::initSession();
        
        $userRole = $_SESSION['user']['peran'] ?? $_SESSION['user']['role'] ?? '';
        $staff = in_array($userRole, ['admin', 'petugas'], true);
        
        header('Location: ' . url($staff ? '/admin' : '/dashboard'));
        exit;
    }
}