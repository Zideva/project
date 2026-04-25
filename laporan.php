<?php
session_start();
require_once __DIR__ . '/../includes/pemeriksaan_functions.php';
require_once __DIR__ . '/../includes/vitamin_functions.php';
require_once __DIR__ . '/../includes/gizi_functions.php';
require_once __DIR__ . '/../includes/imunisasi_functions.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$monthMap = [
    'Januari' => 1,
    'Februari' => 2,
    'Maret' => 3,
    'April' => 4,
    'Mei' => 5,
    'Juni' => 6,
    'Juli' => 7,
    'Agustus' => 8,
    'September' => 9,
    'Oktober' => 10,
    'November' => 11,
    'Desember' => 12,
];
$monthNames = array_keys($monthMap);
$currentMonthIndex = date('n') - 1;
$selectedMonth = $_GET['bulan'] ?? $monthNames[$currentMonthIndex];
$selectedYear = $_GET['tahun'] ?? date('Y');

function parse_report_date($dateString) {
    $dateString = trim($dateString);
    $monthTranslations = [
        'Januari' => 'January',
        'Februari' => 'February',
        'Maret' => 'March',
        'April' => 'April',
        'Mei' => 'May',
        'Juni' => 'June',
        'Juli' => 'July',
        'Agustus' => 'August',
        'September' => 'September',
        'Oktober' => 'October',
        'November' => 'November',
        'Desember' => 'December',
    ];
    $dateString = str_replace(array_keys($monthTranslations), array_values($monthTranslations), $dateString);

    try {
        return new DateTime($dateString);
    } catch (Exception $e) {
        return null;
    }
}

function filter_rows_by_month_year($rows, $month, $year, $monthMap) {
    $targetMonth = $monthMap[$month] ?? null;
    if (!$targetMonth || !is_numeric($year)) {
        return $rows;
    }

    return array_values(array_filter($rows, function ($item) use ($targetMonth, $year) {
        if (empty($item['tanggal'])) {
            return false;
        }

        $date = parse_report_date($item['tanggal']);
        return $date && (int) $date->format('n') === (int) $targetMonth && (int) $date->format('Y') === (int) $year;
    }));
}

function calculate_vitamin_summary($rows) {
    $summary = [
        'total' => count($rows),
        'male' => 0,
        'female' => 0,
        'A100' => 0,
    ];

    foreach ($rows as $row) {
        if (($row['jk'] ?? '') === 'Laki-laki') {
            $summary['male']++;
        } else {
            $summary['female']++;
        }

        if (($row['jenis_vitamin'] ?? '') === 'A 100.000 UI') {
            $summary['A100']++;
        }
    }

    return $summary;
}

function calculate_gizi_summary($rows) {
    $summary = [
        'total' => count($rows),
        'male' => 0,
        'female' => 0,
        'sesuai' => 0,
        'tidak_sesuai' => 0,
    ];

    foreach ($rows as $row) {
        if (($row['jk'] ?? '') === 'Laki-laki') {
            $summary['male']++;
        } else {
            $summary['female']++;
        }

        if (($row['statusWHO'] ?? '') === 'Sesuai Standar WHO') {
            $summary['sesuai']++;
        } else {
            $summary['tidak_sesuai']++;
        }
    }

    return $summary;
}

