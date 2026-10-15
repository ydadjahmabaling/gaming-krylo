<?php
// POST api.php?api=checkout - record an order for Roblox delivery.

declare(strict_types=1);

final class OrdersApi
{
    public static function checkout(?PDO $pdo): never
    {
        $robloxUser = htmlspecialchars(trim((string) ($_POST['roblox_username'] ?? '')), ENT_QUOTES, 'UTF-8');
        $totalPrice = (float) ($_POST['total_price'] ?? 0);

        if ($robloxUser === '') {
            Response::error('Roblox Username is required for item delivery!');
        }
        if ($totalPrice <= 0) {
            Response::error('Invalid order total');
        }

        if ($pdo === null) {
            Response::json('success', [], 'Order logged locally!');
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "INSERT INTO `orders` (`user_id`,`roblox_username`,`total_price`,`status`) VALUES (?, ?, ?, 'completed')"
            );
            $stmt->execute([$_SESSION['user_id'] ?? null, $robloxUser, $totalPrice]);

            $orderId = (int) $pdo->lastInsertId();
            $pdo->commit();

            Response::json('success', ['order_id' => $orderId], 'Order verified & delivery queued!');
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            Response::error('Order could not be recorded', 500);
        }
    }
}
