<?php
include '../koneksi.php';

// Get year filter
$tahun_filter = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date("Y");

// Name search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total data
$sql_count = "SELECT COUNT(*) as total FROM tamu WHERE YEAR(tanggal) = $tahun_filter";
if (!empty($search)) {
    $sql_count .= " AND nama LIKE '%$search%'";
}
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];
$total_pages = ceil($total_data / $limit);

// Get data
$sql_data = "SELECT * FROM tamu WHERE YEAR(tanggal) = $tahun_filter";
if (!empty($search)) {
    $sql_data .= " AND nama LIKE '%$search%'";
}
$sql_data .= " ORDER BY tanggal DESC LIMIT $limit OFFSET $offset";
$result_data = mysqli_query($conn, $sql_data);
// CSV Download
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    // Matikan semua output buffering & error supaya CSV tidak rusak
    ob_clean();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="kehadiran_'.$tahun_filter.'.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['No', 'Nama', 'Tanggal', 'Waktu']);

    $no = 1;
    $sql_all = "SELECT * FROM tamu WHERE YEAR(tanggal) = $tahun_filter";
    if (!empty($search)) {
        $sql_all .= " AND nama LIKE '%$search%'";
    }
    $sql_all .= " ORDER BY tanggal DESC"; // tanpa LIMIT

    $result_all = mysqli_query($conn, $sql_all);

    while ($row = mysqli_fetch_assoc($result_all)) {
        fputcsv($output, [
            $no++,
            $row['nama'],
            date('d/m/Y', strtotime($row['tanggal'])),
            $row['waktu']
        ]);
    }
    fclose($output);
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            max-width: 1200px;
            padding: 2rem 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        
        .card-header h2 {
            font-weight: 300;
            letter-spacing: 0.5px;
            margin: 0;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-select, .form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(86, 171, 47, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%);
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
            transform: scale(1.01);
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f0f0f0;
        }
        
        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }
        
        .page-link {
            border-radius: 10px;
            margin: 0 5px;
            border: none;
            color: #667eea;
            transition: all 0.3s ease;
        }
        
        .page-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
        }
        
        .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        
        .card-footer {
            background: rgba(248, 249, 250, 0.5);
            border-radius: 0 0 15px 15px !important;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
        }
        
        /* Smooth animations */
        * {
            transition: all 0.3s ease;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Rekap Kehadiran Tamu <?= $tahun_filter ?></h2>
            </div>
            
            <div class="card-body">
                <!-- Filter and search form -->
                <form method="get" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="tahun" class="form-label">Pilih Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                            <?php for ($tahun = 2019; $tahun <= date("Y"); $tahun++): ?>
                            <option value="<?= $tahun ?>" <?= $tahun == $tahun_filter ? 'selected' : '' ?>><?= $tahun ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-5">
                        <label for="search" class="form-label">Cari Nama:</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" 
                                   class="form-control" placeholder="Cari nama...">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <a href="?tahun=<?= $tahun_filter ?>&search=<?= urlencode($search) ?>&download=csv" 
                           class="btn btn-success">
                            <i class="bi bi-download"></i> Download CSV
                        </a>
                    </div>
                </form>

                <!-- Attendance table -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="40%">Nama</th>
                                <th width="30%">Tanggal</th>
                                <th width="25%">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_data) > 0): ?>
                                <?php $no = $offset + 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result_data)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= $row['waktu'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada data yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?tahun=<?= $tahun_filter ?>&search=<?= urlencode($search) ?>&page=<?= $page-1 ?>">
                                Previous
                            </a>
                        </li>
                        
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?tahun=<?= $tahun_filter ?>&search=<?= urlencode($search) ?>&page=<?= $p ?>">
                                <?= $p ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?tahun=<?= $tahun_filter ?>&search=<?= urlencode($search) ?>&page=<?= $page+1 ?>">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-light">
                <a href="../admin/dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>