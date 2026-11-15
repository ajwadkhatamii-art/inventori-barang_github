-- Database: db_inventori
-- Cara pakai: buka phpMyAdmin -> tab Import -> pilih file ini
CREATE DATABASE IF NOT EXISTS db_inventori CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_inventori;

DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff'
) ENGINE=InnoDB;

CREATE TABLE kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL,
  deskripsi TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE supplier (
  id_supplier INT AUTO_INCREMENT PRIMARY KEY,
  nama_supplier VARCHAR(100) NOT NULL,
  telepon VARCHAR(20) NOT NULL,
  alamat TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE barang (
  id_barang INT AUTO_INCREMENT PRIMARY KEY,
  kode_barang VARCHAR(20) NOT NULL UNIQUE,
  nama_barang VARCHAR(150) NOT NULL,
  id_kategori INT NOT NULL,
  id_supplier INT NOT NULL,
  harga INT NOT NULL DEFAULT 0,
  stok INT NOT NULL DEFAULT 0,
  gambar VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_barang_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_barang_supplier FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Akun: admin / admin123 dan staff / staff123 (password sudah di-hash bcrypt)
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$8DjKOMruhBtRPwEmLMTgQ.kaC4jAp0d7JFANBcVhQ4OCEyassQJpS', 'admin'),
('Staff Gudang', 'staff', '$2y$10$v0VD8V2/JZONxTpO0FeaD.potUAdMNkMVkXyMWhEtzGK8bGpmL18O', 'staff');

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Elektronik', 'Perangkat elektronik dan aksesoris'),
('Alat Tulis', 'Perlengkapan menulis dan kantor'),
('Makanan Ringan', 'Snack dan minuman kemasan'),
('Perabot', 'Perabot rumah dan kantor');

INSERT INTO supplier (nama_supplier, telepon, alamat) VALUES
('PT Maju Jaya', '081234567890', 'Jl. Merdeka No. 10, Jakarta'),
('CV Sinar Abadi', '082233445566', 'Jl. Sudirman No. 25, Bandung'),
('UD Berkah Selalu', '085611223344', 'Jl. Diponegoro No. 5, Surabaya');

INSERT INTO barang (kode_barang, nama_barang, id_kategori, id_supplier, harga, stok) VALUES
('BRG001', 'Mouse Wireless', 1, 1, 85000, 25),
('BRG002', 'Keyboard USB', 1, 1, 120000, 4),
('BRG003', 'Pulpen Hitam (1 box)', 2, 2, 30000, 50),
('BRG004', 'Buku Tulis A5', 2, 2, 8000, 3),
('BRG005', 'Keripik Kentang 100g', 3, 3, 12000, 40),
('BRG006', 'Air Mineral 600ml (1 dus)', 3, 3, 45000, 15),
('BRG007', 'Kursi Kantor', 4, 1, 350000, 8),
('BRG008', 'Meja Belajar', 4, 2, 450000, 2),
('BRG009', 'Flashdisk 32GB', 1, 1, 60000, 30),
('BRG010', 'Stapler Besar', 2, 2, 25000, 12),
('BRG011', 'Lampu Meja LED', 1, 3, 75000, 18),
('BRG012', 'Biskuit Kelapa', 3, 3, 9000, 6);
