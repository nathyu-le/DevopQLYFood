<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
start_session();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['admin'])) { http_response_code(401); echo '{"error":"authentication_required"}'; exit; }
app_config();
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf();
        if (text_input($_POST, 'action') !== 'backup') { http_response_code(400); echo '{"error":"invalid_action"}'; exit; }
        if (time() - (int) ($_SESSION['last_backup_at'] ?? 0) < 60) { http_response_code(429); echo '{"error":"backup_cooldown"}'; exit; }
        require_once __DIR__ . '/../app/backup_lib.php';
        $summary = backup_create();
        $_SESSION['last_backup_at'] = time();
        echo json_encode(['backup' => $summary], JSON_THROW_ON_ERROR); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); echo '{"error":"method_not_allowed"}'; exit; }
    session_write_close();
    echo json_encode(ops_public_state(ops_collect()), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    ops_event('operations_failed', ['error_class' => get_class($e)]);
    http_response_code(503); echo '{"error":"operations_unavailable"}';
}
