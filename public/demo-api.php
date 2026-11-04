<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
start_session();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['admin'])) { http_response_code(401); echo '{"error":"authentication_required"}'; exit; }
app_config();
require_once __DIR__ . '/../app/demo_lib.php';
try {
    $action = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf();
        $action = text_input($_POST, 'action');
        if (!in_array($action, ['prepare','backup','delete','restore','disconnect','reconnect','download'], true)) {
            http_response_code(400); echo '{"error":"invalid_action"}'; exit;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); echo '{"error":"method_not_allowed"}'; exit;
    }
    session_write_close();
    $data = demo_state($action);
    if ($action === 'download') {
        header('Content-Disposition: attachment; filename="week3-demo-backup.json"');
        header('X-Content-Type-Options: nosniff');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (DemoActionError $e) {
    http_response_code(409); echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    ops_event('demo_operation_failed', ['error_class' => get_class($e)]);
    http_response_code(503); echo '{"error":"Chưa thực hiện được thao tác demo. Kiểm tra cấu hình hosting."}';
}
