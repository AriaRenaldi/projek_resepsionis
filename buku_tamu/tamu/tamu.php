<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../admin/index.php");
    exit();
}

include '../koneksi.php';
$username = $_SESSION['username'];

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM tamu WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        $success_msg = "Data tamu berhasil dihapus!";
        // Refresh the page to show updated data
        header("Location: tamu.php?success=".urlencode($success_msg));
        exit();
    } else {
        $error_msg = "Gagal menghapus data: " . mysqli_error($conn);
    }
}

// Ambil input filter tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Pagination
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Filter query
$filter_sql = "";
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $filter_sql = " WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

// Hitung total data untuk pagination
$query_total = "SELECT COUNT(*) AS total FROM tamu $filter_sql";
$total_result = mysqli_query($conn, $query_total);
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_halaman = ceil($total_data / $batas);

// Ambil data tamu
$query = "SELECT * FROM tamu $filter_sql ORDER BY id DESC LIMIT $mulai, $batas";
$data = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tamu - SMKN 71 Jakarta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
         /* Tambahan loading screen */
    #loading-screen {
        position: fixed;
        width: 100%;
        height: 100%;
        background: white;
        z-index: 2000;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease;
    }

    .spinner {
        border: 8px solid #f3f3f3;
        border-top: 8px solid #3498db;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

.loading-text {
    text-align: center;
    font-size: 18px;
    color: #2c3e50;
    font-weight: 500;
    line-height: 1.6;
}

.loading-text span {
    font-size: 15px;
    color: #7f8c8d;
    font-style: italic;
}
        /* ===== Reset & Body ===== */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #ffffff, #3498db);
            min-height: 100vh;
            display: flex;
            color: #333;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: bold;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 12px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar ul li a:hover {
            background: #3498db;
            transform: translateX(5px);
        }

        .menu-toggle {
            position: fixed;
            top: 15px;
            left: 6px;
            font-size: 24px;
            color: #2980b9;
            cursor: pointer;
            z-index: 1100;
        }

        /* ===== Content ===== */
        .content {
            flex: 1;
            margin-left: 220px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .sidebar.hidden ~ .content {
            margin-left: 0;
        }

        /* ===== Header ===== */
        .header {
            background: rgba(255, 255, 255, 0.9);
            color: #2c3e50;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #3498db;
        }

        .header h2 {
            margin: 10px 0;
            font-size: 22px;
            color: #2c3e50;
        }

        .header p {
            margin: 10px 0 0;
            font-style: italic;
            color: #7f8c8d;
        }

        /* ===== Filter Form ===== */
        form.filter {
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        form.filter label {
            font-weight: 500;
            color: #2c3e50;
        }

        form.filter input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
        }

        form.filter button, .reset-btn {
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        form.filter button {
            background: #3498db;
            color: white;
        }

        form.filter button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .reset-btn {
            background: #e74c3c;
            color: white;
            text-decoration: none;
        }

        .reset-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* ===== Table ===== */
        .table-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #3498db;
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f5f9fc;
        }

        /* Action Buttons */
        .btn-action {
            padding: 6px 10px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #f39c12;
        }

        .btn-edit:hover {
            background: #e67e22;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #e74c3c;
        }

        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: white;
        }

        .alert-success {
            background: #2ecc71;
        }

        .alert-error {
            background: #e74c3c;
        }

        /* ===== Pagination ===== */
        .pagination {
            margin-top: 20px;
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .pagination a {
            padding: 8px 12px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .pagination a.active {
            background: #2c3e50;
        }

        /* Confirmation Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 80%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
                padding: 15px;
            }
            .menu-toggle {
                left: 15px;
            }
            
            form.filter {
                flex-direction: column;
                align-items: stretch;
            }
            
            .modal-content {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('hidden');
    }
    
    // Function to show delete confirmation modal
    function confirmDelete(id) {
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('confirmDeleteBtn').onclick = function() {
            window.location.href = 'tamu.php?delete_id=' + id;
        }
    }
    
    // Function to close modal
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    </script>

    <!-- Tombol toggle menu -->
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    
<!-- Loading Screen --><!-- Loading Screen -->
<div id="loading-screen">
    <div class="spinner"></div>
    <p class="loading-text">Mohon tunggu sebentar...<br><span>Dashboard sedang dipersiapkan ✨</span></p>
</div>


    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Menu</h2>
        <ul>
            <li><a href="../admin/dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="../tamu/tamu.php"><i class="fas fa-users"></i> Tamu</a></li>
            <li><a href="../admin/t_admin.php"><i class="fas fa-user-shield"></i> Admin</a></li>
            <li><a href="kehadiran.php"><i class="fas fa-user-check"></i> Kehadiran</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Laporan</a></li>
            <li><a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="header">
            <h1>Selamat Datang, Di Buku Tamu <?= htmlspecialchars($username) ?></h1>
            <h2>SMKN 71 JAKARTA</h2>
            <p>"Kesuksesan adalah hasil dari kerja keras dan ketekunan."</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>
        
        <form class="filter" method="GET" action="">
            <label><i class="fas fa-calendar-alt"></i> Dari:</label>
            <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>">
            
            <label>Sampai:</label>
            <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>">
            
            <button type="submit"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="tamu.php" class="reset-btn"><i class="fas fa-sync-alt"></i> Reset</a>
        </form>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Instansi</th>
                        <th>Keperluan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = $mulai + 1;
                    while ($row = mysqli_fetch_assoc($data)) {
                        echo "<tr>
                            <td>$no</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['instansi']}</td>
                            <td>{$row['keperluan']}</td>
                            <td>{$row['tanggal']}</td>
                            <td>{$row['waktu']}</td>
                            <td>
                               
                                <a href='#' onclick='confirmDelete({$row['id']})' class='btn-action btn-delete'>
                                    <i class='fas fa-trash'></i> Hapus
                                </a>
                            </td>
                        </tr>";
                        $no++;
                    }
                    if (mysqli_num_rows($data) == 0) {
                        echo "<tr><td colspan='7' style='text-align:center;'>Tidak ada data tamu</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_halaman; $i++) : ?>
                <a class="<?= ($i == $halaman) ? 'active' : '' ?>" href="?halaman=<?= $i ?>&tanggal_awal=<?= urlencode($tanggal_awal) ?>&tanggal_akhir=<?= urlencode($tanggal_akhir) ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Penghapusan</h3>
            <p>Apakah Anda yakin ingin menghapus data tamu ini?</p>
            <p class="text-muted">Data yang dihapus tidak dapat dikembalikan.</p>
            <div class="modal-actions">
                <button onclick="closeModal()" class="btn-action" style="background: #7f8c8d;">Batal</button>
                <button id="confirmDeleteBtn" class="btn-action btn-delete">Hapus</button>
            </div>
        </div>
    </div>
    
<script>
   window.addEventListener('load', function() {
    const loading = document.getElementById('loading-screen');
    setTimeout(() => {
        loading.style.opacity = '0';
        setTimeout(() => loading.style.display = 'none', 500);
    }, 2000); // tampil selama 2 detik
});

</script>
</body>
</html>