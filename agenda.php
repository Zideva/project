<?php
session_start();
require_once __DIR__ . '/../includes/agenda_functions.php';

$isAdmin = isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if (!isset($_SESSION['user_id']) || !$isAdmin) {
        header("Location: agenda.php");
        exit();
    }
    
    if ($action == 'add') {
        add_agenda([
            'tanggal' => $_POST['tanggal'],
            'hari' => $_POST['hari'],
            'nama_posyandu' => $_POST['nama_posyandu'],
            'alamat' => $_POST['alamat'],
            'jam' => $_POST['jam'],
            'deskripsi' => $_POST['deskripsi']
        ]);
        header("Location: agenda.php");
        exit();
    } elseif ($action == 'edit') {
        update_agenda($_POST['id'], [
            'tanggal' => $_POST['tanggal'],
            'hari' => $_POST['hari'],
            'nama_posyandu' => $_POST['nama_posyandu'],
            'alamat' => $_POST['alamat'],
            'jam' => $_POST['jam'],
            'deskripsi' => $_POST['deskripsi']
        ]);
        header("Location: agenda.php");
        exit();
    } elseif ($action == 'delete') {
        delete_agenda($_POST['id']);
        header("Location: agenda.php");
        exit();
    }
}

// Cek apakah ada request edit
$edit_agenda = null;
if (isset($_GET['edit'])) {
    $edit_agenda = get_agenda_by_id($_GET['edit']);
}

