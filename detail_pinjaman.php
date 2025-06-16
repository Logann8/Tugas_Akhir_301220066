<?php
session_start();
$allowed_roles = ['ketua', 'petugas', 'anggota'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'anggota'; // Default ke anggota jika tidak terdefinisi

require 'config/database.php';

$pinjaman_id = $_GET['id'] ?? 0;

if (!$pinjaman_id) {
    $_SESSION['error_message'] = "ID pinjaman tidak ditemukan.";
    header('Location: pinjaman.php');
    exit;
}

$anggota_id_session = null;
if ($user_role === 'anggota') {
    // Dapatkan id_anggota yang terkait dengan user_id yang sedang login
    $user_id_session = $_SESSION['user_id'] ?? 0;
    $query_anggota_id = "SELECT id_anggota FROM anggota WHERE id_user = '$user_id_session' LIMIT 1";
    $result_anggota_id = mysqli_query($conn, $query_anggota_id);
    if ($result_anggota_id && mysqli_num_rows($result_anggota_id) > 0) {
        $row_anggota_id = mysqli_fetch_assoc($result_anggota_id);
        $anggota_id_session = $row_anggota_id['id_anggota'];
    }
}

// Query untuk mengambil data pinjaman berdasarkan ID
$query = "SELECT p.*, a.nama AS nama_anggota, a.email AS email_anggota, a.no_telp AS no_telp_anggota, a.alamat AS alamat_anggota 
          FROM pinjaman p
          JOIN anggota a ON p.id_anggota = a.id_anggota
          WHERE p.id_pinjaman = '$pinjaman_id'";

if ($user_role === 'anggota' && $anggota_id_session) {
    $query .= " AND p.id_anggota = '$anggota_id_session'";
}

$result = mysqli_query($conn, $query);
$pinjaman = mysqli_fetch_assoc($result);

if (!$pinjaman) {
    $_SESSION['error_message'] = "Data pinjaman tidak ditemukan atau Anda tidak memiliki akses.";
    header('Location: pinjaman.php');
    exit;
}

// Format data untuk tampilan
$jumlah_pinjaman_formatted = 'Rp ' . number_format($pinjaman['jumlah_pinjaman'], 0, ',', '.');
$bunga_formatted = $pinjaman['bunga'] . '%';
$total_pinjaman_calculated = $pinjaman['jumlah_pinjaman'] + ($pinjaman['jumlah_pinjaman'] * ($pinjaman['bunga'] / 100));
$total_pinjaman_formatted = 'Rp ' . number_format($total_pinjaman_calculated, 0, ',', '.');

$tanggal_pinjam_formatted = date('d F Y', strtotime($pinjaman['tanggal_pinjaman']));
$tanggal_kembali_formatted = date('d F Y', strtotime($pinjaman['fiscal_date'])); // Menggunakan fiscal_date sebagai tanggal kembali

// Tentukan kelas badge
$status_badge_class = (
    $pinjaman['status'] === 'disetujui' ? 'bg-success-subtle text-success' :
    ($pinjaman['status'] === 'pending' ? 'bg-secondary-subtle text-secondary' :
    'bg-danger-subtle text-danger')
);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pinjaman - SIKOPIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; // Sesuaikan jika ada header ?>
    <div class="main-content">
        <h4 class="fw-bold mb-4">Detail Pinjaman</h4>
        <div class="card p-4 mb-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>ID Pinjaman:</strong> <?php echo htmlspecialchars($pinjaman['id_pinjaman']); ?></p>
                    <p><strong>Nama Anggota:</strong> <?php echo htmlspecialchars($pinjaman['nama_anggota']); ?></p>
                    <p><strong>Email Anggota:</strong> <?php echo htmlspecialchars($pinjaman['email_anggota']); ?></p>
                    <p><strong>No. Telepon Anggota:</strong> <?php echo htmlspecialchars($pinjaman['no_telp_anggota']); ?></p>
                    <p><strong>Alamat Anggota:</strong> <?php echo htmlspecialchars($pinjaman['alamat_anggota']); ?></p>
                    <p><strong>Jumlah Pinjaman:</strong> <?php echo $jumlah_pinjaman_formatted; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tanggal Pinjam:</strong> <?php echo $tanggal_pinjam_formatted; ?></p>
                    <p><strong>Tanggal Kembali:</strong> <?php echo $tanggal_kembali_formatted; ?></p>
                    <p><strong>Bunga:</strong> <?php echo $bunga_formatted; ?></p>
                    <p><strong>Total Pinjaman:</strong> <?php echo $total_pinjaman_formatted; ?></p>
                    <p><strong>Status:</strong> <span class="badge <?php echo $status_badge_class; ?>"><?php echo htmlspecialchars(ucfirst($pinjaman['status'])); ?></span></p>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="pinjaman.php" class="btn btn-secondary rounded-pill px-4"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; // Sesuaikan jika ada footer ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 