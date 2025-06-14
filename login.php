<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIKOPIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-container {
            max-width: 400px;
            width: 100%;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .logo-img {
            display: block;
            margin: 0 auto 20px auto;
            width: 100px;
            height: auto;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        .btn-primary {
            background-color: #ff9800 !important;
            border-color: #ff9800 !important;
        }
        .btn-primary:hover {
            background-color: #e68900 !important;
            border-color: #e68900 !important;
        }
        .register-link {
            font-size: 0.95rem;
            color: #ff9800;
            font-weight: 600;
        }
        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container text-center">
        <img src="https://raw.githubusercontent.com/twbs/icons/main/icons/shield-fill.svg" alt="Logo Koperasi" class="logo-img">
        <h5 class="fw-bold mb-1">Masuk</h5>
        <div class="mb-4">
            <span>atau <a href="register.php" class="register-link">buat akun baru</a></span>
        </div>
        <h4 class="fw-bold mb-4">SIKOPIN</h4>
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
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
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