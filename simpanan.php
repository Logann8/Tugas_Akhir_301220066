<?php
session_start();
$allowed_roles = ['ketua', 'petugas', 'anggota'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'anggota'; // Default ke anggota jika tidak terdefinisi

require 'config/database.php';

// Query untuk mengambil data simpanan
$query = "SELECT s.*, a.nama AS nama_anggota 
          FROM simpanan s
          JOIN anggota a ON s.id_anggota = a.id_anggota
          ORDER BY s.tanggal DESC";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simpanan - SIKOPIN</title>
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
                <a class="nav-link active" href="simpanan.php"><i class="bi bi-wallet2"></i> <span>Simpanan</span></a>
            </li>
            <li class="nav-item mb-1 ms-2">
                <a class="nav-link" href="pinjaman.php"><i class="bi bi-cash-stack"></i> <span>Pinjaman</span></a>
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
        <h4 class="fw-bold mb-4">Simpanan</h4>
        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <?php if (in_array($user_role, ['ketua', 'petugas'])) : ?>
                    <a href="tambah_simpanan.php" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus"></i> Buat</a>
                    <?php endif; ?>
                </div>
                <div>
                    <button class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-download"></i> Unduh</button>
                    <?php if ($user_role === 'ketua') : ?>
                    <button class="btn btn-outline-info rounded-pill px-4 ms-2" onclick="printTable()"><i class="bi bi-printer"></i> Cetak</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="table-responsive" id="simpananTable">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Subtotal</th>
                            <th>Fee</th>
                            <th>Total</th>
                            <th>Fiscal date</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Menentukan kelas badge berdasarkan nilai dari database
                                $type_badge_class = (
                                    $row['jenis_simpanan'] === 'pokok' ? 'bg-danger-subtle text-danger' :
                                    ($row['jenis_simpanan'] === 'wajib' ? 'bg-warning-subtle text-warning' :
                                    'bg-info-subtle text-info')
                                );
                                $plan_badge_class = (
                                    $row['plan'] === 'sekali' ? 'bg-primary-subtle text-primary' :
                                    'bg-dark-subtle text-dark'
                                );
                                $status_badge_class = (
                                    $row['status'] === 'verified' ? 'bg-success-subtle text-success' :
                                    ($row['status'] === 'pending' ? 'bg-secondary-subtle text-secondary' :
                                    'bg-danger-subtle text-danger')
                                );
                                
                                // Format angka ke format rupiah
                                $jumlah_formatted = 'Rp ' . number_format($row['jumlah'], 0, ',', '.');
                                $fee_formatted = 'Rp ' . number_format($row['fee'], 0, ',', '.');
                                $total = $row['jumlah'] + $row['fee'];
                                $total_formatted = 'Rp ' . number_format($total, 0, ',', '.');

                                // Format tanggal
                                $fiscal_date_formatted = date('d F Y H:i', strtotime($row['fiscal_date']));

                                echo '<tr id="simpanan-' . $row['id_simpanan'] . '">';
                                echo '<td>' . $no++ . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_anggota']) . '</td>';
                                echo '<td><span class="badge ' . $type_badge_class . '">' . htmlspecialchars(ucfirst($row['jenis_simpanan'])) . '</span></td>';
                                echo '<td><span class="badge ' . $plan_badge_class . '">' . htmlspecialchars(ucfirst($row['plan'])) . '</span></td>';
                                echo '<td><span class="badge ' . $status_badge_class . '">' . htmlspecialchars(ucfirst($row['status'])) . '</span></td>';
                                echo '<td>' . $jumlah_formatted . '</td>';
                                echo '<td>' . $fee_formatted . '</td>';
                                echo '<td>' . $total_formatted . '</td>';
                                echo '<td>' . $fiscal_date_formatted . '</td>';
                                echo '<td>';
                                // Tombol aksi hanya untuk Ketua dan Petugas
                                if (in_array($user_role, ['ketua', 'petugas'])) {
                                    echo '<a href="edit_simpanan.php?id=' . $row['id_simpanan'] . '" class="text-primary me-2"><i class="bi bi-pencil"></i> Edit</a>';
                                    echo '<a href="#" onclick="confirmDelete(\'proses_hapus_simpanan.php?id=' . $row['id_simpanan'] . '\')" class="text-danger"><i class="bi bi-trash"></i> Hapus</a>';
                                } else {
                                    echo '<a href="detail_simpanan.php?id=' . $row['id_simpanan'] . '" class="text-primary"><i class="bi bi-eye"></i> View</a>';
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="10" class="text-center">Tidak ada data simpanan.</td></tr>';
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

    // Konfirmasi hapus
    function confirmDelete(deleteUrl) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            window.location.href = deleteUrl;
        }
    }

    // Fungsi untuk mencetak tabel
    function printTable() {
        var printContents = document.getElementById("simpananTable").innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents; // Mengembalikan konten asli
        window.location.reload(); // Memuat ulang halaman untuk mengembalikan event listener dan script
    }
    </script>
</body>
</html> 