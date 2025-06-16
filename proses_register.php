<?php
session_start();
require 'config/database.php';

// Nonaktifkan error reporting
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Semua field harus diisi!";
        header('Location: register.php');
        exit;
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Format email tidak valid!";
        header('Location: register.php');
        exit;
    }

    // Validasi password match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok!";
        header('Location: register.php');
        exit;
    }

    // Cek apakah email sudah terdaftar
    $check_query = "SELECT id_user FROM users WHERE email = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    if ($check_stmt === false) {
        $_SESSION['error_message'] = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
        header('Location: register.php');
        exit;
    }
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['error_message'] = "Email sudah terdaftar!";
        header('Location: register.php');
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'unassigned'; // Set role default untuk pendaftaran adalah 'unassigned'

    // Insert user baru
    $query = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        $_SESSION['error_message'] = "Terjadi kesalahan sistem saat menyimpan data. Silakan coba lagi nanti.";
        header('Location: register.php');
        exit;
    }
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal registrasi: " . mysqli_error($conn);
        header('Location: register.php');
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    header('Location: register.php');
    exit;
}

mysqli_close($conn);
?> 