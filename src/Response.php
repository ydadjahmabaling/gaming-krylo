<?php
// Uniform JSON API responses.

declare(strict_types=1);

final class Response
{
    public static function json(string $status, array $data = [], string $message = '', int $httpCode = 200): never
    {
        http_response_code($httpCode);
        echo json_encode(['status' => $status, 'data' => $data, 'message' => $message], JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error(string $message, int $httpCode = 400, array $data = []): never
    {
        self::json('error', $data, $message, $httpCode);
    }
}
