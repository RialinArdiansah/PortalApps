<?php
// ═══════════════════════════════════════════════════════════════════
// Portal Sertifikasi — Configuration
// ═══════════════════════════════════════════════════════════════════

// Load environment file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
} else {
    $env = [];
}

// Helper to read env value with fallback
function env(string $key, mixed $default = null): mixed
{
    global $env;
    return $env[$key] ?? getenv($key) ?: $default;
}

// ── Application ────────────────────────────────────────────────
define('APP_ENV',   env('APP_ENV', 'production'));
define('APP_DEBUG', filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN));
define('APP_URL',   env('APP_URL', 'http://localhost'));
define('APP_NAME',  'Portal Sertifikasi');

// ── Database ───────────────────────────────────────────────────
define('DB_HOST',    env('DB_HOST', '127.0.0.1'));
define('DB_NAME',    env('DB_NAME', 'portal_sertifikasi'));
define('DB_USER',    env('DB_USER', 'root'));
define('DB_PASS',    env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// ── Paths ──────────────────────────────────────────────────────
define('BASE_PATH', __DIR__);

// Base URL prefix for links (auto-set in index.php, fallback for CLI)
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

// ── Timezone ───────────────────────────────────────────────────
date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));

// ── Error Handling ─────────────────────────────────────────────
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    // Log errors to file in production
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
}
