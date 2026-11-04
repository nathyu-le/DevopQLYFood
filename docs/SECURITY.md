# Security và ports Week 3

Ứng dụng live chạy PHP/DB trên cPanel; bài thực nghiệm Docker chạy ở staging tạm của GitHub Actions. Bảng này phân biệt kiểm soát của nhóm với cấu hình do nhà cung cấp hosting quản lý.

## Cổng và firewall

| Môi trường | Cổng và truy cập | Kiểm soát và minh chứng |
|---|---|---|
| Staging runner tạm | App chỉ bind 127.0.0.1:8080; không có port DB được publish | Compose config được kiểm tra tự động; health qua loopback |
| Network Compose | MariaDB 3306 chỉ dành cho app cùng network; không expose ra host | Không có ports trên service db |
| Staging OS firewall | Default deny incoming, allow outgoing, cho loopback, deny TCP 3306 | monitoring/check-firewall.sh cấu hình UFW trên runner riêng và lưu status verbose |
| cPanel live | Website HTTPS 443 theo URL public | Đã upload source thủ công; 7 HTTP checks qua: assets 200, metrics anonymous 401, dashboard về login và private paths 403. FTPS đã deploy thành công source a0e5914 trong run 41785933275; health/revision và 7 HTTP checks qua |
| cPanel OS firewall, SSH và DB network policy | Do nhà cung cấp shared hosting quản lý | Chưa có quyền root/ảnh policy được xác minh; không ghi UFW của runner thành firewall cPanel |

Script firewall chỉ cho chạy khi GITHUB_ACTIONS=true và Compose project `_ci`. Không chạy nó trong shared cPanel và không thay firewall của máy chủ nhiều khách hàng. Không cần thuê VPS cho luồng hiện tại.

Nếu giảng viên yêu cầu bằng chứng firewall/SSH ngay trên hạ tầng live, cần lấy thông tin policy/ports từ nhà cung cấp hoặc giao diện cPanel được cấp quyền. Bằng chứng đó chưa được suy ra từ ảnh cp1 cũ, việc bật Terminal, `.htaccess` hoặc UFW staging.

## Truy cập database

User app trong MariaDB staging được thu hồi toàn bộ quyền mặc định rồi chỉ GRANT SELECT, INSERT, UPDATE, DELETE trên đúng database. Script escape dấu gạch dưới để không biến tên DB thành wildcard. Test thực tế yêu cầu CREATE TABLE bị từ chối, không chỉ đọc cấu hình GRANT.

File init `docker/20-app-grants.sh` chỉ chạy khi volume DB mới được khởi tạo. Với volume đã có, áp dụng thay đổi quyền bằng tài khoản DB admin sau khi kiểm tra; không xóa volume để cập nhật permissions. Trên cPanel, gán bốn quyền CRUD cho user ứng dụng trong Manage My Databases; không mặc định gói mới đã làm đúng nếu chưa có ảnh/SHOW GRANTS được lược thông tin tài khoản.

## Secrets và HTTP

- GitHub Secrets giữ FTP_USER và FTP_PASSWORD. Variables giữ tham số không phải mật khẩu; không công khai hostname/tài khoản FTP trong evidence.
- Docker đọc password từ `.secrets/` bị loại khỏi Git/build context. PHP tạo config riêng ngoài DocumentRoot. Không tái sử dụng password CI cho live.
- Config DB/admin live ở config/config.local.php, không nằm trong gói cập nhật. Backup và state/logs ở var, bị loại khỏi Git/source ZIP và chặn HTTP.
- Dashboard/API metrics yêu cầu session quản trị. Nút backup cần CSRF và có cooldown; restore MariaDB chính không có HTTP action; trang demo có restore SQLite riêng với auth/CSRF và kiểm tra snapshot.
- Session cookie HttpOnly, SameSite Lax, Secure khi HTTPS; CSP, frame protection và no-store được giữ từ ứng dụng.
- Logs chỉ có trường allowlist; không in PDO message, form, query string, password hoặc dump DB. API public health chỉ trả ok/unavailable.

`tools/check-live-security.py` kiểm tra assets mới HTTP 200, anonymous metrics HTTP 401, dashboard chuyển về login và private paths 403/404 trên layout /public/. Script không đọc private response bodies, không dùng credentials admin và không được gọi là kiểm tra firewall OS.

[Minh chứng](../evidence/week3/README.md) · [Cấu hình giám sát](../monitoring/README.md) · [Backup](BACKUP_RESTORE.md).

Kiểm tra sau upload ngày 4/11/2026 đã chạy trên website thật: [kết quả HTTP](../evidence/week3/live-http-checks-2026-11-04.json). Đã xác minh dashboard sau đăng nhập, lọc log và nút backup trên live: [kiểm tra vận hành](../evidence/week3/live-operations-checks-2026-11-04.json). Bài dừng MariaDB chính vẫn chạy ở staging riêng. Trang Demo trực tiếp bổ sung bài backup/xóa/restore và ngắt đường dẫn database SQLite thử ngay trên cPanel; dữ liệu MariaDB chính giữ nguyên.
