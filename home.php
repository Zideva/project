<?php
session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Posyandu Kita</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(245, 247, 250, 0.3) 0%, rgba(195, 207, 226, 0.3) 100%), url('../img/WhatsApp Image 2026-03-10 at 08.40.58.jpeg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #000;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: #000;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-login {
            background-color: #d9d9d9;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: center;
        }

        .left-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .left-section h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .left-section ol {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .left-section li {
            margin-bottom: 0.8rem;
            color: #333;
        }

        .left-section p {
            color: #555;
            line-height: 1.8;
            font-size: 0.95rem;
        }

        .right-section {
            text-align: center;
        }

        .illustration {
            width: 100%;
            max-width: 400px;
            height: auto;
        }

        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }

            .left-section h2 {
                font-size: 1.5rem;
            }

            .navbar {
                padding: 1rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span>Posyandu Kita</span>
        </div>
        <ul class="nav-menu">
            <li><a href="../index.php">Home</a></li>
            <li><a href="layanan.php">Layanan</a></li>
            <li><a href="agenda.php">Agenda</a></li>
            <?php if ($isAdmin): ?>
                <li><a href="laporan.php">Laporan</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../index.php?logout=true" class="btn-login">Logout</a></li>
            <?php else: ?>
                <li><a href="../login.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <div class="content">
            <div class="left-section">
                <h2>Manfaat Posyandu</h2>
                <ol>
                    <li>Memantau pertumbuhan balita</li>
                    <li>Deteksi dini masalah kesehatan</li>
                    <li>Imunisasi lengkap</li>
                    <li>Pemberian vitamin & PMT</li>
                    <li>Konsultasi kesehatan ibu dan anak</li>
                </ol>
                <p>
                    Ayo Ibu-ibu dan Bapak-bapak, mari datang ke Posyandu sesuai jadwal untuk memantau pertumbuhan dan kesehatan balita serta ibu hamil melalui penimbangan, imunisasi, dan pemeriksaan rutin agar anak tumbuh sehat, kuat, dan terhindar dari stunting.
                </p>
            </div>

            <div class="right-section">
                <div style="background: white; padding: 1.5rem; border-radius: 12px; display: inline-block;">
                    <img src="../img/posyandu.jpg" alt="Posyandu Kita" class="illustration">
                </div>n
            </div>
        </div>
    </div>
</body>
</html>