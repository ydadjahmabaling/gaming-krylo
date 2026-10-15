<?php
// PDO connection singleton + automatic schema creation/seeding.

declare(strict_types=1);

final class Database
{
    private static ?PDO $pdo = null;

    /**
     * Returns a shared PDO connection, or null when no MySQL daemon is
     * reachable (preview/demo environments fall back gracefully).
     */
    public static function connection(): ?PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        try {
            $dsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                DB_NAME
            ));
            $pdo->exec(sprintf('USE `%s`', DB_NAME));

            self::initializeSchema($pdo);
            self::seedDefaults($pdo);

            return self::$pdo = $pdo;
        } catch (PDOException $e) {
            // No live MySQL daemon - the API layer handles the fallback.
            return null;
        }
    }

    private static function initializeSchema(PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `password_hash` VARCHAR(255) NOT NULL,
            `role` ENUM('user','admin') DEFAULT 'user',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
            `id` VARCHAR(64) PRIMARY KEY,
            `title` VARCHAR(120) NOT NULL,
            `category` VARCHAR(50) NOT NULL,
            `price` DECIMAL(10,2) NOT NULL,
            `rarity` VARCHAR(30) NOT NULL,
            `stock` INT NOT NULL DEFAULT 0,
            `image_url` TEXT,
            `description` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `roblox_username` VARCHAR(100) NOT NULL,
            `total_price` DECIMAL(10,2) NOT NULL,
            `status` ENUM('pending','completed','canceled') DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB");
    }

    private static function seedDefaults(PDO $pdo): void
    {
        if ((int) $pdo->query('SELECT COUNT(*) FROM `products`')->fetchColumn() === 0) {
            $products = [
                ['p1', 'Kitsune Fruit', 'Blox Fruits', 18.99, 'Mythical', 12, 'https://placehold.co/300x200/0f172a/3b82f6?text=KITSUNE+FRUIT', 'Physical Kitsune fruit in Blox Fruits. High trade demand, rapid speed & burn attacks.'],
                ['p2', 'Harvester Scythe', 'MM2', 24.50, 'Godly', 5, 'https://placehold.co/300x200/0f172a/3b82f6?text=HARVESTER+MM2', 'Ancient Godly Scythe weapon in Murder Mystery 2. High trade value verified clean.'],
                ['p3', 'Titanic Dragon Pet', 'Pet Sim 99', 49.99, 'Exclusive', 3, 'https://placehold.co/300x200/0f172a/3b82f6?text=TITANIC+DRAGON', 'Exclusive Titanic companion pet in Pet Simulator 99 with rideable abilities.'],
                ['p4', 'Shadow Dragon (FR)', 'Adopt Me', 35.00, 'Mythical', 4, 'https://placehold.co/300x200/0f172a/3b82f6?text=SHADOW+DRAGON', 'Fly Ride Shadow Dragon pet from Halloween 2019 in Adopt Me.'],
                ['p5', 'Dough Fruit (Permanent)', 'Blox Fruits', 29.99, 'Mythical', 8, 'https://placehold.co/300x200/0f172a/3b82f6?text=PERM+DOUGH', 'Permanent Dough Fruit gift link code directly sent to your Roblox Account.'],
                ['p6', 'Icebreaker Hammer', 'MM2', 12.00, 'Godly', 15, 'https://placehold.co/300x200/0f172a/3b82f6?text=ICEBREAKER+MM2', 'Icebreaker Godly weapon from MM2 Christmas collection.'],
            ];

            $stmt = $pdo->prepare(
                'INSERT INTO `products` (`id`,`title`,`category`,`price`,`rarity`,`stock`,`image_url`,`description`)
                 VALUES (?,?,?,?,?,?,?,?)'
            );
            foreach ($products as $p) {
                $stmt->execute($p);
            }
        }

        if ((int) $pdo->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'admin'")->fetchColumn() === 0) {
            $stmt = $pdo->prepare(
                'INSERT INTO `users` (`username`,`email`,`password_hash`,`role`) VALUES (?,?,?,\'admin\')'
            );
            $stmt->execute([ADMIN_USERNAME, ADMIN_EMAIL, password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT)]);
        }
    }
}
