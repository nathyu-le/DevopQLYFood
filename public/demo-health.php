<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
start_session();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['admin'])) { http_response_code(401); echo '{"error":"authentication_required"}'; exit; }
session_write_close();
require_once __DIR__ . '/../app/demo_lib.php';
try {
    $data = demo_state();
    $healthy = $data['initialized'] && $data['probe']['health'] === 'ok';
    http_response_code($healthy ? 200 : 503);
    echo json_encode(['scope' => 'isolated_demo', 'status' => $healthy ? 'ok' : 'unavailable']);
} catch (Throwable) { http_response_code(503); echo '{"scope":"isolated_demo","status":"unavailable"}'; }
