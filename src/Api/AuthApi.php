<?php
// POST api.php?api=login          - user login
// POST api.php?api=verify_admin   - proprietary admin key gate

declare(strict_types=1);

final class AuthApi
{
    public static function login(?PDO $pdo): never
    {
        self::throttleLogins();

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = trim((string) ($_POST['password'] ?? ''));

        if ($username === '' || $password === '') {
            Response::error('Username and password required');
        }

        if ($pdo === null) {
            // Preview fallback: session-only login.
            $_SESSION['username'] = $username;
            $_SESSION['login_attempts'] = 0;
            Response::json('success', ['username' => $username, 'role' => 'user'], 'Logged in (session)');
        }

        $stmt = $pdo->prepare('SELECT * FROM `users` WHERE `username` = :u OR `email` = :u LIMIT 1');
        $stmt->execute(['u' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['login_attempts'] = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
            Response::error('Invalid username or password', 401);
        }

        Security::regenerateSession();
        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['login_attempts'] = 0;

        Response::json('success', ['username' => $user['username'], 'role' => $user['role']], 'Logged in successfully');
    }

    public static function verifyAdmin(): never
    {
        $key = trim((string) ($_POST['admin_key'] ?? ''));

        // hash_equals avoids timing side-channels on the key check.
        if ($key === '' || !hash_equals(ADMIN_ACCESS_KEY, $key)) {
            Response::error('Invalid Proprietary Admin Security Key', 403);
        }

        Security::regenerateSession();
        $_SESSION['role'] = 'admin';
        $_SESSION['admin_authenticated'] = true;

        Response::json('success', ['role' => 'admin'], 'Proprietary Admin Access Granted');
    }

    /** Simple per-session brute-force throttle. */
    private static function throttleLogins(): void
    {
        $attempts = (int) ($_SESSION['login_attempts'] ?? 0);
        $blockedUntil = (int) ($_SESSION['login_blocked_until'] ?? 0);

        if ($blockedUntil > time()) {
            Response::error('Too many attempts. Try again shortly.', 429);
        }

        if ($attempts >= LOGIN_MAX_ATTEMPTS) {
            $_SESSION['login_blocked_until'] = time() + LOGIN_COOLDOWN_SECONDS;
            $_SESSION['login_attempts'] = 0;
            Response::error('Too many attempts. Try again shortly.', 429);
        }
    }
}
