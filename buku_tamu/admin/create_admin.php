<?php
session_start();
include "../koneksi.php";

if (isset($_POST['submit'])) {
    // Ambil data dari form
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi input
    if (!empty($email) && !empty($username) && !empty($password)) {
        // Cek apakah email atau username sudah terdaftar
        $check_query = "SELECT id FROM admin WHERE email = '$email' OR username = '$username'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Email atau Username sudah digunakan!');</script>";
        } else {
            // Simpan admin baru ke database (TANPA hash password)
            $query = "INSERT INTO admin (email, username, password) VALUES ('$email', '$username', '$password')";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Admin berhasil ditambahkan!'); window.location.href='t_admin.php';</script>";
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Semua field harus diisi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        body {
           background: linear-gradient(to right, #ffffff, #6dd5fa);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .form-container {
            background: #fff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }
        .form-container h4 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
<div id="loading-screen">
    <div class="spinner"></div>
    <p class="loading-text">Mohon tunggu sebentar...<br><span>Dashboard sedang dipersiapkan ✨</span></p>
</div>

<div class="container form-container">
    <h4 class="mb-4">Tambah Admin</h4>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
       <div class="mb-3">
  
</div>

    <button type="submit" class="btn btn-primary" name="submit">Tambah</button>

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