$agenda_list = get_all_agenda();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - Posyandu Kita</title>
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
            color: #111;
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
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hero-content h1 {
            font-size: 2rem;
            color: #1f3d24;
            margin-bottom: 0.3rem;
        }

        .hero p {
            color: #444;
            font-size: 1rem;
        }

        .btn-add {
            background-color: #4A90E2;
            color: white;
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-add:hover {
            background-color: #357ABD;
        }

        .agenda-list {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
            gap: 1rem;
        }

        .agenda-card {
            background: rgba(255, 255, 255, 0.94);
            border-radius: 14px;
            padding: 1rem;
            border-left: 6px solid #357ABD;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            position: relative;
        }

        .agenda-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.8rem;
        }

        .agenda-content h2 {
            font-size: 1.4rem;
            margin-bottom: 0.2rem;
            color: #102a4d;
        }

        .agenda-content h3 {
            font-size: 1.05rem;
            margin-bottom: 0.38rem;
            color: #222;
        }

        .agenda-content p {
            font-size: 0.94rem;
            color: #4f4f4f;
            line-height: 1.5;
            margin-bottom: 0.35rem;
        }

        .agenda-meta {
            color: #1a4c8b;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 0.15rem;
        }

        .agenda-table-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 14px;
            padding: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            overflow-x: auto;
        }

        .agenda-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            min-width: 900px;
        }

        .agenda-table th,
        .agenda-table td {
            border: 1px solid rgba(0, 0, 0, 0.12);
            padding: 0.85rem 0.95rem;
            text-align: left;
            vertical-align: top;
        }

        .agenda-table th {
            background: #f2f6ff;
            color: #102a4d;
            font-weight: 700;
        }

        .agenda-table tbody tr:nth-child(odd) {
            background: rgba(74, 144, 226, 0.07);
        }

        .agenda-action-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .agenda-table td:nth-child(6) {
            min-width: 180px;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-edit {
            background-color: #4a90e2;
            color: white;
        }

        .btn-edit:hover {
            background-color: #357abd;
        }

        .btn-delete {
            background-color: #e85d4e;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c73826;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }

        .modal.active {
            display: block;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }

        .modal-header h2 {
            color: #102a4d;
            font-size: 1.5rem;
        }

        .close {
            font-size: 2rem;
            font-weight: bold;
            color: #999;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: #333;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }

        .btn-submit, .btn-cancel {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-submit {
            background-color: #4A90E2;
            color: white;
        }

        .btn-submit:hover {
            background-color: #357ABD;
        }

        .btn-cancel {
            background-color: #e0e0e0;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #d0d0d0;
        }

        .confirm-delete {
            text-align: center;
        }

        .confirm-delete p {
            color: #555;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .btn-delete-confirm {
            background-color: #e85d4e;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .btn-delete-confirm:hover {
            background-color: #c73826;
        }

        @media (max-width: 768px) {
            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }

            .container {
                padding: 1rem;
            }

            .hero {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .hero-content h1 {
                font-size: 1.6rem;
            }

            .agenda-card {
                padding: 0.9rem;
            }

            .btn-group {
                width: 100%;
                margin-top: 1rem;
            }

            .btn-edit, .btn-delete {
                flex: 1;
            }

            .modal-content {
                width: 95%;
                margin: 20% auto;
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
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="laporan.php">Laporan</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php?logout=true" class="btn-login">Logout</a></li>
            <?php else: ?>
                <li><a href="../index.php" class="btn-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <div class="hero">
            <div class="hero-content">
                <h1>Agenda Posyandu Kita</h1>
                <p>Jadwal kegiatan posyandu kita, terdekat. Datang tepat waktu untuk mendapatkan layanan kesehatan ibu dan anak terbaik.</p>
            </div>
            <?php if ($isAdmin): ?>
                <button class="btn-add" onclick="openModal('addModal')">+ Tambah Agenda</button>
            <?php endif; ?>
        </div>

        <?php if (empty($agenda_list)): ?>
            <div class="agenda-table-wrapper">
                <p style="text-align: center; padding: 2rem; color: #999; margin: 0;">Belum ada agenda.</p>
            </div>
        <?php else: ?>
            <?php if ($isAdmin): ?>
                <div class="agenda-table-wrapper">
                    <table class="agenda-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari / Tanggal</th>
                                <th>Posyandu</th>
                                <th>Alamat</th>
                                <th>Jam</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agenda_list as $index => $agenda): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($agenda['hari'] . ', ' . $agenda['tanggal']); ?></td>
                                    <td><?php echo htmlspecialchars($agenda['nama_posyandu']); ?></td>
                                    <td><?php echo htmlspecialchars($agenda['alamat']); ?></td>
                                    <td><?php echo htmlspecialchars($agenda['jam']); ?></td>
                                    <td><?php echo htmlspecialchars($agenda['deskripsi']); ?></td>
                                    <td>
                                        <div class="agenda-action-group">
                                            <button class="btn-edit" onclick="editAgenda(<?php echo $agenda['id']; ?>)">Edit</button>
                                            <button class="btn-delete" onclick="deleteAgenda(<?php echo $agenda['id']; ?>)">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="agenda-list">
                    <?php foreach ($agenda_list as $agenda): ?>
                        <article class="agenda-card">
                            <div class="agenda-content">
                                <div class="agenda-card-header">
                                    <div>
                                        <h2><?php echo htmlspecialchars($agenda['hari'] . ', ' . $agenda['tanggal']); ?></h2>
                                        <h3>Jadwal Posyandu di <?php echo htmlspecialchars($agenda['nama_posyandu']); ?></h3>
                                    </div>
                                </div>
                                <p class="agenda-meta"><?php echo htmlspecialchars($agenda['alamat'] . ' · ' . $agenda['jam']); ?></p>
                                <p><?php echo htmlspecialchars($agenda['deskripsi']); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Modal Tambah Agenda -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tambah Agenda Baru</h2>
                <span class="close" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="hari">Hari</label>
                    <input type="text" id="hari" name="hari" placeholder="Contoh: Senin" required>
                </div>
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="text" id="tanggal" name="tanggal" placeholder="Contoh: 10 Maret" required>
                </div>
                <div class="form-group">
                    <label for="nama_posyandu">Nama Posyandu</label>
                    <input type="text" id="nama_posyandu" name="nama_posyandu" placeholder="Contoh: Posyandu Melati" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" placeholder="Contoh: Jl. Melati No.12" required>
                </div>
                <div class="form-group">
                    <label for="jam">Jam</label>
                    <input type="text" id="jam" name="jam" placeholder="Contoh: 08:00 - 11:00" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi kegiatan posyandu" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Agenda -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Agenda</h2>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label for="editHari">Hari</label>
                    <input type="text" id="editHari" name="hari" required>
                </div>
                <div class="form-group">
                    <label for="editTanggal">Tanggal</label>
                    <input type="text" id="editTanggal" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label for="editNamaPosyandu">Nama Posyandu</label>
                    <input type="text" id="editNamaPosyandu" name="nama_posyandu" required>
                </div>
                <div class="form-group">
                    <label for="editAlamat">Alamat</label>
                    <input type="text" id="editAlamat" name="alamat" required>
                </div>
                <div class="form-group">
                    <label for="editJam">Jam</label>
                    <input type="text" id="editJam" name="jam" required>
                </div>
                <div class="form-group">
                    <label for="editDeskripsi">Deskripsi</label>
                    <textarea id="editDeskripsi" name="deskripsi" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Konfirmasi Hapus</h2>
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <form method="POST" class="confirm-delete">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId">
                <p>Apakah Anda yakin ingin menghapus agenda ini?</p>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Batal</button>
                    <button type="submit" class="btn-delete-confirm">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const agendaData = <?php echo json_encode($agenda_list); ?>;

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function editAgenda(id) {
            const agenda = agendaData.find(a => a.id == id);
            if (agenda) {
                document.getElementById('editId').value = agenda.id;
                document.getElementById('editHari').value = agenda.hari;
                document.getElementById('editTanggal').value = agenda.tanggal;
                document.getElementById('editNamaPosyandu').value = agenda.nama_posyandu;
                document.getElementById('editAlamat').value = agenda.alamat;
                document.getElementById('editJam').value = agenda.jam;
                document.getElementById('editDeskripsi').value = agenda.deskripsi;
                openModal('editModal');
            }
        }

        function deleteAgenda(id) {
            document.getElementById('deleteId').value = id;
            openModal('deleteModal');
        }

        // Tutup modal ketika click di luar modal content
        window.onclick = function(event) {
            let modal = event.target;
            if (modal.classList.contains('modal')) {
                modal.classList.remove('active');
            }
        }
    </script>
</body>
</html>