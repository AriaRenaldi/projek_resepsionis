<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

include '../koneksi.php'; // pastikan path ini benar

$admin_id = (int) $_SESSION['admin_id'];
$success = '';
$error = '';

// Ambil data awal
$stmt = $conn->prepare("SELECT email, username, password, level FROM admin WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($email, $username, $password_db, $level);
$stmt->fetch();
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['email'] ?? '';
    $new_username = $_POST['username'] ?? '';
    $new_password = $_POST['password'] ?? '';

    // Validasi email tidak kosong
    if ($new_email === '' || $new_username === '') {
        $error = "Email dan Username tidak boleh kosong untuk mengganti!";
    } else {
        // Cek email unik
        $checkEmail = $conn->prepare("SELECT id FROM admin WHERE email = ? AND id != ?");
        $checkEmail->bind_param("si", $new_email, $admin_id);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $error = "Email sudah digunakan oleh admin lain.";
        } else {
            if ($new_password === '') {
                $up = $conn->prepare("UPDATE admin SET email = ?, username = ? WHERE id = ?");
                $up->bind_param("ssi", $new_email, $new_username, $admin_id);
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $up = $conn->prepare("UPDATE admin SET email = ?, username = ?, password = ? WHERE id = ?");
                $up->bind_param("sssi", $new_email, $new_username, $hashed_password, $admin_id);
            }

            if ($up->execute()) {
                $success = "Profil berhasil diperbarui.";
                $_SESSION['username'] = $new_username;
                $email = $new_email;
                $username = $new_username;
            } else {
                $error = "Gagal memperbarui profil. " . $conn->error;
            }

            $up->close();
        }

        $checkEmail->close();
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            min-height: 100vh;
            background: linear-gradient(115deg, #a5dce4 0%, #3f2bd6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        .profile-card {
            width: 100%;
            max-width: 820px;
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .profile-cover {
            background: radial-gradient(1200px circle at 0% 0%, #8bc4f9 35%, transparent 35%),
                        radial-gradient(1200px circle at 100% 100%, #8bc4f9 35%, transparent 35%),
                        linear-gradient(135deg, #4fa4fc 0%, #3f2bd6 100%);
            height: 140px;
        }

        .avatar {
            display: grid;
            place-items: center;
            font-size: 42px;
            font-weight: 700;
            color: #3f2bd6;
            margin-top: -55px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            border: 6px solid #fff;
            width: 110px;
            height: 110px;
            background: white;
            border-radius: 50%;
        }

        .level-badge {
            font-size: 0.8rem;
        }

        .form-control:focus {
            box-shadow: 0 0 2rem rgba(79, 172, 254, .3);
            border-color: #4fa4fc;
        }
    </style>
</head>
<body>
    
<!-- Loading Screen --><!-- Loading Screen -->
<div id="loading-screen">
    <div class="spinner"></div>
    <p class="loading-text">Mohon tunggu sebentar...<br><span>Dashboard sedang dipersiapkan ✨</span></p>
</div>

<div class="card profile-card">
    <div class="profile-cover"></div>
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar" aria-label="Avatar">
                <?php
                $initial = strtoupper(substr($username, 0, 1));
                echo htmlspecialchars($initial);
                ?>
            </div>
            <div class="flex-grow-1">
                <h3 class="mb-0"><?php echo htmlspecialchars($username); ?>
                    <?php if (!empty($level)) : ?>
                        <span class="badge bg-<?php echo $level == 'admin' ? 'success' : 'secondary'; ?> level-badge">
                            Level: <?php echo htmlspecialchars($level); ?>
                        </span>
                    <?php endif; ?>
                </h3>
                <div class="text-muted">ID: <?php echo $admin_id; ?></div>
            </div>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary mt-3">Kembali ke Dashboard</a>
        <hr class="my-4">

        <?php if ($success): ?>
            <div class="alert alert-success mt-4" role="alert"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-4" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
              <div class="col-12">
    <label class="form-label">Password Saat Ini (hanya lihat)</label>
    <div class="d-flex">
        <input type="password" class="form-control" id="password" value="<?php echo htmlspecialchars($password_db); ?>" readonly>
        <button class="btn btn-outline-secondary" type="button" id="togglePass" aria-label="Tampilkan/Sembunyikan Password">👁</button>
    </div>
    <div class="form-text text-danger">Password tidak dapat diubah di sini.</div>
</div>

            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
const toggleBtn = document.getElementById('togglePass');
const passInput = document.getElementById('password');
toggleBtn.addEventListener('click', () => {
    passInput.type = passInput.type === 'password' ? 'text' : 'password';
    toggleBtn.textContent = passInput.type === 'password' ? '👁' : '🙈';
});
</script>

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