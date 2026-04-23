<?php
/**
 * ============================================================
 * EduAdapt - Kelola Siswa (Guru) — Fase 4 Kasar
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

$user = getCurrentUser();
$active_page = 'students';

$success = '';
$error = '';

// --- DELETE ---
if (isset($_GET['hapus'])) {
    $del_id = $conn->real_escape_string($_GET['hapus']);
    if ($conn->query("DELETE FROM users WHERE id = '$del_id' AND role = 'student'")) {
        $success = "Siswa $del_id berhasil dihapus.";
    } else {
        $error = "Gagal menghapus: " . $conn->error;
    }
}

// --- ADD / UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = trim($_POST['id'] ?? '');
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    $level = $_POST['classification_level'] ?? '';
    $mode  = $_POST['mode'] ?? 'add';

    if (empty($id) || empty($name) || empty($email)) {
        $error = 'ID, Nama, dan Email wajib diisi.';
    } else {
        if ($mode === 'add') {
            if (empty($pass)) { $error = 'Password wajib diisi untuk siswa baru.'; }
            else {
                $hashed = password_hash($pass, PASSWORD_DEFAULT);
                $level_val = !empty($level) ? "'$level'" : "NULL";
                $sql = "INSERT INTO users (id, name, email, password, role, classification_level) VALUES ('$id', '$name', '$email', '$hashed', 'student', $level_val)";
                if ($conn->query($sql)) { $success = "Siswa $id berhasil ditambahkan!"; }
                else { $error = "Gagal: " . $conn->error; }
            }
        } else {
            $level_val = !empty($level) ? "classification_level='$level'" : "classification_level=NULL";
            $sql = "UPDATE users SET name='$name', email='$email', $level_val WHERE id='$id' AND role='student'";
            if (!empty($pass)) {
                $hashed = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET name='$name', email='$email', password='$hashed', $level_val WHERE id='$id' AND role='student'";
            }
            if ($conn->query($sql)) { $success = "Siswa $id berhasil diupdate!"; }
            else { $error = "Gagal: " . $conn->error; }
        }
    }
}

// --- EDIT MODE ---
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = $conn->real_escape_string($_GET['edit']);
    $res = $conn->query("SELECT * FROM users WHERE id = '$edit_id' AND role = 'student'");
    $edit_data = $res->fetch_assoc();
}

// --- READ ---
$students = $conn->query("SELECT id, name, email, classification_level, created_at FROM users WHERE role = 'student' ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa — EduAdapt Admin</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .table-custom { border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
        .table-custom table { margin: 0; }
        .table-custom th { background: #f8fafc; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; padding: 14px 20px; border-bottom: 2px solid #e2e8f0; }
        .table-custom td { padding: 14px 20px; vertical-align: middle; color: #334155; border-bottom: 1px solid #f1f5f9; }
        .badge-level { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .alert-custom { border-radius: 14px; border: none; font-weight: 600; font-size: 0.875rem; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 1100px; margin: 0 auto; padding: 40px 24px;" class="animate-in">
    <h1 style="font-size: 1.875rem; font-weight: 800; color: #1e293b;">Data Siswa</h1>
    <p style="color: #94a3b8; margin-bottom: 28px;">Tambah, edit, atau hapus data siswa terdaftar.</p>

    <?php if ($success): ?><div class="alert alert-success alert-custom mb-3"><i class="bi bi-check-circle-fill me-1"></i> <?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger alert-custom mb-3"><i class="bi bi-exclamation-circle-fill me-1"></i> <?= $error ?></div><?php endif; ?>

    <!-- Form Tambah/Edit -->
    <div class="card-custom mb-4">
        <h5 style="font-weight: 700; margin-bottom: 16px;"><?= $edit_data ? 'Edit Siswa' : 'Tambah Siswa Baru' ?></h5>
        <form method="POST">
            <input type="hidden" name="mode" value="<?= $edit_data ? 'edit' : 'add' ?>">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">ID Siswa</label>
                    <input type="text" name="id" class="form-control" required placeholder="S004" value="<?= htmlspecialchars($edit_data['id'] ?? '') ?>" <?= $edit_data ? 'readonly' : '' ?>>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required placeholder="Nama siswa" value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="siswa@gmail.com" value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Password <?= $edit_data ? '(kosongkan jika tidak diubah)' : '' ?></label>
                    <input type="text" name="password" class="form-control" placeholder="••••" <?= $edit_data ? '' : 'required' ?>>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:12px; font-weight:700; color:#64748b;">Level</label>
                    <select name="classification_level" class="form-select">
                        <option value="">— Belum diset —</option>
                        <option value="Beginner" <?= ($edit_data['classification_level'] ?? '') === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                        <option value="Intermediate" <?= ($edit_data['classification_level'] ?? '') === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                        <option value="Advanced" <?= ($edit_data['classification_level'] ?? '') === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn" style="background: #4f46e5; color: #fff; font-weight: 700; padding: 10px 24px; border-radius: 12px;">
                    <i class="bi bi-<?= $edit_data ? 'pencil-square' : 'person-plus-fill' ?> me-1"></i> <?= $edit_data ? 'Update' : 'Simpan' ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="students.php" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 600;">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel Siswa -->
    <div class="table-custom bg-white">
        <table class="table mb-0">
            <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Level</th><th>Terdaftar</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                <?php while ($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><code style="color:#4f46e5; font-weight:700;"><?= $s['id'] ?></code></td>
                    <td style="font-weight: 700;"><?= htmlspecialchars($s['name']) ?></td>
                    <td style="color: #64748b;"><?= $s['email'] ?></td>
                    <td>
                        <?php
                        $lv = $s['classification_level'] ?? '';
                        $colors = ['Beginner' => ['#dcfce7','#16a34a'], 'Intermediate' => ['#dbeafe','#2563eb'], 'Advanced' => ['#f3e8ff','#9333ea']];
                        if (isset($colors[$lv])):
                        ?>
                            <span class="badge-level" style="background:<?= $colors[$lv][0] ?>; color:<?= $colors[$lv][1] ?>;"><?= $lv ?></span>
                        <?php else: ?>
                            <span style="color:#cbd5e1;">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="color:#94a3b8; font-size:13px;"><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                    <td class="text-end">
                        <a href="students.php?edit=<?= $s['id'] ?>" class="btn-action" style="background:#eef2ff; color:#4f46e5;">Edit</a>
                        <a href="students.php?hapus=<?= $s['id'] ?>" class="btn-action" style="background:#fef2f2; color:#dc2626;" onclick="return confirm('Yakin hapus siswa ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
