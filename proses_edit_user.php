<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Optional
    $role = $_POST['role'];

    // Validasi input
    if (empty($nama) || empty($email) || empty($role)) {
        $_SESSION['error_message'] = "Nama, Email, dan Role wajib diisi.";
        header('Location: edit_user.php?id=' . $id_user);
        exit;
    }

    $query = "UPDATE users SET nama = ?, email = ?, role = ?";
    $types = "sss";
    $params = [$nama, $email, $role];

    // Jika password diisi, hash dan tambahkan ke query update
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $types .= "s";
        $params[] = $hashed_password;
    }

    $query .= " WHERE id_user = ?";
    $types .= "i";
    $params[] = $id_user;

    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Data user berhasil diperbarui.";
        header('Location: user.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header('Location: edit_user.php?id=' . $id_user);
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?> 