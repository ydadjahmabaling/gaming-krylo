<?php
// Central configuration for Gaming Krylo.
// Every value can be overridden via environment variables (recommended for production).

declare(strict_types=1);

define('DB_HOST', getenv('KRYLO_DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('KRYLO_DB_NAME') ?: 'krylo_gaming_db');
define('DB_USER', getenv('KRYLO_DB_USER') ?: 'root');
define('DB_PASS', (getenv('KRYLO_DB_PASS') !== false) ? getenv('KRYLO_DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

// Proprietary admin access key (demo default - override in production!).
define('ADMIN_ACCESS_KEY', getenv('KRYLO_ADMIN_KEY') ?: 'KRYLO2026');

// Default credentials for the auto-seeded admin account.
define('ADMIN_USERNAME', getenv('KRYLO_ADMIN_USER') ?: 'admin');
define('ADMIN_EMAIL',    getenv('KRYLO_ADMIN_EMAIL') ?: 'admin@krylodigital.com');
define('ADMIN_PASSWORD', getenv('KRYLO_ADMIN_PASS') ?: 'KRYLO2026');

// 'production' enforces CSRF strictly. Set APP_ENV=demo to allow the demo token.
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('STRICT_CSRF', APP_ENV !== 'demo');

// Max login attempts per session before a cooldown is applied.
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_COOLDOWN_SECONDS', 60);
