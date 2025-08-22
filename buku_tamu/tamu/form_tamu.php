<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../admin/index.php");
    exit();
}

include '../koneksi.php';
$username = $_SESSION['username'];

// Notifikasi sukses
$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

$error = '';

// Ambil tanggal & waktu saat ini
$tanggal = date('Y-m-d');
$waktu   = date('H:i');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $instansi = mysqli_real_escape_string($conn, trim($_POST['instansi']));
    $keperluan = mysqli_real_escape_string($conn, trim($_POST['keperluan']));

    // Validate input
    if (empty($nama) || empty($instansi) || empty($keperluan)) {
        $error = 'Semua field harus diisi!';
    } else {
        // Insert data
        $query = "INSERT INTO tamu (nama, instansi, keperluan, tanggal, waktu) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $instansi, $keperluan, $tanggal, $waktu);
        
        if (mysqli_stmt_execute($stmt)) {
            // Gunakan session + redirect agar form bersih
            $_SESSION['success'] = 'Data tamu berhasil disimpan!';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error = 'Gagal menyimpan data: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Buku Tamu - SMKN 71 Jakarta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* CSS (tidak berubah) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        .header {
            background: linear-gradient(135deg, #1abc9c, #16a085);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        .form-content {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1abc9c;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1abc9c, #16a085);
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(26, 188, 156, 0.4);
        }
        .btn-secondary {
            background: #ccc;
            color: #333;
            text-align: center;
            display: block;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background: #bbb;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 600px) {
            .form-container {
                margin: 10px;
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <h1><i class="fas fa-book"></i> Form Buku Tamu</h1>
            <p>Silakan isi form berikut untuk mencatat kunjungan Anda</p>
        </div>

        <div class="form-content">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama"><i class="fas fa-user"></i> Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" value="<?= $success ? '' : (isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="instansi"><i class="fas fa-building"></i> Instansi/Perusahaan *</label>
                    <input type="text" id="instansi" name="instansi" value="<?= $success ? '' : (isset($_POST['instansi']) ? htmlspecialchars($_POST['instansi']) : '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="keperluan"><i class="fas fa-tasks"></i> Keperluan Kunjungan *</label>
                    <textarea id="keperluan" name="keperluan" required><?= $success ? '' : (isset($_POST['keperluan']) ? htmlspecialchars($_POST['keperluan']) : '') ?></textarea>
                </div>
            </form>

            <!-- Tombol Simpan -->
            <button type="button" class="btn btn-primary" onclick="konfirmasiData()">
                <i class="fas fa-save"></i> Simpan Data
            </button>
            <a href="dashboard_tamu.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

<script>
  function konfirmasiData() {
    const nama = document.getElementById('nama').value.trim();
    const instansi = document.getElementById('instansi').value.trim();
    const keperluan = document.getElementById('keperluan').value.trim();

    if (nama === "" || instansi === "" || keperluan === "") {
      Swal.fire({
        icon: 'warning',
        title: 'Lengkapi form!',
        text: 'Semua field wajib diisi.'
      });
      return;
    }

    Swal.fire({
      title: 'Konfirmasi Data',
      html: `
        <strong>Nama:</strong> ${nama}<br>
        <strong>Instansi:</strong> ${instansi}<br>
        <strong>Keperluan:</strong> ${keperluan}<br><br>
        Pastikan data sudah benar.`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sudah Benar & Kirim',
      cancelButtonText: 'Edit Kembali'
    }).then((result) => {
      if (result.isConfirmed) {
        document.querySelector("form").submit();
      }
    });
  }
</script>

</body>
</html>
