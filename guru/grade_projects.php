<?php
/**
 * ============================================================
 * EduAdapt - Evaluasi Projek (Guru) — Fase 4
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

$user = getCurrentUser();
$active_page = 'grade';

$success = '';

// --- Handle Penilaian POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id'] ?? 0);
    $score      = intval($_POST['score'] ?? 0);
    $feedback   = $conn->real_escape_string(trim($_POST['feedback'] ?? ''));

    if ($project_id > 0 && $score >= 0 && $score <= 100) {
        $conn->query("UPDATE projects SET score = $score, teacher_feedback = '$feedback' WHERE id = $project_id");
        $success = "Nilai berhasil disimpan!";
    }
}

// --- Ambil semua projek + data siswa ---
$projects = $conn->query("
    SELECT p.*, u.name AS student_name, u.classification_level
    FROM projects p
    JOIN users u ON u.id = p.student_id
    ORDER BY p.submitted_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Projek — EduAdapt Admin</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .project-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 16px;
            transition: all 0.2s;
        }
        .project-card:hover { border-color: #c7d2fe; }
        .file-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #475569;
        }
        .grade-form { background: #f8fafc; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; margin-top: 16px; }
        .score-display { font-size: 2rem; font-weight: 900; color: #4f46e5; }
        .alert-custom { border-radius: 14px; border: none; font-weight: 600; font-size: 0.875rem; }
        .badge-level { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 900px; margin: 0 auto; padding: 40px 24px;" class="animate-in">
    <h1 style="font-size: 1.875rem; font-weight: 800; color: #1e293b;">Evaluasi Projek Akhir</h1>
    <p style="color: #94a3b8; margin-bottom: 28px;">Penilaian manual untuk laporan pengembangan siswa.</p>

    <?php if ($success): ?><div class="alert alert-success alert-custom mb-3"><i class="bi bi-check-circle-fill me-1"></i> <?= $success ?></div><?php endif; ?>

    <?php if ($projects->num_rows > 0): ?>
        <?php while ($p = $projects->fetch_assoc()):
            $lv = $p['classification_level'] ?? '';
            $colors = ['Beginner' => ['#dcfce7','#16a34a'], 'Intermediate' => ['#dbeafe','#2563eb'], 'Advanced' => ['#f3e8ff','#9333ea']];
            $c = $colors[$lv] ?? ['#f1f5f9','#64748b'];
        ?>
        <div class="project-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div style="flex: 1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h5 style="font-weight: 800; color: #1e293b; margin: 0;"><?= htmlspecialchars($p['student_name']) ?></h5>
                        <span class="badge-level" style="background:<?= $c[0] ?>; color:<?= $c[1] ?>;"><?= $lv ?: '—' ?></span>
                        <span style="font-size:11px; font-weight:700; color:#94a3b8; background:#f1f5f9; padding:3px 8px; border-radius:6px;">
                            <?= date('d M Y', strtotime($p['submitted_at'])) ?>
                        </span>
                    </div>
                    <div class="file-badge">
                        <i class="bi bi-file-earmark-text-fill" style="color: #4f46e5;"></i>
                        <?= htmlspecialchars($p['file_name']) ?>
                        <a href="../<?= htmlspecialchars($p['file_path']) ?>" download style="color: #4f46e5; font-weight: 700; margin-left: 8px;">Unduh</a>
                    </div>
                </div>
                <div style="text-align: center; min-width: 80px;">
                    <?php if ($p['score'] !== null): ?>
                        <div class="score-display"><?= $p['score'] ?></div>
                        <div style="font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Skor</div>
                    <?php else: ?>
                        <span style="background:#fef3c7; color:#d97706; padding:6px 14px; border-radius:8px; font-size:11px; font-weight:800; text-transform:uppercase;">Belum Dinilai</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Penilaian -->
            <div class="grade-form">
                <form method="POST" class="d-flex gap-3 align-items-end flex-wrap">
                    <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                    <div style="width: 120px;">
                        <label style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Skor (0-100)</label>
                        <input type="number" name="score" class="form-control" min="0" max="100" value="<?= $p['score'] ?? '' ?>" required style="font-weight:700; color:#4f46e5;">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Umpan Balik</label>
                        <input type="text" name="feedback" class="form-control" placeholder="Komentar untuk siswa..." value="<?= htmlspecialchars($p['teacher_feedback'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn" style="background:#4f46e5; color:#fff; font-weight:700; padding:10px 20px; border-radius:12px; white-space:nowrap;">
                        <i class="bi bi-save-fill me-1"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align:center; padding:80px 20px;">
            <i class="bi bi-inbox" style="font-size:48px; color:#e2e8f0;"></i>
            <h3 style="color:#94a3b8; font-weight:700; margin-top:16px;">Belum Ada Projek</h3>
            <p style="color:#cbd5e1; font-size:14px;">Siswa belum ada yang mengirim file projek.</p>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
