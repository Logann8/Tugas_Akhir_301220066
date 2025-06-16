<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        $_SESSION['error_message'] = "Semua field harus diisi!";
        header('Location: tambah_user.php');
        exit;
    }

    // Validasi password match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok!";
        header('Location: tambah_user.php');
        exit;
    }

    // Validasi role
    $allowed_roles = ['ketua', 'petugas', 'anggota'];
    if (!in_array($role, $allowed_roles)) {
        $_SESSION['error_message'] = "Role tidak valid!";
        header('Location: tambah_user.php');
        exit;
    }

    // Cek apakah username sudah ada
    $check_query = "SELECT * FROM users WHERE nama = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['error_message'] = "Username sudah digunakan!";
        header('Location: tambah_user.php');
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user baru
    $query = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    $email = $username . "@koperasi.com"; // Membuat email default dari username
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "User berhasil ditambahkan!";
        header('Location: user.php');
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan user: " . mysqli_error($conn);
        header('Location: tambah_user.php');
    }

    mysqli_stmt_close($stmt);
} else {
    header('Location: tambah_user.php');
}

mysqli_close($conn);
exit;
?> 