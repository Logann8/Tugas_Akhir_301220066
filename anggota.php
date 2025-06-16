<?php
session_start();
$allowed_roles = ['ketua', 'petugas'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'anggota'; // Default ke anggota jika tidak terdefinisi

require 'config/database.php';

// Query untuk mengambil data anggota
$query = "SELECT * FROM anggota ORDER BY nama ASC";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota - SIKOPIN</title>
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
        <h4 class="fw-bold mb-4">Daftar Anggota</h4>
        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="tambah_anggota.php" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus"></i> Tambah Anggota</a>
                </div>
                <div>
                    <button class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-download"></i> Unduh</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $no++ . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['alamat']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['no_telp']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                echo '<td>';
                                echo '<div class="d-flex gap-2">';
                                echo '<a href="edit_anggota.php?id=' . $row['id_anggota'] . '" class="btn btn-warning btn-sm">';
                                echo '<i class="bi bi-pencil"></i> Edit</a>';
                                echo '<button type="button" class="btn btn-danger btn-sm" onclick="hapusAnggota(' . $row['id_anggota'] . ')">';
                                echo '<i class="bi bi-trash"></i> Hapus</button>';
                                echo '</div>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center">Tidak ada data anggota.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>Menampilkan <?php echo mysqli_num_rows($result); ?> dari <?php echo mysqli_num_rows($result); ?></div>
                <div>
                    <select class="form-select form-select-sm d-inline-block" style="width: 120px;">
                        <option>Per halaman</option>
                        <option selected>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Tampilkan waktu realtime
    function updateDateTime() {
        const now = new Date();
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('datetime').textContent = now.toLocaleString('id-ID', options);
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    function hapusAnggota(id) {
        if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
            window.location.href = 'proses_hapus_anggota.php?id=' + id;
        }
    }
    </script>
</body>
</html> 