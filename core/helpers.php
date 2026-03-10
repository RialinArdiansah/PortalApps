<?php
// ═══════════════════════════════════════════════════════════════════
// Global helper functions
// ═══════════════════════════════════════════════════════════════════

/**
 * Escape HTML to prevent XSS.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a UUID v4.
 */
function generate_uuid(): string
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Format number as Rupiah.
 */
function format_rupiah(int|float $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian format.
 */
function format_date(string $date, string $format = 'd M Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Get CSRF token (generate if not exists).
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render hidden CSRF input.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF token.
 */
function verify_csrf(): void
{
    $token = $_POST['_csrf']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? '';

    // Also check JSON body for _csrf if Content-Type is json
    if (empty($token) && stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $body = json_decode(file_get_contents('php://input'), true);
        $token = $body['_csrf'] ?? '';
    }

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF token mismatch.');
    }
}

/**
 * Redirect to URL.
 */
function redirect(string $url): void
{
    header('Location: ' . url($url));
    exit;
}

/**
 * Set flash message.
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message.
 */
function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Render a view within the layout.
 */
function view(string $viewFile, array $data = [], string $layout = 'layouts/app'): void
{
    extract($data);
    $viewContent = BASE_PATH . "/views/{$viewFile}.php";
    require BASE_PATH . "/views/{$layout}.php";
}

/**
 * Return JSON response.
 */
function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get POST input safely.
 */
function input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Get JSON body input.
 */
function json_input(): array
{
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

/**
 * Check if current path matches.
 */
function is_active(string $path): bool
{
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Strip BASE_URL prefix
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    if ($baseUrl && strpos($current, $baseUrl) === 0) {
        $current = substr($current, strlen($baseUrl));
    }
    if (empty($current)) $current = '/';
    if ($path === '/') {
        return $current === '/';
    }
    return str_starts_with($current, $path);
}

/**
 * Generate a URL with base path prefix.
 */
function url(string $path = '/'): string
{
    $base = defined('BASE_URL') ? BASE_URL : '';
    return $base . $path;
}

/**
 * Generate asset URL.
 */
function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}
