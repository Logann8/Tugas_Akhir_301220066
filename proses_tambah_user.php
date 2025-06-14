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
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validasi input
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
        header('Location: tambah_user.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Konfirmasi password tidak cocok.";
        header('Location: tambah_user.php');
        exit;
    }

    // Validasi tambahan: Petugas hanya bisa menambahkan peran 'anggota' atau 'unassigned'
    if (($_SESSION['user_role'] ?? '') === 'petugas') {
        if ($role === 'ketua' || $role === 'petugas') {
            $_SESSION['error_message'] = "Petugas tidak diizinkan membuat user dengan role Ketua atau Petugas.";
            header('Location: tambah_user.php');
            exit;
        }
    }

    // Cek apakah email sudah terdaftar
    $stmt_check_email = mysqli_prepare($conn, "SELECT id_user FROM users WHERE email = ?");
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);

    if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
        $_SESSION['error_message'] = "Email sudah terdaftar. Gunakan email lain.";
        header('Location: tambah_user.php');
        exit;
    }
    mysqli_stmt_close($stmt_check_email);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "User berhasil ditambahkan.";
        header('Location: user.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Error saat menyimpan data: " . mysqli_stmt_error($stmt);
        header('Location: tambah_user.php');
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?> 