<?php
declare(strict_types=1);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/../app/operations.php';
ops_register_request();
$health = ops_database_health();
try { ops_collect($health); }
catch (Throwable) { error_log('Restaurant Menu: collector storage unavailable'); }
if ($health['health'] !== 'ok') { http_response_code(503); }
echo json_encode(['status' => $health['health']]);
