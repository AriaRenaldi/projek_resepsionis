<?php
include '../koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); window.location.href='t_admin.php';</script>";
    exit;
}

// Ambil data admin berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM admin WHERE id = '$id'");
$admin = mysqli_fetch_assoc($query);

if (!$admin) {
    echo "<script>alert('Data admin tidak ditemukan'); window.location.href='t_admin.php';</script>";
    exit;
}
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $level = $_POST['level'];

    // Cek apakah email sudah digunakan oleh admin lain
    $check = mysqli_query($conn, "SELECT id FROM admin WHERE email = '$email' AND id != '$id'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email sudah digunakan oleh admin lain!');</script>";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update = mysqli_query($conn, "UPDATE admin SET email='$email', username='$username', password='$hashed_password', level='$level' WHERE id='$id'");
        } else {
            $update = mysqli_query($conn, "UPDATE admin SET email='$email', username='$username', level='$level' WHERE id='$id'");
        }

        if ($update) {
            echo "<script>alert('Admin berhasil diupdate'); window.location.href='t_admin.php';</script>";
        } else {
            echo "<script>alert('Gagal update admin: " . mysqli_error($conn) . "');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #6dd5fa, #ffffff);
            padding: 40px;
            font-family: Arial, sans-serif;
        }

        .form-container {
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        h4 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: #2c3e50;
        }
               /* Tambahan loading screen */
  /* Tambahan loading screen */
    #loading-screen {
        position: fixed;
    top: 0;
    left: 0;
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
    </style>
</head>
<body>

<!-- Loading Screen --><!-- Loading Screen -->
<div id="loading-screen">
    <div class="spinner"></div>
    <p class="loading-text">Mohon tunggu sebentar...<br><span>Dashboard sedang dipersiapkan ✨</span></p>
</div>

<div class="form-container">
    <h4>Edit Admin</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Password (kosongkan jika tidak ingin ubah)</label>
            <input type="password" class="form-control" name="password">
        </div>
        <div class="mb-3">
            <label>Level</label>
            <select class="form-select" name="level" required>
                <option value="on" <?= $admin['level'] == 'on' ? 'selected' : '' ?>>On</option>
                <option value="off" <?= $admin['level'] == 'off' ? 'selected' : '' ?>>Off</option>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="t_admin.php" class="btn btn-secondary">Kembali</a>
    </form>
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
