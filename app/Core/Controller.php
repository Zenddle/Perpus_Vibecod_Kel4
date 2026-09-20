<?php
namespace App\Core;

class Controller
{
    // Render view di dalam layout. $view contoh: 'auth/login'
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        ob_start();
        require APP_PATH . "/Views/{$view}.php";
        $content = ob_get_clean();
        require APP_PATH . "/Views/layouts/{$layout}.php";
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    // Pesan sekali tampil (flash)
    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function notFound(): never
    {
        http_response_code(404);
        exit('404 - Data tidak ditemukan');
    }
}
