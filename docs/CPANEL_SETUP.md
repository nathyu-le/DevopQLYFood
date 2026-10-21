# Triển khai app PHP/MySQL trên hosting cPanel

**Trạng thái ngày 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được. Ghi nhận này chưa thay cho video/ảnh CRUD và minh chứng hạ tầng Week 1.

## Source và thư mục

Bản source đã cung cấp để upload: commit `ca9ef4fcd9214266af681963caf2076e44bc7767`. Upload ZIP vào Home Directory bằng File Manager rồi Extract; chọn domain/subdomain riêng có DocumentRoot trỏ vào `DevopQLYFood/public`. Chỉ `public/` được phục vụ ra web; cấu hình, SQL và các thư mục khác nằm ngoài DocumentRoot.

Đường dẫn Home do hosting cấp có thể khác nhau. Nếu nhà cung cấp giới hạn DocumentRoot bên trong `public_html`, cần điều chỉnh cách đặt thư mục và các đường dẫn require theo môi trường; không coi việc mở được URL `/public` là đã bảo vệ thư mục cha. Ghi đường dẫn thật vào minh chứng trước khi hoàn thiện sơ đồ.

URL đang dùng có dạng `/DevopQLYFood/public/`. Cần kiểm tra Domains và File Manager để xác nhận DocumentRoot cùng vị trí các thư mục riêng; chưa kết luận cấu hình ngoài public đã được bảo vệ chỉ vì ứng dụng truy cập được.

## Database và PHP

1. Tạo DB mới và user riêng trong Manage My Databases/MySQL Databases hoặc Database Wizard; ghi đầy đủ tên có prefix cPanel.
2. Gán quyền SELECT, INSERT, UPDATE, DELETE cho tài khoản app. Import schema bằng phiên phpMyAdmin của cPanel có quyền tạo bảng.
3. Chọn đúng DB trong phpMyAdmin, import `database/schema.sql`; tùy chọn import `database/seed.sql` một lần. File SQL gộp đã cung cấp gồm hai phần này; không import cả file gộp và seed riêng vì sẽ thêm dữ liệu mẫu hai lần.
4. Chọn PHP 8.2+ có pdo_mysql, mbstring và session; ghi phiên bản DB thật. Source đã qua test với MySQL 8.0, cần xác minh môi trường hosting riêng.

## Cấu hình cục bộ

- Có PHP CLI phù hợp: từ thư mục source chạy `php tools/configure.php`; script hỏi DB và admin, kiểm tra bảng rồi tạo cấu hình. Script từ chối ghi đè file đã tồn tại.
- Cấu hình bằng File Manager: copy `config/config.example.php` thành `config/config.local.php`, điền DB host/port/name/user/password và admin username/password_hash. DB host dùng đúng giá trị hosting cấp, không suy ra từ tên domain.
- `password_hash` phải là hash hợp lệ tạo cho mật khẩu admin riêng, không phải mật khẩu DB hoặc mật khẩu thuần. Trường này để trống khiến app báo lỗi cấu hình HTTP 500.
- Chỉ sửa file local trên hosting; không đưa credentials, mật khẩu/hash quản trị thật hoặc file config local vào repo và minh chứng.
- PHP CLI và PHP phục vụ website có thể có cấu hình khác nhau; kiểm tra extension của đúng môi trường khi có lỗi.

## Kiểm tra và ghi minh chứng

1. Mở HTTPS và `/health.php`, ghi kết quả thực tế; HTTP 200 với status ok xác nhận kết nối và đọc bảng, chưa chứng minh thao tác ghi.
2. Theo [DEMO.md](DEMO.md): đăng nhập, thêm món mới, tải lại/tìm kiếm, SELECT cùng bản ghi, sửa giá/trạng thái và đối chiếu lại DB.
3. Lưu ảnh/video có URL, ID bản ghi và ngày; ghi người thực hiện tại [evidence/week1/](../evidence/week1/README.md).
4. Thu OS/tài nguyên, web server/PHP/DB, DocumentRoot, quyền user/SSH và firewall trong [thông tin server](../evidence/server/README.md).
5. Đối chiếu riêng yêu cầu cài Ubuntu Server/VM, EC2 hoặc VPS của đề. Có website chạy trên shared hosting chưa tự chứng minh nhóm đã thực hiện các bước quản trị hệ điều hành; chưa đánh dấu toàn bộ Week 1 hoàn tất.

## Tài liệu tham khảo

- [cPanel File Manager](https://docs.cpanel.net/cpanel/files/file-manager/)
- [cPanel Domains](https://docs.cpanel.net/cpanel/domains/domains/)
- [cPanel Manage My Databases](https://docs.cpanel.net/cpanel/databases/manage-my-databases/)
- [PHP password_hash](https://www.php.net/manual/en/function.password-hash.php)
