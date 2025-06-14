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

    // Ambil role user yang sedang diedit dari database untuk validasi backend
    $stmt_get_current_role = mysqli_prepare($conn, "SELECT role FROM users WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt_get_current_role, "i", $id_user);
    mysqli_stmt_execute($stmt_get_current_role);
    $result_current_role = mysqli_stmt_get_result($stmt_get_current_role);
    $current_user_data = mysqli_fetch_assoc($result_current_role);
    mysqli_stmt_close($stmt_get_current_role);

    if (!$current_user_data) {
        $_SESSION['error_message'] = "User tidak ditemukan.";
        header('Location: user.php');
        exit;
    }

    $current_target_role = $current_user_data['role'];

    // Validasi backend: Petugas tidak bisa mengedit Ketua atau Petugas lain
    if (($_SESSION['user_role'] ?? '') === 'petugas') {
        // Petugas tidak boleh mengedit user dengan role Ketua atau Petugas
        if ($current_target_role === 'ketua' || $current_target_role === 'petugas') {
            $_SESSION['error_message'] = "Petugas tidak diizinkan mengedit user dengan role Ketua atau Petugas.";
            header('Location: user.php');
            exit;
        }
        // Petugas tidak boleh mengubah role user menjadi Ketua atau Petugas
        if ($role === 'ketua' || $role === 'petugas') {
            $_SESSION['error_message'] = "Petugas tidak diizinkan mengubah role user menjadi Ketua atau Petugas.";
            header('Location: edit_user.php?id=' . $id_user);
            exit;
        }
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