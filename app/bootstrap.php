<?php
declare(strict_types=1);

const APP_ROOT = __DIR__ . '/..';
const CATEGORIES = [
    'main' => 'Món chính',
    'starter' => 'Khai vị',
    'drink' => 'Đồ uống',
    'dessert' => 'Tráng miệng',
];

ini_set('display_errors', '0');
ini_set('log_errors', '1');
date_default_timezone_set('Asia/Ho_Chi_Minh');

set_exception_handler(static function (Throwable $exception): void {
    // Do not log exception messages: they may contain connection details or user data.
    error_log('Restaurant Menu: unhandled ' . get_class($exception));
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="vi"><meta charset="utf-8"><title>Tạm thời gián đoạn</title>';
    echo '<h1>Chưa thể xử lý yêu cầu</h1><p>Vui lòng thử lại hoặc liên hệ người quản trị.</p></html>';
});

function app_config(): array
{
    static $config = null;
    if ($config === null) {
        $path = APP_ROOT . '/config/config.local.php';
        if (!is_file($path)) {
            http_response_code(503);
            header('Content-Type: text/html; charset=utf-8');
            echo '<!doctype html><html lang="vi"><meta charset="utf-8"><title>Cần cấu hình</title>';
            echo '<h1>Ứng dụng chưa được cấu hình</h1><p>Người quản trị cần hoàn thành các bước trong README trước khi sử dụng.</p></html>';
            exit;
        }
        $config = require $path;
        if (!is_array($config) || empty($config['db']) || empty($config['admin']['password_hash'])) {
            throw new RuntimeException('Invalid application configuration.');
        }
    }
    return $config;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $config = app_config()['db'];
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'], (int) $config['port'], $config['name']);
        $pdo = new PDO($dsn, $config['user'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('restaurant_menu_session');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; object-src 'none'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function require_admin(): void
{
    start_session();
    app_config();
    if (empty($_SESSION['admin'])) {
        redirect('login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void
{
    $provided = $_POST['csrf'] ?? null;
    if (!is_string($provided) || !hash_equals(csrf_token(), $provided)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        exit('Phiên làm việc không hợp lệ. Vui lòng tải lại trang.');
    }
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function text_input(array $source, string $key): string
{
    return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : '';
}

function positive_id(mixed $value): ?int
{
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
}

function validate_dish(array $input): array
{
    $values = [
        'name' => text_input($input, 'name'),
        'category' => text_input($input, 'category'),
        'price' => text_input($input, 'price'),
        'description' => text_input($input, 'description'),
        'is_available' => text_input($input, 'is_available'),
    ];
    $errors = [];
    if ($values['name'] === '' || mb_strlen($values['name']) > 160) {
        $errors['name'] = 'Tên món phải có từ 1 đến 160 ký tự.';
    }
    if (!array_key_exists($values['category'], CATEGORIES)) {
        $errors['category'] = 'Vui lòng chọn danh mục hợp lệ.';
    }
    if (!preg_match('/\A(?:0|[1-9][0-9]{0,8})\z/', $values['price'])) {
        $errors['price'] = 'Giá phải là số nguyên từ 0 đến 999.999.999 đồng.';
    }
    if (mb_strlen($values['description']) > 1000) {
        $errors['description'] = 'Mô tả tối đa 1.000 ký tự.';
    }
    if (!in_array($values['is_available'], ['0', '1'], true)) {
        $errors['is_available'] = 'Tình trạng phục vụ không hợp lệ.';
    }
    return [$values, $errors];
}

function money(mixed $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' đ';
}

function icon(string $name, string $class = ''): string
{
    $paths = [
        'menu' => '<path d="M4 5h16M4 12h16M4 19h16"/><circle cx="7" cy="5" r="1"/><circle cx="7" cy="12" r="1"/><circle cx="7" cy="19" r="1"/>',
        'leaf' => '<path d="M20 4C9 2 2 9 5 16c7 3 14-4 15-12Z"/><path d="m4 20 11-11"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/>',
        'edit' => '<path d="m15 5 4 4M4 20l5-1L20 8a2.8 2.8 0 0 0-4-4L5 15l-1 5Z"/>',
        'trash' => '<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13M10 11v5M14 11v5"/>',
        'arrow' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
        'logout' => '<path d="M10 4H4v16h6M9 12h11m-4-4 4 4-4 4"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'bowl' => '<path d="M3 11h18c0 5-4 8-9 8s-9-3-9-8ZM6 21h12M8 3v4M12 2v5M16 3v4"/>',
    ];
    return '<svg class="icon ' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}
