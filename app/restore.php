<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/backup_lib.php';
try {
    $options = getopt('', ['file:', 'confirm-db:']);
    if (!isset($options['file'], $options['confirm-db'])) { throw new RuntimeException('Missing confirmation.'); }
    echo json_encode(backup_restore((string) $options['file'], (string) $options['confirm-db']), JSON_THROW_ON_ERROR) . PHP_EOL;
} catch (Throwable $e) {
    ops_event('restore_rejected', ['error_class' => get_class($e)]);
    fwrite(STDERR, 'Restore refused or rolled back. Requires a valid snapshot, isolated *_test/*_ci DB and exact --confirm-db.' . PHP_EOL); exit(1);
}
