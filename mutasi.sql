CREATE TABLE mutasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_cabang VARCHAR(100) NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL,
    jenis VARCHAR(50) NOT NULL,
    nama_driver VARCHAR(100) NOT NULL,
    tanggal_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
