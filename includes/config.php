<?php
/**
 * ============================================================
 * EduAdapt - Config (baca .env)
 * ============================================================
 * Cara pakai:
 *   require_once __DIR__ . '/../includes/config.php';
 *   echo getEnv('TINYMCE_API_KEY');
 * ============================================================
 */

// Parse .env sekali, simpan di global
function loadEnv($path = null) {
    static $env = null;
    if ($env !== null) return $env;

    $env = [];
    $file = $path ?? __DIR__ . '/../.env';

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue; // skip komentar
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
    }
    return $env;
}

function env($key, $default = '') {
    $env = loadEnv();
    return $env[$key] ?? $default;
}
?>
