<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor - SIKOPIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            padding-top: 1rem;
        }
        .sidebar .nav-link.active {
            background: #e9ecef;
            font-weight: bold;
        }
        .sidebar .nav-link {
            color: #333;
        }
        .topbar {
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }
        .profile-circle {
            width: 32px;
            height: 32px;
            background: #bbb;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }
        .card-stat {
            min-width: 180px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3">
            <div class="mb-4 d-flex align-items-center">
                <div class="profile-circle me-2"><i class="bi bi-person"></i></div>
                <span class="fw-bold">SIKOPIN</span>
            </div>
            <ul class="nav flex-column mb-4">
                <li class="nav-item mb-1">
                    <a class="nav-link active" href="#"><i class="bi bi-speedometer2 me-2"></i>Dasbor</a>
                </li>
                <li class="nav-item mb-1">
                    <span class="text-muted small ms-2">Main</span>
                </li>
                <li class="nav-item mb-1 ms-2">
                    <a class="nav-link" href="#"><i class="bi bi-wallet2 me-2"></i>Simpanan</a>
                </li>
                <li class="nav-item mb-1 ms-2">
                    <a class="nav-link" href="#"><i class="bi bi-cash-stack me-2"></i>Pinjaman</a>
                </li>
                <li class="nav-item mb-1 mt-2">
                    <span class="text-muted small ms-2">Master Data</span>
                </li>
                <li class="nav-item mb-1 ms-2">
                    <a class="nav-link" href="#"><i class="bi bi-people me-2"></i>Anggota</a>
                </li>
                <li class="nav-item mb-1 mt-2">
                    <span class="text-muted small ms-2">Settings</span>
                </li>
                <li class="nav-item mb-1 ms-2">
                    <a class="nav-link" href="#"><i class="bi bi-person-gear me-2"></i>User</a>
                </li>
            </ul>
        </div>
        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="topbar d-flex justify-content-between align-items-center">
                <span></span>
                <span class="fw-bold">SIKOPIN</span>
                <div class="profile-circle"><i class="bi bi-person"></i></div>
            </div>
            <div class="container-fluid mt-4">
                <h3 class="fw-bold mb-4">Dasbor</h3>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card card-stat">
                            <div class="card-body text-center">
                                <div class="fw-bold">Customer</div>
                                <div class="display-6">1</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card card-stat">
                            <div class="card-body text-center">
                                <div class="fw-bold">Deposit</div>
                                <div class="display-6">1</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card card-stat">
                            <div class="card-body text-center">
                                <div class="fw-bold">Loan</div>
                                <div class="display-6">1</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="fw-bold mb-2">Annual Report</div>
                        <canvas id="annualChart" height="120"></canvas>
                        <div class="mt-2 text-end">
                            <label class="me-2"><input type="checkbox" checked disabled> Simpanan</label>
                            <label class="ms-2"><input type="checkbox" checked disabled> Loan</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script>
    // Dummy data untuk chart
    const ctx = document.getElementById('annualChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [
                {
                    label: 'Simpanan',
                    data: [0.2,0.4,0.5,0.7,0.8,0.9,1,0.8,0.7,0.6,0.5,0.4],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3
                },
                {
                    label: 'Loan',
                    data: [0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.6,0.5,0.4,0.3,0.2],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25,135,84,0.1)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1.0,
                    ticks: { stepSize: 0.1 }
                }
            }
        }
    });
    </script>
</body>
</html> 