<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'anggota'; // Default ke anggota jika tidak terdefinisi

require 'config/database.php';

// Ambil data anggota untuk dropdown
$query_anggota = "SELECT id_anggota, nama FROM anggota ORDER BY nama ASC";
$result_anggota = mysqli_query($conn, $query_anggota);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pinjaman - SIKOPIN</title>
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
                <a class="nav-link active" href="pinjaman.php"><i class="bi bi-cash-stack"></i> <span>Pinjaman</span></a>
            </li>

            <?php if (in_array($user_role, ['ketua', 'petugas'])) : ?>
            <li class="nav-item mb-1 mt-2">
                <span class="text-muted small ms-2">Master Data</span>
            </li>
            <li class="nav-item mb-1 ms-2">
                <a class="nav-link" href="anggota.php"><i class="bi bi-people"></i> <span>Anggota</span></a>
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
        <h4 class="fw-bold mb-4">Tambah Pinjaman</h4>
        <div class="card p-4">
            <form action="proses_tambah_pinjaman.php" method="POST">
                <div class="mb-3">
                    <label for="id_anggota" class="form-label">Anggota</label>
                    <select class="form-select" id="id_anggota" name="id_anggota" required>
                        <option value="">Pilih Anggota</option>
                        <?php while ($row = mysqli_fetch_assoc($result_anggota)) : ?>
                            <option value="<?php echo $row['id_anggota']; ?>"><?php echo htmlspecialchars($row['nama']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="jumlah_pinjaman" class="form-label">Jumlah Pinjaman</label>
                    <input type="number" class="form-control" id="jumlah_pinjaman" name="jumlah_pinjaman" required min="0">
                </div>
                <div class="mb-3">
                    <label for="tanggal_pinjam" class="form-label">Tanggal Pinjaman</label>
                    <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjaman" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                    <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" required>
                </div>
                <div class="mb-3">
                    <label for="bunga" class="form-label">Bunga (%)</label>
                    <input type="number" class="form-control" id="bunga" name="bunga" value="0" min="0" step="0.01">
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="pending">Pending</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus"></i> Tambah Pinjaman</button>
                <a href="pinjaman.php" class="btn btn-secondary rounded-pill px-4">Batal</a>
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