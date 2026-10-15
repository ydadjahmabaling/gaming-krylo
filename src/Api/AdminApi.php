<?php
// POST api.php?api=save_product    - create/update a listing (admin only)
// POST api.php?api=delete_product  - remove a listing (admin only)

declare(strict_types=1);

final class AdminApi
{
    public static function saveProduct(?PDO $pdo): never
    {
        Security::requireAdmin();

        $id       = trim((string) ($_POST['id'] ?? ''));
        $title    = htmlspecialchars(trim((string) ($_POST['title'] ?? '')), ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars(trim((string) ($_POST['category'] ?? '')), ENT_QUOTES, 'UTF-8');
        $rarity   = htmlspecialchars(trim((string) ($_POST['rarity'] ?? 'Mythical')), ENT_QUOTES, 'UTF-8');
        $desc     = htmlspecialchars(trim((string) ($_POST['description'] ?? '')), ENT_QUOTES, 'UTF-8');
        $price    = (float) ($_POST['price'] ?? 0);
        $stock    = (int) ($_POST['stock'] ?? 0);
        $image    = filter_var((string) ($_POST['image'] ?? ''), FILTER_SANITIZE_URL) ?: null;

        if ($title === '' || $price <= 0) {
            Response::error('Valid title and price are required');
        }

        if ($pdo === null) {
            Response::json('fallback_save', [
                'id' => $id !== '' ? $id : 'p_' . time(),
                'title' => $title, 'category' => $category, 'price' => $price,
                'rarity' => $rarity, 'stock' => $stock, 'image' => $image, 'desc' => $desc,
            ]);
        }

        if ($id !== '') {
            $stmt = $pdo->prepare(
                'UPDATE `products` SET `title` = ?, `category` = ?, `price` = ?, `rarity` = ?, `stock` = ?, `image_url` = ?, `description` = ? WHERE `id` = ?'
            );
            $stmt->execute([$title, $category, $price, $rarity, $stock, $image, $desc, $id]);
            Response::json('success', [], 'Product updated successfully in MySQL');
        }

        $newId = 'p_' . time() . '_' . random_int(100, 999);
        $stmt = $pdo->prepare(
            'INSERT INTO `products` (`id`,`title`,`category`,`price`,`rarity`,`stock`,`image_url`,`description`) VALUES (?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([$newId, $title, $category, $price, $rarity, $stock, $image, $desc]);

        Response::json('success', ['id' => $newId], 'New Roblox item listed successfully');
    }

    public static function deleteProduct(?PDO $pdo): never
    {
        Security::requireAdmin();

        $id = trim((string) ($_POST['id'] ?? ''));
        if ($id === '' || !preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $id)) {
            Response::error('Invalid product id');
        }

        if ($pdo === null) {
            Response::json('success', [], 'Deleted locally');
        }

        $stmt = $pdo->prepare('DELETE FROM `products` WHERE `id` = ?');
        $stmt->execute([$id]);

        Response::json('success', [], 'Product removed from database');
    }
}
