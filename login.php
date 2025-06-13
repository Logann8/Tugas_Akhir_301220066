<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIKOPIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .login-container {
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
        .register-link {
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="profile-img mb-3">
            <i class="bi bi-person"></i>
        </div>
        <h3 class="text-center fw-bold mb-1">Sign in</h3>
        <div class="text-center mb-2">
            <span>Masuk sebagai <b>Petugas Koperasi</b></span><br>
            <span class="register-link">atau <a href="register.php">buat akun baru</a></span>
        </div>
        <h5 class="text-center fw-bold mb-3">SIKOPIN</h5>
        <form action="proses_login.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Alamat email</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Kata sandi</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                        <span class="bi bi-eye" id="eyeIcon"></span>
                    </button>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
    <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
    </script>
</body>
</html> 