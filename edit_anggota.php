<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'anggota'; // Default ke anggota jika tidak terdefinisi

require 'config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query_anggota = "SELECT * FROM anggota WHERE id_anggota = ?";
    $stmt = mysqli_prepare($conn, $query_anggota);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $anggota = mysqli_fetch_assoc($result);
} else {
    header('Location: anggota.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - SIKOPIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar d-flex flex-column align-items-center p-3">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_nama'] ?? 'Admin'); ?>" class="profile-img" alt="Profile">
        <ul class="nav flex-column w-100">
            <li class="nav-item mb-1">
                <a class="nav-link" href="dasbor.php"><i class="bi bi-house-door"></i> <span>Dasbor</span></a>
            </li>
            <li class="nav-item mb-1">
                <span class="text-muted small ms-2">Main</span>
            </li>
            <li class="nav-item mb-1 ms-2">
                <a class="nav-link" href="simpanan.php"><i class="bi bi-wallet2"></i> <span>Simpanan</span></a>
            </li>
            <li class="nav-item mb-1 ms-2">
                <a class="nav-link" href="pinjaman.php"><i class="bi bi-cash-stack"></i> <span>Pinjaman</span></a>
            </li>

            <?php if (in_array($user_role, ['ketua', 'petugas'])) : ?>
            <li class="nav-item mb-1 mt-2">
                <span class="text-muted small ms-2">Master Data</span>
            </li>
            <li class="nav-item mb-1 ms-2">
                <a class="nav-link active" href="anggota.php"><i class="bi bi-people"></i> <span>Anggota</span></a>
            </li>
            <?php endif; ?>

            <?php if ($user_role === 'ketua') : ?>
            <li class="nav-item mb-1 mt-2">
                <span class="text-muted small ms-2">Settings</span>
            </li>
            <li class="nav-item mb-1 ms-2">
                <a class="nav-link" href="user.php"><i class="bi bi-person-gear"></i> <span>User</span></a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="topbar">
        <span class="fw-bold fs-5">SIKOPIN</span>
        <span id="datetime" class="text-muted"></span>
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold text-dark"><?php echo htmlspecialchars($_SESSION['user_nama'] ?? $_SESSION['user_email']); ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill ms-2">Logout <i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
    <div class="main-content">
        <h4 class="fw-bold mb-4">Edit Anggota</h4>
        <div class="card p-4">
            <form action="proses_edit_anggota.php" method="POST">
                <input type="hidden" name="id_anggota" value="<?php echo $anggota['id_anggota']; ?>">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($anggota['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo htmlspecialchars($anggota['alamat']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="tel" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($anggota['telepon']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($anggota['email']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-save"></i> Simpan Perubahan</button>
                <a href="anggota.php" class="btn btn-secondary rounded-pill px-4">Batal</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function updateDateTime() {
        const now = new Date();
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('datetime').textContent = now.toLocaleString('id-ID', options);
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
    </script>
</body>
</html> 