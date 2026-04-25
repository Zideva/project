<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan - Posyandu Kita</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 2.5rem;
            color: #000;
            margin-bottom: 2rem;
            font-weight: bold;
        }

        .services {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .service-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 1.5rem;
            align-items: center;
            cursor: pointer;
            transition: transform .16s ease, box-shadow .16s ease;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
        }

        .service-icon {
            font-size: 3rem;
            min-width: 60px;
            text-align: center;
        }

        .service-content h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 0.3rem;
        }

        .service-content p {
            color: #666;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }

            .navbar {
                padding: 1rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .service-card {
                padding: 1rem;
                gap: 1rem;
            }

            .service-icon {
                font-size: 2rem;
            }

            .service-content h3 {
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
                <li><a href="../index.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <h1 class="page-title">Layanan Posyandu</h1>

        <div class="services">
            <div class="service-card" onclick="window.location.href='pemeriksaanpertumbuhan.php'">
                <div class="service-content">
                    <h3>Pemeriksaan Pertumbuhan</h3>
                    <p>mengukur berat badan, tinggi badan, dan perkembangan balita.</p>
                </div>
            </div>

            <div class="service-card" onclick="window.location.href='imunisasi.php'"> 
                <div class="service-content">
                    <h3>Imunisasi</h3>
                    <p>pemberian imunisasi lengkap untuk bayi dan balita.</p>
                </div>
            </div>

            <div class="service-card" onclick="window.location.href='vitamin.php'">
                <div class="service-content">
                    <h3>Pemberian Vitamin</h3>
                    <p>memberikan vitamin A, vitamin D, dan MPT untuk balita</p>
                </div>
            </div>

            <div class="service-card" onclick="window.location.href='pemeriksaan_gizi.php'">
                <div class="service-content">
                    <h3>Pemeriksaan Gizi</h3>
                    <p>penilaian status gizi untuk mencegah stunting dan memantau tumbuh kembang.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>