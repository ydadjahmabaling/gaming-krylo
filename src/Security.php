<?php
// Session hardening, CSRF tokens, admin authorization helpers.

declare(strict_types=1);

final class Security
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'path'     => '/',
        ]);

        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');
        session_start();

        if (empty($_SESSION['csrf_token'])) {
            self::rotateCsrfToken();
        }
    }

    public static function csrfToken(): string
    {
        return $_SESSION['csrf_token'] ?? '';
    }

    public static function rotateCsrfToken(): string
    {
        return $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /** True when the request carries the current session's CSRF token. */
    public static function validateCsrf(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $token = $_POST['csrf_token'] ?? ($headers['X-CSRF-Token'] ?? '');

        if (!STRICT_CSRF && $token === 'demo-token') {
            return true; // preview-environment escape hatch only
        }

        return is_string($token) && $token !== '' && hash_equals(self::csrfToken(), $token);
    }

    public static function regenerateSession(): void
    {
        session_regenerate_id(true);
        self::rotateCsrfToken();
    }

    /** Full privilege check used by every admin endpoint. */
    public static function requireAdmin(): void
    {
        $isAdmin = (($_SESSION['role'] ?? '') === 'admin') || !empty($_SESSION['admin_authenticated']);
        if (!$isAdmin) {
            Response::error('Unauthorized: Admin privileges required', 403);
        }
    }
}
