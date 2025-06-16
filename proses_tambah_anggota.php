<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? '';
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    // Validasi input
    if (empty($id_user) || empty($telepon) || empty($alamat)) {
        $_SESSION['error_message'] = "Semua field harus diisi!";
        header('Location: tambah_anggota.php');
        exit;
    }

    // Validasi format telepon
    if (!preg_match('/^[0-9]{10,15}$/', $telepon)) {
        $_SESSION['error_message'] = "Format nomor telepon tidak valid!";
        header('Location: tambah_anggota.php');
        exit;
    }

    // Cek apakah user sudah menjadi anggota
    $check_query = "SELECT * FROM anggota WHERE id_user = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "i", $id_user);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['error_message'] = "User ini sudah terdaftar sebagai anggota!";
        header('Location: tambah_anggota.php');
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Insert anggota baru
    $query = "INSERT INTO anggota (id_user, telepon, alamat) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iss", $id_user, $telepon, $alamat);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Anggota berhasil ditambahkan!";
        header('Location: anggota.php');
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan anggota: " . mysqli_error($conn);
        header('Location: tambah_anggota.php');
    }

    mysqli_stmt_close($stmt);
} else {
    header('Location: tambah_anggota.php');
}

mysqli_close($conn);
exit;
?> 