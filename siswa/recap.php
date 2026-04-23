<?php
/**
 * ============================================================
 * EduAdapt - Rekap Nilai Siswa (Fase 3)
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('student');

$user = getCurrentUser();
$active_page = 'recap';

// Ambil modul + progress
$stmt = $conn->prepare("
    SELECT m.id, m.title, m.type, m.sequence_order,
           sp.score, sp.is_completed, sp.completed_at
    FROM modules m
    LEFT JOIN student_progress sp ON sp.module_id = m.id AND sp.student_id = ?
    WHERE m.level = ?
    ORDER BY m.sequence_order ASC
");
$stmt->bind_param("ss", $user['id'], $user['level']);
$stmt->execute();
$modules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hitung statistik
$total = count($modules);
$completed = 0;
$total_score = 0;
$has_scores = 0;
foreach ($modules as $m) {
    if ($m['is_completed']) {
        $completed++;
        $total_score += $m['score'];
        $has_scores++;
    }
}
$avg_score = $has_scores > 0 ? round($total_score / $has_scores, 1) : 0;
$progress_pct = $total > 0 ? round(($completed / $total) * 100) : 0;

// Ambil nilai projek
$stmt = $conn->prepare("SELECT score, teacher_feedback FROM projects WHERE student_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->bind_param("s", $user['id']);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Saya — EduAdapt</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .stat-mini {
            background: #fff;
            padding: 24px;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-mini-label {
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }
        .stat-mini-value { font-size: 1.875rem; font-weight: 900; }

        .recap-table {
            background: #fff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .recap-table table { width: 100%; border-collapse: collapse; }
        .recap-table thead tr {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        .recap-table th {
            padding: 16px 24px;
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
        }
        .recap-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9375rem;
            color: #334155;
        }
        .recap-table tr:hover { background: #fafbfd; }
        .recap-table tr:last-child td { border-bottom: none; }

        .score-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 800;
        }
        .status-done { background: #dcfce7; color: #16a34a; }
        .status-pending { color: #94a3b8; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 1100px; margin: 0 auto; padding: 40px 24px;" class="animate-in">
    
    <h1 style="font-size: 1.875rem; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Rekapan Nilai Saya</h1>
    <p style="color: #94a3b8; margin-bottom: 32px;">Pantau perkembangan belajarmu di sini.</p>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-mini">
                <div class="stat-mini-label">Rata-rata Kuis</div>
                <div class="stat-mini-value" style="color: #4f46e5;"><?= $avg_score ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-mini">
                <div class="stat-mini-label">Modul Selesai</div>
                <div class="stat-mini-value" style="color: #16a34a;"><?= $completed ?> / <?= $total ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-mini">
                <div class="stat-mini-label">Pencapaian Jalur</div>
                <div class="stat-mini-value" style="color: #f59e0b;"><?= $progress_pct ?>%</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="recap-table">
        <table>
            <thead>
                <tr>
                    <th>Materi / Modul</th>
                    <th>Status</th>
                    <th>Skor Kuis</th>
                    <th>Diselesaikan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modules as $m): ?>
                <tr>
                    <td style="font-weight: 700;"><?= htmlspecialchars($m['title']) ?></td>
                    <td>
                        <?php if ($m['is_completed']): ?>
                            <span style="color: #16a34a; font-weight: 700; font-size: 12px; text-transform: uppercase;">
                                <i class="bi bi-check-circle-fill me-1"></i> Selesai
                            </span>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-weight: 700; font-size: 12px; text-transform: uppercase;">
                                <i class="bi bi-lock-fill me-1"></i> Belum
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($m['is_completed']): ?>
                            <span class="score-pill status-done"><?= $m['score'] ?> / 100</span>
                        <?php else: ?>
                            <span style="color: #cbd5e1;">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="color: #94a3b8; font-size: 0.8125rem;">
                        <?= $m['completed_at'] ? date('d M Y, H:i', strtotime($m['completed_at'])) : '—' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Project Score (if any) -->
    <?php if ($project && $project['score']): ?>
    <div class="stat-mini mt-4" style="border-left: 4px solid #4f46e5;">
        <div class="stat-mini-label">Nilai Projek Akhir</div>
        <div class="stat-mini-value" style="color: #4f46e5;"><?= $project['score'] ?> <span style="font-size: 0.875rem; color: #94a3b8; font-weight: 600;">/ 100</span></div>
        <?php if ($project['teacher_feedback']): ?>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 8px; font-style: italic;">
                "<?= htmlspecialchars($project['teacher_feedback']) ?>"
            </p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
