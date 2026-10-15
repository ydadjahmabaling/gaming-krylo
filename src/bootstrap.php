<?php
// Gaming Krylo - Application bootstrap
// Owned and operated by Krylo Digital LLC

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Api/ProductsApi.php';
require_once __DIR__ . '/Api/AuthApi.php';
require_once __DIR__ . '/Api/AdminApi.php';
require_once __DIR__ . '/Api/OrdersApi.php';
