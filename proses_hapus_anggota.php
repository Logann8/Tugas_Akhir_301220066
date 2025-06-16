<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

require 'config/database.php';

if (isset($_GET['id'])) {
    $id_anggota = $_GET['id'];
    
    // Hapus data simpanan terlebih dahulu
    $query_simpanan = "DELETE FROM simpanan WHERE id_anggota = ?";
    $stmt_simpanan = mysqli_prepare($conn, $query_simpanan);
    mysqli_stmt_bind_param($stmt_simpanan, "i", $id_anggota);
    mysqli_stmt_execute($stmt_simpanan);
    mysqli_stmt_close($stmt_simpanan);
    
    // Hapus data pinjaman
    $query_pinjaman = "DELETE FROM pinjaman WHERE id_anggota = ?";
    $stmt_pinjaman = mysqli_prepare($conn, $query_pinjaman);
    mysqli_stmt_bind_param($stmt_pinjaman, "i", $id_anggota);
    mysqli_stmt_execute($stmt_pinjaman);
    mysqli_stmt_close($stmt_pinjaman);
    
    // Kemudian hapus data anggota
    $query_anggota = "DELETE FROM anggota WHERE id_anggota = ?";
    $stmt_anggota = mysqli_prepare($conn, $query_anggota);
    mysqli_stmt_bind_param($stmt_anggota, "i", $id_anggota);
    
    if (mysqli_stmt_execute($stmt_anggota)) {
        $_SESSION['success_message'] = "Data anggota berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data anggota: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt_anggota);
} else {
    $_SESSION['error_message'] = "ID anggota tidak valid.";
}

mysqli_close($conn);
header('Location: anggota.php');
exit;
?> 