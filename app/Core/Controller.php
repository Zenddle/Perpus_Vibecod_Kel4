<?php
<<<<<<< HEAD
class Controller {
    
    // Method untuk memanggil file tampilan (View)
    public function view($view, $data = []) {
        // Menggunakan __DIR__ agar posisinya pasti dimulai dari folder Core/
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Menambahkan path lengkap di pesan error agar mudah dilacak jika salah lagi
            die("View file not found. Sistem mencarinya di: " . $viewPath);
        }
    }

    // Method untuk memanggil file database (Model)
    public function model($model) {
        $modelPath = __DIR__ . '/../Models/' . $model . '.php';
        
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model;
        } else {
            die("Model file not found di: " . $modelPath);
        }
=======
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
>>>>>>> d80d0347eedc134252bcd32e4dd4b3210c948e17
    }

    // Pesan sekali tampil (flash)
    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }
}
