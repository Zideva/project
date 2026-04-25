<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$error = '';
$result = null;

function get_gizi_status($umur, $bb, $tb) {
    $ranges = [
        [1, 6, 4.0, 8.0, 60, 68],
        [7, 12, 6.0, 10.0, 67, 75],
        [13, 24, 8.0, 12.0, 76, 86],
        [25, 36, 10.0, 14.0, 85, 95],
        [37, 48, 12.0, 16.0, 94, 103],
        [49, 60, 14.0, 18.0, 102, 110],
    ];

    foreach ($ranges as $range) {
        if ($umur >= $range[0] && $umur <= $range[1]) {
            $statusBB = ($bb >= $range[2] && $bb <= $range[3]) ? 'Sesuai' : 'Tidak sesuai';
            $statusTB = ($tb >= $range[4] && $tb <= $range[5]) ? 'Sesuai' : 'Tidak sesuai';

            return [
                'statusBB' => $statusBB,
                'statusTB' => $statusTB,
                'minBB' => $range[2],
                'maxBB' => $range[3],
                'minTB' => $range[4],
                'maxTB' => $range[5],
            ];
        }
    }

    return [
        'statusBB' => 'Tidak sesuai',
        'statusTB' => 'Tidak sesuai',
        'minBB' => 15.0,
        'maxBB' => 20.0,
        'minTB' => 102,
        'maxTB' => 110,
    ];
}

require_once __DIR__ . '/../includes/gizi_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $umur = intval($_POST['umur'] ?? 0);
    $jk = $_POST['jk'] ?? '';
    $bb = floatval($_POST['bb'] ?? 0);
    $tb = floatval($_POST['tb'] ?? 0);

    if ($nama === '' || $umur <= 0 || $jk === '' || $bb <= 0 || $tb <= 0) {
        $error = 'Semua kolom wajib diisi dengan nilai valid.';
    } else {
        $status = get_gizi_status($umur, $bb, $tb);
        $result = [
            'nama' => htmlspecialchars($nama),
            'umur' => $umur,
            'jk' => $jk,
            'bb' => $bb,
            'tb' => $tb,
            'statusBB' => $status['statusBB'],
            'statusTB' => $status['statusTB'],
            'statusWHO' => ($status['statusBB'] === 'Sesuai' && $status['statusTB'] === 'Sesuai') ? 'Sesuai Standar WHO' : 'Tidak sesuai Standar WHO',
            'rangeBB' => $status['minBB'] . ' - ' . $status['maxBB'] . ' kg',
            'rangeTB' => $status['minTB'] . ' - ' . $status['maxTB'] . ' cm',
            'tanggal' => date('d M Y'),
            'user_id' => $_SESSION['user_id'],
        ];

        add_gizi_entry($result);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Gizi - Posyandu Kita</title>
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
            margin-bottom: 1.25rem;
            font-weight: bold;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
            gap: 1rem;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: #222;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #cfd9e8;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .btn {
            border: none;
            border-radius: 9px;
            color: #fff;
            font-weight: 700;
            padding: 0.75rem 1.2rem;
            cursor: pointer;
        }

        .btn-primary { background: #4A90E2; }
        .btn-secondary { background: #8fa8bc; }

        .result-box {
            margin-top: 1.5rem;
            padding: 1.25rem;
            border-radius: 12px;
            background: #eef4ff;
            border: 1px solid #b8d2ff;
        }

        .result-item {
            margin-bottom: 0.75rem;
            color: #1f3d6b;
        }

        .status {
            margin-top: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            background: #d6ebff;
            color: #0f3a67;
            font-weight: 700;
            display: inline-block;
        }

        .error {
            background: rgba(220, 53, 69, 0.2);
            color: #b54858;
            padding: 0.7rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .nav-menu { gap: 1rem; font-size: 0.9rem; }
            .navbar { padding: 1rem; }
            .page-title { font-size: 2rem; }
            .form-grid { grid-template-columns: 1fr; }
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

    <div class="container">
        <h1 class="page-title">Pemeriksaan Gizi</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="post">
                <div class="form-grid">
                    <div>
                        <label for="nama">Nama Balita</label>
                        <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="umur">Umur (bulan)</label>
                        <input type="number" name="umur" id="umur" min="1" max="120" value="<?php echo htmlspecialchars($_POST['umur'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="jk">Jenis Kelamin</label>
                        <select name="jk" id="jk" required>
                            <option value="">--Pilih--</option>
                            <option value="Laki-laki" <?php echo (($_POST['jk'] ?? '') === 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo (($_POST['jk'] ?? '') === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="bb">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="bb" id="bb" min="1" value="<?php echo htmlspecialchars($_POST['bb'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="tb">Tinggi Badan (cm)</label>
                        <input type="number" step="0.1" name="tb" id="tb" min="30" value="<?php echo htmlspecialchars($_POST['tb'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Hitung Gizi</button>
                </div>
            </form>
        </div>

        <?php if ($result): ?>
            <div class="card result-box">
                <h2>Hasil Pemeriksaan Gizi</h2>
                <div class="result-item"><strong>Nama:</strong> <?php echo $result['nama']; ?></div>
                <div class="result-item"><strong>Umur:</strong> <?php echo $result['umur']; ?> bulan</div>
                <div class="result-item"><strong>Jenis Kelamin:</strong> <?php echo $result['jk']; ?></div>
                <div class="result-item"><strong>Berat Badan:</strong> <?php echo $result['bb']; ?> kg</div>
                <div class="result-item"><strong>Tinggi Badan:</strong> <?php echo $result['tb']; ?> cm</div>
                <div class="result-item"><strong>Rentang Normal BB (WHO):</strong> <?php echo $result['rangeBB']; ?></div>
                <div class="result-item"><strong>Rentang Normal TB (WHO):</strong> <?php echo $result['rangeTB']; ?></div>
                <div class="status">Status BB: <?php echo $result['statusBB']; ?></div>
                <div class="status">Status TB: <?php echo $result['statusTB']; ?></div>
                <div class="status" style="margin-top: 0.8rem; background: <?php echo ($result['statusBB'] === 'Sesuai' && $result['statusTB'] === 'Sesuai') ? '#d6ffea' : '#ffe6e6'; ?>; color: <?php echo ($result['statusBB'] === 'Sesuai' && $result['statusTB'] === 'Sesuai') ? '#0f542f' : '#8a1f1f'; ?>;">
                    <?php echo ($result['statusBB'] === 'Sesuai' && $result['statusTB'] === 'Sesuai') ? 'Sesuai Standar WHO' : 'Tidak sesuai Standar WHO'; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>