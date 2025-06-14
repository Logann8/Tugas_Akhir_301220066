<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, "SELECT id_user, nama, email, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];

        header('Location: dasbor.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Email atau kata sandi salah!";
        header('Location: login.php');
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    header('Location: login.php');
    exit;
}

mysqli_close($conn);
?> 