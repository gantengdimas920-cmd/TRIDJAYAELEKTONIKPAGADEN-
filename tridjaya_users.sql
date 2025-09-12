
-- SQL Dump: Tabel users untuk Aplikasi Kasir Tridjaya Elektronik

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pegawai') DEFAULT 'pegawai',
    nama_toko VARCHAR(100) NOT NULL
);

-- Contoh data admin default (username: admin, password: admin)
INSERT INTO users (username, password, role, nama_toko)
VALUES ('admin', 'admin', 'admin', 'TRIDJAYA ELEKTRONIK PAGADEN');
