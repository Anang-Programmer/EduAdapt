<?php
/**
 * ============================================================
 * EduAdapt - Hasil Tes Diagnostik
 * ============================================================
 * Tampilkan skor & level setelah tes, lalu redirect ke dashboard
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('student');

$user = getCurrentUser();

// Ambil hasil dari session
$score   = $_SESSION['diag_score'] ?? null;
$correct = $_SESSION['diag_correct'] ?? 0;
$total   = $_SESSION['diag_total'] ?? 10;
$level   = $_SESSION['diag_level'] ?? $user['level'];

// Jika tidak ada hasil diagnostik, redirect ke dashboard
if ($score === null) {
    header("Location: dashboard.php");
    exit;
}

// Bersihkan session diagnostik (hanya tampil sekali)
unset($_SESSION['diag_score'], $_SESSION['diag_correct'], $_SESSION['diag_total'], $_SESSION['diag_level']);

// Level colors
$level_config = [
    'Beginner'     => ['color' => '#16a34a', 'bg' => '#dcfce7', 'icon' => 'bi-star-fill',    'desc' => 'Kamu akan memulai dari fondasi dasar. Step by step, kita bangun skill kamu dari nol!'],
    'Intermediate' => ['color' => '#2563eb', 'bg' => '#dbeafe', 'icon' => 'bi-star-half',     'desc' => 'Kamu sudah punya dasar yang oke! Saatnya naik level dengan konsep yang lebih aplikatif.'],
    'Advanced'     => ['color' => '#9333ea', 'bg' => '#f3e8ff', 'icon' => 'bi-stars',         'desc' => 'Impressive! Kamu langsung masuk jalur mahir. Materi berat menunggumu, warrior! 🔥'],
];
$lc = $level_config[$level] ?? $level_config['Beginner'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Diagnostik — EduAdapt</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .result-card {
            max-width: 520px;
            width: 100%;
            background: #fff;
            border-radius: 32px;
            padding: 56px 48px;
            box-shadow: 0 8px 40px -8px rgba(0,0,0,0.08);
            border: 1px solid #f1f5f9;
            text-align: center;
            animation: resultPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes resultPop {
            from { opacity: 0; transform: scale(0.85) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .result-icon {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            font-size: 40px;
            animation: bounceIn 0.8s ease 0.3s both;
        }
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        .score-display {
            font-size: 4rem;
            font-weight: 900;
            color: #1e293b;
            line-height: 1;
            margin-bottom: 4px;
            animation: countUp 1s ease-out 0.5s both;
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .score-label {
            font-size: 0.875rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 28px;
        }

        .level-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 99px;
            font-size: 1.125rem;
            font-weight: 800;
            margin-bottom: 16px;
            animation: fadeIn 0.5s ease 0.8s both;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .level-desc {
            color: #64748b;
            font-size: 0.875rem;
            line-height: 1.7;
            margin-bottom: 36px;
            animation: fadeIn 0.5s ease 1s both;
        }

        .btn-dashboard {
            display: inline-block;
            width: 100%;
            padding: 18px 32px;
            border-radius: 16px;
            background: #4f46e5;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px -2px rgba(79, 70, 229, 0.3);
            animation: fadeIn 0.5s ease 1.2s both;
        }
        .btn-dashboard:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.4);
            color: #fff;
        }

        /* Confetti simple */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            animation: confettiFall 3s ease-in forwards;
            z-index: -1;
        }
        @keyframes confettiFall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body>

    <!-- Simple confetti effect -->
    <script>
        const colors = ['#4f46e5', '#6366f1', '#8b5cf6', '#f59e0b', '#22c55e', '#ef4444'];
        for (let i = 0; i < 30; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 2 + 's';
            confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
            confetti.style.width = (6 + Math.random() * 8) + 'px';
            confetti.style.height = (6 + Math.random() * 8) + 'px';
            document.body.appendChild(confetti);
        }
    </script>

    <div class="result-card">
        <!-- Icon -->
        <div class="result-icon" style="background: <?= $lc['bg'] ?>; color: <?= $lc['color'] ?>;">
            <i class="bi <?= $lc['icon'] ?>"></i>
        </div>

        <!-- Score -->
        <div class="score-display"><?= $score ?></div>
        <div class="score-label"><?= $correct ?> dari <?= $total ?> jawaban benar</div>

        <!-- Level Badge -->
        <div class="level-badge" style="background: <?= $lc['bg'] ?>; color: <?= $lc['color'] ?>;">
            <i class="bi bi-award-fill"></i>
            Level: <?= $level ?>
        </div>

        <!-- Description -->
        <p class="level-desc"><?= $lc['desc'] ?></p>

        <!-- CTA -->
        <a href="dashboard.php" class="btn-dashboard">
            Mulai Belajar Sekarang <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>

</body>
</html>
