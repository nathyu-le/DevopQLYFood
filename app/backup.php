<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/backup_lib.php';
try {
    $options = getopt('', ['keep::']);
    echo json_encode(backup_create((int) ($options['keep'] ?? 14)), JSON_THROW_ON_ERROR) . PHP_EOL;
} catch (Throwable $e) {
    ops_event('backup_failed', ['error_class' => get_class($e)]);
    fwrite(STDERR, 'Backup failed; check private configuration and storage permissions. No credentials are printed.' . PHP_EOL); exit(1);
}
