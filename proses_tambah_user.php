<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'ketua') {
    header('Location: login.php');
    exit;
}
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if (!in_array($role, ['ketua', 'petugas', 'anggota'])) {
        echo "<script>alert('Role tidak valid!'); window.location='tambah_user.php';</script>";
        exit;
    }
    if ($password !== $confirm_password) {
        echo "<script>alert('Konfirmasi kata sandi tidak cocok!'); window.location='tambah_user.php';</script>";
        exit;
    }
    $query = "SELECT * FROM petugas WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email sudah terdaftar!'); window.location='tambah_user.php';</script>";
        exit;
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert = "INSERT INTO petugas (email, password, nama, role) VALUES ('$email', '$hashed_password', '$nama', '$role')";
    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location='user.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah user!'); window.location='tambah_user.php';</script>";
    }
    exit;
} else {
    header('Location: tambah_user.php');
    exit;
} 