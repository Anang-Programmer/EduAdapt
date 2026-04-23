<?php
/**
 * ============================================================
 * EduAdapt - Dashboard Guru/Admin
 * ============================================================
 * Placeholder — akan diimplementasikan di Fase 4
 * ============================================================
 */

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

$user = getCurrentUser();
$active_page = 'dashboard';

// Statistik cepat
$count_beginner     = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='student' AND classification_level='Beginner'")->fetch_assoc()['t'];
$count_intermediate = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='student' AND classification_level='Intermediate'")->fetch_assoc()['t'];
$count_advanced     = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='student' AND classification_level='Advanced'")->fetch_assoc()['t'];
$count_total        = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='student'")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru — EduAdapt</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .stat-card {
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1e293b;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-top: 4px;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.5s ease-out forwards; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main style="max-width: 1100px; margin: 0 auto; padding: 40px 24px;">
    
    <div class="animate-in">
        <h1 style="font-size: 1.875rem; font-weight: 800; color: #1e293b;">Dashboard Statistik</h1>
        <p style="color: #94a3b8; margin-bottom: 32px;">Gambaran umum performa kelas dan persebaran kompetensi.</p>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon mb-3" style="background: #e0e7ff; color: #4f46e5;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-number"><?= $count_total ?></div>
                    <div class="stat-label">Total Siswa</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon mb-3" style="background: #dcfce7; color: #16a34a;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-number"><?= $count_beginner ?></div>
                    <div class="stat-label">Level Beginner</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon mb-3" style="background: #dbeafe; color: #2563eb;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-number"><?= $count_intermediate ?></div>
                    <div class="stat-label">Level Intermediate</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon mb-3" style="background: #f3e8ff; color: #9333ea;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-number"><?= $count_advanced ?></div>
                    <div class="stat-label">Level Advanced</div>
                </div>
            </div>
        </div>

        <div class="stat-card" style="text-align: center; padding: 60px;">
            <i class="bi bi-gear-fill" style="font-size: 48px; color: #e2e8f0;"></i>
            <h3 style="color: #94a3b8; font-weight: 700; margin-top: 16px;">Panel Guru (Fase 4)</h3>
            <p style="color: #cbd5e1; font-size: 14px;">CRUD lengkap akan dibuat di fase terakhir. Saat ini fokus ke sisi siswa.</p>
        </div>
    </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
