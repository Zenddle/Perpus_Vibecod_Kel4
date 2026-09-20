<?php
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
    }
}