<?php
session_start();
require_once __DIR__ . '/includes/user_functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } else {
        list($registered, $message) = register_user($username, $password);
        if ($registered) {
            header('Location: login.php?registered=1');
            exit();
        }
        $error = $message;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Posyandu Kita</title>
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
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 80px);
        }

        .register-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .register-box h2 {
            color: #1f3d24;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .register-box form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .register-box input {
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        .register-box button {
            padding: 0.9rem;
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
        }

        .register-box button:hover {
            transform: translateY(-2px);
        }

        .notice {
            margin-top: 1rem;
            text-align: center;
            color: #444;
        }

        .notice a {
            color: #1f3d24;
            text-decoration: none;
            font-weight: 600;
        }

        .message {
            background: rgba(102, 204, 51, 0.14);
            color: #1f3d24;
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .error {
            background: rgba(220, 53, 69, 0.14);
            color: #c82333;
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }

            .register-box {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Posyandu Kita</div>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="pages/layanan.php">Layanan</a></li>
            <li><a href="pages/agenda.php">Agenda</a></li>
            <li><a href="index.php" class="btn-login">Login</a></li>
        </ul>
    </nav>

    <div class="register-container">
        <div class="register-box">
            <h2>Daftar Akun Baru</h2>

            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
                <button type="submit">Daftar</button>
            </form>

            <div class="notice">
                Sudah punya akun? <a href="index.php">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
