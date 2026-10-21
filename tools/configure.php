<?php
declare(strict_types=1);
// CLI only. Credentials are written outside public/ and never printed.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
function ask(string $question, string $default = '', bool $secret = false): string
{
    echo $question . ($default !== '' ? ' [' . $default . ']' : '') . ': ';
    $canHide = $secret && function_exists('shell_exec') && function_exists('stream_isatty') && stream_isatty(STDIN);
    if ($canHide) {
        shell_exec('stty -echo');
    }
    try {
        $line = fgets(STDIN);
    } finally {
        if ($canHide) {
            shell_exec('stty echo');
            echo PHP_EOL;
        }
    }
    if ($line === false) {
        throw new RuntimeException('Không đọc được dữ liệu nhập.');
    }
    $value = rtrim($line, "\r\n");
    return $value !== '' ? $value : $default;
}
try {
    $path = __DIR__ . '/../config/config.local.php';
    if (file_exists($path)) {
        throw new RuntimeException('config.local.php đã tồn tại. Script không ghi đè cấu hình.');
    }
    if (!extension_loaded('pdo_mysql') || !extension_loaded('mbstring')) {
        throw new RuntimeException('Cần cài phần mở rộng pdo_mysql và mbstring.');
    }
    echo "CẤU HÌNH BẾP NHÀ — thông tin chỉ lưu trên máy này.\n";
    $config = [
        'db' => [
            'host' => ask('DB host', '127.0.0.1'),
            'port' => (int) ask('DB port', '3306'),
            'name' => ask('DB name', 'restaurant_menu'),
            'user' => ask('DB user', 'menu_app'),
            'password' => ask('DB password', '', true),
        ],
        'admin' => ['username' => ask('Tên đăng nhập quản trị', 'admin')],
    ];
    $password = ask('Mật khẩu quản trị (ít nhất 12 ký tự)', '', true);
    $confirm = ask('Nhập lại mật khẩu quản trị', '', true);
    if (strlen($password) < 12 || strlen($password) > 72 || $password !== $confirm) {
        throw new RuntimeException('Mật khẩu cần từ 12 đến 72 byte và hai lần nhập phải khớp.');
    }
    $config['admin']['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    $db = $config['db'];
    $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['name']), $db['user'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->query('SELECT id FROM dishes LIMIT 1');
    umask(0077);
    $file = fopen($path, 'x');
    if (!$file) {
        throw new RuntimeException('Không tạo được file cấu hình.');
    }
    try {
        $content = "<?php\ndeclare(strict_types=1);\nreturn " . var_export($config, true) . ";\n";
        if (fwrite($file, $content) !== strlen($content)) {
            throw new RuntimeException('Không ghi đủ file cấu hình.');
        }
    } finally {
        fclose($file);
    }
    echo "Đã kiểm tra DB và tạo config/config.local.php (không đưa file này lên Git).\n";
} catch (Throwable $exception) {
    // PDO error text can include connection details, so do not print it.
    $message = $exception instanceof PDOException ? 'Không kết nối được DB hoặc chưa import schema.sql. Kiểm tra cấu hình rồi chạy lại.' : $exception->getMessage();
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}
