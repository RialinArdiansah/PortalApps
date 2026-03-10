<?php
class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'        => $user['id'],
            'full_name' => $user['full_name'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?string
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    // ── Gate helpers (match Laravel model methods) ──

    public static function isAdmin(): bool
    {
        return in_array(self::role(), ['Super admin', 'admin']);
    }

    public static function canViewAll(): bool
    {
        return in_array(self::role(), ['Super admin', 'admin', 'manager']);
    }

    public static function isSuperAdmin(): bool
    {
        return self::role() === 'Super admin';
    }

    // ── Middleware ──

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            echo 'Akses ditolak';
            exit;
        }
    }

    public static function requireSuperAdmin(): void
    {
        self::requireLogin();
        if (!self::isSuperAdmin()) {
            http_response_code(403);
            echo 'Akses ditolak — hanya Super Admin';
            exit;
        }
    }

    public static function requireAdminOrManager(): void
    {
        self::requireLogin();
        if (!self::canViewAll()) {
            http_response_code(403);
            echo 'Akses ditolak';
            exit;
        }
    }
}
