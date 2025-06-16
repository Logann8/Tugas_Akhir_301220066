<?php
require 'config/database.php';

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Pemeriksaan Tabel Users</h2>";

// Cek koneksi database
if ($conn) {
    echo "✅ Koneksi database berhasil<br>";
} else {
    echo "❌ Koneksi database gagal<br>";
    exit;
}

// Cek apakah tabel users ada
$query = "SHOW TABLES LIKE 'users'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "✅ Tabel users ditemukan<br>";
    
    // Cek struktur tabel
    $query = "DESCRIBE users";
    $result = mysqli_query($conn, $query);
    
    $required_columns = [
        'id_user' => 'int',
        'nama' => 'varchar',
        'email' => 'varchar',
        'password' => 'varchar',
        'role' => 'varchar'
    ];
    
    $existing_columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $existing_columns[$row['Field']] = $row['Type'];
    }
    
    // Cek kolom yang diperlukan
    $missing_columns = [];
    foreach ($required_columns as $column => $type) {
        if (!isset($existing_columns[$column])) {
            $missing_columns[] = $column;
        }
    }
    
    if (empty($missing_columns)) {
        echo "✅ Semua kolom yang diperlukan ada<br>";
    } else {
        echo "❌ Kolom yang hilang: " . implode(', ', $missing_columns) . "<br>";
        
        // Tambahkan kolom yang hilang
        foreach ($missing_columns as $column) {
            $type = $required_columns[$column];
            $query = "ALTER TABLE users ADD COLUMN $column $type";
            if (mysqli_query($conn, $query)) {
                echo "✅ Kolom $column berhasil ditambahkan<br>";
            } else {
                echo "❌ Gagal menambahkan kolom $column: " . mysqli_error($conn) . "<br>";
            }
        }
    }
    
    // Cek data di tabel
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<h3>Data Users yang Ada:</h3>";
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
        echo "❌ Tidak ada data users<br>";
        
        // Tambahkan user admin default jika tidak ada data
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('Admin', 'admin@koperasi.com', '$admin_password', 'ketua')";
        
        if (mysqli_query($conn, $query)) {
            echo "✅ User admin default berhasil ditambahkan<br>";
            echo "Email: admin@koperasi.com<br>";
            echo "Password: admin123<br>";
        } else {
            echo "❌ Gagal menambahkan user admin: " . mysqli_error($conn) . "<br>";
        }
    }
} else {
    echo "❌ Tabel users tidak ditemukan<br>";
    
    // Buat tabel users
    $query = "CREATE TABLE users (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('ketua', 'petugas', 'anggota') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $query)) {
        echo "✅ Tabel users berhasil dibuat<br>";
        
        // Tambahkan user admin default
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('Admin', 'admin@koperasi.com', '$admin_password', 'ketua')";
        
        if (mysqli_query($conn, $query)) {
            echo "✅ User admin default berhasil ditambahkan<br>";
            echo "Email: admin@koperasi.com<br>";
            echo "Password: admin123<br>";
        } else {
            echo "❌ Gagal menambahkan user admin: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "❌ Gagal membuat tabel users: " . mysqli_error($conn) . "<br>";
    }
}

mysqli_close($conn);
?> 