<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM petugas WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_petugas'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: dasbor.php');
        exit;
    } else {
        echo "<script>alert('Email atau kata sandi salah!'); window.location='login.php';</script>";
        exit;
    }
} else {
    header('Location: login.php');
    exit;
} 