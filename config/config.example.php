<?php
declare(strict_types=1);

// Copy to config.local.php, or use: php tools/configure.php
// Keep config.local.php OUTSIDE the public document root and out of Git.
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'restaurant_menu',
        'user' => 'menu_app',
        'password' => '',
    ],
    'admin' => [
        'username' => 'admin',
        // Generated with password_hash(); never put a plain-text password here.
        'password_hash' => '',
    ],
];
