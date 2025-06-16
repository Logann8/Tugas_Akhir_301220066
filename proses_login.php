<?php
session_start();
require 'config/database.php';

// Nonaktifkan error reporting
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validasi input
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Email dan password harus diisi!";
        header('Location: login.php');
        exit;
    }

    // Cek user di database
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt === false) {
        $_SESSION['error_message'] = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
        header('Location: login.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect berdasarkan role
            switch ($user['role']) {
                case 'ketua':
                case 'petugas':
                case 'anggota':
                    header('Location: dasbor.php');
                    exit;
                default:
                    $_SESSION['error_message'] = "Role tidak valid!";
                    header('Location: login.php');
                    exit;
            }
        } else {
            $_SESSION['error_message'] = "Password salah!";
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Email tidak ditemukan!";
        header('Location: login.php');
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    header('Location: login.php');
    exit;
}

mysqli_close($conn);
exit;
?> 