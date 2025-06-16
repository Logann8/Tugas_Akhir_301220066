<?php
session_start();
$allowed_roles = ['ketua', 'petugas', 'anggota'];
if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), $allowed_roles)) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'anggota'; // Default ke anggota jika tidak terdefinisi

require 'config/database.php';

$simpanan_id = $_GET['id'] ?? 0;

if (!$simpanan_id) {
    $_SESSION['error_message'] = "ID simpanan tidak ditemukan.";
    header('Location: simpanan.php');
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

// Query untuk mengambil data simpanan berdasarkan ID
$query = "SELECT s.*, a.nama AS nama_anggota, a.email AS email_anggota, a.no_telp AS no_telp_anggota, a.alamat AS alamat_anggota 
          FROM simpanan s
          JOIN anggota a ON s.id_anggota = a.id_anggota
          WHERE s.id_simpanan = '$simpanan_id'";

if ($user_role === 'anggota' && $anggota_id_session) {
    $query .= " AND s.id_anggota = '$anggota_id_session'";
}

$result = mysqli_query($conn, $query);
$simpanan = mysqli_fetch_assoc($result);

if (!$simpanan) {
    $_SESSION['error_message'] = "Data simpanan tidak ditemukan atau Anda tidak memiliki akses.";
    header('Location: simpanan.php');
    exit;
}

// Format data untuk tampilan
$jumlah_formatted = 'Rp ' . number_format($simpanan['jumlah'], 0, ',', '.');
$fee_formatted = 'Rp ' . number_format($simpanan['fee'], 0, ',', '.');
$total = $simpanan['jumlah'] + $simpanan['fee'];
$total_formatted = 'Rp ' . number_format($total, 0, ',', '.');
$fiscal_date_formatted = date('d F Y H:i', strtotime($simpanan['fiscal_date']));

// Tentukan kelas badge
$type_badge_class = (
    $simpanan['jenis_simpanan'] === 'pokok' ? 'bg-danger-subtle text-danger' :
    ($simpanan['jenis_simpanan'] === 'wajib' ? 'bg-warning-subtle text-warning' :
    'bg-info-subtle text-info')
);
$plan_badge_class = (
    $simpanan['plan'] === 'sekali' ? 'bg-primary-subtle text-primary' :
    'bg-dark-subtle text-dark'
);
$status_badge_class = (
    $simpanan['status'] === 'verified' ? 'bg-success-subtle text-success' :
    ($simpanan['status'] === 'pending' ? 'bg-secondary-subtle text-secondary' :
    'bg-danger-subtle text-danger')
);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Simpanan - SIKOPIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; // Sesuaikan jika ada header ?>
    <div class="main-content">
        <h4 class="fw-bold mb-4">Detail Simpanan</h4>
        <div class="card p-4 mb-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>ID Simpanan:</strong> <?php echo htmlspecialchars($simpanan['id_simpanan']); ?></p>
                    <p><strong>Nama Anggota:</strong> <?php echo htmlspecialchars($simpanan['nama_anggota']); ?></p>
                    <p><strong>Email Anggota:</strong> <?php echo htmlspecialchars($simpanan['email_anggota']); ?></p>
                    <p><strong>No. Telepon Anggota:</strong> <?php echo htmlspecialchars($simpanan['no_telp_anggota']); ?></p>
                    <p><strong>Alamat Anggota:</strong> <?php echo htmlspecialchars($simpanan['alamat_anggota']); ?></p>
                    <p><strong>Jenis Simpanan:</strong> <span class="badge <?php echo $type_badge_class; ?>"><?php echo htmlspecialchars(ucfirst($simpanan['jenis_simpanan'])); ?></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Plan:</strong> <span class="badge <?php echo $plan_badge_class; ?>"><?php echo htmlspecialchars(ucfirst($simpanan['plan'])); ?></span></p>
                    <p><strong>Status:</strong> <span class="badge <?php echo $status_badge_class; ?>"><?php echo htmlspecialchars(ucfirst($simpanan['status'])); ?></span></p>
                    <p><strong>Jumlah:</strong> <?php echo $jumlah_formatted; ?></p>
                    <p><strong>Fee:</strong> <?php echo $fee_formatted; ?></p>
                    <p><strong>Total:</strong> <?php echo $total_formatted; ?></p>
                    <p><strong>Tanggal Transaksi:</strong> <?php echo $fiscal_date_formatted; ?></p>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="simpanan.php" class="btn btn-secondary rounded-pill px-4"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; // Sesuaikan jika ada footer ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 