<?php
declare(strict_types=1);
// No session or credentials are returned by this endpoint.
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $path = __DIR__ . '/../config/config.local.php';
    if (!is_file($path)) {
        throw new RuntimeException('Configuration missing.');
    }
    $config = require $path;
    $db = $config['db'];
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $db['host'], (int) $db['port'], $db['name']), $db['user'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->query('SELECT id FROM dishes LIMIT 1');
    echo json_encode(['status' => 'ok']);
} catch (Throwable $exception) {
    error_log('Restaurant Menu: health check failed (' . get_class($exception) . ')');
    http_response_code(503);
    echo json_encode(['status' => 'unavailable']);
}
