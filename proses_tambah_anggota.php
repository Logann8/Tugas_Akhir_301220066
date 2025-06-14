<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];

    // Validasi input
    if (empty($nama) || empty($alamat) || empty($telepon) || empty($email)) {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
        header('Location: tambah_anggota.php');
        exit;
    }

    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, "INSERT INTO anggota (nama, alamat, telepon, email) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $alamat, $telepon, $email);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Data anggota berhasil ditambahkan.";
        header('Location: anggota.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header('Location: tambah_anggota.php');
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?> 