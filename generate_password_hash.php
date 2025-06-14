<?php
    $password = "123456"; // GANTI DENGAN PASSWORD YANG ANDA INGINKAN!
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    echo "Hash untuk ketua: " . $hashed_password;
    ?>