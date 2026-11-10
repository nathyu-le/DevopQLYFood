<?php
declare(strict_types=1);
// Read-only installation check. Do not print configuration values or exception messages.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$root = dirname(__DIR__);
$failed = false;
$check = static function (bool $ok, string $name) use (&$failed): void {
    echo ($ok ? 'PASS ' : 'FAIL ') . $name . PHP_EOL;
    $failed = $failed || !$ok;
};
$check(PHP_VERSION_ID >= 80200, 'PHP 8.2 or newer');
foreach (['pdo_mysql', 'pdo_sqlite', 'mbstring', 'session'] as $extension) {
    $check(extension_loaded($extension), 'Extension ' . $extension);
}
foreach (['app/bootstrap.php', 'public/health.php', 'public/demo.php'] as $relative) {
    $check(is_readable($root . '/' . $relative), 'Readable ' . $relative);
}
$configPath = $root . '/config/config.local.php';
$config = null;
try {
    if (is_readable($configPath)) {
        $config = require $configPath;
    }
} catch (Throwable $exception) {
    // The exception may contain private filesystem or connection information.
}
$valid = is_array($config) && isset($config['db'], $config['admin']['username'], $config['admin']['password_hash'])
    && is_array($config['db']) && is_string($config['admin']['password_hash'])
    && password_get_info($config['admin']['password_hash'])['algoName'] !== 'unknown';
$check($valid, 'Local configuration and admin password hash');
if ($valid && extension_loaded('pdo_mysql')) {
    try {
        $db = $config['db'];
        $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $db['host'], (int) $db['port'], $db['name']),
            $db['user'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->query('SELECT id, name, category, price, description, is_available, created_at, updated_at FROM dishes LIMIT 1');
        $check(true, 'Database connection and dishes schema');
    } catch (Throwable $exception) {
        $check(false, 'Database connection and dishes schema');
    }
}
$private = $root . '/var';
$check(is_dir($private) && is_writable($private), 'Private var directory writable by this PHP user');
if (is_dir($private)) {
    $check(((int) fileperms($private) & 0007) === 0, 'Private var has no world permissions');
}
echo 'No secrets displayed. No data or configuration changed. Check HTTP access controls separately.' . PHP_EOL;
exit($failed ? 1 : 0);
