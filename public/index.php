<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
require_admin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = text_input($_POST, 'action');
    $id = positive_id($_POST['id'] ?? null);
    if (!in_array($action, ['create', 'update', 'toggle', 'delete'], true)) {
        http_response_code(400);
        exit('Thao tác không hợp lệ.');
    }
    if ($action !== 'create') {
        if ($id === null) {
            http_response_code(400);
            exit('Mã món không hợp lệ.');
        }
        $existing = $pdo->prepare('SELECT id FROM dishes WHERE id = ?');
        $existing->execute([$id]);
        if (!$existing->fetch()) {
            http_response_code(404);
            exit('Không tìm thấy món ăn.');
        }
    }
    if ($action === 'create' || $action === 'update') {
        [$values, $errors] = validate_dish($_POST);
        if ($errors) {
            $_SESSION['form'] = ['values' => $values, 'errors' => $errors];
            redirect($action === 'update' ? 'index.php?edit=' . $id . '#dish-form' : 'index.php#dish-form');
        }
        $params = [$values['name'], $values['category'], $values['price'], $values['description'], (int) $values['is_available']];
        if ($action === 'create') {
            $statement = $pdo->prepare('INSERT INTO dishes (name, category, price, description, is_available) VALUES (?, ?, ?, ?, ?)');
            $statement->execute($params);
            $_SESSION['flash'] = 'Đã thêm món mới vào thực đơn.';
        } else {
            $statement = $pdo->prepare('UPDATE dishes SET name = ?, category = ?, price = ?, description = ?, is_available = ? WHERE id = ?');
            $statement->execute([...$params, $id]);
            $_SESSION['flash'] = 'Đã lưu thay đổi của món ăn.';
        }
    } elseif ($action === 'toggle') {
        $statement = $pdo->prepare('UPDATE dishes SET is_available = 1 - is_available WHERE id = ?');
        $statement->execute([$id]);
        $_SESSION['flash'] = 'Đã cập nhật tình trạng phục vụ.';
    } else {
        $statement = $pdo->prepare('DELETE FROM dishes WHERE id = ?');
        $statement->execute([$id]);
        $_SESSION['flash'] = 'Đã xóa món khỏi thực đơn.';
    }
    redirect('index.php');
}

