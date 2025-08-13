<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Resepsionis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #ffffff, #3498db);
        }

        .sidebar {
            position: fixed;
            left: -250px;
            top: 0;
            width: 250px;
            height: 100%;
            background: #2c3e50;
            color: white;
            transition: left 0.3s ease;
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            padding: 1rem;
            background: #1a252f;
            margin: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
        }

        .sidebar ul li:hover {
            background: #34495e;
            cursor: pointer;
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

        .content {
            padding: 2rem;
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .sidebar.active ~ .content {
            margin-left: 250px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 1rem 2rem;
        }

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-btn {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: bold;
            color: #2c3e50;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .profile-content {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 200px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            border-radius: 10px;
            z-index: 1200;
        }

.profile-link {
    display: block;
    padding: 8px 10px;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    margin-bottom: 8px;
    text-align: center;
    font-size: 14px;
}
.profile-link:hover {
    background: #2980b9;
}


        .profile-dropdown:hover .profile-content {
            display: block;
        }

        .profile-content p {
            margin: 0 0 10px;
            font-size: 14px;
        }

        .logout-btn {
            display: block;
            text-align: center;
            padding: 8px 10px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
<!-- Loading Screen --><!-- Loading Screen -->
<div id="loading-screen">
    <div class="spinner"></div>
    <p class="loading-text">Mohon tunggu sebentar...<br><span>Dashboard sedang dipersiapkan ✨</span></p>
</div>


<!-- Tombol toggle menu -->
<div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2>Menu</h2>
    <ul>
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="../tamu/tamu.php"><i class="fas fa-users"></i> Tamu</a></li>
        <li><a href="t_admin.php"><i class="fas fa-user-shield"></i> Admin</a></li>
        <li><a href="../tamu/kehadiran.php"><i class="fas fa-user-check"></i> Kehadiran</a></li>
        <li><a href="#"><i class="fas fa-file-alt"></i> Laporan</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
<!-- Konten Utama --><!-- Konten Utama -->
<div class="content">
    <div class="topbar">
       
    </div>


    <div class="card">
       <div class="content">
  
      <center><h1>Selamat Datang, <a href="profil_admin.php" style="text-decoration: none; color: #007bff;"><?= htmlspecialchars($username) ?></a>!</h1></center> <!-- Menampilkan pesan selamat datang dengan username -->
      <center><h2>SMKN 71 JAKARTA</h2></center> <!-- Menampilkan nama sekolah -->
  
<h2>Dashboard Resepsionis SMKN 71 Jakarta siap membantumu hari ini.</h2>
<p>Klik menu di samping untuk mulai bekerja 💼</p>
<p><em>"Kesuksesan adalah hasil dari kerja keras dan ketekunan."</em></p>

    </div>
</div>

<!-- Script untuk toggle sidebar -->
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
    
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
