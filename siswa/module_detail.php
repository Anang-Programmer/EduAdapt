<?php
/**
 * ============================================================
 * EduAdapt - Detail Modul (Fase 3)
 * ============================================================
 * Tampilkan: embed video, bacaan materi, kuis AJAX 5 soal
 * Jika lulus (skor >= 80), tandai modul selesai & unlock next
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('student');

$user = getCurrentUser();
$active_page = 'dashboard';

// Validasi ID modul
$module_id = intval($_GET['id'] ?? 0);
if ($module_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

// Ambil data modul
$stmt = $conn->prepare("SELECT * FROM modules WHERE id = ? AND level = ?");
$stmt->bind_param("is", $module_id, $user['level']);
$stmt->execute();
$module = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$module) {
    header("Location: dashboard.php");
    exit;
}

// Cek apakah modul ini terkunci (modul sebelumnya belum selesai)
if ($module['sequence_order'] > 1) {
    $prev_stmt = $conn->prepare("SELECT m.id FROM modules m 
        LEFT JOIN student_progress sp ON sp.module_id = m.id AND sp.student_id = ?
        WHERE m.level = ? AND m.sequence_order = ? AND (sp.is_completed IS NULL OR sp.is_completed = 0)");
    $prev_order = $module['sequence_order'] - 1;
    $prev_stmt->bind_param("ssi", $user['id'], $user['level'], $prev_order);
    $prev_stmt->execute();
    $prev_result = $prev_stmt->get_result();
    if ($prev_result->num_rows > 0) {
        // Modul sebelumnya belum selesai, redirect
        header("Location: dashboard.php");
        exit;
    }
    $prev_stmt->close();
}

// Ambil progress siswa untuk modul ini
$stmt = $conn->prepare("SELECT * FROM student_progress WHERE student_id = ? AND module_id = ?");
$stmt->bind_param("si", $user['id'], $module_id);
$stmt->execute();
$my_progress = $stmt->get_result()->fetch_assoc();
$stmt->close();

$is_completed = $my_progress && $my_progress['is_completed'];

// Ambil soal kuis untuk modul ini
$quiz_stmt = $conn->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d FROM module_quizzes WHERE module_id = ? ORDER BY id ASC");
$quiz_stmt->bind_param("i", $module_id);
$quiz_stmt->execute();
$quizzes = $quiz_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$quiz_stmt->close();
$total_quiz = count($quizzes);

// Level color
$level_colors = [
    'Beginner'     => ['bg' => '#dcfce7', 'text' => '#16a34a'],
    'Intermediate' => ['bg' => '#dbeafe', 'text' => '#2563eb'],
    'Advanced'     => ['bg' => '#f3e8ff', 'text' => '#9333ea'],
];
$lc = $level_colors[$module['level']] ?? $level_colors['Beginner'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($module['title']) ?> — EduAdapt</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-weight: 600;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 10px;
            transition: all 0.2s;
            font-size: 0.9375rem;
        }
        .back-btn:hover { color: #4f46e5; background: #eef2ff; }

        .video-container {
            aspect-ratio: 16/9;
            background: #0f172a;
            border-radius: 24px;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 8px 30px -8px rgba(0,0,0,0.15);
        }
        .video-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .video-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            position: relative;
        }
        .video-placeholder::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.6));
        }
        .play-icon {
            width: 72px; height: 72px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            border: 2px solid rgba(255,255,255,0.25);
            z-index: 1;
            transition: transform 0.3s;
        }
        .video-placeholder:hover .play-icon { transform: scale(1.1); }

        .content-card {
            background: #fff;
            border-radius: 24px;
            padding: 36px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .content-card h3, .content-card h4 {
            color: #1e293b;
            font-weight: 800;
        }

        .content-card p, .content-card li {
            color: #475569;
            line-height: 1.8;
        }

        .content-card code {
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.875em;
            color: #4f46e5;
        }

        .content-card pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 16px;
            overflow-x: auto;
            font-size: 0.8125rem;
            line-height: 1.8;
        }
        .content-card pre code {
            background: none;
            color: inherit;
            padding: 0;
        }

        .content-card .alert {
            border-radius: 16px;
            border: none;
            font-size: 0.875rem;
        }

        /* Quiz Section */
        .quiz-panel {
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            position: sticky;
            top: 100px;
        }

        .quiz-option {
            display: block;
            width: 100%;
            text-align: left;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 8px;
        }
        .quiz-option:hover:not(:disabled) { border-color: #4f46e5; background: #eef2ff; color: #4338ca; }
        .quiz-option:disabled { cursor: not-allowed; opacity: 0.6; }
        .quiz-option.correct { border-color: #22c55e; background: #f0fdf4; color: #16a34a; font-weight: 600; }
        .quiz-option.wrong { border-color: #ef4444; background: #fef2f2; color: #dc2626; }

        .feedback-box {
            padding: 14px 18px;
            border-radius: 14px;
            font-size: 0.8125rem;
            font-weight: 500;
            margin-top: 12px;
            display: none;
            animation: fadeSlide 0.3s ease;
        }
        .feedback-correct { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .feedback-wrong { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .quiz-progress {
            height: 6px;
            background: #f1f5f9;
            border-radius: 99px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .quiz-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #4f46e5);
            transition: width 0.5s ease;
        }

        /* Result panel */
        .result-panel {
            text-align: center;
            padding: 20px 0;
        }
        .result-icon-lg {
            width: 72px; height: 72px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.5s ease-out; }

        .completed-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 1100px; margin: 0 auto; padding: 32px 24px;" class="animate-in">
    
    <!-- Back Button -->
    <a href="dashboard.php" class="back-btn mb-4">
        <i class="bi bi-chevron-left"></i> Kembali ke Dashboard
    </a>

    <div class="row g-4">
        <!-- Left Column: Video + Content -->
        <div class="col-lg-8">
            
            <!-- Video Section -->
            <div class="video-container mb-4">
                <?php if (!empty($module['video_url'])): ?>
                    <iframe src="<?= htmlspecialchars($module['video_url']) ?>" 
                            allowfullscreen 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                    </iframe>
                <?php else: ?>
                    <div class="video-placeholder">
                        <div class="play-icon"><i class="bi bi-play-fill"></i></div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Module Info -->
            <div class="d-flex align-items-center gap-3 mb-4">
                <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 12px; border-radius: 8px; background: <?= $lc['bg'] ?>; color: <?= $lc['text'] ?>;">
                    <?= $module['level'] ?>
                </span>
                <span style="color: #94a3b8; font-size: 0.8125rem;">Modul <?= $module['sequence_order'] ?> • <?= $module['type'] ?></span>
                <?php if ($is_completed): ?>
                    <span class="completed-badge"><i class="bi bi-check-circle-fill"></i> Sudah Selesai</span>
                <?php endif; ?>
            </div>

            <!-- Content Card -->
            <div class="content-card">
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 24px;">
                    <?= htmlspecialchars($module['title']) ?>
                </h2>
                <div class="materi-content">
                    <?= $module['content_text'] ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Quiz Panel -->
        <div class="col-lg-4">
            <div class="quiz-panel" id="quizPanel">
                <h3 style="font-size: 1.125rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">
                    <i class="bi bi-question-circle-fill" style="color: #4f46e5;"></i> Kuis Modul
                </h3>
                <p style="color: #94a3b8; font-size: 0.8125rem; margin-bottom: 20px;">
                    Uji pemahamanmu untuk <?= $is_completed ? 'review materi' : 'membuka modul berikutnya' ?>.
                </p>

                <?php if ($total_quiz > 0): ?>
                    <!-- Quiz Progress -->
                    <div class="quiz-progress">
                        <div class="quiz-progress-fill" id="quizProgressBar" style="width: 0%;"></div>
                    </div>

                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;" id="quizCounter">
                        Soal 1 dari <?= $total_quiz ?>
                    </div>

                    <!-- Quiz Questions Container -->
                    <div id="quizContainer">
                        <?php foreach ($quizzes as $idx => $quiz): ?>
                        <div class="quiz-question" data-index="<?= $idx ?>" data-quiz-id="<?= $quiz['id'] ?>" style="<?= $idx > 0 ? 'display:none;' : '' ?>">
                            <p style="font-weight: 700; color: #1e293b; font-size: 0.9375rem; margin-bottom: 14px; line-height: 1.6;">
                                <?= htmlspecialchars($quiz['question_text']) ?>
                            </p>
                            <div class="quiz-options">
                                <?php 
                                $opts = ['a' => $quiz['option_a'], 'b' => $quiz['option_b'], 'c' => $quiz['option_c'], 'd' => $quiz['option_d']];
                                foreach ($opts as $key => $text): 
                                ?>
                                <button type="button" class="quiz-option" data-option="<?= $key ?>" onclick="checkAnswer(<?= $quiz['id'] ?>, '<?= $key ?>', <?= $idx ?>, this)">
                                    <?= htmlspecialchars($text) ?>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <div class="feedback-box" id="feedback_<?= $idx ?>"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Result Container (hidden initially) -->
                    <div id="quizResult" style="display: none;"></div>

                <?php else: ?>
                    <div style="text-align: center; padding: 24px 0; color: #94a3b8;">
                        <i class="bi bi-check-circle" style="font-size: 32px; color: #e2e8f0;"></i>
                        <p style="margin-top: 8px; font-size: 0.875rem;">Tidak ada kuis untuk modul ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    const totalQuiz = <?= $total_quiz ?>;
    const moduleId = <?= $module_id ?>;
    let currentQuiz = 0;
    let correctCount = 0;
    let answeredCount = 0;

    function checkAnswer(quizId, selectedOption, questionIndex, btnElement) {
        // Disable semua option di soal ini
        const questionDiv = btnElement.closest('.quiz-question');
        const allOptions = questionDiv.querySelectorAll('.quiz-option');
        allOptions.forEach(btn => btn.disabled = true);

        // AJAX ke server untuk cek jawaban
        const formData = new FormData();
        formData.append('quiz_id', quizId);
        formData.append('selected', selectedOption);

        fetch('api/quiz_check.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            answeredCount++;
            const feedbackBox = document.getElementById('feedback_' + questionIndex);

            if (data.correct) {
                correctCount++;
                btnElement.classList.add('correct');
                feedbackBox.className = 'feedback-box feedback-correct';
                feedbackBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + data.feedback;
            } else {
                btnElement.classList.add('wrong');
                // Highlight jawaban benar
                allOptions.forEach(opt => {
                    if (opt.dataset.option === data.correct_option) {
                        opt.classList.add('correct');
                    }
                });
                feedbackBox.className = 'feedback-box feedback-wrong';
                feedbackBox.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Jawaban benar: <strong>' + data.correct_answer_text + '</strong><br><span style="opacity:0.85;">' + data.feedback + '</span>';
            }
            feedbackBox.style.display = 'block';

            // Lanjut ke soal berikutnya setelah delay
            setTimeout(() => {
                if (questionIndex < totalQuiz - 1) {
                    goToNextQuiz(questionIndex + 1);
                } else {
                    showQuizResult();
                }
            }, 1800);
        })
        .catch(err => {
            console.error('Quiz check error:', err);
            alert('Gagal memeriksa jawaban. Pastikan server berjalan.');
        });
    }

    function goToNextQuiz(nextIndex) {
        // Hide semua quiz questions
        document.querySelectorAll('.quiz-question').forEach(q => q.style.display = 'none');

        // Show next
        const next = document.querySelector(`.quiz-question[data-index="${nextIndex}"]`);
        if (next) {
            next.style.display = 'block';
            next.style.animation = 'fadeSlide 0.3s ease';
            currentQuiz = nextIndex;
        }

        // Update progress
        const percent = ((nextIndex + 1) / totalQuiz) * 100;
        document.getElementById('quizProgressBar').style.width = percent + '%';
        document.getElementById('quizCounter').textContent = `Soal ${nextIndex + 1} dari ${totalQuiz}`;
    }

    function showQuizResult() {
        const score = Math.round((correctCount / totalQuiz) * 100);
        const passed = score >= 80;

        // Simpan hasil ke server
        const formData = new FormData();
        formData.append('module_id', moduleId);
        formData.append('score', score);
        formData.append('action', 'save_progress');

        fetch('api/quiz_check.php', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            console.log('Progress saved:', data);
        });

        // Update UI
        document.getElementById('quizContainer').style.display = 'none';
        document.getElementById('quizProgressBar').style.width = '100%';
        document.getElementById('quizCounter').style.display = 'none';

        const resultDiv = document.getElementById('quizResult');
        resultDiv.style.display = 'block';

        if (passed) {
            resultDiv.innerHTML = `
                <div class="result-panel">
                    <div class="result-icon-lg" style="background: #dcfce7; color: #16a34a;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 6px;">Lulus! Skor: ${score}</h4>
                    <p style="color: #64748b; font-size: 0.8125rem; margin-bottom: 6px;">${correctCount} dari ${totalQuiz} jawaban benar</p>
                    <p style="color: #16a34a; font-size: 0.8125rem; font-weight: 600; margin-bottom: 20px;">
                        ✨ Modul berikutnya sudah terbuka!
                    </p>
                    <a href="dashboard.php" class="btn w-100" style="background: #4f46e5; color: #fff; font-weight: 700; padding: 14px; border-radius: 14px; font-size: 0.9375rem;">
                        Kembali & Lihat Progres
                    </a>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="result-panel">
                    <div class="result-icon-lg" style="background: #fef3c7; color: #d97706;">
                        <i class="bi bi-emoji-neutral-fill"></i>
                    </div>
                    <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 6px;">Skor: ${score} / 100</h4>
                    <p style="color: #64748b; font-size: 0.8125rem; margin-bottom: 6px;">${correctCount} dari ${totalQuiz} jawaban benar</p>
                    <p style="color: #d97706; font-size: 0.8125rem; font-weight: 600; margin-bottom: 20px;">
                        Kamu butuh skor minimal 80 untuk lulus. Baca ulang materinya ya! 💪
                    </p>
                    <button onclick="retryQuiz()" class="btn w-100" style="background: #f59e0b; color: #fff; font-weight: 700; padding: 14px; border-radius: 14px; font-size: 0.9375rem; border: none;">
                        <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
                    </button>
                </div>
            `;
        }
    }

    function retryQuiz() {
        // Reset semua state
        correctCount = 0;
        answeredCount = 0;
        currentQuiz = 0;

        // Reset UI
        document.getElementById('quizResult').style.display = 'none';
        document.getElementById('quizContainer').style.display = 'block';
        document.getElementById('quizCounter').style.display = 'block';
        document.getElementById('quizCounter').textContent = `Soal 1 dari ${totalQuiz}`;
        document.getElementById('quizProgressBar').style.width = '0%';

        // Reset semua questions
        document.querySelectorAll('.quiz-question').forEach((q, i) => {
            q.style.display = i === 0 ? 'block' : 'none';
            q.querySelectorAll('.quiz-option').forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('correct', 'wrong');
            });
            const fb = q.querySelector('.feedback-box');
            if (fb) { fb.style.display = 'none'; fb.innerHTML = ''; }
        });
    }
</script>

</body>
</html>
