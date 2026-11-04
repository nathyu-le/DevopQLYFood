<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
require_admin();
?>
<!doctype html><html lang="vi"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Demo trực tiếp Week 3 · Bếp Nhà</title>
<link rel="stylesheet" href="assets/operations.css?v=2"><link rel="stylesheet" href="assets/demo.css?v=1">
<script src="assets/demo.js?v=1" defer></script></head><body><main>
<header><div><a class="brand" href="index.php">BẾP NHÀ <span>DEMO WEEK 3</span></a><h1>Demo trực tiếp</h1><p>Tự bấm từng bước để kiểm tra backup, restore và phục hồi dịch vụ.</p></div><nav><a href="operations.php">Dashboard</a><a href="index.php">Thực đơn</a></nav></header>
<p class="scope">Cơ sở dữ liệu SQLite thử riêng trên hosting cPanel · Cảnh báo sau 2 mẫu lỗi · Phục hồi sau 2 mẫu tốt</p>
<div id="message" class="banner" role="status">Bắt đầu bằng nút 1 Tạo dữ liệu thử.</div>
<section class="metrics" aria-label="Tình trạng demo"><article><span class="eyebrow">DATABASE DEMO</span><strong id="demo-db">Chưa chuẩn bị</strong><small id="demo-time">Đang kiểm tra</small></article><article><span class="eyebrow">HEALTH HTTP THỰC TẾ</span><strong id="demo-http">—</strong><small>Phản hồi từ dịch vụ demo</small></article><article><span class="eyebrow">CẢNH BÁO</span><strong id="demo-alert">—</strong><small id="demo-counts">Cập nhật mỗi 5 giây</small></article><article><span class="eyebrow">DỮ LIỆU SAU RESTORE</span><strong id="demo-match">Chưa kiểm tra</strong><small>Đối chiếu toàn bộ trường với backup</small></article></section>
<section class="demo-panel"><h2>1 Backup và phục hồi dữ liệu</h2><p>Đi lần lượt từ trái sang phải. Bảng bên dưới hiển thị dữ liệu đang có trong database demo.</p><div class="actions"><button data-action="prepare" id="prepare">1 Tạo dữ liệu thử</button><button data-action="backup" id="backup">2 Backup</button><button data-action="delete" id="delete" class="danger">3 Xóa dữ liệu thử</button><button data-action="restore" id="restore">4 Restore</button><button data-action="download" id="download" class="button-secondary">Tải file backup</button></div>
<p id="demo-backup">Chưa có backup của lượt demo này.</p><div class="table-wrap"><table><thead><tr><th>ID</th><th>Tên món thử</th><th>Giá</th><th>Trạng thái</th></tr></thead><tbody id="demo-rows"></tbody></table></div></section>
<section class="demo-panel"><h2>2 Tạo lỗi và phục hồi kết nối</h2><p>Bấm Tạo lỗi kết nối, chờ cảnh báo FIRING. Sau đó bấm Phục hồi kết nối và theo dõi trạng thái về OK.</p><div class="actions"><button data-action="disconnect" id="disconnect" class="danger">5 Tạo lỗi kết nối</button><button data-action="reconnect" id="reconnect">6 Phục hồi kết nối</button><a href="demo-health.php" target="_blank" rel="noopener">Mở health demo</a></div><p class="chart-note">Thao tác ngắt đường dẫn tới database demo. Kết nối demo tự phục hồi sau 3 phút nếu bỏ quên.</p></section>
<section class="demo-panel"><h2>Nhật ký demo trực tiếp</h2><div class="table-wrap"><table><thead><tr><th>Thời gian</th><th>Sự kiện</th><th>Kết quả</th></tr></thead><tbody id="demo-events"></tbody></table></div></section>
<input type="hidden" id="demo-csrf" value="<?= e(csrf_token()) ?>"><footer><span>Demo dữ liệu thử trên cPanel</span><span>Đã đăng nhập quản trị</span></footer>
</main></body></html>
