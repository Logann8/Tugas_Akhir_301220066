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
    $jenis_simpanan = $_POST['jenis_simpanan'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal']; // Ini akan datang sebagai datetime-local format
    $plan = $_POST['plan'];
    $status = $_POST['status'];
    $fee = $_POST['fee'];

    // Validasi input (minimal)
    if (empty($id_anggota) || empty($jenis_simpanan) || empty($jumlah) || empty($tanggal) || empty($plan) || empty($status)) {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
        header('Location: tambah_simpanan.php');
        exit;
    }

    // Konversi tanggal ke format yang sesuai untuk MySQL DATETIME
    $tanggal_db = date('Y-m-d H:i:s', strtotime($tanggal));

    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, "INSERT INTO simpanan (id_anggota, jenis_simpanan, jumlah, tanggal, plan, status, fee) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isdssss", $id_anggota, $jenis_simpanan, $jumlah, $tanggal_db, $plan, $status, $fee);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Data simpanan berhasil ditambahkan.";
        header('Location: simpanan.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header('Location: tambah_simpanan.php');
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?> 