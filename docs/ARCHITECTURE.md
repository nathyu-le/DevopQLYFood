# Kiến trúc hosting hiện tại và phương án Ubuntu

**Cập nhật 21/10/2026:** ứng dụng đang truy cập tại [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/). Sơ đồ mô tả các phần đã có bằng chứng; thông số chưa biết được ghi rõ. [Ảnh Server Information và Domains](../evidence/server/README.md).

![Kiến trúc hosting cPanel hiện tại](../architecture.png)

## Thành phần hiện tại

| Thành phần | Hiện trạng và bằng chứng | Chưa xác minh |
|---|---|---|
| Client / mạng | Trình duyệt truy cập HTTPS; login và health HTTP 200 | Chính sách mạng/firewall của nhà cung cấp |
| Hosting | Gói `cp1`, Linux x86_64, Shared IP `160.191.243.50` theo cPanel | Distro/version OS, CPU/RAM/disk quota, quyền quản trị |
| Web server / runtime | Apache Version 2.4.67 theo Server Information; PHP app đang chạy | PHP version/extensions, cấu hình handler; chưa có bằng chứng reverse proxy riêng do nhóm cấu hình |
| Source / vùng web | Domain Document Root `/public_html`; URL app `/DevopQLYFood/public/` | Đường dẫn OS đầy đủ cần File Manager xác nhận |
| Database | MariaDB `10.11.16-MariaDB-cll-lve`; SELECT bản ghi ID 9 khớp app | DB host/port, cùng máy hay máy khác, datadir và chính sách lưu trữ |
| Quản trị | cPanel/File Manager và phpMyAdmin dùng triển khai/đối chiếu DB | SSH/Terminal, user/role và firewall/ports |

**Luồng đọc:** trình duyệt → HTTPS → web server/PHP của hosting → PDO SELECT bảng `dishes` → HTML về client. **Luồng ghi:** form quản trị → session/CSRF/validation → INSERT/UPDATE/DELETE → SELECT lại danh sách. [Ảnh app và SQL](../evidence/application/demo-2026-10-21.md) chứng minh thao tác thêm/sửa ID 9. DB dùng driver PDO MySQL để kết nối MariaDB; CI riêng vẫn dùng MySQL 8.0.

## Cổng và lưu trữ

- HTTPS ở URL mặc định dùng TCP 443, đã truy cập thành công. Đây không phải bằng chứng nhóm đã cấu hình firewall. Force HTTPS Redirect đang Off; không suy ra SSL bị tắt.
- Port SSH, DB port, địa chỉ DB và các port listening khác chưa được cung cấp; không ghi `127.0.0.1:3306` thành hiện trạng hosting.
- Source nằm trong tài khoản hosting; URL và Document Root cho thấy layout dự kiến `public_html/DevopQLYFood/public`. Chưa xác định đường dẫn `/home/...` đầy đủ.
- MariaDB lưu dữ liệu phía hosting; datadir, vị trí máy DB, backup và retention chưa xác minh. Không gán `/var/lib/mysql` cho hosting.

## Bảo vệ các thư mục riêng

Kiểm tra HTTP ngày 21/10/2026 thấy `database/schema.sql` và `README.md` ở thư mục cha `public/` còn tải được. Chỉ mở URL `/public/` chưa đủ để bảo vệ `config/`, `app/` và `database/`.

Repo có `.htaccess` đặt **cạnh `public/`** cho layout cPanel hiện tại, chặn mọi URL ngoài `public/`. Cần upload và kiểm tra thực tế theo [CPANEL_SETUP.md](CPANEL_SETUP.md#bảo-vệ-layout-hiện-tại); chưa ghi bản chặn có hiệu lực. Khi hosting cho phép, ưu tiên domain/subdomain riêng trỏ DocumentRoot trực tiếp vào `public/` và đặt thư mục riêng ngoài vùng web.

## Phương án Ubuntu/VM/VPS tham chiếu

[MANUAL_SETUP.md](MANUAL_SETUP.md) giữ hướng dẫn Ubuntu 24.04 nếu chọn môi trường đó để đáp ứng phần cài/cấu hình server và các tuần tiếp theo. Thiết kế tham chiếu dùng Apache/PHP cùng MySQL, DB loopback `127.0.0.1:3306`, source `/var/www/DevopQLYFood`, DocumentRoot `/var/www/DevopQLYFood/public`; SSH chỉ cho nguồn quản trị, DB không public. Các giá trị này **chưa phải hiện trạng hosting**.

Docker/Compose, volumes, pipeline deploy, monitoring và backup/restore thuộc checkpoint sau. Khi đổi môi trường, cập nhật sơ đồ và thu bằng chứng mới.
