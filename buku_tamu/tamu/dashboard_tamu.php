<?php
include '../koneksi.php';

// Jika ingin, bisa set default nama pengunjung
$username = "Pengunjung";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tamu - SMKN 71 Jakarta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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

        .dashboard-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 600px;
        }

        .header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 18px;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
            text-align: center;
        }

        .welcome-section {
            margin-bottom: 40px;
        }

        .welcome-section h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .welcome-section p {
            color: #7f8c8d;
            font-size: 18px;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
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
            min-width: 250px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1abc9c, #16a085);
            color: white;
            box-shadow: 0 4px 15px rgba(26, 188, 156, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(26, 188, 156, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(149, 165, 166, 0.4);
        }

        .btn i {
            margin-right: 10px;
        }

        .info-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .info-section h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .info-section p {
            color: #7f8c8d;
            font-size: 16px;
        }

        .footer {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .dashboard-container {
                margin: 10px;
                border-radius: 15px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 28px;
            }

            .content {
                padding: 30px 20px;
            }

            .btn {
                font-size: 16px;
                padding: 12px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1><i class="fas fa-building"></i> SMKN 71 Jakarta</h1>
            <p>Sistem Buku Tamu Digital</p>
        </div>

        <div class="content">
            <div class="welcome-section">
                <h2>Selamat Datang, <?= htmlspecialchars($username) ?>!</h2>
                <p>
                    Terima kasih telah mengunjungi SMKN 71 Jakarta. 
                    Silakan gunakan form buku tamu digital kami untuk mencatat kunjungan Anda.
                </p>
            </div>

            <div class="action-buttons">
                <a href="form_tamu.php" class="btn btn-primary">
                    <i class="fas fa-pen"></i> Isi Buku Tamu
                </a>
              
            </div>

            <div class="info-section">
                <h3><i class="fas fa-info-circle"></i> Informasi Penting</h3>
                <p>
                    Pastikan semua data yang Anda masukkan akurat dan lengkap. 
                    Data Anda akan digunakan untuk keperluan administrasi dan keamanan sekolah.
                </p>
            </div>
        </div>

        <div class="footer">
            &copy; <?= date('Y') ?> SMKN 71 Jakarta - Sistem Buku Tamu Digital
        </div>
    </div>
</body>
</html>
