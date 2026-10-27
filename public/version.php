<?php
declare(strict_types=1);
// Operational version check. No credentials or filesystem paths are returned.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$path = __DIR__ . '/../REVISION';
$revision = is_file($path) ? trim((string) file_get_contents($path)) : 'manual';
echo json_encode(['revision' => $revision]);
