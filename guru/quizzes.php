<?php
/**
 * ============================================================
 * EduAdapt - Kelola Kuis per Modul (Guru)
 * ============================================================
 * CRUD soal kuis: question, 4 opsi, jawaban benar, feedback
 * Akses: guru/quizzes.php?module_id=X
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

$user = getCurrentUser();
$active_page = 'modules';

$module_id = intval($_GET['module_id'] ?? 0);
if ($module_id <= 0) {
    header("Location: modules.php");
    exit;
}

// Ambil data modul
$mod = $conn->query("SELECT * FROM modules WHERE id = $module_id")->fetch_assoc();
if (!$mod) {
    header("Location: modules.php");
    exit;
}

$success = '';
$error = '';

// --- DELETE ---
if (isset($_GET['hapus'])) {
    $del_id = intval($_GET['hapus']);
    if ($conn->query("DELETE FROM module_quizzes WHERE id = $del_id AND module_id = $module_id")) {
        $success = "Soal berhasil dihapus.";
    } else { $error = "Gagal menghapus: " . $conn->error; }
}

// --- ADD / UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id   = intval($_POST['quiz_id'] ?? 0);
    $question  = $conn->real_escape_string(trim($_POST['question_text'] ?? ''));
    $opt_a     = $conn->real_escape_string(trim($_POST['option_a'] ?? ''));
    $opt_b     = $conn->real_escape_string(trim($_POST['option_b'] ?? ''));
    $opt_c     = $conn->real_escape_string(trim($_POST['option_c'] ?? ''));
    $opt_d     = $conn->real_escape_string(trim($_POST['option_d'] ?? ''));
    $correct   = $conn->real_escape_string(trim($_POST['correct_option'] ?? ''));
    $feedback  = $conn->real_escape_string(trim($_POST['feedback'] ?? ''));
    $mode      = $_POST['mode'] ?? 'add';

    if (empty($question) || empty($opt_a) || empty($opt_b) || empty($opt_c) || empty($opt_d) || empty($correct) || empty($feedback)) {
        $error = 'Semua field wajib diisi!';
    } else {
        if ($mode === 'edit' && $quiz_id > 0) {
            $sql = "UPDATE module_quizzes SET 
                    question_text='$question', option_a='$opt_a', option_b='$opt_b', 
                    option_c='$opt_c', option_d='$opt_d', correct_option='$correct', feedback='$feedback'
                    WHERE id=$quiz_id AND module_id=$module_id";
            if ($conn->query($sql)) { $success = "Soal berhasil diupdate!"; }
            else { $error = "Gagal update: " . $conn->error; }
        } else {
            $sql = "INSERT INTO module_quizzes (module_id, question_text, option_a, option_b, option_c, option_d, correct_option, feedback)
                    VALUES ($module_id, '$question', '$opt_a', '$opt_b', '$opt_c', '$opt_d', '$correct', '$feedback')";
            if ($conn->query($sql)) { $success = "Soal baru berhasil ditambahkan!"; }
            else { $error = "Gagal: " . $conn->error; }
        }
    }
}

// --- EDIT MODE ---
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_data = $conn->query("SELECT * FROM module_quizzes WHERE id = $edit_id AND module_id = $module_id")->fetch_assoc();
}

// --- READ ---
$quizzes = $conn->query("SELECT * FROM module_quizzes WHERE module_id = $module_id ORDER BY id ASC");
$quiz_count = $quizzes->num_rows;

// Level colors
$level_colors = ['Beginner' => ['#dcfce7','#16a34a'], 'Intermediate' => ['#dbeafe','#2563eb'], 'Advanced' => ['#f3e8ff','#9333ea']];
$lc = $level_colors[$mod['level']] ?? ['#f1f5f9','#64748b'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kuis — <?= htmlspecialchars($mod['title']) ?></title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .quiz-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .quiz-card:hover { border-color: #c7d2fe; }
        .option-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 6px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .option-correct { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
        .option-wrong { background: #f8fafc; border: 1px solid #f1f5f9; color: #64748b; }
        .option-label {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 12px;
            flex-shrink: 0;
        }
        .feedback-preview {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.8125rem;
            color: #4338ca;
            margin-top: 12px;
        }
        .badge-level { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .alert-custom { border-radius: 14px; border: none; font-weight: 600; font-size: 0.875rem; }
        .correct-radio { accent-color: #4f46e5; width: 18px; height: 18px; cursor: pointer; }
        .form-label-sm { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 4px; }
        .back-link { color: #64748b; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 10px; transition: all 0.2s; }
        .back-link:hover { color: #4f46e5; background: #eef2ff; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 900px; margin: 0 auto; padding: 32px 24px;" class="animate-in">
    
    <!-- Breadcrumb -->
    <a href="modules.php" class="back-link mb-3">
        <i class="bi bi-chevron-left"></i> Kembali ke Daftar Modul
    </a>

    <!-- Module Info Header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-level" style="background:<?= $lc[0] ?>; color:<?= $lc[1] ?>;"><?= $mod['level'] ?></span>
                <span style="color: #94a3b8; font-size: 12px; font-weight: 600;">Modul <?= $mod['sequence_order'] ?></span>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0;">
                Kuis: <?= htmlspecialchars($mod['title']) ?>
            </h1>
            <p style="color: #94a3b8; font-size: 0.875rem; margin-top: 4px;">
                <?= $quiz_count ?> soal terdaftar — Siswa butuh skor ≥ 80 untuk lulus
            </p>
        </div>
    </div>

    <?php if ($success): ?><div class="alert alert-success alert-custom mb-3"><i class="bi bi-check-circle-fill me-1"></i> <?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger alert-custom mb-3"><i class="bi bi-exclamation-circle-fill me-1"></i> <?= $error ?></div><?php endif; ?>

    <!-- Form Tambah/Edit Soal -->
    <div class="card-custom mb-4" id="formSection">
        <h5 style="font-weight: 700; margin-bottom: 20px;">
            <i class="bi bi-<?= $edit_data ? 'pencil-square' : 'plus-circle-fill' ?>" style="color: #4f46e5;"></i>
            <?= $edit_data ? 'Edit Soal' : 'Tambah Soal Baru' ?>
        </h5>
        <form method="POST">
            <input type="hidden" name="mode" value="<?= $edit_data ? 'edit' : 'add' ?>">
            <input type="hidden" name="quiz_id" value="<?= $edit_data['id'] ?? '' ?>">
            
            <!-- Pertanyaan -->
            <div class="mb-3">
                <label class="form-label-sm">Pertanyaan</label>
                <textarea name="question_text" class="form-control" rows="2" required placeholder="Tulis pertanyaan kuis di sini..."><?= htmlspecialchars($edit_data['question_text'] ?? '') ?></textarea>
            </div>

            <!-- 4 Opsi Jawaban + Radio untuk jawaban benar -->
            <label class="form-label-sm mb-2">Opsi Jawaban — pilih radio untuk jawaban yang BENAR ✅</label>
            
            <?php 
            $options = ['a', 'b', 'c', 'd'];
            $labels = ['A', 'B', 'C', 'D'];
            foreach ($options as $i => $opt): 
                $field = "option_$opt";
                $is_correct = ($edit_data['correct_option'] ?? '') === $opt;
            ?>
            <div class="d-flex align-items-center gap-3 mb-2">
                <input type="radio" name="correct_option" value="<?= $opt ?>" class="correct-radio" <?= $is_correct ? 'checked' : '' ?> required title="Tandai sebagai jawaban benar">
                <div class="option-label" style="background: <?= $is_correct ? '#dcfce7' : '#f1f5f9' ?>; color: <?= $is_correct ? '#16a34a' : '#94a3b8' ?>;">
                    <?= $labels[$i] ?>
                </div>
                <input type="text" name="option_<?= $opt ?>" class="form-control" required 
                       placeholder="Opsi <?= $labels[$i] ?>" 
                       value="<?= htmlspecialchars($edit_data[$field] ?? '') ?>">
            </div>
            <?php endforeach; ?>

            <!-- Feedback -->
            <div class="mt-3 mb-3">
                <label class="form-label-sm">Feedback (tampil setelah siswa menjawab) 💬</label>
                <textarea name="feedback" class="form-control" rows="2" required placeholder="Tulis feedback yang fun/humoris untuk siswa... contoh: Mantap! Kamu udah paham CSS dasar kayak pro! 🎨"><?= htmlspecialchars($edit_data['feedback'] ?? '') ?></textarea>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                    💡 Tips: Buat feedback yang santai & humoris supaya siswa semangat belajar!
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn" style="background: #4f46e5; color: #fff; font-weight: 700; padding: 10px 24px; border-radius: 12px;">
                    <i class="bi bi-<?= $edit_data ? 'check-lg' : 'plus-lg' ?> me-1"></i> <?= $edit_data ? 'Update Soal' : 'Simpan Soal' ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="quizzes.php?module_id=<?= $module_id ?>" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 600;">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Daftar Soal -->
    <h5 style="font-weight: 700; margin-bottom: 16px; color: #1e293b;">
        📋 Daftar Soal (<?= $quiz_count ?> soal)
    </h5>

    <?php if ($quiz_count > 0): ?>
        <?php $no = 1; while ($q = $quizzes->fetch_assoc()): ?>
        <div class="quiz-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-3">
                    <span style="width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 14px; color: #94a3b8;">
                        <?= $no++ ?>
                    </span>
                    <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 0.9375rem; line-height: 1.5;">
                        <?= htmlspecialchars($q['question_text']) ?>
                    </p>
                </div>
                <div class="d-flex gap-1 flex-shrink-0 ms-2">
                    <a href="quizzes.php?module_id=<?= $module_id ?>&edit=<?= $q['id'] ?>#formSection" class="btn-action" style="background: #eef2ff; color: #4f46e5;">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="quizzes.php?module_id=<?= $module_id ?>&hapus=<?= $q['id'] ?>" class="btn-action" style="background: #fef2f2; color: #dc2626;" onclick="return confirm('Yakin hapus soal ini?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>

            <!-- Options Preview -->
            <?php 
            $opts = ['a' => $q['option_a'], 'b' => $q['option_b'], 'c' => $q['option_c'], 'd' => $q['option_d']];
            foreach ($opts as $key => $text): 
                $is_correct = ($q['correct_option'] === $key);
            ?>
            <div class="option-row <?= $is_correct ? 'option-correct' : 'option-wrong' ?>">
                <span class="option-label" style="background: <?= $is_correct ? '#dcfce7' : '#e2e8f0' ?>; color: <?= $is_correct ? '#16a34a' : '#94a3b8' ?>;">
                    <?= strtoupper($key) ?>
                </span>
                <span><?= htmlspecialchars($text) ?></span>
                <?php if ($is_correct): ?>
                    <i class="bi bi-check-circle-fill ms-auto" style="color: #16a34a;"></i>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <!-- Feedback Preview -->
            <div class="feedback-preview">
                <i class="bi bi-chat-quote-fill me-1"></i> <?= htmlspecialchars($q['feedback']) ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card-custom text-center" style="padding: 60px;">
            <i class="bi bi-question-circle" style="font-size: 48px; color: #e2e8f0;"></i>
            <h4 style="color: #94a3b8; font-weight: 700; margin-top: 16px;">Belum Ada Soal</h4>
            <p style="color: #cbd5e1; font-size: 14px;">Tambahkan minimal 5 soal kuis untuk modul ini agar siswa bisa mengerjakan evaluasi.</p>
        </div>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
