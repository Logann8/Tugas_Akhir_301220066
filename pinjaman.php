<?php
session_start();
$allowed_roles = ['ketua', 'petugas', 'anggota'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'anggota'; // Default ke anggota jika tidak terdefinisi

require 'config/database.php';

$anggota_id = null;
if ($user_role === 'anggota') {
    // Dapatkan id_anggota yang terkait dengan user_id yang sedang login
    // Asumsi: Ada kolom id_user di tabel anggota yang terhubung dengan user_id di tabel petugas
    $user_id_session = $_SESSION['user_id'] ?? 0;
    $query_anggota_id = "SELECT id_anggota FROM anggota WHERE id_user = '$user_id_session' LIMIT 1";
    $result_anggota_id = mysqli_query($conn, $query_anggota_id);
    if ($result_anggota_id && mysqli_num_rows($result_anggota_id) > 0) {
        $row_anggota_id = mysqli_fetch_assoc($result_anggota_id);
        $anggota_id = $row_anggota_id['id_anggota'];
    }
}

// Query untuk mengambil data pinjaman
$query = "SELECT p.*, a.nama AS nama_anggota 
          FROM pinjaman p
          JOIN anggota a ON p.id_anggota = a.id_anggota";

if ($user_role === 'anggota' && $anggota_id) {
    $query .= " WHERE p.id_anggota = '$anggota_id'";
}

$query .= " ORDER BY p.tanggal_pinjaman DESC";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjaman - SIKOPIN</title>
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

            <?php if (in_array($user_role, ['ketua', 'petugas'])) : ?>
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
        <h4 class="fw-bold mb-4">Pinjaman</h4>
        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <?php if (in_array($user_role, ['ketua', 'petugas'])) : ?>
                    <a href="tambah_pinjaman.php" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus"></i> Buat</a>
                    <?php endif; ?>
                </div>
                <div>
                    <button class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-download"></i> Unduh</button>
                    <?php if ($user_role === 'ketua') : ?>
                    <button class="btn btn-outline-info rounded-pill px-4 ms-2" onclick="printTable()"><i class="bi bi-printer"></i> Cetak</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="table-responsive" id="pinjamanTable">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Jumlah Pinjaman</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Bunga</th>
                            <th>Total Pinjaman</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status_badge_class = (
                                    $row['status'] === 'disetujui' ? 'bg-success-subtle text-success' :
                                    ($row['status'] === 'pending' ? 'bg-secondary-subtle text-secondary' :
                                    'bg-danger-subtle text-danger')
                                );

                                $jumlah_pinjaman_formatted = 'Rp ' . number_format($row['jumlah_pinjaman'], 0, ',', '.');
                                $bunga_formatted = $row['bunga'] . '%';

                                // Hitung total pinjaman secara dinamis jika diperlukan untuk tampilan
                                $total_pinjaman_calculated = $row['jumlah_pinjaman'] + ($row['jumlah_pinjaman'] * ($row['bunga'] / 100));
                                $total_pinjaman_formatted = 'Rp ' . number_format($total_pinjaman_calculated, 0, ',', '.');

                                $tanggal_pinjam_formatted = date('d F Y', strtotime($row['tanggal_pinjaman']));
                                // Menggunakan fiscal_date sebagai tanggal kembali
                                $tanggal_kembali_formatted = date('d F Y', strtotime($row['fiscal_date']));

                                echo '<tr id="pinjaman-' . $row['id_pinjaman'] . '">';
                                echo '<td>' . $no++ . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_anggota']) . '</td>';
                                echo '<td>' . $jumlah_pinjaman_formatted . '</td>';
                                echo '<td>' . $tanggal_pinjam_formatted . '</td>';
                                echo '<td>' . $tanggal_kembali_formatted . '</td>';
                                echo '<td><span class="badge ' . $status_badge_class . '">' . htmlspecialchars(ucfirst($row['status'])) . '</span></td>';
                                echo '<td>' . $bunga_formatted . '</td>';
                                echo '<td>' . $total_pinjaman_formatted . '</td>';
                                echo '<td>';
                                if (in_array($user_role, ['ketua', 'petugas'])) {
                                    echo '<a href="edit_pinjaman.php?id=' . $row['id_pinjaman'] . '" class="text-primary me-2"><i class="bi bi-pencil"></i> Edit</a>';
                                    echo '<a href="#" onclick="confirmDelete(\'proses_hapus_pinjaman.php?id=' . $row['id_pinjaman'] . '\')" class="text-danger"><i class="bi bi-trash"></i> Hapus</a>';
                                } else {
                                    echo '<a href="detail_pinjaman.php?id=' . $row['id_pinjaman'] . '" class="text-primary"><i class="bi bi-eye"></i> View</a>';
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="9" class="text-center">Tidak ada data pinjaman.</td></tr>';
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
    function updateDateTime() {
        const now = new Date();
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('datetime').textContent = now.toLocaleString('id-ID', options);
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    function confirmDelete(deleteUrl) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            window.location.href = deleteUrl;
        }
    }

    function printTable() {
        var printContents = document.getElementById("pinjamanTable").innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents; 
        window.location.reload(); 
    }
    </script>
</body>
</html> 