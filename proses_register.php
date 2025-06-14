<?php
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama = isset($_POST['nama']) ? mysqli_real_escape_string($conn, $_POST['nama']) : '';
    $role = 'anggota'; // Register biasa hanya bisa anggota

    if ($password !== $confirm_password) {
        echo "<script>alert('Konfirmasi kata sandi tidak cocok!'); window.location='register.php';</script>";
        exit;
    }

    $query = "SELECT * FROM petugas WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email sudah terdaftar!'); window.location='register.php';</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert = "INSERT INTO petugas (email, password, nama, role) VALUES ('$email', '$hashed_password', '$nama', '$role')";
    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Registrasi gagal!'); window.location='register.php';</script>";
    }
    exit;
} else {
    header('Location: register.php');
    exit;
} 