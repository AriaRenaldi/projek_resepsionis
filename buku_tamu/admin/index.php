<?php
session_start();
include "../koneksi.php"; // Pastikan file koneksi tersedia

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi form
    if (empty($email) || empty($password)) {
        echo "<script>alert('Email dan password wajib diisi!'); window.location.href='index.php';</script>";
        exit();
    }

    // Cek user dengan password langsung (TIDAK AMAN)
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Email atau password salah!'); window.location.href='index.php';</script>";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #ffffffff, #3498db);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 6px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .error {
            background: #ffe6e6;
            color: #c0392b;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
        .link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .link a {
            color: #3498db;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Loading Screen --><!-- Loading Screen -->
<div id="loading-screen">
    <div class="spinner"></div>
    <p class="loading-text">Mohon tunggu sebentar...<br><span>Dashboard sedang dipersiapkan ✨</span></p>
</div>

<div class="login-box">
    <h2>Login Admin</h2>
<?php if (!empty($error)) { ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required placeholder="Masukkan email">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn">Login</button>
    </form>

    <div class="link">
         <a href="reset_password_admin.php">Lupa Password?</a>
    </div>
     <div class="link">
         <a href="register_admin.php">Belum Punya Akun?</a>
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
