<?php
// Konfigurasi utama. Ubah sesuai environment.
return [
    'app' => [
        'name'  => 'Perpustakaan',
        'debug' => true,
        'timezone' => 'Asia/Makassar',
        'max_pinjam'      => 3,   // maksimal buku aktif (pending + dipinjam) per siswa
        'durasi_default'  => 7,   // hari, dipakai sebagai pilihan awal saat admin menyetujui
    ],
    'db' => [
        'host' => 'localhost',
        'name' => 'perpustakaan_1',
        'user' => 'root',
        'pass' => '',
    ],
];
