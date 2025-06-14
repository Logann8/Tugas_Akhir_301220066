<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
        header('Location: register.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Konfirmasi password tidak cocok.";
        header('Location: register.php');
        exit;
    }

    // Cek apakah email sudah terdaftar
    $stmt_check_email = mysqli_prepare($conn, "SELECT id_user FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt_check_email, "s", $email);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);

    if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
        $_SESSION['error_message'] = "Email sudah terdaftar. Gunakan email lain atau login.";
        header('Location: register.php');
        exit;
    }
    mysqli_stmt_close($stmt_check_email);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Default role untuk user baru adalah 'unassigned'
    $default_role = 'unassigned';

    // Masukkan user baru ke database
    $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $hashed_password, $default_role);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Registrasi berhasil! Akun Anda akan diverifikasi oleh admin.";
        header('Location: login.php'); // Arahkan ke halaman login setelah registrasi
        exit;
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
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