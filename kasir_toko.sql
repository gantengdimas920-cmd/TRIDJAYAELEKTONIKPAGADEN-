-- Buat database
CREATE DATABASE kasir_toko;

USE kasir_toko;

-- Tabel barang
CREATE TABLE barang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(255),
  harga INT
);

-- Tabel transaksi
CREATE TABLE transaksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  barang_id INT,
  tanggal DATETIME
);
