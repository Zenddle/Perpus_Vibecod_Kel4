-- Skema + data contoh. Impor lewat phpMyAdmin (tab Import) atau:
--   mysql -u root < database/schema.sql
-- PERHATIAN: tabel lama dengan nama sama akan DIHAPUS dan dibuat ulang.

CREATE DATABASE IF NOT EXISTS perpustakaan_1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpustakaan_1;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS borrowings;
DROP TABLE IF EXISTS borrow_requests;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  nim        VARCHAR(20)  NOT NULL PRIMARY KEY,
  nama       VARCHAR(150) NOT NULL,
  password   VARCHAR(255) NOT NULL,
  peran      ENUM('admin','siswa') NOT NULL DEFAULT 'siswa',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE books (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul       VARCHAR(200) NOT NULL,
  penulis     VARCHAR(150) NOT NULL DEFAULT '',
  penerbit    VARCHAR(150) NOT NULL DEFAULT '',
  tahun       SMALLINT UNSIGNED NULL,
  sinopsis    TEXT NULL,
  cover       VARCHAR(100) NULL,               -- nama file di uploads/covers/
  category_id INT UNSIGNED NULL,
  stok        INT UNSIGNED NOT NULL DEFAULT 1, -- jumlah eksemplar
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- status: pending (diajukan) -> approved (dipinjam) -> returned | rejected
-- nim/nama/judul disalin supaya riwayat tetap terbaca walau user atau buku dihapus
CREATE TABLE borrowings (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id             VARCHAR(20)  NULL,
  book_id             INT UNSIGNED NULL,
  nim_peminjam        VARCHAR(20)  NOT NULL,
  nama_peminjam       VARCHAR(150) NOT NULL,
  judul_buku          VARCHAR(200) NOT NULL,
  status              ENUM('pending','approved','rejected','returned') NOT NULL DEFAULT 'pending',
  tanggal_pengajuan   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tanggal_pinjam      DATE NULL,
  tanggal_kembali     DATE NULL,               -- jatuh tempo
  tanggal_dikembalikan DATE NULL,
  diproses_oleh       VARCHAR(20) NULL,
  INDEX idx_user_status (user_id, status),
  INDEX idx_book_status (book_id, status),
  FOREIGN KEY (user_id) REFERENCES users(nim)  ON DELETE SET NULL,
  FOREIGN KEY (book_id) REFERENCES books(id)   ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------- Data contoh ----------
-- Admin  : ID admin        / password admin123
-- Siswa  : 2024001, 2024002 / password siswa123
INSERT INTO users (nim, nama, password, peran) VALUES
  ('admin',   'Administrator', '$2y$10$Nlr2Fwo6dRJ9ZtxBNnSkb..EDB/srgAR1rqQg3d50Ig4napzZs726', 'admin'),
  ('2024001', 'Siti Aisyah',   '$2y$10$9WuyptMzS00hk6mml4ftJ.t2orOo7PM7HWmRXst9edqF1RyT5kefe', 'siswa'),
  ('2024002', 'Budi Santoso',  '$2y$10$9WuyptMzS00hk6mml4ftJ.t2orOo7PM7HWmRXst9edqF1RyT5kefe', 'siswa');

INSERT INTO categories (nama_kategori) VALUES
  ('Novel'), ('Sains'), ('Sejarah'), ('Teknologi'), ('Agama'), ('Pelajaran');

INSERT INTO books (judul, penulis, penerbit, tahun, sinopsis, category_id, stok) VALUES
  ('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Kisah sepuluh anak dari keluarga miskin di Belitung yang berjuang mendapatkan pendidikan bersama dua guru mereka.', 1, 2),
  ('Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 'Perjalanan Minke, pribumi terpelajar di masa kolonial Hindia Belanda, dan pergulatannya melawan ketidakadilan.', 1, 1),
  ('Sapiens: Riwayat Singkat Umat Manusia', 'Yuval Noah Harari', 'KPG', 2017, 'Sejarah umat manusia dari zaman batu hingga era modern.', 3, 1),
  ('Kosmos', 'Carl Sagan', 'Gramedia', 2013, 'Perjalanan memahami alam semesta, dari asal-usul bintang hingga tempat manusia di dalamnya.', 2, 2),
  ('Dasar-Dasar Pemrograman Web', 'Tim Penulis', 'Informatika', 2020, 'Pengantar HTML, CSS, JavaScript, dan PHP untuk pemula.', 4, 3),
  ('Algoritma dan Struktur Data', 'Rinaldi Munir', 'Informatika', 2016, 'Konsep dasar algoritma, struktur data, dan analisis kompleksitas.', 4, 1),
  ('Matematika Kelas X', 'Kemdikbud', 'Kemdikbud', 2021, 'Buku pelajaran matematika untuk siswa kelas X.', 6, 5),
  ('Sirah Nabawiyah', 'Safiyyur Rahman Al-Mubarakfuri', 'Pustaka Al-Kautsar', 2010, 'Sejarah kehidupan Nabi Muhammad SAW.', 5, 2);
