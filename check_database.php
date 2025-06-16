<?php
require 'config/database.php';

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Check</h2>";

// Cek koneksi database
if ($conn) {
    echo "✅ Koneksi database berhasil<br>";
} else {
    echo "❌ Koneksi database gagal<br>";
    exit;
}

// Cek tabel users
$query = "SHOW TABLES LIKE 'users'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    echo "✅ Tabel users ditemukan<br>";
    
    // Cek struktur tabel users
    $query = "DESCRIBE users";
    $result = mysqli_query($conn, $query);
    echo "<h3>Struktur Tabel Users:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Cek data di tabel users
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    echo "<h3>Data di Tabel Users:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_user'] . "</td>";
        echo "<td>" . $row['nama'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Tabel users tidak ditemukan<br>";
}

// Cek session
echo "<h3>Session Check:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "Session Save Path: " . session_save_path() . "<br>";

mysqli_close($conn);
?> 