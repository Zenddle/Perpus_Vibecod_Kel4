<?php

class HomeController extends Controller {
    public function index() {
        $data['judul'] = 'Halaman Utama';
        
        // Contoh pemanggilan model (opsional)
        // $userModel = $this->model('User');
        // $data['users'] = $userModel->getAllUsers();

        $this->view('home/index', $data);
    }
}