$rows = filter_rows_by_month_year(get_all_pemeriksaan(), $selectedMonth, $selectedYear, $monthMap);
$vitamin_rows = filter_rows_by_month_year(get_all_vitamin_entries(), $selectedMonth, $selectedYear, $monthMap);
$vitamin_summary = calculate_vitamin_summary($vitamin_rows);
$imunisasi_rows = filter_rows_by_month_year(get_all_imunisasi_entries(), $selectedMonth, $selectedYear, $monthMap);
$imunisasi_summary = get_imunisasi_summary($imunisasi_rows);
$gizi_rows = filter_rows_by_month_year(get_all_gizi_entries(), $selectedMonth, $selectedYear, $monthMap);
$gizi_summary = calculate_gizi_summary($gizi_rows);
$totalReports = count($rows);
$totalVitaminEntries = count($vitamin_rows);
$totalImunisasiEntries = count($imunisasi_rows);
$totalNormalBB = 0;
$totalNormalTB = 0;
$totalSesuai = 0;
foreach ($rows as $row) {
    if (($row['statusBB'] ?? '') === 'Normal') {
        $totalNormalBB++;
    }
    if (($row['statusTB'] ?? '') === 'Normal') {
        $totalNormalTB++;
    }
    if (($row['statusPerkembangan'] ?? '') === 'Normal') {
        $totalSesuai++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Posyandu Kita</title>
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
            color: #222;
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

        .nav-menu a.active {
            color: #0a4b06;
            font-weight: 700;
            text-decoration: underline;
        }

        .btn-login {
            background-color: #d9d9d9;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 1.5rem auto 2rem;
            padding: 1rem;
        }

        .hero {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1.3rem 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.14);
        }

        .hero h1 {
            font-size: 2rem;
            color: #1b3f75;
            margin-bottom: 0.4rem;
        }

        .hero p {
            color: #444;
            font-size: 1rem;
        }

        .filter-box {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 1rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 1rem;
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,0.12);
        }

        .filter-box label {
            font-weight: 600;
            color: #2a2a2a;
        }

        .filter-box input,
        .filter-box select,
        .filter-box button {
            padding: 0.5rem 0.7rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
        }

        .filter-box button {
            background: #4A90E2;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 700;
        }

        .report-card {
            margin-top: 1rem;
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 1rem;
            border: 1px solid rgba(0,0,0,0.14);
        }

        .report-card h2 {
            margin-bottom: 0.75rem;
            color: #1b3f75;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.75rem;
        }

        th, td {
            border: 1px solid rgba(0,0,0,0.18);
            padding: 0.6rem 0.7rem;
            text-align: center;
            font-size: 0.93rem;
        }

        th {
            background: #e8f1ff;
            color: #0d2c5d;
            font-weight: 700;
        }

        tbody tr:nth-child(odd) {
            background: rgba(74, 144, 226, 0.12);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .summary-item {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.12);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            box-shadow: 0 7px 20px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-info {
            display: flex;
            flex-direction: column;
        }

        .summary-title {
            font-weight: 700;
            color: #194f8c;
            margin-bottom: 0.3rem;
            font-size: 0.94rem;
        }

        .summary-value {
            font-size: 1.8rem;
            color: #2d2d2d;
        }

        .summary-sub {
            font-size: 0.85rem;
            color: #50606f;
            margin-top: 0.2rem;
        }

        .summary-icon {
            font-size: 2rem;
            color: #4A90E2;
            margin-left: 0.6rem;
        }

        @media (max-width: 768px) {
            .container { padding: 0.8rem; }
            .navbar { padding: 0.7rem 1rem; }
            .nav-menu { gap: 1rem; }
            .hero h1 { font-size: 1.5rem; }
            .filter-box { flex-direction: column; align-items: stretch; }
            .filter-box label { margin-bottom: 0.2rem; }
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
            <li><a href="laporan.php" class="active">Laporan</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php?logout=true" class="btn-login">Logout</a></li>
            <?php else: ?>
                <li><a href="../index.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <div class="hero">
            <h1>Laporan Posyandu Kita</h1>
            <p>Rekap laporan kegiatan posyandu setiap bulan untuk memantau pelayanan kesehatan balita dan ibu.</p>

            <form class="filter-box" method="GET" action="laporan.php">
                <label for="bulan">Filter Laporan - Bulan</label>
                <select id="bulan" name="bulan">
                    <?php foreach ($monthNames as $monthOption): ?>
                        <option value="<?php echo $monthOption; ?>" <?php echo $selectedMonth === $monthOption ? 'selected' : ''; ?>><?php echo $monthOption; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="tahun">Tahun</label>
                <input id="tahun" name="tahun" type="number" min="2020" max="2030" value="<?php echo htmlspecialchars($selectedYear); ?>">

                <button type="submit">Terapkan</button>
            </form>
        </div>

        <section class="report-card">
            <h2>Laporan Layanan Posyandu</h2>

            <?php if ($totalReports === 0): ?>
                <div class="table-wrap" style="padding: 2rem; text-align: center; color: #455a16; background: #f4f9ed; border-radius: 12px;">
                    Tidak ada data layanan yang masuk. Silakan tambahkan pemeriksaan pertumbuhan di halaman Layanan untuk melihat laporan.
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Umur</th>
                                <th>JK</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>Status BB</th>
                                <th>Status TB</th>
                                <th>Perkembangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $index => $item): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['tanggal']); ?></td>
                                    <td><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($item['umur']); ?></td>
                                    <td><?php echo htmlspecialchars($item['jk']); ?></td>
                                    <td><?php echo htmlspecialchars($item['bb']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tb']); ?></td>
                                    <td><?php echo htmlspecialchars($item['statusBB']); ?></td>
                                    <td><?php echo htmlspecialchars($item['statusTB']); ?></td>
                                    <td><?php echo htmlspecialchars($item['statusPerkembangan']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Total Laporan</span>
                            <span class="summary-value"><?php echo $totalReports; ?></span>
                            <span class="summary-sub">Jumlah entri layanan</span>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Status BB Normal</span>
                            <span class="summary-value"><?php echo $totalNormalBB; ?></span>
                            <span class="summary-sub">Balita</span>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Status TB Normal</span>
                            <span class="summary-value"><?php echo $totalNormalTB; ?></span>
                            <span class="summary-sub">Balita</span>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Perkembangan Normal</span>
                            <span class="summary-value"><?php echo $totalSesuai; ?></span>
                            <span class="summary-sub">Balita</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <section class="report-card" style="margin-top: 1rem;">
            <h2>Laporan Gizi</h2>
            <?php if (empty($gizi_rows)): ?>
                <div class="table-wrap" style="padding: 2rem; text-align: center; color: #455a16; background: #f4f9ed; border-radius: 12px;">
                    Tidak ada data pemeriksaan gizi. Silakan tambahkan data di halaman Pemeriksaan Gizi.
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Balita</th>
                                <th>Umur</th>
                                <th>JK</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>Status BB</th>
                                <th>Status TB</th>
                                <th>Status WHO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gizi_rows as $index => $item): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['tanggal']); ?></td>
                                    <td><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($item['umur']); ?></td>
                                    <td><?php echo htmlspecialchars($item['jk']); ?></td>
                                    <td><?php echo htmlspecialchars($item['bb']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tb']); ?></td>
                                    <td><?php echo htmlspecialchars($item['statusBB']); ?></td>
                                    <td><?php echo htmlspecialchars($item['statusTB']); ?></td>
                                    <td><?php echo htmlspecialchars($item['statusWHO']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Total Gizi</span>
                            <span class="summary-value"><?php echo $gizi_summary['total']; ?></span>
                            <span class="summary-sub">Entri</span>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Sesuai WHO</span>
                            <span class="summary-value"><?php echo $gizi_summary['sesuai']; ?></span>
                            <span class="summary-sub">Entri</span>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Tidak Sesuai WHO</span>
                            <span class="summary-value"><?php echo $gizi_summary['tidak_sesuai']; ?></span>
                            <span class="summary-sub">Entri</span>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Balita Laki-Laki</span>
                            <span class="summary-value"><?php echo $gizi_summary['male']; ?></span>
                            <span class="summary-sub">Jumlah</span>
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Balita Perempuan</span>
                            <span class="summary-value"><?php echo $gizi_summary['female']; ?></span>
                            <span class="summary-sub">Jumlah</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <section class="report-card" style="margin-top: 1rem;">
            <h2>Laporan Imunisasi</h2>
            <?php if ($totalImunisasiEntries === 0): ?>
                <div class="table-wrap" style="padding: 2rem; text-align: center; color: #455a16; background: #f4f9ed; border-radius: 12px;">
                    Tidak ada data imunisasi / vitamin yang masuk. Silakan tambahkan data di halaman Imunisasi.
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Balita</th>
                                <th>Usia (Bulan)</th>
                                <th>JK</th>
                                <th>Jenis Imunisasi</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($imunisasi_rows as $index => $item): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($item['umur']); ?></td>
                                    <td><?php echo htmlspecialchars($item['jk']); ?></td>
                                    <td><?php echo htmlspecialchars($item['jenis_imunisasi']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tanggal']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Total Imunisasi</span>
                            <span class="summary-value"><?php echo $imunisasi_summary['total']; ?></span>
                            <span class="summary-sub">Entri</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Balita Laki-Laki</span>
                            <span class="summary-value"><?php echo $imunisasi_summary['male']; ?></span>
                            <span class="summary-sub">Jumlah</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Balita Perempuan</span>
                            <span class="summary-value"><?php echo $imunisasi_summary['female']; ?></span>
                            <span class="summary-sub">Jumlah</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">BCG</span>
                            <span class="summary-value"><?php echo $imunisasi_summary['BCG']; ?></span>
                            <span class="summary-sub">Dosis</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Polio</span>
                            <span class="summary-value"><?php echo $imunisasi_summary['Polio']; ?></span>
                            <span class="summary-sub">Dosis</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <section class="report-card" style="margin-top: 1rem;">
            <h2>Laporan Vitamin</h2>
            <?php if ($totalVitaminEntries === 0): ?>
                <div class="table-wrap" style="padding: 2rem; text-align: center; color: #455a16; background: #f4f9ed; border-radius: 12px;">
                    Tidak ada data vitamin yang masuk. Silakan tambahkan data di halaman Vitamin.
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Balita</th>
                                <th>Usia (Bulan)</th>
                                <th>JK</th>
                                <th>Jenis Vitamin</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vitamin_rows as $index => $item): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($item['umur']); ?></td>
                                    <td><?php echo htmlspecialchars($item['jk']); ?></td>
                                    <td><?php echo htmlspecialchars($item['jenis_vitamin']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tanggal']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Total Vitamin</span>
                            <span class="summary-value"><?php echo $vitamin_summary['total']; ?></span>
                            <span class="summary-sub">Entri</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Balita Laki-Laki</span>
                            <span class="summary-value"><?php echo $vitamin_summary['male']; ?></span>
                            <span class="summary-sub">Jumlah</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Balita Perempuan</span>
                            <span class="summary-value"><?php echo $vitamin_summary['female']; ?></span>
                            <span class="summary-sub">Jumlah</span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-info">
                            <span class="summary-title">Vitamin A 100.000 UI</span>
                            <span class="summary-value"><?php echo $vitamin_summary['A100']; ?></span>
                            <span class="summary-sub">Dosis</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>