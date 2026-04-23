<?php
/**
 * ============================================================
 * EduAdapt - Auth Helper
 * ============================================================
 * Include file ini di setiap halaman yang butuh proteksi login.
 * 
 * Cara pakai:
 *   require_once __DIR__ . '/../includes/auth.php';
 *   requireLogin();  // redirect ke login jika belum login
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Ambil data user dari session
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
        'level' => $_SESSION['user_level'] ?? null,
    ];
}

/**
 * Set session setelah login berhasil
 */
function setUserSession($user) {
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];
    $_SESSION['user_level'] = $user['classification_level'];
}

/**
 * Update level di session (setelah diagnostic)
 */
function updateSessionLevel($level) {
    $_SESSION['user_level'] = $level;
}

/**
 * Redirect berdasarkan role & level user
 */
function redirectByRole($base_url = '') {
    $role  = $_SESSION['user_role'] ?? '';
    $level = $_SESSION['user_level'] ?? null;

    if ($role === 'admin') {
        header("Location: {$base_url}guru/dashboard.php");
    } elseif ($role === 'student' && empty($level)) {
        header("Location: {$base_url}siswa/diagnostic.php");
    } else {
        header("Location: {$base_url}siswa/dashboard.php");
    }
    exit;
}

/**
 * Proteksi halaman — redirect ke login jika belum login
 */
function requireLogin($redirect_url = '../index.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit;
    }
}

/**
 * Proteksi halaman khusus role tertentu
 */
function requireRole($role, $redirect_url = '../index.php') {
    requireLogin($redirect_url);
    if ($_SESSION['user_role'] !== $role) {
        header("Location: $redirect_url");
        exit;
    }
}

/**
 * Logout — hapus session dan redirect
 */
function logout($redirect_url = 'index.php') {
    session_unset();
    session_destroy();
    header("Location: $redirect_url");
    exit;
}
?>
