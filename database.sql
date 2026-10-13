CREATE DATABASE IF NOT EXISTS inventaris_db;
USE inventaris_db;

DROP TABLE IF EXISTS log_aktivitas;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS supplier;
DROP TABLE IF EXISTS kategori;

CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    alamat TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    id_kategori INT NOT NULL,
    id_supplier INT NOT NULL,
    stok INT NOT NULL,
    harga INT NOT NULL,
    deskripsi TEXT,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_produk_supplier FOREIGN KEY (id_supplier) REFERENCES supplier(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aktivitas VARCHAR(255) NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO kategori (id, nama_kategori) VALUES
(1, 'Elektronik'),
(2, 'Alat Tulis Kantor'),
(3, 'Furnitur'),
(4, 'Komputer & Aksesoris'),
(5, 'Peralatan Jaringan');

INSERT INTO supplier (id, nama_supplier, no_telp, alamat) VALUES
(1, 'PT Megah Sukses Mandiri', '081234567890', 'Jl. Merdeka No. 10, Jakarta Pusat'),
(2, 'CV Sinar Jaya Sentosa', '081987654321', 'Jl. Ahmad Yani No. 45, Bandung'),
(3, 'PT Sumber Berkah Sejahtera', '082134567891', 'Jl. Diponegoro No. 88, Surabaya'),
(4, 'CV Maju Lancar Bersama', '085234567892', 'Jl. Gajah Mada No. 12, Yogyakarta'),
(5, 'PT Mitra Prima Logistik', '087834567893', 'Jl. Sudirman No. 23, Semarang');

INSERT INTO produk (id, nama_produk, id_kategori, id_supplier, stok, harga, deskripsi) VALUES
(1, 'Laptop Asus Vivobook 14', 4, 1, 12, 8500000, 'Laptop kantor 14 inci spesifikasi Core i3'),
(2, 'Mouse Wireless Logitech M170', 4, 2, 35, 165000, 'Mouse wireless hemat baterai'),
(3, 'Kertas HVS Sinar Dunia A4 80gr', 2, 3, 50, 48000, 'Satu rim kertas HVS A4'),
(4, 'Kursi Kantor Ergonomis Fortuner', 3, 4, 8, 850000, 'Kursi putar jaring dengan penyangga punggung'),
(5, 'Router Wi-Fi TP-Link Archer C6', 5, 5, 15, 420000, 'Router gigabit dual band AC1200'),
(6, 'Spidol Boardmarker Snowman Hitam', 2, 3, 40, 9500, 'Spidol papan tulis dapat diisi ulang'),
(7, 'Monitor LED LG 24 Inci Full HD', 1, 1, 10, 1350000, 'Monitor IPS 75Hz port HDMI dan VGA');
