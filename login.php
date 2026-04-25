<?php
session_start();
require_once __DIR__ . '/includes/user_functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
if (isset($_GET['registered'])) {
    $success = 'Pendaftaran berhasil. Silakan login.';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user = get_user_by_username($username);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        header("Location: index.php");
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Posyandu Kita</title>
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

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 80px);
        }

        .login-box {
            background: rgba(128, 128, 128, 0.85);
            padding: 2.5rem;
            border-radius: 12px;
            width: 100%;
            max-width: 350px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .login-box h2 {
            color: white;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-box form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .login-box input {
            padding: 0.8rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
        }

        .login-box button {
            padding: 0.9rem;
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
        }

        .login-box button:hover {
            transform: translateY(-2px);
        }

        .error {
            background: rgba(220, 53, 69, 0.3);
            color: #fff;
            padding: 0.7rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .message {
            background: rgba(102, 204, 51, 0.2);
            color: #1f3d24;
            padding: 0.7rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }
            
            .login-box {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span> Posyandu Kita</span>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Layanan</a></li>
            <li><a href="pages/agenda.php">Agenda</a></li>
            <li><a href="login.php" class="btn-login">Login</a></li>
        </ul>
    </nav>

    <div class="login-container">
        <div class="login-box">
            <h2>Login Posyandu</h2>
            
            <?php if (!empty($success)): ?>
                <div class="message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <p style="margin-top: 1rem; color: #fff; text-align: center;">Belum punya akun? <a href="register.php" style="color: #1f3d24; font-weight: 600; text-decoration: underline;">Daftar sekarang</a></p>
        </div>
    </div>
</body>
</html>