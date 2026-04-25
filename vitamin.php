<?php
session_start();
require_once __DIR__ . '/../includes/vitamin_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $umur = intval($_POST['umur'] ?? 0);
    $jk = $_POST['jk'] ?? '';
    $jenis_vitamin = $_POST['jenis_vitamin'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

    if ($nama === '' || $umur <= 0 || $jk === '' || $jenis_vitamin === '' || $tanggal === '') {
        $error = 'Semua kolom data balita harus diisi.';
    } else {
        add_vitamin_entry([
            'tanggal' => $tanggal,
            'nama' => htmlspecialchars($nama),
            'umur' => $umur,
            'jk' => $jk,
            'jenis_vitamin' => $jenis_vitamin,
            'user_id' => $_SESSION['user_id'],
        ]);
        header('Location: vitamin.php');
        exit();
    }
}

$rows = get_user_vitamin_entries($_SESSION['user_id']);
$summary = get_vitamin_summary($rows);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberian Vitamin - Posyandu Kita</title>
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

        .page-header {
            background: rgba(255,255,255,0.95);
            margin: 1.5rem auto 0;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
            max-width: 1200px;
        }

        .page-header h1 {
            font-size: 2.2rem;
            color: #194f8c;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto 2rem;
            padding: 1rem;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .card {
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
            padding: 1.5rem;
        }

        .card h2 {
            font-size: 1.45rem;
            margin-bottom: 1rem;
            color: #1b3f75;
        }

        .data-box {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .data-item {
            background: #eef5ff;
            border-radius: 14px;
            padding: 1rem;
            border: 1px solid #c7d9f7;
        }

        .data-item strong {
            display: block;
            font-size: 2rem;
            color: #357abd;
            margin-bottom: 0.3rem;
        }

        .data-item span {
            color: #5a6c50;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.45rem;
            color: #34463d;
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            border: 1px solid #d8e7d1;
            background: #f7fdf4;
            font-size: 1rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #1e7d2e;
            border: none;
            color: white;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-secondary {
            background: #eef4ea;
            border: 1px solid #c8dcc7;
            color: #2f4f34;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
            margin-top: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 0.95rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e6efe2;
        }

        th {
            background: #f1fbf3;
            color: #1f5b2f;
            font-weight: 700;
        }

        tbody tr:nth-child(even) {
            background: #f8fdf6;
        }

        .error {
            background: #ffe7e7;
            border: 1px solid #f2c4c4;
            color: #8f1f1f;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        @media (max-width: 960px) {
            .card-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Posyandu Kita</div>
        <ul class="nav-menu">
            <li><a href="../index.php">Home</a></li>
            <li><a href="layanan.php">Layanan</a></li>
            <li><a href="agenda.php">Agenda</a></li>
            <?php if ($isAdmin): ?>
                <li><a href="laporan.php">Laporan</a></li>
            <?php endif; ?>
            <li><a href="../index.php?logout=true" class="btn-login">Logout</a></li>
        </ul>
    </nav>

    <div class="page-header">
        <h1>Pemberian Vitamin</h1>
        <p>Catat pemberian vitamin bagi balita untuk membantu pemantauan kesehatan dan imunisasi lanjutan.</p>
    </div>

    <div class="container">
        <?php if ($error !== ''): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card-grid">
            <div class="card">
                <h2>Form Data Balita</h2>
                <form method="POST" action="vitamin.php">
                    <div class="form-group">
                        <label for="nama">Nama Balita</label>
                        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="umur">Umur (Bulan)</label>
                        <input type="number" id="umur" name="umur" min="1" value="<?php echo htmlspecialchars($_POST['umur'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="jk">Jenis Kelamin</label>
                        <select id="jk" name="jk" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" <?php echo (($_POST['jk'] ?? '') === 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo (($_POST['jk'] ?? '') === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jenis_vitamin">Jenis Vitamin</label>
                        <select id="jenis_vitamin" name="jenis_vitamin" required>
                            <option value="">Pilih Vitamin</option>
                            <option value="Vitamin A 100.000 UI" <?php echo (($_POST['jenis_vitamin'] ?? '') === 'Vitamin A 100.000 UI') ? 'selected' : ''; ?>>Vitamin A 100.000 UI</option>
                            <option value="Vitamin A 200.000 UI" <?php echo (($_POST['jenis_vitamin'] ?? '') === 'Vitamin A 200.000 UI') ? 'selected' : ''; ?>>Vitamin A 200.000 UI</option>
                            <option value="Vitamin B 100.000 UI" <?php echo (($_POST['jenis_vitamin'] ?? '') === 'Vitamin B 100.000 UI') ? 'selected' : ''; ?>>Vitamin B 100.000 UI</option>
                            <option value="Vitamin B 200.000 UI" <?php echo (($_POST['jenis_vitamin'] ?? '') === 'Vitamin B 200.000 UI') ? 'selected' : ''; ?>>Vitamin B 200.000 UI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')); ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Simpan Data</button>
                        <button type="reset" class="btn-secondary">Reset</button>
                    </div>
                </form>
            </div>
            <div class="card">
                <h2>Ringkasan Pemberian Vitamin</h2>
                <div class="data-box">
                    <div class="data-item">
                        <strong><?php echo $summary['total']; ?></strong>
                        <span>Total pemberian vitamin</span>
                    </div>
                    <div class="data-item">
                        <strong><?php echo $summary['male']; ?></strong>
                        <span>Balita Laki-laki</span>
                    </div>
                    <div class="data-item">
                        <strong><?php echo $summary['female']; ?></strong>
                        <span>Balita Perempuan</span>
                    </div>
                    <div class="data-item">
                        <strong><?php echo $summary['A100']; ?></strong>
                        <span>Vitamin A 100.000 UI</span>
                    </div>
                    <div class="data-item">
                        <strong><?php echo $summary['A200']; ?></strong>
                        <span>Vitamin A 200.000 UI</span>
                    </div>
                    <div class="data-item">
                        <strong><?php echo $summary['B100']; ?></strong>
                        <span>Vitamin B 100.000 UI</span>
                    </div>
                    <div class="data-item">
                        <strong><?php echo $summary['B200']; ?></strong>
                        <span>Vitamin B 200.000 UI</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>