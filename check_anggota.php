<?php
require 'config/database.php';

$result = mysqli_query($conn, 'SELECT COUNT(*) as total FROM anggota');
$row = mysqli_fetch_assoc($result);

echo 'Jumlah data di tabel anggota: ' . $row['total'];
?> 