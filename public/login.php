<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
start_session();
$config = app_config();
if (!empty($_SESSION['admin'])) {
    redirect('index.php');
}
$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = text_input($_POST, 'username');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    if (hash_equals((string) $config['admin']['username'], $username)
        && password_verify($password, (string) $config['admin']['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $username;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        redirect('index.php');
    }
    $error = 'Tên đăng nhập hoặc mật khẩu chưa đúng.';
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập · Bếp Nhà</title><link rel="stylesheet" href="assets/app.css">
</head>
<body class="login-page">
<main class="login-shell">
    <section class="login-story">
        <a class="brand brand-light" href="login.php"><?= icon('leaf') ?><span>Bếp Nhà<span class="brand-sub">MENU MANAGER</span></span></a>
        <div><span class="eyebrow">MỖI MÓN ĂN, MỘT CÂU CHUYỆN</span><h1>Thực đơn gọn gàng.<br>Bếp luôn sẵn sàng.</h1><p>Một nơi để chăm chút món ăn, cập nhật giá và giữ thực đơn của quán luôn mới.</p></div>
        <span class="story-foot">Quản lý thực đơn quán ăn</span>
    </section>
    <section class="login-form-panel">
        <span class="eyebrow">CHÀO MỪNG BẠN </span><h2>Vào bếp thôi.</h2><p class="muted">Đăng nhập để cập nhật món, giá và tình trạng phục vụ.</p>
        <?php if ($error): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?>
        <form method="post" class="stack">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <label>Tên đăng nhập<input name="username" required maxlength="100" autocomplete="username" value="<?= e($username) ?>" autofocus></label>
            <label>Mật khẩu<input type="password" name="password" required autocomplete="current-password"></label>
            <button class="button button-primary button-wide" type="submit">Đăng nhập <?= icon('arrow') ?></button>
        </form>
        <p class="login-note">Tài khoản do người quản trị dự án cấu hình.</p>
    </section>
</main>
</body></html>
