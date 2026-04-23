<?php
/**
 * ============================================================
 * EduAdapt - Upload Projek Akhir (Fase 3)
 * ============================================================
 * Form upload file PDF/Word ke server + simpan ke tabel projects
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('student');

$user = getCurrentUser();
$active_page = 'project';

// Buat folder uploads jika belum ada
$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$success = '';
$error = '';

// --- Handle Upload POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['project_file'])) {
    $file = $_FILES['project_file'];
    
    // Validasi tipe file
    $allowed_types = [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    $allowed_ext = ['pdf', 'doc', 'docx'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Terjadi kesalahan saat upload. Coba lagi.';
    } elseif (!in_array($ext, $allowed_ext)) {
        $error = 'Format file tidak didukung. Gunakan PDF, DOC, atau DOCX.';
    } elseif ($file['size'] > 10 * 1024 * 1024) { // Max 10MB
        $error = 'Ukuran file terlalu besar. Maksimal 10MB.';
    } else {
        // Generate nama file unik
        $safe_name = $user['id'] . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
        $dest_path = $upload_dir . $safe_name;
        
        if (move_uploaded_file($file['tmp_name'], $dest_path)) {
            // Simpan ke database
            $stmt = $conn->prepare("INSERT INTO projects (student_id, file_name, file_path) VALUES (?, ?, ?)");
            $relative_path = 'uploads/' . $safe_name;
            $original_name = $file['name'];
            $stmt->bind_param("sss", $user['id'], $original_name, $relative_path);
            
            if ($stmt->execute()) {
                $success = 'File berhasil diupload! Guru akan segera menilai proyekmu.';
            } else {
                $error = 'Gagal menyimpan data ke database.';
            }
            $stmt->close();
        } else {
            $error = 'Gagal memindahkan file. Cek permission folder uploads.';
        }
    }
}

// --- Ambil riwayat upload siswa ---
$stmt = $conn->prepare("SELECT * FROM projects WHERE student_id = ? ORDER BY submitted_at DESC");
$stmt->bind_param("s", $user['id']);
$stmt->execute();
$my_projects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Instruksi level
$level_task = [
    'Beginner'     => 'Buatlah sebuah <strong>Landing Page Portofolio</strong> sederhana menggunakan HTML5 dan CSS murni.',
    'Intermediate' => 'Buatlah <strong>Website Interaktif</strong> dengan JavaScript DOM manipulation dan layout responsif menggunakan Flexbox/Grid.',
    'Advanced'     => 'Buatlah <strong>Single Page Application (SPA)</strong> yang mengintegrasikan data dari API publik eksternal.',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projek Akhir — EduAdapt</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .instruction-card {
            background: #fff;
            border-radius: 24px;
            padding: 36px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 14px -2px rgba(0,0,0,0.04);
        }
        .upload-zone {
            background: #eef2ff;
            border: 2px dashed #c7d2fe;
            border-radius: 24px;
            padding: 56px 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-zone:hover { background: #e0e7ff; border-color: #a5b4fc; }
        .upload-zone.dragover { background: #e0e7ff; border-color: #4f46e5; transform: scale(1.01); }

        .upload-icon {
            width: 80px; height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 4px 14px -4px rgba(0,0,0,0.08);
            font-size: 32px; color: #4f46e5;
            transition: transform 0.3s;
        }
        .upload-zone:hover .upload-icon { transform: scale(1.1); }

        .file-input-hidden { display: none; }

        .history-card {
            background: #fff;
            border-radius: 20px;
            padding: 20px 24px;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }
        .history-card:hover { border-color: #c7d2fe; }

        .file-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }

        .alert-custom {
            border-radius: 16px;
            border: none;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 16px 20px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 900px; margin: 0 auto; padding: 40px 24px;" class="animate-in">
    
    <h1 style="font-size: 1.875rem; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Projek Akhir</h1>
    <p style="color: #94a3b8; margin-bottom: 32px;">Tunjukkan kemampuan terbaikmu melalui karya nyata.</p>

    <!-- Alert Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-custom mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-custom mb-4">
            <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Instruction Card -->
    <div class="instruction-card mb-4">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width: 48px; height: 48px; background: #4f46e5; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; box-shadow: 0 4px 12px -2px rgba(79,70,229,0.3);">
                <i class="bi bi-layout-text-window-reverse"></i>
            </div>
            <div>
                <h3 style="font-weight: 800; color: #1e293b; margin: 0; font-size: 1.125rem;">Instruksi Projek</h3>
                <p style="margin: 0; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                    Target Level: <?= $user['level'] ?>
                </p>
            </div>
        </div>
        
        <p style="color: #475569; line-height: 1.8; margin-bottom: 16px;">
            <?= $level_task[$user['level']] ?? $level_task['Beginner'] ?>
        </p>

        <p style="color: #475569; line-height: 1.8;">Cakupan yang harus ada:</p>
        <ul style="color: #475569; line-height: 2; padding-left: 20px;">
            <li>Header dengan navigasi interaktif</li>
            <li>Hero Section (intro diri yang memukau)</li>
            <li>Gallery Project (minimal 3 gambar dengan grid)</li>
            <li>Contact Form (tampilan dengan input yang rapi)</li>
        </ul>

        <div style="background: #f8fafc; border-radius: 14px; padding: 16px 20px; border: 1px solid #e2e8f0; margin-top: 16px;">
            <p style="font-size: 0.8125rem; font-weight: 600; color: #475569; margin: 0;">
                <i class="bi bi-info-circle-fill me-1" style="color: #4f46e5;"></i>
                <strong>Format:</strong> PDF atau Word (max 10MB). Sertakan screenshot dan penjelasan kode.
            </p>
        </div>
    </div>

    <!-- Upload Zone -->
    <form method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="upload-zone" id="uploadZone" onclick="document.getElementById('fileInput').click();">
            <div class="upload-icon">
                <i class="bi bi-cloud-arrow-up-fill"></i>
            </div>
            <h3 style="font-weight: 800; color: #1e293b; margin-bottom: 6px;">Upload Laporan Projek</h3>
            <p style="color: #94a3b8; max-width: 360px; margin: 0 auto 24px; font-size: 0.9375rem;">
                Unggah file dokumentasi pengembangan dalam format PDF atau Word.
            </p>
            <p style="color: #4f46e5; font-weight: 700; font-size: 0.875rem;" id="fileNameDisplay">
                Klik untuk memilih file atau drag & drop di sini
            </p>
        </div>
        <input type="file" name="project_file" id="fileInput" class="file-input-hidden" accept=".pdf,.doc,.docx" onchange="handleFileSelect(this)">
        <button type="submit" id="submitBtn" class="btn w-100 mt-3" style="display: none; background: #4f46e5; color: #fff; font-weight: 700; padding: 16px; border-radius: 16px; font-size: 1rem; box-shadow: 0 4px 14px -2px rgba(79,70,229,0.3);">
            <i class="bi bi-send-fill me-2"></i> Upload Sekarang
        </button>
    </form>

    <!-- Upload History -->
    <?php if (!empty($my_projects)): ?>
    <div class="mt-5">
        <h3 style="font-weight: 800; color: #1e293b; margin-bottom: 16px; font-size: 1.125rem;">
            <i class="bi bi-clock-history me-1" style="color: #94a3b8;"></i> Riwayat Upload
        </h3>
        <?php foreach ($my_projects as $p): ?>
        <div class="history-card">
            <div class="file-icon" style="background: <?= $p['score'] ? '#dcfce7' : '#eef2ff' ?>; color: <?= $p['score'] ? '#16a34a' : '#4f46e5' ?>;">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 0.9375rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= htmlspecialchars($p['file_name']) ?>
                </p>
                <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                    Dikirim: <?= date('d M Y, H:i', strtotime($p['submitted_at'])) ?>
                </p>
                <?php if ($p['teacher_feedback']): ?>
                    <p style="color: #475569; font-size: 0.8125rem; margin: 4px 0 0; font-style: italic;">
                        💬 "<?= htmlspecialchars($p['teacher_feedback']) ?>"
                    </p>
                <?php endif; ?>
            </div>
            <div style="text-align: center; min-width: 70px;">
                <?php if ($p['score'] !== null): ?>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #4f46e5;"><?= $p['score'] ?></div>
                    <div style="font-size: 10px; color: #94a3b8; font-weight: 700;">SKOR</div>
                <?php else: ?>
                    <span style="background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase;">Menunggu</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const submitBtn = document.getElementById('submitBtn');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    function handleFileSelect(input) {
        if (input.files.length > 0) {
            const file = input.files[0];
            fileNameDisplay.innerHTML = `<i class="bi bi-file-earmark-check-fill me-1"></i> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            submitBtn.style.display = 'block';
            uploadZone.style.borderColor = '#4f46e5';
            uploadZone.style.background = '#e0e7ff';
        }
    }

    // Drag & Drop
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });
    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect(fileInput);
        }
    });
</script>

</body>
</html>
