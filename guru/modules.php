<?php
/**
 * ============================================================
 * EduAdapt - Kelola Modul (Guru) — Fase 4 Kasar
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

requireRole('admin');

$user = getCurrentUser();
$active_page = 'modules';

$success = '';
$error = '';

// --- DELETE ---
if (isset($_GET['hapus'])) {
    $del_id = intval($_GET['hapus']);
    if ($conn->query("DELETE FROM modules WHERE id = $del_id")) {
        $success = "Modul berhasil dihapus.";
    } else { $error = "Gagal menghapus: " . $conn->error; }
}

// --- ADD / UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mod_id   = intval($_POST['id'] ?? 0);
    $level    = $conn->real_escape_string($_POST['level'] ?? '');
    $title    = $conn->real_escape_string($_POST['title'] ?? '');
    $type     = $conn->real_escape_string($_POST['type'] ?? '');
    $video    = $conn->real_escape_string($_POST['video_url'] ?? '');
    $content  = $conn->real_escape_string($_POST['content_text'] ?? '');
    $seq      = intval($_POST['sequence_order'] ?? 1);
    $mode     = $_POST['mode'] ?? 'add';

    if (empty($title) || empty($level)) {
        $error = 'Judul dan Level wajib diisi.';
    } else {
        if ($mode === 'edit' && $mod_id > 0) {
            $sql = "UPDATE modules SET level='$level', title='$title', type='$type', video_url='$video', content_text='$content', sequence_order=$seq WHERE id=$mod_id";
            if ($conn->query($sql)) { $success = "Modul berhasil diupdate!"; }
            else { $error = "Gagal: " . $conn->error; }
        } else {
            $sql = "INSERT INTO modules (level, title, type, video_url, content_text, sequence_order) VALUES ('$level', '$title', '$type', '$video', '$content', $seq)";
            if ($conn->query($sql)) { $success = "Modul baru berhasil ditambahkan!"; }
            else { $error = "Gagal: " . $conn->error; }
        }
    }
}

// --- EDIT MODE ---
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_data = $conn->query("SELECT * FROM modules WHERE id = $edit_id")->fetch_assoc();
}

// --- FILTER ---
$filter_level = $_GET['filter'] ?? 'All';
$where = $filter_level !== 'All' ? "WHERE m.level = '" . $conn->real_escape_string($filter_level) . "'" : "";
$modules = $conn->query("SELECT m.*, (SELECT COUNT(*) FROM module_quizzes WHERE module_id = m.id) AS quiz_count FROM modules m $where ORDER BY m.level, m.sequence_order ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Modul — EduAdapt Admin</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="https://cdn.tiny.cloud/1/<?= env('TINYMCE_API_KEY') ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .table-custom { border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .table-custom table { margin: 0; }
        .table-custom th { background: #f8fafc; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; padding: 14px 16px; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { padding: 12px 16px; vertical-align: middle; color: #334155; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .badge-level { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .filter-btn { padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; border: 1px solid #e2e8f0; background: #fff; color: #64748b; text-decoration: none; transition: all 0.2s; }
        .filter-btn:hover, .filter-btn.active { background: #4f46e5; color: #fff; border-color: #4f46e5; }
        .alert-custom { border-radius: 14px; border: none; font-weight: 600; font-size: 0.875rem; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 1100px; margin: 0 auto; padding: 40px 24px;" class="animate-in">
    <h1 style="font-size: 1.875rem; font-weight: 800; color: #1e293b;">Kelola Modul Pembelajaran</h1>
    <p style="color: #94a3b8; margin-bottom: 28px;">CRUD modul materi dengan filter level.</p>

    <?php if ($success): ?><div class="alert alert-success alert-custom mb-3"><i class="bi bi-check-circle-fill me-1"></i> <?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger alert-custom mb-3"><i class="bi bi-exclamation-circle-fill me-1"></i> <?= $error ?></div><?php endif; ?>

    <!-- Form -->
    <div class="card-custom mb-4">
        <h5 style="font-weight: 700; margin-bottom: 16px;"><?= $edit_data ? 'Edit Modul' : 'Tambah Modul Baru' ?></h5>
        <form method="POST">
            <input type="hidden" name="mode" value="<?= $edit_data ? 'edit' : 'add' ?>">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Level</label>
                    <select name="level" class="form-select" required>
                        <option value="Beginner" <?= ($edit_data['level'] ?? '') === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                        <option value="Intermediate" <?= ($edit_data['level'] ?? '') === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                        <option value="Advanced" <?= ($edit_data['level'] ?? '') === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Judul Modul</label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Tipe</label>
                    <input type="text" name="type" class="form-control" placeholder="Video & Kuis" value="<?= htmlspecialchars($edit_data['type'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Urutan</label>
                    <input type="number" name="sequence_order" class="form-control" value="<?= $edit_data['sequence_order'] ?? 1 ?>" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Video URL (YouTube embed)</label>
                    <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/embed/..." value="<?= htmlspecialchars($edit_data['video_url'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Konten Materi</label>
                    <textarea name="content_text" id="contentEditor" rows="8"><?= htmlspecialchars($edit_data['content_text'] ?? '') ?></textarea>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">💡 Gunakan editor di atas untuk format teks, tambah heading, list, code block, dll.</div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn" style="background:#4f46e5; color:#fff; font-weight:700; padding:10px 24px; border-radius:12px;">
                    <i class="bi bi-<?= $edit_data ? 'pencil-square' : 'plus-circle-fill' ?> me-1"></i> <?= $edit_data ? 'Update' : 'Simpan' ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="modules.php" class="btn btn-outline-secondary" style="border-radius:12px; font-weight:600;">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Filter -->
    <div class="d-flex gap-2 mb-3">
        <a href="modules.php?filter=All" class="filter-btn <?= $filter_level === 'All' ? 'active' : '' ?>">Semua</a>
        <a href="modules.php?filter=Beginner" class="filter-btn <?= $filter_level === 'Beginner' ? 'active' : '' ?>">Beginner</a>
        <a href="modules.php?filter=Intermediate" class="filter-btn <?= $filter_level === 'Intermediate' ? 'active' : '' ?>">Intermediate</a>
        <a href="modules.php?filter=Advanced" class="filter-btn <?= $filter_level === 'Advanced' ? 'active' : '' ?>">Advanced</a>
    </div>

    <!-- Tabel Modul -->
    <div class="table-custom bg-white">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Level</th><th>Judul</th><th>Tipe</th><th>Video</th><th>Kuis</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                <?php if ($modules->num_rows > 0): ?>
                    <?php while ($m = $modules->fetch_assoc()):
                        $colors = ['Beginner' => ['#dcfce7','#16a34a'], 'Intermediate' => ['#dbeafe','#2563eb'], 'Advanced' => ['#f3e8ff','#9333ea']];
                        $c = $colors[$m['level']] ?? ['#f1f5f9','#64748b'];
                    ?>
                    <tr>
                        <td style="font-weight:800; color:#94a3b8;"><?= $m['sequence_order'] ?></td>
                        <td><span class="badge-level" style="background:<?= $c[0] ?>; color:<?= $c[1] ?>;"><?= $m['level'] ?></span></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($m['title']) ?></td>
                        <td style="color:#64748b;"><?= htmlspecialchars($m['type']) ?></td>
                        <td><?= $m['video_url'] ? '✅' : '—' ?></td>
                        <td>
                            <a href="quizzes.php?module_id=<?= $m['id'] ?>" class="btn-action" style="background:#fef3c7; color:#b45309; white-space:nowrap;">
                                <i class="bi bi-question-circle-fill"></i> <?= $m['quiz_count'] ?> soal
                            </a>
                        </td>
                        <td class="text-end">
                            <a href="modules.php?edit=<?= $m['id'] ?>" class="btn-action" style="background:#eef2ff; color:#4f46e5;">Edit</a>
                            <a href="modules.php?hapus=<?= $m['id'] ?>&filter=<?= $filter_level ?>" class="btn-action" style="background:#fef2f2; color:#dc2626;" onclick="return confirm('Yakin hapus modul ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center" style="padding:40px; color:#94a3b8;">Tidak ada modul untuk filter ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    // Inisialisasi TinyMCE rich text editor
    tinymce.init({
        selector: '#contentEditor',
        height: 350,
        menubar: false,
        plugins: 'lists link code codesample table',
        toolbar: 'undo redo | blocks bold italic underline | bullist numlist | link codesample | code',
        content_style: `
            body { font-family: 'Inter', 'Segoe UI', sans-serif; font-size: 14px; line-height: 1.8; color: #334155; padding: 12px; }
            h3 { color: #1e293b; font-size: 18px; font-weight: 700; margin-top: 16px; }
            h4 { color: #1e293b; font-size: 16px; font-weight: 700; margin-top: 12px; }
            code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 0.875em; color: #4f46e5; }
            pre { background: #1e293b; color: #e2e8f0; padding: 16px; border-radius: 12px; }
            ul, ol { padding-left: 20px; }
        `,
        block_formats: 'Paragraph=p; Heading 3=h3; Heading 4=h4; Preformatted=pre',
        placeholder: 'Tulis konten materi di sini... Gunakan toolbar di atas untuk formatting.',
        branding: false,
        promotion: false,
        license_key: 'gpl',
    });
</script>
</body>
</html>
