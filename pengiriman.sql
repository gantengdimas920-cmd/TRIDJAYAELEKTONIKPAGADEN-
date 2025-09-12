CREATE TABLE pengiriman (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_konsumen VARCHAR(255) NOT NULL,
    nama_barang VARCHAR(255) NOT NULL,
    jenis VARCHAR(255) NOT NULL,
    nama_driver VARCHAR(255) NOT NULL,
    tanggal_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
