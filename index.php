<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
$isAdmin = isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posyandu Kita</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(245, 247, 250, 0.3) 0%, rgba(195, 207, 226, 0.3) 100%), url('./img/WhatsApp Image 2026-03-10 at 08.40.58.jpeg');
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
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .hero {
            text-align: center;
            padding: 4rem 2rem;
            color: #333;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #4A90E2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #4A90E2;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            border: 2px solid #4A90E2;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #4A90E2;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .feature-card a {
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #555;
            line-height: 1.6;
        }

        .footer {
            background: rgba(74, 144, 226, 0.1);
            padding: 2rem;
            text-align: center;
            margin-top: 4rem;
        }

        .footer p {
            color: #333;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

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
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span>Posyandu Kita</span>
        </div>
        <ul class="nav-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="index.php">Home</a></li>
                <li><a href="pages/layanan.php">Layanan</a></li>
                <li><a href="pages/agenda.php">Agenda</a></li>
                <?php if ($isAdmin): ?>
                    <li><a href="pages/laporan.php">Laporan</a></li>
                <?php endif; ?>
                <li><a href="index.php?logout=true" class="btn-login">Logout</a></li>
            <?php else: ?>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Layanan</a></li>
                <li><a href="pages/agenda.php">Agenda</a></li>
                <li><a href="login.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="hero">
        <h1>Selamat Datang di Posyandu Kita</h1>
        <p>Platform digital untuk memantau kesehatan balita dan ibu hamil. Mari bersama wujudkan generasi sehat dan cerdas.</p>
        <div class="cta-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="pages/layanan.php" class="btn-primary">Buka Layanan</a>
            <?php else: ?>
                <a href="login.php" class="btn-primary">Masuk ke Sistem</a>
                <a href="register.php" class="btn-secondary">Daftar Akun</a>
            <?php endif; ?>
        </div>
    </section>

    <div class="container">
        <section id="features" class="features">
            <div class="feature-card">
                <a href="<?php echo isset($_SESSION['user_id']) ? 'pages/pemeriksaanpertumbuhan.php' : 'login.php'; ?>">
                    <h3>Monitoring Pertumbuhan</h3>
                    <p>Pantau pertumbuhan balita secara rutin dengan data yang akurat dan mudah diakses.</p>
                </a>
            </div>
            <div class="feature-card">
                <a href="<?php echo isset($_SESSION['user_id']) ? 'pages/imunisasi.php' : 'login.php'; ?>">
                    <h3>Imunisasi</h3>
                    <p>Catat dan pantau imunisasi balita agar vaksinasi terlaksana tepat waktu dan lengkap.</p>
                </a>
            </div>
            <div class="feature-card">
                <a href="<?php echo isset($_SESSION['user_id']) ? 'pages/vitamin.php' : 'login.php'; ?>">
                    <h3>Pemberian Vitamin</h3>
                    <p>Catat dan pantau pemberian vitamin serta PMT untuk kesehatan optimal.</p>
                </a>
            </div>
            <div class="feature-card">
                <a href="<?php echo isset($_SESSION['user_id']) ? 'pages/pemeriksaan_gizi.php' : 'login.php'; ?>">
                    <h3>Pemeriksaan Gizi</h3>
                    <p>Periksa status gizi balita dan pastikan sesuai standar WHO untuk pertumbuhan sehat.</p>
                </a>
            </div>
            <div class="feature-card">
                <a href="pages/agenda.php">
                    <h3>Agenda</h3>
                    <p>Kelola dan akses agenda Posyandu dengan mudah untuk perencanaan kegiatan.</p>
                </a>
            </div>
        </section>
    </div>

    <footer class="footer">
        <p>&copy; 2026 Posyandu Kita. Semua hak dilindungi.</p>
    </footer>
</body>
</html>
