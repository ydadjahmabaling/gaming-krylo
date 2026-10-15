<?php
// GET api.php?api=products - public catalog listing.

declare(strict_types=1);

final class ProductsApi
{
    public static function index(?PDO $pdo): never
    {
        if ($pdo === null) {
            Response::json('fallback', [], 'Using SQLite/LocalStorage Fallback');
        }

        $stmt = $pdo->query('SELECT * FROM `products` ORDER BY `created_at` DESC');
        Response::json('success', $stmt->fetchAll());
    }
}
