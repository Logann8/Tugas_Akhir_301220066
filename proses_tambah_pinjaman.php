<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_anggota = $_POST['id_anggota'];
    $jumlah_pinjaman = $_POST['jumlah_pinjaman'];
    $tanggal_pinjaman = $_POST['tanggal_pinjaman'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $bunga = $_POST['bunga'];
    $status = $_POST['status'];

    // Validasi input (minimal)
    if (empty($id_anggota) || empty($jumlah_pinjaman) || empty($tanggal_pinjaman) || empty($tanggal_kembali) || !isset($bunga) || empty($status)) {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
        header('Location: tambah_pinjaman.php');
        exit;
    }

    // Hitung total pinjaman (jumlah_pinjaman + (jumlah_pinjaman * bunga/100))
    $total_pinjaman = $jumlah_pinjaman + ($jumlah_pinjaman * ($bunga / 100));

    // Nilai default untuk jangka_waktu (sementara, karena wajib diisi tapi tidak ada di form)
    $jangka_waktu_default = 1; // Sesuaikan dengan logika bisnis Anda jika perlu

    // Query untuk menambah data pinjaman
    $query = "INSERT INTO pinjaman (id_anggota, jumlah_pinjaman, tanggal_pinjaman, jangka_waktu, bunga, status, fiscal_date) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ididsds", $id_anggota, $jumlah_pinjaman, $tanggal_pinjaman, $jangka_waktu_default, $bunga, $status, $tanggal_kembali);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Data pinjaman berhasil ditambahkan.";
        header('Location: pinjaman.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header('Location: tambah_pinjaman.php');
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?> 