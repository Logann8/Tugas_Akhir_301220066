<?php
session_start();

if (isset($_SESSION['user_id'])) {
    // Jika sudah login, arahkan ke dasbor
    header('Location: dasbor.php');
    exit;
} else {
    // Jika belum login, arahkan ke halaman login
    header('Location: login.php');
    exit;
}
?> 