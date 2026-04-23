<?php
/**
 * ============================================================
 * EduAdapt - Dashboard Siswa (Jalur Belajar Adaptif)
 * ============================================================
 * Menampilkan modul sesuai classification_level siswa
 * dengan logika sequential lock
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('student');

$user = getCurrentUser();
$active_page = 'dashboard';

// Jika level belum diset, redirect ke diagnostic
if (empty($user['level'])) {
    header("Location: diagnostic.php");
    exit;
}

// Ambil modul sesuai level siswa
$level = $user['level'];
$stmt = $conn->prepare("SELECT * FROM modules WHERE level = ? ORDER BY sequence_order ASC");
$stmt->bind_param("s", $level);
$stmt->execute();
$modules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ambil progress siswa
$stmt = $conn->prepare("SELECT module_id, score, is_completed FROM student_progress WHERE student_id = ?");
$stmt->bind_param("s", $user['id']);
$stmt->execute();
$progress_result = $stmt->get_result();
$progress = [];
while ($row = $progress_result->fetch_assoc()) {
    $progress[$row['module_id']] = $row;
}
$stmt->close();

// Hitung statistik
$total_modules = count($modules);
$completed_count = 0;
foreach ($modules as $mod) {
    if (isset($progress[$mod['id']]) && $progress[$mod['id']]['is_completed']) {
        $completed_count++;
    }
}
$progress_percent = $total_modules > 0 ? round(($completed_count / $total_modules) * 100) : 0;

// Level badge color
$level_colors = [
    'Beginner'     => ['bg' => '#dcfce7', 'text' => '#16a34a', 'icon_bg' => '#dcfce7', 'icon_text' => '#16a34a'],
    'Intermediate' => ['bg' => '#dbeafe', 'text' => '#2563eb', 'icon_bg' => '#dbeafe', 'icon_text' => '#2563eb'],
    'Advanced'     => ['bg' => '#f3e8ff', 'text' => '#9333ea', 'icon_bg' => '#f3e8ff', 'icon_text' => '#9333ea'],
];
$lc = $level_colors[$level] ?? $level_colors['Beginner'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jalur Belajar — EduAdapt</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .module-card {
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            border: 2px solid #fff;
            box-shadow: 0 4px 14px -2px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .module-card:hover:not(.locked) {
            box-shadow: 0 8px 30px -4px rgba(79, 70, 229, 0.12);
            border-color: #c7d2fe;
            transform: translateY(-4px);
        }
        .module-card.locked {
            opacity: 0.6;
            filter: grayscale(30%);
            cursor: not-allowed;
        }
        .module-card.completed { border-color: #bbf7d0; }

        .module-icon {
            width: 52px; height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .status-badge {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .progress-card {
            background: #fff;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .progress-bar-custom {
            height: 12px;
            background: #f1f5f9;
            border-radius: 99px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #4f46e5);
            border-radius: 99px;
            transition: width 1.5s ease-out;
        }

        .level-card {
            background: #fff;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .module-action {
            font-size: 0.875rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 1100px; margin: 0 auto; padding: 40px 24px;">
    
    <!-- Top Section: Title + Stats -->
    <div class="row align-items-end mb-4 animate-in">
        <div class="col-md-7">
            <h1 style="font-size: 1.875rem; font-weight: 800; color: #1e293b;">Jalur Belajar Saya</h1>
            <p style="color: #94a3b8; margin-bottom: 24px;">Selesaikan modul untuk membuka tantangan berikutnya.</p>

            <!-- Progress Card -->
            <div class="progress-card" style="max-width: 420px;">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span style="font-size: 0.875rem; font-weight: 700; color: #475569;">Total Progres Belajar</span>
                    <span style="font-size: 1.5rem; font-weight: 900; color: #4f46e5;"><?= $progress_percent ?>%</span>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-bar-fill" style="width: <?= $progress_percent ?>%;"></div>
                </div>
                <p style="font-size: 11px; color: #94a3b8; margin-top: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">
                    <i class="bi bi-trophy-fill" style="color: #f59e0b;"></i>
                    <?= $completed_count ?> dari <?= $total_modules ?> Modul Terselesaikan
                </p>
            </div>
        </div>
        <div class="col-md-5 mt-3 mt-md-0 d-flex justify-content-md-end">
            <div class="level-card" style="min-width: 200px;">
                <div style="width: 52px; height: 52px; border-radius: 16px; background: <?= $lc['icon_bg'] ?>; color: <?= $lc['icon_text'] ?>; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="bi bi-award-fill"></i>
                </div>
                <div>
                    <p style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin: 0;">Level Siswa</p>
                    <p style="font-size: 1.25rem; font-weight: 900; color: #1e293b; margin: 0;"><?= $level ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Cards Grid -->
    <div class="row g-4">
        <?php foreach ($modules as $idx => $mod): 
            $is_completed = isset($progress[$mod['id']]) && $progress[$mod['id']]['is_completed'];
            
            // Lock logic: modul pertama selalu terbuka, sisanya harus modul sebelumnya selesai
            $is_locked = false;
            if ($idx > 0) {
                $prev_mod = $modules[$idx - 1];
                $is_locked = !isset($progress[$prev_mod['id']]) || !$progress[$prev_mod['id']]['is_completed'];
            }

            $card_class = $is_locked ? 'locked' : ($is_completed ? 'completed' : '');
            $delay_class = 'animate-delay-' . min($idx + 1, 3);
        ?>
        <div class="col-md-4 animate-in <?= $delay_class ?>">
            <?php if (!$is_locked): ?>
            <a href="module_detail.php?id=<?= $mod['id'] ?>" class="module-card <?= $card_class ?>">
            <?php else: ?>
            <div class="module-card <?= $card_class ?>">
            <?php endif; ?>
                
                <!-- Top: Icon + Status -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="module-icon" style="background: <?= $is_completed ? '#dcfce7' : ($is_locked ? '#f1f5f9' : '#e0e7ff') ?>; color: <?= $is_completed ? '#16a34a' : ($is_locked ? '#94a3b8' : '#4f46e5') ?>;">
                        <?php if ($is_completed): ?>
                            <i class="bi bi-check-circle-fill"></i>
                        <?php elseif ($is_locked): ?>
                            <i class="bi bi-lock-fill"></i>
                        <?php else: ?>
                            <i class="bi bi-book-fill"></i>
                        <?php endif; ?>
                    </div>
                    <?php if ($is_completed): ?>
                        <span class="status-badge" style="background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;">100% Selesai</span>
                    <?php elseif ($is_locked): ?>
                        <span class="status-badge" style="background: #f1f5f9; color: #94a3b8;">Terkunci</span>
                    <?php else: ?>
                        <span class="status-badge" style="background: #e0e7ff; color: #4f46e5; animation: pulse 2s infinite;">Sedang Aktif</span>
                    <?php endif; ?>
                </div>

                <!-- Title & Type -->
                <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin-bottom: 6px; flex-grow: 1;">
                    <?= htmlspecialchars($mod['title']) ?>
                </h4>
                <p style="color: #94a3b8; font-size: 0.875rem; margin-bottom: 24px;">
                    <?= htmlspecialchars($mod['type']) ?>
                </p>

                <!-- Action -->
                <div class="module-action" style="color: <?= $is_locked ? '#94a3b8' : '#4f46e5' ?>;">
                    <?php if ($is_locked): ?>
                        Selesaikan modul sebelumnya
                    <?php elseif ($is_completed): ?>
                        Tinjau Materi <i class="bi bi-chevron-right"></i>
                    <?php else: ?>
                        Mulai Belajar <i class="bi bi-chevron-right"></i>
                    <?php endif; ?>
                </div>

            <?php if (!$is_locked): ?>
            </a>
            <?php else: ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
