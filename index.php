<?php
/**
 * ============================================================
 * EduAdapt - Login & Routing
 * ============================================================
 * File utama: halaman login + redirect berdasarkan role
 * ============================================================
 */

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

// --- Handle Logout ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout('index.php');
}

// --- Jika sudah login, redirect ---
if (isLoggedIn()) {
    redirectByRole();
}

// --- Handle Login POST ---
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi!';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, classification_level FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                setUserSession($user);
                redirectByRole();
            } else {
                $error = 'Password salah! Coba lagi ya';
            }
        } else {
            $error = 'Email tidak ditemukan di sistem.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduAdapt — Masuk ke Portal</title>
    <meta name="description" content="EduAdapt - Intelligent Adaptive Learning Management System. Masuk untuk memulai perjalanan belajar web development.">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            position: relative;
            overflow: hidden;
        }

        /* Animated gradient background */
        .bg-gradient-animated {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(79, 70, 229, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(99, 102, 241, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
            animation: bgFloat 15s ease-in-out infinite alternate;
        }
        @keyframes bgFloat {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.05) translate(-10px, -10px); }
        }

        /* Floating dots decoration */
        .floating-dot {
            position: fixed;
            border-radius: 50%;
            opacity: 0.15;
            animation: floatDot 20s ease-in-out infinite;
        }
        .dot-1 { width: 300px; height: 300px; background: #4f46e5; top: -100px; right: -80px; animation-delay: 0s; }
        .dot-2 { width: 200px; height: 200px; background: #8b5cf6; bottom: -60px; left: -40px; animation-delay: -5s; }
        .dot-3 { width: 120px; height: 120px; background: #6366f1; top: 40%; left: 10%; animation-delay: -10s; }
        @keyframes floatDot {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, -30px) rotate(5deg); }
            50% { transform: translate(-10px, 20px) rotate(-3deg); }
            75% { transform: translate(15px, 10px) rotate(2deg); }
        }

        /* Login card */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 
                0 4px 6px -1px rgba(0,0,0,0.05),
                0 20px 60px -10px rgba(79, 70, 229, 0.1);
            border: 1px solid #f1f5f9;
            animation: cardSlideUp 0.6s ease-out;
        }
        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo icon */
        .logo-icon {
            width: 64px;
            height: 64px;
            background: #4f46e5;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px -4px rgba(79, 70, 229, 0.35);
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 8px 24px -4px rgba(79, 70, 229, 0.35); }
            50% { box-shadow: 0 12px 32px -4px rgba(79, 70, 229, 0.5); }
        }
        .logo-icon i { color: #fff; font-size: 28px; }

        /* Title */
        .login-title {
            font-size: 1.625rem;
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            margin-bottom: 6px;
        }
        .login-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            text-align: center;
            margin-bottom: 32px;
        }

        /* Form inputs */
        .form-label-custom {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.9375rem;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #fff;
            transition: all 0.25s ease;
            outline: none;
        }
        .form-input::placeholder { color: #c4cdd5; }
        .form-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 14px;
            background: #4f46e5;
            color: #fff;
            font-size: 1.0625rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px -2px rgba(79, 70, 229, 0.3);
            margin-top: 8px;
        }
        .btn-login:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        /* Error alert */
        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: shakeError 0.4s ease;
        }
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* Bottom badge */
        .login-badge {
            text-align: center;
            margin-top: 28px;
            font-size: 0.6875rem;
            color: #c4cdd5;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            font-weight: 700;
        }

        /* Password toggle */
        .input-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: #4f46e5; }
    </style>
</head>
<body>

    <!-- Background effects -->
    <div class="bg-gradient-animated"></div>
    <div class="floating-dot dot-1"></div>
    <div class="floating-dot dot-2"></div>
    <div class="floating-dot dot-3"></div>

    <!-- Login Card -->
    <div class="login-card">
        <!-- Title -->
        <h1 class="login-title">Selamat Datang</h1>
        <p class="login-subtitle">Masuk ke portal untuk memulai perjalanan codingmu.</p>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="error-alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="index.php" autocomplete="on">
            <div class="mb-3">
                <label class="form-label-custom">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="contoh@gmail.com" 
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    autofocus
                    id="inputEmail"
                >
            </div>
            <div class="mb-4">
                <label class="form-label-custom">Password</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••" 
                        required
                        id="inputPassword"
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword()" id="toggleBtn">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login" id="btnLogin">
                Masuk Sekarang
            </button>
        </form>

        <div class="login-badge">
            EduAdapt LMS • Adaptive Learning
        </div>
    </div>

    <!-- Scripts -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const input = document.getElementById('inputPassword');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        }
    </script>
</body>
</html>