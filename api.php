<?php
// Gaming Krylo - REST API entry point.
// All endpoints live behind this single dispatcher: strict headers, CSRF
// validation on POST, a route whitelist, and per-resource handler classes.

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Security::startSession();

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if (!Security::validateCsrf()) {
    Response::error('Invalid CSRF Security Token', 403);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = (string) filter_input(INPUT_GET, 'api', FILTER_SANITIZE_SPECIAL_CHARS);

// Route whitelist: method + action => [HandlerClass, method]
$routes = [
    'GET'  => [
        'products' => [ProductsApi::class, 'index'],
    ],
    'POST' => [
        'login'          => [AuthApi::class, 'login'],
        'verify_admin'   => [AuthApi::class, 'verifyAdmin'],
        'save_product'   => [AdminApi::class, 'saveProduct'],
        'delete_product' => [AdminApi::class, 'deleteProduct'],
        'checkout'       => [OrdersApi::class, 'checkout'],
    ],
];

if (!isset($routes[$method][$action])) {
    Response::error('Invalid Endpoint', 404);
}

$pdo = Database::connection();
[$class, $handler] = $routes[$method][$action];
$class::$handler($pdo);
