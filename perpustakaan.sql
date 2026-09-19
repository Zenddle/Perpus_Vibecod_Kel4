CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE users (
    nim VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    peran ENUM('admin', 'anggota', 'petugas') NOT NULL DEFAULT 'anggota'
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    category_id INT,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    deskripsi TEXT,
    cover VARCHAR(255),
    status ENUM('tersedia', 'habis', 'nonaktif') NOT NULL DEFAULT 'tersedia',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    book_id INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'returned', 'overdue') NOT NULL DEFAULT 'pending',
    perpanjangan TINYINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(nim) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE borrow_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    book_id INT NOT NULL,
    tanggal_pengajuan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    keterangan VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(nim) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);
