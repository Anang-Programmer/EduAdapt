<?php
/**
 * ============================================================
 * EduAdapt - Header / Navbar
 * ============================================================
 * Include di setiap halaman setelah <body>
 * 
 * Set variabel $active_page sebelum include:
 *   $active_page = 'dashboard'; // dashboard, recap, project
 * ============================================================
 */

$user = getCurrentUser();
$is_guru = ($user['role'] ?? '') === 'admin';
?>

<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    body { font-family: 'Inter', sans-serif !important; background-color: #f8fafc; }

    .edu-navbar {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.85rem 1.5rem;
        position: sticky;
        top: 0;
        z-index: 1050;
    }
    .edu-brand {
        font-size: 1.25rem;
        font-weight: 800;
        color: #4f46e5;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .edu-brand span { color: #1e293b; }
    .edu-brand:hover { color: #4f46e5; }

    .edu-nav-link {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    .edu-nav-link:hover { color: #4f46e5; background: #f1f5f9; }
    .edu-nav-link.active { color: #4f46e5; }

    .edu-user-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
        padding: 0.4rem 0.75rem;
        border-radius: 0.75rem;
        border: none;
        background: none;
        transition: all 0.2s;
    }
    .edu-user-btn:hover { background: #fef2f2; }
    .edu-user-btn:hover .edu-avatar { background: #fee2e2; color: #dc2626; }
    .edu-user-btn:hover .edu-user-name { color: #dc2626; }

    .edu-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s;
    }
    .edu-user-name {
        font-size: 0.875rem;
        font-weight: 700;
        color: #334155;
        transition: all 0.2s;
    }

    .edu-divider {
        width: 1px;
        height: 24px;
        background: #e2e8f0;
        margin: 0 8px;
    }
</style>

<nav class="edu-navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="max-width: 1200px; margin: 0 auto;">
        
        <!-- Brand -->
        <?php if ($is_guru): ?>
            <a href="../guru/dashboard.php" class="edu-brand">
                <i class="bi bi-layers" style="font-size: 1.5rem;"></i>
                EduAdapt<span> Admin - Guru</span>
            </a>
        <?php else: ?>
            <a href="../siswa/dashboard.php" class="edu-brand">
                EduAdapt
            </a>
        <?php endif; ?>

        <!-- Nav Links (Desktop) -->
        <div class="d-none d-md-flex align-items-center gap-1">
            <?php if ($is_guru): ?>
                <a href="../guru/dashboard.php" class="edu-nav-link <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>">Statistik</a>
                <a href="../guru/students.php" class="edu-nav-link <?= ($active_page ?? '') === 'students' ? 'active' : '' ?>">Siswa</a>
                <a href="../guru/modules.php" class="edu-nav-link <?= ($active_page ?? '') === 'modules' ? 'active' : '' ?>">Materi</a>
                <a href="../guru/grade_projects.php" class="edu-nav-link <?= ($active_page ?? '') === 'grade' ? 'active' : '' ?>">Evaluasi Projek</a>
            <?php else: ?>
                <a href="../siswa/dashboard.php" class="edu-nav-link <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>">Jalur Belajar</a>
                <a href="../siswa/recap.php" class="edu-nav-link <?= ($active_page ?? '') === 'recap' ? 'active' : '' ?>">Nilai Saya</a>
                <a href="../siswa/project_upload.php" class="edu-nav-link <?= ($active_page ?? '') === 'project' ? 'active' : '' ?>">Projek Akhir</a>
            <?php endif; ?>

            <div class="edu-divider"></div>

            <a href="../index.php?action=logout" class="edu-user-btn">
                <div class="edu-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <span class="edu-user-name">Keluar</span>
            </a>
        </div>

        <!-- Mobile Toggle -->
        <button class="btn d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
            <i class="bi bi-list" style="font-size: 1.5rem; color: #4f46e5;"></i>
        </button>
    </div>
</nav>

<!-- Mobile Offcanvas Menu -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" style="font-weight: 800; color: #4f46e5;">
            <i class="bi bi-code-slash me-2"></i>EduAdapt
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div class="d-flex flex-column gap-2">
            <?php if ($is_guru): ?>
                <a href="../guru/dashboard.php" class="edu-nav-link <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-bar-chart-fill me-2"></i>Statistik</a>
                <a href="../guru/students.php" class="edu-nav-link <?= ($active_page ?? '') === 'students' ? 'active' : '' ?>"><i class="bi bi-people-fill me-2"></i>Siswa</a>
                <a href="../guru/modules.php" class="edu-nav-link <?= ($active_page ?? '') === 'modules' ? 'active' : '' ?>"><i class="bi bi-book-fill me-2"></i>Materi</a>
                <a href="../guru/grade_projects.php" class="edu-nav-link <?= ($active_page ?? '') === 'grade' ? 'active' : '' ?>"><i class="bi bi-clipboard-check-fill me-2"></i>Evaluasi Projek</a>
            <?php else: ?>
                <a href="../siswa/dashboard.php" class="edu-nav-link <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-book-fill me-2"></i>Jalur Belajar</a>
                <a href="../siswa/recap.php" class="edu-nav-link <?= ($active_page ?? '') === 'recap' ? 'active' : '' ?>"><i class="bi bi-graph-up me-2"></i>Nilai Saya</a>
                <a href="../siswa/project_upload.php" class="edu-nav-link <?= ($active_page ?? '') === 'project' ? 'active' : '' ?>"><i class="bi bi-upload me-2"></i>Projek Akhir</a>
            <?php endif; ?>
        </div>
        <div class="mt-auto pt-3 border-top">
            <div class="d-flex align-items-center gap-3 mb-3 px-2">
                <div class="edu-avatar"><i class="bi bi-person-fill"></i></div>
                <div>
                    <div style="font-weight: 700; font-size: 14px; color: #1e293b;"><?= htmlspecialchars($user['name'] ?? 'User') ?></div>
                    <div style="font-size: 12px; color: #94a3b8;"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                </div>
            </div>
            <a href="../index.php?action=logout" class="btn btn-outline-danger w-100 rounded-3" style="font-weight: 700;">
                <i class="bi bi-box-arrow-right me-2"></i>Keluar
            </a>
        </div>
    </div>
</div>
