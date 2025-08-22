<?php
// --- Koneksi ke database ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "resepsionis";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// --- Tahun sekarang ---
$tahun_sekarang = date("Y");

// --- Ambil filter tahun ---
$tahun_filter = isset($_GET['tahun']) ? (int)$_GET['tahun'] : $tahun_sekarang;
$tamu_terakhir = mysqli_query($koneksi, "
    SELECT * FROM tamu 
    WHERE YEAR(tanggal) = '$tahun_filter' 
    ORDER BY tanggal DESC 
    LIMIT 1
");
$tamu = mysqli_fetch_assoc($tamu_terakhir);


// --- Ambil data grafik per bulan ---
$grafik = mysqli_query($koneksi, "
    SELECT MONTH(tanggal) AS bulan, COUNT(*) AS jumlah
    FROM tamu
    WHERE YEAR(tanggal) = '$tahun_filter'
    GROUP BY MONTH(tanggal)
    ORDER BY MONTH(tanggal)
");

$data_grafik = [];
while ($row = mysqli_fetch_assoc($grafik)) {
    $data_grafik[] = $row;
}

// Nama bulan
$nama_bulan = [
    1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
    5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
    9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tamu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        h2, h5 {
            text-align: center;
        }
        .btn-custom {
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container">

    <h2 class="mb-4 text-primary">📑 Laporan Kunjungan Tamu</h2>

    <!-- Filter Tahun -->
    <div class="card p-4">
        <form method="GET" class="row g-2 align-items-center justify-content-center">
            <div class="col-md-3">
                <label for="tahun" class="form-label fw-bold">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    <?php for ($t = 2019; $t <= $tahun_sekarang; $t++): ?>
                        <option value="<?= $t ?>" <?= ($t == $tahun_filter) ? 'selected' : '' ?>>
                            <?= $t ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Info Tamu Terakhir -->
    <div class="card p-4">
        <h5 class="fw-bold text-success">👤 Tamu Terakhir</h5>
        <?php if ($tamu): ?>
            <p><strong>Nama:</strong> <?= htmlspecialchars($tamu['nama']) ?></p>
            <p><strong>Tanggal:</strong> <?= $tamu['tanggal'] ?></p>
        <?php else: ?>
            <p class="text-muted">Belum ada data tamu.</p>
        <?php endif; ?>
    </div>

    <!-- Grafik -->
    <div class="card p-4">
        <h5 class="fw-bold text-info mb-3">📊 Statistik Tamu Bulanan - <?= $tahun_filter ?></h5>
        <canvas id="grafikTamu" height="120"></canvas>
    </div>

    <!-- Tombol Aksi -->
    <div class="text-center mt-3">
        <a href="../admin/dashboard.php" class="btn btn-outline-secondary btn-custom me-2">⬅ Kembali ke Dashboard</a>
        <button class="btn btn-success btn-custom" onclick="downloadChart()">⬇ Unduh Grafik</button>
    </div>
</div>

<script>
    const dataGrafik = <?= json_encode($data_grafik) ?>;
    const namaBulan = <?= json_encode($nama_bulan) ?>;

    const labels = [];
    const dataJumlah = [];

    for (let i = 1; i <= 12; i++) {
        labels.push(namaBulan[i]);
        const found = dataGrafik.find(item => item.bulan == i);
        dataJumlah.push(found ? parseInt(found.jumlah) : 0);
    }

    const ctx = document.getElementById('grafikTamu').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Tamu',
                data: dataJumlah,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(13, 110, 253, 0.9)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.parsed.y} tamu`;
                        }
                    }
                },
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
function downloadChart() {
    // Pastikan grafik sudah selesai di-render
    setTimeout(() => {
        const url = chart.toBase64Image('image/png', 2); // 2x quality
        const link = document.createElement('a');
        link.href = url;
        link.download = "laporan_tamu_<?= $tahun_filter ?>.png";
        link.click();
    }, 500);
}

</script>
</body>
</html>
