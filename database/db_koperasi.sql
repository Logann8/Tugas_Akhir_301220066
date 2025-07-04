-- Buat database
CREATE DATABASE IF NOT EXISTS db_koperasi;
USE db_koperasi;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('ketua', 'petugas', 'anggota') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Anggota
CREATE TABLE IF NOT EXISTS anggota (
    id_anggota INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    telepon VARCHAR(20),
    alamat TEXT NOT NULL,
    tanggal_daftar DATE NOT NULL,
    status ENUM('aktif', 'tidak aktif') DEFAULT 'aktif',
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- Tabel Simpanan
CREATE TABLE IF NOT EXISTS simpanan (
    id_simpanan INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT NOT NULL,
    jenis_simpanan ENUM('pokok', 'wajib', 'sukarela') NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    keterangan TEXT,
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    plan ENUM('sekali', 'bulanan') DEFAULT 'sekali',
    fee DECIMAL(15,2) DEFAULT 0,
    fiscal_date DATETIME,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
);

-- Tabel Pinjaman
CREATE TABLE IF NOT EXISTS pinjaman (
    id_pinjaman INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT NOT NULL,
    jumlah_pinjaman DECIMAL(15,2) NOT NULL,
    tanggal_pinjaman DATE NOT NULL,
    jangka_waktu INT NOT NULL, -- dalam bulan
    bunga DECIMAL(5,2) NOT NULL, -- persentase
    status ENUM('pending', 'disetujui', 'ditolak', 'lunas') DEFAULT 'pending',
    keterangan TEXT,
    fiscal_date DATETIME,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
);

-- Tabel Angsuran
CREATE TABLE IF NOT EXISTS angsuran (
    id_angsuran INT PRIMARY KEY AUTO_INCREMENT,
    id_pinjaman INT NOT NULL,
    angsuran_ke INT NOT NULL,
    jumlah_angsuran DECIMAL(15,2) NOT NULL,
    tanggal_bayar DATE,
    status ENUM('belum', 'sudah') DEFAULT 'belum',
    FOREIGN KEY (id_pinjaman) REFERENCES pinjaman(id_pinjaman)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (nama, email, password, role) VALUES 
('Administrator', 'admin@koperasi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ketua'); 