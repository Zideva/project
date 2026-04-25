<?php
session_start();
require_once __DIR__ . '/../includes/imunisasi_functions.php';

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
    $jenis_imunisasi = $_POST['jenis_imunisasi'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

    if ($nama === '' || $umur <= 0 || $jk === '' || $jenis_imunisasi === '' || $tanggal === '') {
        $error = 'Semua kolom data balita harus diisi.';
    } else {
        add_imunisasi_entry([
            'tanggal' => $tanggal,
            'nama' => htmlspecialchars($nama),
            'umur' => $umur,
            'jk' => $jk,
            'jenis_imunisasi' => $jenis_imunisasi,
            'user_id' => $_SESSION['user_id'],
        ]);
        header('Location: imunisasi.php');
        exit();
    }
}

$rows = get_user_imunisasi_entries($_SESSION['user_id']);
$summary = get_imunisasi_summary($rows);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Imunisasi - Posyandu Kita</title>
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
            color: #172a16;
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
            background: #e8ffe3;
            margin: 1.5rem auto 0;
            padding: 1.2rem 1.5rem;
            border-radius: 20px;
            max-width: 1200px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .page-header h1 {
            font-size: 2.4rem;
            color: #0b3b15;
            margin-bottom: 0.35rem;
        }

        .page-header p {
            color: #2b5034;
            font-size: 1rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto 2rem;
            padding: 1rem;
        }

        .page-row {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr 1.2fr;
            margin-top: 1rem;
        }

        .card {
            background: rgba(255,255,255,0.96);
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 16px 35px rgba(0,0,0,0.08);
        }

        .card h2 {
            font-size: 1.55rem;
            margin-bottom: 1rem;
            color: #12461f;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c5135;
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.95rem 1rem;
            border-radius: 14px;
            border: 1px solid #cfe5d0;
            background: #f6fff7;
            font-size: 1rem;
            color: #1d3f24;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn-primary,
        .btn-secondary,
        .btn-action {
            border: none;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-primary {
            background: #2d8a39;
            color: #fff;
            padding: 0.95rem 1.6rem;
        }

        .btn-secondary {
            background: #f5f9f2;
            color: #2c5135;
            padding: 0.95rem 1.6rem;
            border: 1px solid #cfe5d0;
        }

        .btn-action {
            background: #0f6e29;
            color: #fff;
            padding: 0.65rem 1rem;
        }

        .table-wrap {
            overflow-x: auto;
            margin-top: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 100%;
        }

        th, td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid #dfe7de;
            text-align: left;
        }

        th {
            background: #e7f7e5;
            color: #1c4f28;
            font-weight: 700;
        }

        tbody tr:nth-child(even) {
            background: #f4f9f3;
        }

        .status {
            display: inline-flex;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .status-done {
            background: #d5f5d6;
            color: #1e5f28;
        }

        .status-pending {
            background: #fff2b8;
            color: #7b6600;
        }

        .schedule-row.active {
            background: rgba(102,204,51,0.14);
        }

        .error {
            background: #ffe7e7;
            border: 1px solid #f1b5b5;
            color: #7f1c1c;
            padding: 1rem;
            border-radius: 16px;
            margin-bottom: 1rem;
        }

        @media (max-width: 960px) {
            .page-row {
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
        <h1>Pemeriksaan Imunisasi</h1>
        <p>Isi data balita dan pilih imunisasi yang akan diberikan. Status jadwal akan berubah setelah tercatat.</p>
    </div>

    <div class="container">
        <?php if ($error !== ''): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="page-row">
            <div class="card">
                <h2>Data Balita</h2>
                <form method="POST" action="imunisasi.php" id="imunisasi-form">
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
                        <label for="jenis_imunisasi">Jenis Imunisasi</label>
                        <select id="jenis_imunisasi" name="jenis_imunisasi" required>
                            <option value="">Pilih Imunisasi</option>
                            <option value="BCG" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'BCG') ? 'selected' : ''; ?>>BCG</option>
                            <option value="Hepatitis B" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'Hepatitis B') ? 'selected' : ''; ?>>Hepatitis B</option>
                            <option value="Polio" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'Polio') ? 'selected' : ''; ?>>Polio</option>
                            <option value="DTP" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'DTP') ? 'selected' : ''; ?>>DTP</option>
                            <option value="Hib" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'Hib') ? 'selected' : ''; ?>>Hib</option>
                            <option value="PCV" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'PCV') ? 'selected' : ''; ?>>PCV</option>
                            <option value="Rotavirus" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'Rotavirus') ? 'selected' : ''; ?>>Rotavirus</option>
                            <option value="MR" <?php echo (($_POST['jenis_imunisasi'] ?? '') === 'MR') ? 'selected' : ''; ?>>MR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')); ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Mulai Pemeriksaan</button>
                        <button type="button" class="btn-secondary" id="reset-form">Reset</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <h2>Tentang Imunisasi</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Jenis Imunisasi</th>
                                <th>Pemberian Ke-</th>
                                <th>Usia</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $schedule = [
                                ['BCG', '1', '5 Bulan'],
                                ['Hepatitis B', '5', '4 Bulan'],
                                ['Polio', '3', '6 Bulan'],
                                ['DTP', '2', '10 Bulan'],
                                ['Hib', '4', '12 Bulan'],
                                ['PCV', '3', '6 Bulan'],
                                ['Rotavirus', '5', '6 Bulan'],
                                ['MR', '1', '9 Bulan'],
                            ];
                            foreach ($schedule as $row):
                                $count = $summary[$row[0]] ?? 0;
                                $given = $count > 0;
                            ?>
                                <tr class="schedule-row<?php echo $given ? ' active' : ''; ?>">
                                    <td><?php echo $row[0]; ?></td>
                                    <td><?php echo $row[1]; ?></td>
                                    <td><?php echo $row[2]; ?></td>
                                    <td><span class="status <?php echo $given ? 'status-done' : 'status-pending'; ?>"><?php echo $given ? 'Sudah' : 'Belum'; ?></span></td>
                                    <td><button type="button" class="btn-action assign-imunisasi" data-imunisasi="<?php echo $row[0]; ?>">Berikan</button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p style="margin-top: 1rem; color: #3d5f39; font-weight: 600;">Pilih imunisasi di tabel untuk mempermudah pengisian form, lalu tekan "Mulai Pemeriksaan".</p>
            </div>
        </div>

    </div>

    <script>
        document.querySelectorAll('.assign-imunisasi').forEach(function(button) {
            button.addEventListener('click', function() {
                var jenis = button.dataset.imunisasi;
                var select = document.getElementById('jenis_imunisasi');
                select.value = jenis;
                document.querySelectorAll('.schedule-row').forEach(function(row) {
                    row.classList.remove('active');
                });
                button.closest('tr').classList.add('active');
            });
        });

        document.getElementById('reset-form').addEventListener('click', function() {
            document.getElementById('imunisasi-form').reset();
        });
    </script>
</body>
</html>