-- Buat database
CREATE DATABASE IF NOT EXISTS db_koperasi;
USE db_koperasi;

-- Tabel Anggota
CREATE TABLE IF NOT EXISTS anggota (
    id_anggota INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telp VARCHAR(15),
    email VARCHAR(50),
    tanggal_daftar DATE NOT NULL,
    status ENUM('aktif', 'tidak aktif') DEFAULT 'aktif'
);

-- Tabel Simpanan
CREATE TABLE IF NOT EXISTS simpanan (
    id_simpanan INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT,
    jenis_simpanan ENUM('pokok', 'wajib', 'sukarela') NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    keterangan TEXT,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
);

-- Tabel Pinjaman
CREATE TABLE IF NOT EXISTS pinjaman (
    id_pinjaman INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT,
    jumlah_pinjaman DECIMAL(15,2) NOT NULL,
    tanggal_pinjaman DATE NOT NULL,
    jangka_waktu INT NOT NULL, -- dalam bulan
    bunga DECIMAL(5,2) NOT NULL, -- persentase
    status ENUM('pending', 'disetujui', 'ditolak', 'lunas') DEFAULT 'pending',
    keterangan TEXT,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
);

-- Tabel Angsuran
CREATE TABLE IF NOT EXISTS angsuran (
    id_angsuran INT PRIMARY KEY AUTO_INCREMENT,
    id_pinjaman INT,
    angsuran_ke INT NOT NULL,
    jumlah_angsuran DECIMAL(15,2) NOT NULL,
    tanggal_bayar DATE,
    status ENUM('belum', 'sudah') DEFAULT 'belum',
    FOREIGN KEY (id_pinjaman) REFERENCES pinjaman(id_pinjaman)
); 