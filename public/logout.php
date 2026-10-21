<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}
check_csrf();
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', [
    'expires' => time() - 42000, 'path' => $params['path'],
    'secure' => $params['secure'], 'httponly' => true, 'samesite' => 'Lax',
]);
session_destroy();
redirect('login.php');
