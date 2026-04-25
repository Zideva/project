<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Posyandu Kita</title>
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

        .dashboard {
            min-height: calc(100vh - 80px);
            padding: 2rem;
        }

        .welcome {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .welcome h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 0.8rem;
        }

        .card h3 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .card p {
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }
            
            .cards {
                grid-template-columns: 1fr;
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
            <li><a href="?logout=true" class="btn-login">Logout</a></li>
        </ul>
    </nav>

    <div class="dashboard">
        <div class="welcome">
            <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h1>
            <p>Anda berhasil login ke sistem Posyandu Kita</p>
            <p style="margin-top: 0.75rem; font-weight: 600; opacity: 0.9;">Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'user'); ?></p>
        </div>

        <div class="cards">
            <a class="card" href="layanan.php">
                <div class="card-icon"></div>
                <h3>Layanan</h3>
                <p>Kelola layanan dan informasi posyandu</p>
            </a>

            <a class="card" href="agenda.php">
                <div class="card-icon"></div>
                <h3>Agenda</h3>
                <p>Lihat jadwal kegiatan posyandu</p>
            </a>

            <?php if ($isAdmin): ?>
                <a class="card" href="laporan.php">
                    <div class="card-icon"></div>
                    <h3>Laporan</h3>
                    <p>Buat laporan kegiatan</p>
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
