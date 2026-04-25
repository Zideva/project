<?php
session_start();
require_once __DIR__ . '/../includes/pemeriksaan_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$error = '';
$result = null;

function get_age_ranges($umur) {
    $ranges = [
        [1, 6, 6.0, 9.5, 60, 68],
        [7, 12, 7.5, 11.0, 67, 75],
        [13, 24, 9.5, 12.5, 76, 86],
        [25, 36, 11.5, 14.5, 85, 95],
        [37, 48, 13.5, 17.5, 94, 103],
        [49, 60, 15.5, 20.5, 102, 110],
    ];

    foreach ($ranges as $range) {
        if ($umur >= $range[0] && $umur <= $range[1]) {
            return [
                'bb_min' => $range[2],
                'bb_max' => $range[3],
                'tb_min' => $range[4],
                'tb_max' => $range[5],
            ];
        }
    }

    return [
        'bb_min' => 15.5,
        'bb_max' => 20.5,
        'tb_min' => 102,
        'tb_max' => 110,
    ];
}

function get_expected_milestones($umur) {
    if ($umur <= 6) {
        return [
            'duduk' => true,
            'merangkak' => false,
            'berjalan' => false,
            'bicara' => false,
        ];
    }
    if ($umur <= 12) {
        return [
            'duduk' => true,
            'merangkak' => true,
            'berjalan' => false,
            'bicara' => true,
        ];
    }
    return [
        'duduk' => true,
        'merangkak' => true,
        'berjalan' => true,
        'bicara' => true,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $umur = intval($_POST['umur'] ?? 0);
    $jk = $_POST['jk'] ?? '';
    $bb = floatval($_POST['bb'] ?? 0);
    $tb = floatval($_POST['tb'] ?? 0);
    $duduk = isset($_POST['duduk']);
    $merangkak = isset($_POST['merangkak']);
    $berjalan = isset($_POST['berjalan']);
    $bicara = isset($_POST['bicara']);

    if ($nama === '' || $umur <= 0 || $jk === '' || $bb <= 0 || $tb <= 0) {
        $error = 'Semua kolom wajib diisi dengan nilai valid.';
    } else {
        $ranges = get_age_ranges($umur);

        if ($bb < $ranges['bb_min']) {
            $statusBB = 'Belum sesuai';
        } elseif ($bb <= $ranges['bb_max']) {
            $statusBB = 'Normal';
        } else {
            $statusBB = 'Perlu diperhatikan';
        }

        if ($tb < $ranges['tb_min']) {
            $statusTB = 'Belum sesuai';
        } elseif ($tb <= $ranges['tb_max']) {
            $statusTB = 'Normal';
        } else {
            $statusTB = 'Perlu diperhatikan';
        }

        $expected = get_expected_milestones($umur);
        $missing = [];
        foreach ($expected as $field => $shouldBe) {
            if ($shouldBe && !$$field) {
                $missing[] = $field;
            }
        }

        $statusPerkembangan = empty($missing) ? 'Normal' : 'Belum sesuai';
        $result = "Berat: $statusBB, Tinggi: $statusTB, Perkembangan: $statusPerkembangan";

        $summary = [
            'tanggal' => date('d M Y'),
            'nama' => htmlspecialchars($nama),
            'umur' => $umur,
            'jk' => $jk,
            'bb' => $bb,
            'tb' => $tb,
            'duduk' => $duduk ? 'Ya' : 'Tidak',
            'merangkak' => $merangkak ? 'Ya' : 'Tidak',
            'berjalan' => $berjalan ? 'Ya' : 'Tidak',
            'bicara' => $bicara ? 'Ya' : 'Tidak',
            'statusBB' => $statusBB,
            'statusTB' => $statusTB,
            'statusPerkembangan' => $statusPerkembangan,
            'user_id' => $_SESSION['user_id'],
        ];

        add_pemeriksaan($summary);
        $_POST = [];
    }
}

$rows = get_user_pemeriksaan($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Pertumbuhan - Posyandu Kita</title>
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

        .card-title {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1rem;
        }

        .card-icon {
            font-size: 1.7rem;
        }

        .card h2 {
            font-size: 1.5rem;
            margin: 0;
            color: #10304f;
        }

        .section-title {
            font-size: 1.1rem;
            margin-top: 1rem;
            margin-bottom: 0.6rem;
            color: #34556d;
            font-weight: 700;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }

            .container { padding: 1rem; }
            .form-grid { grid-template-columns: 1fr; }
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: #222;
        }

        input, select {
            width: 100%;
            padding: 0.7rem 0.8rem;
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
            padding: 0.65rem 1.1rem;
            cursor: pointer;
        }

        .btn-primary { background: #2f8f4b; }
        .btn-secondary { background: #8fa8bc; }

        .status-strip {
            margin-top: 1rem;
            padding: 0.8rem 1rem;
            background: #eef8f2;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
            font-weight: 600;
        }

        .status-chip {
            background: #34a853;
            color: #fff;
            border-radius: 999px;
            padding: 0.2rem 0.7rem;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .status-strip .not-normal {
            background: #e67e22;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.8rem;
            background: white;
        }

        th, td {
            border: 1px solid #d4dee9;
            padding: 0.7rem 0.85rem;
            text-align: left;
            color: #24334f;
        }

        th { background: #edf3fb; }

        .error {
            background: rgba(220, 53, 69, 0.2);
            color: #b54858;
            padding: 0.7rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .info { margin-top: 0.8rem; color: #2f8f4b; font-weight: 600; }
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
        <h1 class="page-title">Pemeriksaan Pertumbuhan</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-title">     
                <h2>Data Balita</h2>
            </div>

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
                </div>

                <div class="section-title">Pengukuran</div>
                <div class="form-grid">
                    <div>
                        <label for="bb">Berat Badan (kg)</label>
                        <input type="number" name="bb" id="bb" step="0.1" min="1" value="<?php echo htmlspecialchars($_POST['bb'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="tb">Tinggi Badan (cm)</label>
                        <input type="number" name="tb" id="tb" step="0.1" min="30" value="<?php echo htmlspecialchars($_POST['tb'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="section-title">Perkembangan</div>
                <div class="checkbox-row">
                    <label class="checkbox-item"><input type="checkbox" name="duduk" <?php echo isset($_POST['duduk']) ? 'checked' : ''; ?>> Duduk</label>
                    <label class="checkbox-item"><input type="checkbox" name="merangkak" <?php echo isset($_POST['merangkak']) ? 'checked' : ''; ?>> Merangkak</label>
                    <label class="checkbox-item"><input type="checkbox" name="berjalan" <?php echo isset($_POST['berjalan']) ? 'checked' : ''; ?>> Berjalan</label>
                    <label class="checkbox-item"><input type="checkbox" name="bicara" <?php echo isset($_POST['bicara']) ? 'checked' : ''; ?>> Bicara</label>
                </div>

                <div class="btn-group">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>

                <?php if ($result): ?>
                    <div class="status-strip">
                        <span>Berat: <span class="status-chip <?php echo $statusBB !== 'Normal' ? 'not-normal' : ''; ?>"><?php echo $statusBB; ?></span></span>
                        <span>Tinggi: <span class="status-chip <?php echo $statusTB !== 'Normal' ? 'not-normal' : ''; ?>"><?php echo $statusTB; ?></span></span>
                        <span>Perkembangan: <span class="status-chip <?php echo $statusPerkembangan !== 'Normal' ? 'not-normal' : ''; ?>"><?php echo $statusPerkembangan; ?></span></span>
                    </div>
                <?php elseif (!empty($rows)): $last = end($rows); ?>
                    <div class="status-strip">
                        <span>Berat: <span class="status-chip <?php echo $last['statusBB'] !== 'Normal' ? 'not-normal' : ''; ?>"><?php echo $last['statusBB']; ?></span></span>
                        <span>Tinggi: <span class="status-chip <?php echo $last['statusTB'] !== 'Normal' ? 'not-normal' : ''; ?>"><?php echo $last['statusTB']; ?></span></span>
                        <span>Perkembangan: <span class="status-chip <?php echo $last['statusPerkembangan'] !== 'Normal' ? 'not-normal' : ''; ?>"><?php echo $last['statusPerkembangan']; ?></span></span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

    </div>
</body>
</html>
