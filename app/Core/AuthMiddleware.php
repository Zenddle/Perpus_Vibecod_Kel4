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
            'siswa' => self::siswa(),
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

        if (!in_array($_SESSION['user']['peran'] ?? '', $allowed, true)) {
            http_response_code(403);
            exit('403 - Akses ditolak');
        }
    }

    // Halaman siswa: admin diarahkan ke dashboard admin
    private static function siswa(): void
    {
        self::auth();

        if (($_SESSION['user']['peran'] ?? '') !== 'siswa') {
            self::redirectHome();
        }
    }

    // Admin ke /admin, siswa ke /dashboard
    public static function redirectHome(): never
    {
        self::initSession();

        $isAdmin = ($_SESSION['user']['peran'] ?? '') === 'admin';
        header('Location: ' . url($isAdmin ? '/admin' : '/dashboard'));
        exit;
    }
}
