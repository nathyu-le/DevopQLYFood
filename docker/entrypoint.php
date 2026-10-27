<?php
declare(strict_types=1);

// Only the container entrypoint runs this file. No secret is printed.
function read_secret(string $variable): string
{
    $path = getenv($variable);
    $value = $path !== false && is_readable($path) ? file_get_contents($path) : false;
    if ($value === false || trim($value) === '') {
        throw new RuntimeException('A required container secret is missing.');
    }
    return rtrim($value, "\r\n");
}

try {
    $password = read_secret('ADMIN_PASSWORD_FILE');
    if (strlen($password) < 16) {
        throw new RuntimeException('The administrator password must have at least 16 characters.');
    }
    $config = [
        'db' => [
            'host' => getenv('DB_HOST') ?: 'db',
            'port' => (int) (getenv('DB_PORT') ?: 3306),
            'name' => getenv('DB_NAME') ?: 'restaurant_menu',
            'user' => getenv('DB_USER') ?: 'menu_app',
            'password' => read_secret('DB_PASSWORD_FILE'),
        ],
        'admin' => [
            'username' => getenv('ADMIN_USERNAME') ?: 'admin',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ],
    ];
    umask(0027);
    $path = '/var/www/html/config/config.local.php';
    if (file_put_contents($path, "<?php\nreturn " . var_export($config, true) . ";\n", LOCK_EX) === false
        || !chgrp($path, 'www-data') || !chmod($path, 0640)) {
        throw new RuntimeException('Unable to write the container configuration.');
    }
} catch (Throwable $exception) {
    fwrite(STDERR, "Container configuration failed. Check secret files and configuration permissions.\n");
    exit(1);
}
