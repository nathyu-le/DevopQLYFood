<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
require_admin();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Vận hành · Bếp Nhà</title>
    <link rel="stylesheet" href="assets/operations.css">
    <script src="assets/operations.js" defer></script>
</head>
<body>
<main>
    <header>
        <div><a class="brand" href="index.php">BẾP NHÀ <span>VẬN HÀNH</span></a><h1>Sức khỏe hệ thống</h1><p>Theo dõi tài nguyên, database và sự kiện của ứng dụng.</p></div>
        <nav><a href="index.php">Thực đơn</a><form action="logout.php" method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button class="logout" type="submit">Đăng xuất</button></form></nav>
    </header>
    <section class="status-line"><span class="live-dot"></span><strong>Cập nhật mỗi 5 giây khi mở trang</strong><span id="updated">Đang lấy dữ liệu…</span></section>
    <div id="alert-banner" class="banner" role="status">Đang kiểm tra các cảnh báo.</div>
    <section class="metrics" aria-label="Chỉ số hiện tại">
        <article><span class="eyebrow">CPU</span><strong id="cpu">—</strong><small id="cpu-source">Đang xác định nguồn đo</small></article>
        <article><span class="eyebrow">RAM</span><strong id="memory">—</strong><small id="memory-source">Đang xác định phạm vi</small></article>
        <article><span class="eyebrow">DATABASE</span><strong id="health">—</strong><small>Kết nối và đọc bảng dishes</small></article>
        <article><span class="eyebrow">ĐỘ TRỄ DB</span><strong id="latency">—</strong><small>Thời gian probe thực tế</small></article>
    </section>
    <section class="charts">
        <article><div class="section-head"><h2>CPU theo thời gian</h2><span>0–100%</span></div><svg id="cpu-chart" viewBox="0 0 600 150" role="img" aria-label="Biểu đồ CPU"></svg><p class="chart-note">60 mẫu gần nhất · khoảng trống là chỉ số chưa có</p></article>
        <article><div class="section-head"><h2>RAM theo thời gian</h2><span>% giới hạn nguồn đo</span></div><svg id="memory-chart" viewBox="0 0 600 150" role="img" aria-label="Biểu đồ RAM"></svg><p class="chart-note">Không suy ra quota cPanel từ số liệu hệ điều hành dùng chung</p></article>
    </section>
    <section class="details">
        <article><div class="section-head"><h2>Cảnh báo</h2><span id="alert-state">—</span></div><div id="alerts"></div><p class="chart-note">Firing sau 2 mẫu lỗi liên tiếp; phục hồi sau 2 mẫu tốt. CPU/RAM: ngưỡng 85%.</p></article>
        <article><div class="section-head"><h2>Backup gần nhất</h2><button id="backup-button" type="button">Tạo backup</button></div><p id="backup-status">Chưa lấy thông tin backup.</p><p class="chart-note">Lưu riêng với quyền hạn chế; giữ 14 snapshot mới nhất. Restore chỉ chạy bằng CLI trên DB thử có xác nhận.</p><input type="hidden" id="ops-csrf" value="<?= e(csrf_token()) ?>"></article>
    </section>
    <section class="log-section"><div class="section-head"><h2>Nhật ký ứng dụng</h2><div class="filters"><input id="log-filter" aria-label="Lọc sự kiện" placeholder="Lọc sự kiện hoặc request ID"><input id="log-since" type="datetime-local" aria-label="Xem log từ thời điểm"></div></div><div class="table-wrap"><table><thead><tr><th>Thời gian</th><th>Sự kiện</th><th>Chi tiết</th></tr></thead><tbody id="logs"></tbody></table></div><p class="chart-note">100 dòng mới nhất; thời gian hiển thị Việt Nam. Không ghi mật khẩu, nội dung form hoặc query string.</p></section>
    <footer><span id="revision">Revision: —</span><span>Ops Monitor · Week 3</span></footer>
</main>
</body></html>