$search = mb_substr(text_input($_GET, 'q'), 0, 160);
$category = text_input($_GET, 'category');
$status = text_input($_GET, 'status');
$where = [];
$params = [];
if ($search !== '') {
    $where[] = '(name LIKE ? OR description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if (array_key_exists($category, CATEGORIES)) {
    $where[] = 'category = ?';
    $params[] = $category;
}
if (in_array($status, ['0', '1'], true)) {
    $where[] = 'is_available = ?';
    $params[] = (int) $status;
}
$statement = $pdo->prepare('SELECT * FROM dishes' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC');
$statement->execute($params);
$dishes = $statement->fetchAll();
$stats = $pdo->query('SELECT COUNT(*) AS total, COALESCE(SUM(is_available), 0) AS available FROM dishes')->fetch();
$editing = null;
$editId = positive_id($_GET['edit'] ?? null);
if ($editId !== null) {
    $statement = $pdo->prepare('SELECT * FROM dishes WHERE id = ?');
    $statement->execute([$editId]);
    $editing = $statement->fetch();
    if (!$editing) {
        http_response_code(404);
        exit('Không tìm thấy món cần sửa.');
    }
}
$form = $_SESSION['form'] ?? null;
unset($_SESSION['form']);
$values = $form['values'] ?? $editing ?? ['name' => '', 'category' => 'main', 'price' => '', 'description' => '', 'is_available' => '1'];
$errors = $form['errors'] ?? [];
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Thực đơn · Bếp Nhà</title><link rel="stylesheet" href="assets/app.css"><script src="assets/app.js" defer></script></head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="index.php"><?= icon('leaf') ?><span>Bếp Nhà<span class="brand-sub">MENU MANAGER</span></span></a>
        <span class="nav-label">KHÔNG GIAN QUẢN LÝ</span>
        <a class="nav-item active" href="index.php" aria-current="page"><?= icon('menu') ?> Thực đơn</a>
        <div class="sidebar-note"><?= icon('bowl') ?><strong>Món ngon bắt đầu từ<br>một thực đơn chỉn chu.</strong><p>Cập nhật hôm nay.<br>Sẵn sàng phục vụ.</p></div>
        <form method="post" action="logout.php" class="logout-form"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button class="nav-item" type="submit"><?= icon('logout') ?> Đăng xuất</button></form>
    </aside>
    <main class="content">
        <header class="topbar"><span>Không gian của quán <span class="crumb">/ Thực đơn</span></span><span class="account"><span class="avatar"><?= e(mb_strtoupper(mb_substr($_SESSION['admin'], 0, 1))) ?></span><?= e($_SESSION['admin']) ?></span></header>
        <section class="page-heading"><div><span class="eyebrow">THỰC ĐƠN CỦA QUÁN</span><h1>Mỗi món ngon, một lựa chọn.</h1><p class="muted">Quản lý món ăn, giá bán và tình trạng phục vụ trong một nơi.</p></div><a href="#dish-form" class="button button-primary"><?= icon('plus') ?> Thêm món mới</a></section>
        <?php if ($flash): ?><div class="notice notice-success" role="status"><?= icon('check') ?><?= e($flash) ?></div><?php endif; ?>
        <section class="stats" aria-label="Tổng quan thực đơn">
            <div class="stat"><span class="stat-label">Tổng số món</span><strong><?= (int) $stats['total'] ?><small>món trong thực đơn</small></strong><span class="stat-symbol"><?= icon('menu') ?></span></div>
            <div class="stat"><span class="stat-label">Sẵn sàng phục vụ</span><strong><?= (int) $stats['available'] ?><small>món đang còn</small></strong><span class="stat-symbol green"><?= icon('check') ?></span></div>
            <div class="stat"><span class="stat-label">Tạm ngưng phục vụ</span><strong><?= (int) $stats['total'] - (int) $stats['available'] ?><small>món đang hết</small></strong><span class="stat-symbol amber"><?= icon('bowl') ?></span></div>
        </section>
        <div class="workspace">
            <section class="panel menu-panel">
                <div class="panel-heading"><div><h2>Danh sách món</h2><p><?= count($dishes) ?> món theo bộ lọc hiện tại</p></div><span class="soft-label">Thực đơn</span></div>
                <form method="get" class="filters" role="search">
                    <label class="search-box"><span class="sr-only">Tìm món ăn</span><?= icon('search') ?><input name="q" placeholder="Tìm tên món hoặc mô tả…" value="<?= e($search) ?>" maxlength="160"></label>
                    <label><span class="sr-only">Danh mục</span><select name="category"><option value="">Tất cả danh mục</option><?php foreach (CATEGORIES as $key => $label): ?><option value="<?= e($key) ?>" <?= $category === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
                    <label><span class="sr-only">Tình trạng</span><select name="status"><option value="">Tất cả trạng thái</option><option value="1" <?= $status === '1' ? 'selected' : '' ?>>Còn món</option><option value="0" <?= $status === '0' ? 'selected' : '' ?>>Hết món</option></select></label>
                    <button class="button button-light" type="submit">Lọc</button>
                    <?php if ($search !== '' || $category !== '' || $status !== ''): ?><a class="clear-filter" href="index.php">Bỏ lọc</a><?php endif; ?>
                </form>
                <?php if (!$dishes): ?><div class="empty-state"><?= icon('bowl') ?><h3><?= $where ? 'Chưa tìm thấy món phù hợp' : 'Thực đơn đang chờ món đầu tiên' ?></h3><p><?= $where ? 'Thử đổi từ khóa hoặc bỏ bộ lọc để xem lại thực đơn.' : 'Thêm một món mới bằng biểu mẫu bên cạnh để bắt đầu.' ?></p></div>
                <?php else: ?>
                <div class="table-scroll"><table><thead><tr><th>Món ăn</th><th>Giá bán</th><th>Tình trạng</th><th><span class="sr-only">Thao tác</span></th></tr></thead><tbody>
                <?php foreach ($dishes as $dish): ?>
                    <tr data-dish-id="<?= (int) $dish['id'] ?>">
                        <td><div class="dish-cell"><span class="dish-icon category-<?= e(array_key_exists($dish['category'], CATEGORIES) ? $dish['category'] : 'main') ?>"><?= icon('bowl') ?></span><div><strong class="dish-name"><?= e($dish['name']) ?></strong><span class="dish-category"><?= e(CATEGORIES[$dish['category']] ?? $dish['category']) ?> · #<?= (int) $dish['id'] ?></span><?php if ($dish['description'] !== ''): ?><span class="dish-description"><?= e($dish['description']) ?></span><?php endif; ?></div></div></td>
                        <td class="price"><?= e(money($dish['price'])) ?></td>
                        <td><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $dish['id'] ?>"><button type="submit" class="status-pill <?= $dish['is_available'] ? 'available' : 'unavailable' ?>" aria-label="Đổi tình trạng món <?= e($dish['name']) ?>"><span class="status-dot"></span><?= $dish['is_available'] ? 'Còn món' : 'Hết món' ?></button></form></td>
                        <td><div class="row-actions"><a class="icon-button" href="?edit=<?= (int) $dish['id'] ?>#dish-form" aria-label="Sửa món <?= e($dish['name']) ?>" title="Sửa món"><?= icon('edit') ?></a><form method="post" data-confirm="Xóa món này khỏi thực đơn? Thao tác này không thể hoàn tác."><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $dish['id'] ?>"><button class="icon-button danger" type="submit" aria-label="Xóa món <?= e($dish['name']) ?>" title="Xóa món"><?= icon('trash') ?></button></form></div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody></table></div>
                <?php endif; ?>
                <div class="table-foot">Chạm vào trạng thái để đổi giữa còn món và hết món.</div>
            </section>
            <section class="panel form-panel" id="dish-form" aria-labelledby="form-title">
                <div class="form-title-icon"><?= icon($editing ? 'edit' : 'plus') ?></div><span class="eyebrow"><?= $editing ? 'CẬP NHẬT THỰC ĐƠN' : 'THÊM CHÚT HƯƠNG VỊ' ?></span><h2 id="form-title"><?= $editing ? 'Chỉnh sửa món ăn' : 'Thêm món mới' ?></h2><p class="muted">Điền thông tin để <?= $editing ? 'cập nhật món trong' : 'thêm món vào' ?> thực đơn.</p>
                <?php if ($errors): ?><div class="notice notice-error" role="alert"><strong>Chưa lưu được món ăn.</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <form method="post" class="stack dish-form">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>"><?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>"><?php endif; ?>
                    <label>Tên món <span class="required">*</span><input name="name" required maxlength="160" placeholder="Ví dụ: Cơm gà Hội An" value="<?= e($values['name']) ?>" <?= isset($errors['name']) ? 'aria-invalid="true"' : '' ?>></label>
                    <label>Danh mục <span class="required">*</span><select name="category" required><?php foreach (CATEGORIES as $key => $label): ?><option value="<?= e($key) ?>" <?= $values['category'] === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
                    <label>Giá bán (VNĐ) <span class="required">*</span><input type="number" name="price" min="0" max="999999999" step="1" required placeholder="45000" value="<?= e($values['price']) ?>" <?= isset($errors['price']) ? 'aria-invalid="true"' : '' ?>><span class="field-hint">Nhập số nguyên, không có dấu chấm hoặc dấu phẩy.</span></label>
                    <label>Tình trạng phục vụ<select name="is_available"><option value="1" <?= (string) $values['is_available'] === '1' ? 'selected' : '' ?>>Còn món</option><option value="0" <?= (string) $values['is_available'] === '0' ? 'selected' : '' ?>>Hết món</option></select></label>
                    <label>Mô tả món<textarea name="description" maxlength="1000" rows="3" placeholder="Nguyên liệu, hương vị hoặc cách phục vụ…"><?= e($values['description']) ?></textarea></label>
                    <button class="button button-primary button-wide" type="submit"><?= icon($editing ? 'check' : 'plus') ?><?= $editing ? 'Lưu thay đổi' : 'Thêm vào thực đơn' ?></button>
                    <?php if ($editing): ?><a class="cancel-link" href="index.php#dish-form">Hủy chỉnh sửa</a><?php endif; ?>
                </form>
            </section>
        </div>
        <footer class="page-footer"><span>Bếp Nhà · Quản lý thực đơn</span><span>Gọn gàng hơn, phục vụ tốt hơn.</span></footer>
    </main>
</div>
</body></html>
