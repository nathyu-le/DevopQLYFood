<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
require_admin();
$revision = is_readable(__DIR__ . '/../REVISION') ? trim((string) file_get_contents(__DIR__ . '/../REVISION')) : 'manual';
if (!preg_match('/\A[0-9a-f]{40}\z/', $revision)) { $revision = 'manual'; }
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Bảo vệ Week 4 · Bếp Nhà</title><link rel="stylesheet" href="assets/operations.css?v=2"></head><body><main>
<header><div><a class="brand" href="index.php">BẾP NHÀ <span>WEEK 4</span></a><h1>Bảo vệ cuối kỳ</h1><p id="release-note">Mốc trình bày Week 4: lần 2</p></div><nav><a href="index.php">Thực đơn</a><a href="operations.php">Dashboard</a><a href="demo.php">Demo trực tiếp Week 3</a></nav></header>
<section class="details"><article><h2>1 Cập nhật và triển khai</h2><p>Đổi dòng mốc trình bày trong source của trang Week 4, commit lên main và theo dõi ba job. Sau khi deploy thành công, tải lại trang và đối chiếu revision.</p><p><a href="https://github.com/nathyu-le/DevopQLYFood/actions/workflows/container-pipeline.yml" target="_blank" rel="noopener">Mở pipeline</a> · <a href="version.php" target="_blank" rel="noopener">Xem revision</a> · <a href="health.php" target="_blank" rel="noopener">Health chính</a></p></article><article><h2>2 Theo dõi và phục hồi</h2><p>Mở Dashboard để xem sức khỏe MariaDB chính và nhật ký. Trang demo Week 3 được giữ nguyên, dùng SQLite thử riêng cho backup, xóa, restore và lỗi kết nối.</p><p><a href="operations.php">Mở Dashboard</a> · <a href="demo.php">Mở demo dữ liệu thử</a></p></article></section>
<section class="log-section"><h2>3 Xử lý sự cố và bàn giao</h2><p>Xác định phạm vi lỗi, đối chiếu health và log, tìm nguyên nhân, khắc phục rồi kiểm tra dữ liệu. Kết thúc bài thử ở HTTP 200 và cảnh báo OK.</p><p><a href="https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/TROUBLESHOOTING.md" target="_blank" rel="noopener">Playbook sự cố</a> · <a href="https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/FINAL_DEPLOYMENT.md" target="_blank" rel="noopener">Hướng dẫn cài mới</a></p></section>
<footer><span>Revision: <?= e($revision) ?></span><span>Trang Week 4 riêng · Giữ nguyên demo Week 3</span></footer>
</main></body></html>
