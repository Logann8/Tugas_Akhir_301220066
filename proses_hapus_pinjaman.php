<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if (isset($_GET['id'])) {
    $id_pinjaman = $_GET['id'];

    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($conn, "DELETE FROM pinjaman WHERE id_pinjaman = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_pinjaman);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Data pinjaman berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);

header('Location: pinjaman.php');
exit;
?> 