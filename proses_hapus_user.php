<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    // Ambil role user yang akan dihapus dari database untuk validasi backend
    $stmt_get_target_role = mysqli_prepare($conn, "SELECT role FROM users WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt_get_target_role, "i", $id_user);
    mysqli_stmt_execute($stmt_get_target_role);
    $result_target_role = mysqli_stmt_get_result($stmt_get_target_role);
    $target_user_data = mysqli_fetch_assoc($result_target_role);
    mysqli_stmt_close($stmt_get_target_role);

    if (!$target_user_data) {
        $_SESSION['error_message'] = "User tidak ditemukan.";
        header('Location: user.php');
        exit;
    }

    $target_user_role = $target_user_data['role'];

    // Validasi backend: Petugas tidak bisa menghapus Ketua atau Petugas lain
    if (($_SESSION['user_role'] ?? '') === 'petugas') {
        if ($target_user_role === 'ketua' || $target_user_role === 'petugas') {
            $_SESSION['error_message'] = "Petugas tidak diizinkan menghapus user dengan role Ketua atau Petugas.";
            header('Location: user.php');
            exit;
        }
    }

    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "User berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

header('Location: user.php');
exit;
?> 