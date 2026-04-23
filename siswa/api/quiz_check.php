<?php
/**
 * ============================================================
 * EduAdapt - Quiz Check API (AJAX Endpoint)
 * ============================================================
 * Endpoint untuk:
 * 1. Cek jawaban kuis (POST: quiz_id, selected)
 * 2. Simpan progress (POST: module_id, score, action=save_progress)
 * ============================================================
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../koneksi.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();

// --- Action: Save Progress ---
if (isset($_POST['action']) && $_POST['action'] === 'save_progress') {
    $module_id = intval($_POST['module_id'] ?? 0);
    $score     = intval($_POST['score'] ?? 0);
    $passed    = $score >= 80;

    if ($module_id <= 0) {
        echo json_encode(['error' => 'Invalid module_id']);
        exit;
    }

    // Cek apakah sudah ada record progress
    $check = $conn->prepare("SELECT id, score, is_completed FROM student_progress WHERE student_id = ? AND module_id = ?");
    $check->bind_param("si", $user['id'], $module_id);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    $check->close();

    if ($existing) {
        // Update hanya jika skor baru lebih tinggi atau belum completed
        if ($score > $existing['score'] || (!$existing['is_completed'] && $passed)) {
            $stmt = $conn->prepare("UPDATE student_progress SET score = ?, is_completed = ?, completed_at = IF(?, NOW(), completed_at) WHERE student_id = ? AND module_id = ?");
            $stmt->bind_param("iiisi", $score, $passed, $passed, $user['id'], $module_id);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Insert baru
        $completed_at = $passed ? date('Y-m-d H:i:s') : null;
        $stmt = $conn->prepare("INSERT INTO student_progress (student_id, module_id, score, is_completed, completed_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiis", $user['id'], $module_id, $score, $passed, $completed_at);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode([
        'success' => true,
        'score'   => $score,
        'passed'  => $passed,
        'message' => $passed ? 'Modul selesai! Modul berikutnya terbuka.' : 'Belum lulus, coba lagi.'
    ]);
    exit;
}

// --- Action: Check Answer ---
$quiz_id  = intval($_POST['quiz_id'] ?? 0);
$selected = trim($_POST['selected'] ?? '');

if ($quiz_id <= 0 || empty($selected)) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// Ambil jawaban benar + feedback + semua opsi
$stmt = $conn->prepare("SELECT correct_option, feedback, option_a, option_b, option_c, option_d FROM module_quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$quiz) {
    echo json_encode(['error' => 'Quiz not found']);
    exit;
}

$is_correct = ($selected === $quiz['correct_option']);
$correct_text = $quiz['option_' . $quiz['correct_option']];

echo json_encode([
    'correct'             => $is_correct,
    'correct_option'      => $quiz['correct_option'],
    'correct_answer_text' => $correct_text,
    'feedback'            => $quiz['feedback'],
    'selected'            => $selected,
]);
