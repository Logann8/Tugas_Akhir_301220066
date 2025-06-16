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

    // Validasi input
    if (empty($id_user)) {
        $_SESSION['error_message'] = "Silakan pilih user!";
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

    // Ambil nama dan email dari tabel users
    $user_data_query = "SELECT nama, email FROM users WHERE id_user = ?";
    $user_data_stmt = mysqli_prepare($conn, $user_data_query);
    mysqli_stmt_bind_param($user_data_stmt, "i", $id_user);
    mysqli_stmt_execute($user_data_stmt);
    $user_data_result = mysqli_stmt_get_result($user_data_stmt);
    $user_data = mysqli_fetch_assoc($user_data_result);
    mysqli_stmt_close($user_data_stmt);

    if (!$user_data) {
        $_SESSION['error_message'] = "Data user tidak ditemukan.";
        header('Location: tambah_anggota.php');
        exit;
    }

    $nama = $user_data['nama'];
    $email = $user_data['email'];

    // Insert anggota baru
    $query = "INSERT INTO anggota (id_user, nama, email, no_telp, alamat, tanggal_daftar, status) VALUES (?, ?, ?, ?, ?, CURDATE(), 'aktif')";
    $stmt = mysqli_prepare($conn, $query);
    
    // Debug: Cek apakah prepare statement berhasil
    if ($stmt === false) {
        $_SESSION['error_message'] = "Gagal menyiapkan query insert anggota: " . mysqli_error($conn);
        header('Location: tambah_anggota.php');
        exit;
    }

    // Set nilai default untuk telepon dan alamat (string kosong agar tidak melanggar NOT NULL)
    $default_telepon = ''; 
    $default_alamat = ''; 
    
    mysqli_stmt_bind_param($stmt, "issss", $id_user, $nama, $email, $default_telepon, $default_alamat);

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