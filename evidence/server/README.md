# Cấu hình hosting đang dùng — 21/10/2026

Nguồn: hai ảnh cPanel do người triển khai cung cấp và cho phép công khai để minh chứng Week 1. Đây là thông tin tài khoản đang dùng, không lấy cấu hình gói quảng cáo làm hiện trạng.

| Thông tin | Giá trị quan sát |
|---|---|
| Môi trường / gói | Hosting cPanel, Hosting Package `cp1` |
| OS / kiến trúc | `linux`, `x86_64`; chưa xác định bản phân phối/version OS |
| Kernel | `6.12.0-211.55.1.el10_2.x86_64` |
| cPanel | Server Information báo `134.0 (build 61)` |
| Web server | Apache Version `2.4.67` theo Server Information |
| Database | `10.11.16-MariaDB-cll-lve` theo Server Information |
| PHP / extensions | Chưa có ảnh phiên bản thực tế; source cần PHP 8.2+, pdo_mysql, mbstring và session |
| Shared IP Address | `160.191.243.50`; đây là IP dùng chung hiển thị trong cPanel |
| Domain / Document Root | `thitruongmmo.com`, `/public_html` theo Domains; đường dẫn trong tài khoản, chưa phải đường dẫn OS đầy đủ |
| Website | `https://thitruongmmo.com/DevopQLYFood/public/` |
| Redirects To / Force HTTPS Redirect | `Not Redirected` / `Off` ở thời điểm chụp; HTTPS vẫn truy cập được |
| CPU / RAM / disk quota | Chưa có ảnh thông số tài khoản |
| User / SSH / firewall | Chưa xác minh quyền, port listening hoặc chính sách firewall |
| Source / DB host, port, datadir | Source suy ra theo URL ở `public_html/DevopQLYFood`, cần File Manager xác nhận; DB host/port/datadir chưa được cung cấp |

## Ảnh gốc

![cPanel Server Information: cp1, Linux, Apache và MariaDB](2026-10-21-server-information.jpeg)

![cPanel Domains: public_html và Force HTTPS Redirect Off](2026-10-21-domain-root.jpeg)

Chỉ ghi OS là Linux theo ảnh; chưa có bằng chứng đã cài Ubuntu, tạo user/SSH hoặc tự cấu hình firewall. Không suy ra quyền root hay Docker từ việc có cPanel.

## Kiểm tra vùng phục vụ web

Kiểm tra GET các file source công khai, không chứa credentials, ngày 21/10/2026:

| URL tương đối từ `/DevopQLYFood/` | Kết quả trước bản sửa |
|---|---|
| `public/health.php` | HTTP 200, `{"status":"ok"}` |
| `database/schema.sql` | HTTP 200, nội dung khớp schema công khai của repo |
| `README.md` | HTTP 200, tải được tài liệu của repo |

Kết quả cho thấy các thư mục cạnh `public/` còn nằm trong vùng HTTP truy cập được. Không kiểm tra hoặc công khai cấu hình thật. Repo đã bổ sung `.htaccess` để chỉ cho phép URL trong `public/`; **chưa xác nhận đã upload và có hiệu lực trên hosting**. [Cách cài và kiểm tra](../../docs/CPANEL_SETUP.md#bảo-vệ-layout-hiện-tại).

## Minh chứng cần bổ sung

- Ảnh PHP version/extensions và quota CPU/RAM/disk của tài khoản, không chụp credentials.
- File Manager xác nhận vị trí source; DB host/port và vị trí dữ liệu nếu có quyền xem.
- Quyền SSH/Terminal và chính sách ports/firewall; đối chiếu yêu cầu server của đề.
- Sau khi upload `.htaccess`: health/login/assets vẫn hoạt động, schema và README trả 403.

Nếu bổ sung Ubuntu có quyền quản trị, ghi kết quả thật từ `cat /etc/os-release`, `lscpu`, `free -h`, `df -h`, `php --version`, `apache2 -v`, `mysql --version`, `ss -lnt` và `sudo ufw status verbose`. Chỉ chạy trên đúng môi trường/quyền tương ứng.
