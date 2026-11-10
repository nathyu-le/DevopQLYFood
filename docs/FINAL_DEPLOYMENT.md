# Cài mới và tái tạo bản cuối trên cPanel

Hướng chính là cPanel PHP/MariaDB, không yêu cầu VPS hoặc Docker trên hosting. Dùng source của release `week4-final-v1.0.0`; nếu sửa trong buổi bảo vệ, ghi thêm SHA/run mới. Không lấy gói runtime cập nhật để thay cho toàn bộ source cài lần đầu.

## 1 Chuẩn bị một tài khoản hosting mới

Cần domain/SSL, PHP 8.2 trở lên, extensions pdo_mysql, pdo_sqlite, mbstring, session, File Manager và MariaDB/MySQL. Terminal hữu ích cho CLI nhưng thao tác cấu hình vẫn có đường File Manager/phpMyAdmin. Không suy ra PHP CLI giống PHP của domain; kiểm tra cả hai khi dùng CLI.

Tải source của tag từ trang Releases hoặc clone trên máy có Git:

```bash
git clone --branch week4-final-v1.0.0 --depth 1 https://github.com/nathyu-le/DevopQLYFood.git
cd DevopQLYFood
```

Upload source bằng File Manager và giải nén trong thư mục dự án. Ưu tiên DocumentRoot trỏ trực tiếp `public/`, phần còn lại ngoài vùng web. Với URL hiện tại dạng `/DevopQLYFood/public/`, giữ root `.htaccess` đúng thư mục DevopQLYFood để chặn URL ngoài public. Đọc [chi tiết layout](CPANEL_SETUP.md); không đặt rule chặn vào public_html của cả domain.

## 2 Tạo database và cấu hình

1. cPanel → Manage My Databases/Database Wizard: tạo database và user riêng, giữ đầy đủ prefix.
2. phpMyAdmin: chọn đúng DB mới, import `database/schema.sql`; seed là tùy chọn và chỉ import một lần. Tạo bảng bằng quyền quản trị riêng.
3. Gán cho user ứng dụng SELECT, INSERT, UPDATE, DELETE. Đối chiếu quyền thực tế qua giao diện được cấp; không giả định đã đúng chỉ từ tài liệu.
4. Chạy `php tools/configure.php` bằng đúng PHP CLI. Nhập host, port, DB name/user/password theo hosting và mật khẩu admin riêng. Script không ghi đè config tồn tại, kiểm tra đọc bảng rồi tạo hash và file local.
5. Không có CLI: copy `config/config.example.php` thành `config/config.local.php` trong File Manager; nhập cấu hình và password_hash hợp lệ. Không dùng công cụ hash online hoặc commit config thật.
6. Tạo thư mục `var` thuộc đúng user chạy PHP, quyền 0700; config chỉ cho chủ tài khoản/PHP đọc. Không chữa lỗi ghi file bằng chmod 777.

Từ project root, chạy kiểm tra chỉ đọc:

```bash
php tools/preflight.php
```

Phải PASS PHP/extensions, file ứng dụng, cấu hình/hash, DB/schema và quyền ghi private var. Script không in secrets và không sửa dữ liệu. Nếu PHP worker và CLI khác user, kiểm tra thêm thao tác backup trên web. Thiếu pdo_sqlite: bật extension cho đúng domain để trang demo hoạt động.

## 3 Kiểm tra trước khi bật tự động cập nhật

- Mở HTTPS login và health.php: 200/status ok. Đăng nhập, tạo một món thử, tải lại và sửa; ghi ID thật.
- Mở operations.php: DB hoạt động; thời gian mẫu cập nhật. Mở demo.php và chạy đủ 6 bước trên dữ liệu riêng.
- Kiểm tra anonymous ops-api/demo-api/demo-health trả 401, dashboard/demo chuyển về login; private config/schema/var không tải được. Không chụp hoặc mở nội dung config thật để làm minh chứng.
- Xác nhận TLS website hợp lệ. Nếu mới cài chưa qua pipeline, version có thể là manual; chỉ đối chiếu SHA sau deployment.

## 4 Cấu hình pipeline source đến cPanel

Theo [CICD_SETUP.md](CICD_SETUP.md): tạo FTP account giới hạn thư mục dự án; GitHub Secrets giữ user/password, Variables giữ host/port/root/base URL và tên xác minh TLS khi hosting dùng alias khác certificate. Giữ certificate validation trên control và data channel.

Bật CPANEL_DEPLOY_ENABLED sau khi cài mới và health đã ổn. Push source lên main: build/test → staging cùng image ID → deploy source FTPS → kiểm tra health/revision và HTTP security. Chỉ đổi docs có thể không chạy pipeline; dùng Run workflow main để chạy lại nếu cần.

## 5 Backup và rollback

Backup trước thay schema/cấu hình. Nút trên dashboard đọc MariaDB và giữ 14 snapshot private; cron chưa tự cài, xem [OPERATIONS.md](OPERATIONS.md). Giữ bản sao source/config/DB ở nơi riêng, không đưa vào GitHub.

Nếu source mới lỗi: giữ nguyên dữ liệu, xác định commit tốt và tạo commit hoàn tác thay đổi source bằng Git; pipeline kiểm tra rồi deploy lại. Không sửa REVISION tay để giả trạng thái. Gói deploy không thực hiện rollback DB. Nếu schema đã đổi, cần kế hoạch tương thích và phục hồi DB riêng; không dùng restore demo SQLite cho MariaDB chính.

## 6 Tái tạo môi trường thử trên máy sạch

Máy cần Git, Python 3, Docker Engine và Compose plugin. Theo [DOCKER_SETUP.md](DOCKER_SETUP.md): clone tag → `python3 tools/setup-containers.py` → `docker compose up -d --build --wait --wait-timeout 180`. Mở loopback port 8080. Secrets sinh riêng, DB volume giữ dữ liệu qua recreate; không dùng down -v khi cần giữ dữ liệu.

CI cuối kỳ thực hiện lại build mới, preflight, CRUD/persistence, backup/restore, cảnh báo và isolation; đây là kiểm chứng tái tạo Docker, không chứng minh đã provision lại tài khoản cPanel của nhà cung cấp.
