<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIKOPIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .register-container {
            max-width: 400px;
            margin: 40px auto;
            border: 2px solid #888;
            border-radius: 10px;
            padding: 32px 24px 24px 24px;
            background: #fff;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            background: #eee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            font-size: 2rem;
            color: #aaa;
        }
        .form-check-label {
            font-size: 0.95rem;
        }
        .login-link {
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="profile-img mb-3">
            <i class="bi bi-person"></i>
        </div>
        <h3 class="text-center fw-bold mb-1">Buat Akun Baru</h3>
        <div class="text-center mb-2">
            <span>Daftar sebagai <b>Petugas Koperasi</b></span><br>
            <span class="login-link">atau <a href="login.php">masuk</a></span>
        </div>
        <h5 class="text-center fw-bold mb-3">SIKOPIN</h5>
        <form action="proses_register.php" method="post">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required autofocus>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Alamat email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata sandi</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi kata sandi</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
</body>
</html> 