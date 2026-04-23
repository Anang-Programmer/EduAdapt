<?php
/**
 * ============================================================
 * EduAdapt - Tes Diagnostik (Fase 2)
 * ============================================================
 * 10 soal konsep dasar web → auto-set classification_level
 * Skor: 0-59 = Beginner, 60-84 = Intermediate, 85-100 = Advanced
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('student');

$user = getCurrentUser();

// Jika sudah punya level, langsung ke dashboard
if (!empty($user['level'])) {
    header("Location: dashboard.php");
    exit;
}

// --- Handle POST: Hitung skor & update level ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? [];
    
    // Ambil semua soal + jawaban benar
    $questions = $conn->query("SELECT id, correct_option FROM diagnostic_questions ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
    
    $correct = 0;
    $total = count($questions);
    
    foreach ($questions as $q) {
        $user_answer = $answers[$q['id']] ?? '';
        if ($user_answer === $q['correct_option']) {
            $correct++;
        }
    }
    
    // Hitung skor (persentase)
    $score = ($total > 0) ? round(($correct / $total) * 100) : 0;
    
    // Tentukan level
    if ($score >= 85) {
        $level = 'Advanced';
    } elseif ($score >= 60) {
        $level = 'Intermediate';
    } else {
        $level = 'Beginner';
    }
    
    // Update ke database
    $stmt = $conn->prepare("UPDATE users SET classification_level = ? WHERE id = ?");
    $stmt->bind_param("ss", $level, $user['id']);
    $stmt->execute();
    $stmt->close();
    
    // Update session
    updateSessionLevel($level);
    
    // Simpan hasil sementara untuk ditampilkan
    $_SESSION['diag_score'] = $score;
    $_SESSION['diag_correct'] = $correct;
    $_SESSION['diag_total'] = $total;
    $_SESSION['diag_level'] = $level;
    
    header("Location: diagnostic_result.php");
    exit;
}

// --- Ambil semua soal diagnostik ---
$questions = $conn->query("SELECT * FROM diagnostic_questions ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
$total_questions = count($questions);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Diagnostik — EduAdapt</title>
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Header section */
        .diag-header {
            text-align: center;
            margin-bottom: 32px;
            animation: fadeIn 0.5s ease;
        }
        .diag-header h2 {
            font-size: 1.875rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .diag-header p { color: #94a3b8; font-size: 0.9375rem; }

        /* Question card */
        .question-card {
            max-width: 700px;
            width: 100%;
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.06);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            animation: slideUp 0.4s ease;
        }

        /* Progress bar */
        .progress-track {
            height: 6px;
            background: #f1f5f9;
            width: 100%;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #4f46e5);
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0 3px 3px 0;
        }

        /* Question content */
        .question-body {
            padding: 40px 48px;
        }
        @media (max-width: 576px) {
            .question-body { padding: 28px 24px; }
        }

        .question-badge {
            display: inline-block;
            padding: 5px 14px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-radius: 99px;
            margin-bottom: 20px;
        }

        .question-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        /* Option buttons */
        .option-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 18px 22px;
            border-radius: 16px;
            border: 2px solid #f1f5f9;
            background: #fff;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-bottom: 12px;
            text-align: left;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #475569;
        }
        .option-btn:hover {
            border-color: #4f46e5;
            background: #eef2ff;
            color: #4338ca;
            transform: translateX(4px);
        }
        .option-btn:active {
            transform: scale(0.98);
        }
        .option-btn .arrow {
            color: #cbd5e1;
            transition: color 0.2s;
            font-size: 18px;
        }
        .option-btn:hover .arrow { color: #4f46e5; }

        /* Selected state */
        .option-btn.selected {
            border-color: #4f46e5;
            background: #eef2ff;
            color: #4338ca;
            font-weight: 600;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideQuestion {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .question-slide {
            display: none;
        }
        .question-slide.active {
            display: block;
            animation: slideQuestion 0.35s ease-out;
        }

        /* Timer bar / question indicator */
        .question-dots {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 28px;
        }
        .q-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.3s;
        }
        .q-dot.done { background: #4f46e5; }
        .q-dot.current { background: #4f46e5; transform: scale(1.3); box-shadow: 0 0 0 4px rgba(79,70,229,0.15); }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="diag-header">
        <h2>Tes Diagnostik</h2>
        <p>Mari cari tahu sejauh mana kemampuan front-end kamu!</p>
    </div>

    <!-- Question Card -->
    <form method="POST" action="diagnostic.php" id="diagnosticForm">
        <div class="question-card">
            <!-- Progress Bar -->
            <div class="progress-track">
                <div class="progress-fill" id="progressBar" style="width: 10%;"></div>
            </div>

            <!-- Questions (one at a time) -->
            <?php foreach ($questions as $idx => $q): ?>
            <div class="question-body question-slide <?= $idx === 0 ? 'active' : '' ?>" data-index="<?= $idx ?>">
                
                <span class="question-badge">
                    Soal <?= $idx + 1 ?> dari <?= $total_questions ?>
                </span>

                <h3 class="question-text">
                    <?= htmlspecialchars($q['question_text']) ?>
                </h3>

                <div class="options-container">
                    <?php 
                    $options = [
                        'a' => $q['option_a'],
                        'b' => $q['option_b'],
                        'c' => $q['option_c'],
                        'd' => $q['option_d'],
                    ];
                    foreach ($options as $key => $text): 
                    ?>
                    <button type="button" 
                            class="option-btn" 
                            onclick="selectAnswer(<?= $q['id'] ?>, '<?= $key ?>', <?= $idx ?>, this)">
                        <span><?= htmlspecialchars($text) ?></span>
                        <i class="bi bi-chevron-right arrow"></i>
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- Hidden input untuk menyimpan jawaban -->
                <input type="hidden" name="answers[<?= $q['id'] ?>]" id="answer_<?= $q['id'] ?>" value="">
            </div>
            <?php endforeach; ?>

            <!-- Question dots -->
            <div class="question-dots" id="questionDots">
                <?php for ($i = 0; $i < $total_questions; $i++): ?>
                <div class="q-dot <?= $i === 0 ? 'current' : '' ?>" id="dot_<?= $i ?>"></div>
                <?php endfor; ?>
            </div>
            <div style="height: 24px;"></div>
        </div>
    </form>

    <script>
        const totalQuestions = <?= $total_questions ?>;
        let currentIndex = 0;

        function selectAnswer(questionId, optionKey, questionIndex, btnElement) {
            // Simpan jawaban ke hidden input
            document.getElementById('answer_' + questionId).value = optionKey;

            // Visual feedback - highlight selected
            const container = btnElement.closest('.options-container');
            container.querySelectorAll('.option-btn').forEach(btn => btn.classList.remove('selected'));
            btnElement.classList.add('selected');

            // Delay sebelum pindah ke soal berikutnya
            setTimeout(() => {
                if (questionIndex < totalQuestions - 1) {
                    goToQuestion(questionIndex + 1);
                } else {
                    // Soal terakhir — submit form
                    submitDiagnostic();
                }
            }, 400);
        }

        function goToQuestion(index) {
            // Hide semua slides
            document.querySelectorAll('.question-slide').forEach(slide => {
                slide.classList.remove('active');
            });

            // Show slide yang dipilih
            const nextSlide = document.querySelector(`.question-slide[data-index="${index}"]`);
            if (nextSlide) {
                nextSlide.classList.add('active');
                currentIndex = index;
            }

            // Update progress bar
            const percent = ((index + 1) / totalQuestions) * 100;
            document.getElementById('progressBar').style.width = percent + '%';

            // Update dots
            for (let i = 0; i < totalQuestions; i++) {
                const dot = document.getElementById('dot_' + i);
                dot.classList.remove('current', 'done');
                if (i < index) dot.classList.add('done');
                if (i === index) dot.classList.add('current');
            }
        }

        function submitDiagnostic() {
            // Animasi loading
            const card = document.querySelector('.question-card');
            card.innerHTML = `
                <div style="padding: 80px 40px; text-align: center;">
                    <div style="width: 64px; height: 64px; border: 4px solid #e2e8f0; border-top-color: #4f46e5; border-radius: 50%; margin: 0 auto 24px; animation: spin 0.8s linear infinite;"></div>
                    <h3 style="font-weight: 800; color: #1e293b; margin-bottom: 8px;">Menganalisis Jawaban...</h3>
                    <p style="color: #94a3b8; font-size: 14px;">Menentukan level belajar yang tepat untukmu</p>
                </div>
                <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
            `;

            // Submit form setelah animasi singkat
            setTimeout(() => {
                document.getElementById('diagnosticForm').submit();
            }, 1500);
        }
    </script>

</body>
</html>
