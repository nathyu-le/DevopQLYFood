# Triển khai app PHP/MySQL trên hosting cPanel

**Môi trường live hiện tại:** [khotrick.org/DevopQLYFood/public/](https://khotrick.org/DevopQLYFood/public/). Hosting mới có Terminal; tại lần kiểm tra, shell không tìm thấy lệnh Docker. Website chạy PHP/DB trực tiếp, còn image Docker được deploy vào staging tạm trên GitHub runner.

Tài liệu này dùng cho **cài đặt/cấu hình ban đầu**. Những lần cập nhật source tiếp theo dùng [pipeline FTPS](CICD_SETUP.md). [Ảnh thông số cp1 và thao tác app ngày 21/10/2026](../evidence/week1/README.md) là minh chứng Week 1, không phải cấu hình hosting mới.

## Source và thư mục

Lấy source từ repository này để cài lần đầu. Upload ZIP vào Home Directory bằng File Manager rồi Extract; chọn domain/subdomain riêng có DocumentRoot trỏ vào `DevopQLYFood/public`. Chỉ `public/` được phục vụ ra web; cấu hình, SQL và các thư mục khác nằm ngoài DocumentRoot.

Đường dẫn Home do hosting cấp có thể khác nhau. Nếu nhà cung cấp giới hạn DocumentRoot bên trong `public_html`, cần điều chỉnh cách đặt thư mục và các đường dẫn require theo môi trường; không coi việc mở được URL `/public` là đã bảo vệ thư mục cha. Ghi đường dẫn thật vào minh chứng trước khi hoàn thiện sơ đồ.

Với layout dự án nằm dưới `public_html` và URL dạng `/DevopQLYFood/public/`, cần chặn HTTP vào các thư mục cạnh `public/`. Đợt kiểm tra Week 1 ghi nhận schema/README trả HTTP 200 trước bản sửa; sau upload `.htaccess`, hai URL trả 403, health/login/CSS vẫn trả 200. Khi cài gói mới, kiểm tra lại các URL để xác nhận quy tắc có hiệu lực. [Log](../evidence/server/http-protection-2026-10-21.md).

## Bảo vệ layout hiện tại

1. Trong cPanel File Manager, mở `public_html/DevopQLYFood` — thư mục chứa `app`, `config`, `database`, `public`. Bật **Settings → Show Hidden Files (dotfiles)**.
2. Tải [file `.htaccess` trong repo](../.htaccess) vào đúng thư mục đó. Nếu tải về tên `cpanel-project.htaccess`, đổi tên thành **`.htaccess`**. Không đặt vào `public/` hoặc `public_html/` của toàn domain. Nếu đã có `.htaccess`, tải bản cũ về máy trước và ghép các dòng dưới vào đầu file; cần xem nội dung trước khi ghi đè cấu hình khác.

```apache
RewriteEngine On
RewriteRule !^public(?:/|$) - [F,L]
```

Quy tắc chạy trong thư mục dự án: chỉ cho phép HTTP với đường dẫn bắt đầu `public/` hoặc chính thư mục `public`. Các `require` PHP đọc config/app từ ổ đĩa vẫn hoạt động.

3. Kiểm tra `/DevopQLYFood/public/health.php` trả 200/status ok, login và CSS/JS vẫn tải được. `/DevopQLYFood/database/schema.sql`, `/DevopQLYFood/README.md` và `/DevopQLYFood/` phải trả **403**. Không mở file config thật để chụp minh chứng.
4. Nếu website trả 500 sau khi thêm file, gỡ đúng các dòng vừa thêm/khôi phục bản đã tải về, rồi hỏi hosting về `mod_rewrite` và quyền `AllowOverride FileInfo`, hoặc chuyển domain/subdomain sang DocumentRoot `public/`. Nếu schema vẫn trả 200, bản chặn chưa có hiệu lực.

**Phạm vi kiểm tra bản sửa:** đối chiếu quy tắc với tài liệu Apache và kiểm tra pattern cho các đường dẫn public/private. Môi trường soạn không có Apache; kiểm tra pattern không thay cho integration Apache. Sau khi người triển khai upload, đã xác minh HTTP trên hosting: health/login/CSS 200, schema/README 403 ngày 21/10/2026.

## Database và PHP

1. Tạo DB mới và user riêng trong Manage My Databases/MySQL Databases hoặc Database Wizard; ghi đầy đủ tên có prefix cPanel.
2. Gán quyền SELECT, INSERT, UPDATE, DELETE cho tài khoản app. Import schema bằng phiên phpMyAdmin của cPanel có quyền tạo bảng.
3. Chọn đúng DB trong phpMyAdmin, import `database/schema.sql`; tùy chọn import `database/seed.sql` một lần. Không import seed lặp lại vì sẽ thêm dữ liệu mẫu nhiều lần.
4. Chọn PHP 8.2 trở lên cho đúng domain; ứng dụng cần pdo_mysql, mbstring và session. Ảnh PHP/MariaDB ở Week 1 thuộc cp1 cũ; kiểm tra phiên bản gói mới trong cPanel trước khi ghi thông số. Source checks dùng MySQL 8.0 tạm, Docker CI/staging dùng MariaDB 10.11 tạm; chúng không phải DB hosting.

## Cấu hình cục bộ

- Có PHP CLI phù hợp: từ thư mục source chạy `php tools/configure.php`; script hỏi DB và admin, kiểm tra bảng rồi tạo cấu hình. Script từ chối ghi đè file đã tồn tại.
- Cấu hình bằng File Manager: copy `config/config.example.php` thành `config/config.local.php`, điền DB host/port/name/user/password và admin username/password_hash. DB host dùng đúng giá trị hosting cấp, không suy ra từ tên domain.
- `password_hash` phải là hash hợp lệ tạo cho mật khẩu admin riêng, không phải mật khẩu DB hoặc mật khẩu thuần. Trường này để trống khiến app báo lỗi cấu hình HTTP 500.
- Chỉ sửa file local trên hosting; không đưa credentials, mật khẩu/hash quản trị thật hoặc file config local vào repo và minh chứng.
- PHP CLI và PHP phục vụ website có thể có cấu hình khác nhau; kiểm tra extension của đúng môi trường khi có lỗi.

## Kiểm tra và ghi minh chứng

1. Mở HTTPS và `/DevopQLYFood/public/health.php`; [log ngày 21/10/2026](../evidence/application/http-access-2026-10-21.md) đã ghi HTTP 200/status ok. Health xác nhận kết nối và đọc bảng; ảnh thao tác app/SELECT được lưu riêng.
2. Theo [Kịch bản Week 1](WEEK1_PRESENTATION.md): đăng nhập, thêm món mới, tải lại/tìm kiếm, SELECT cùng bản ghi, sửa giá/trạng thái và đối chiếu lại DB.
3. Lưu ảnh/video có URL, ID bản ghi và ngày; ghi người thực hiện tại [evidence/week1/](../evidence/week1/README.md).
4. Thu thông số cPanel/PHP/quota/DocumentRoot của đúng gói đang dùng nếu cập nhật minh chứng. [Thông tin server Week 1](../evidence/server/README.md) thuộc cp1 cũ; phần OS/user/SSH/firewall còn thiếu được theo dõi riêng.
5. Đối chiếu riêng yêu cầu cài Ubuntu Server/VM, EC2 hoặc VPS của đề. Có website chạy trên shared hosting chưa tự chứng minh nhóm đã thực hiện các bước quản trị hệ điều hành; chưa đánh dấu toàn bộ Week 1 hoàn tất.

## Tài liệu tham khảo

- [cPanel File Manager](https://docs.cpanel.net/cpanel/files/file-manager/)
- [cPanel Domains](https://docs.cpanel.net/cpanel/domains/domains/)
- [cPanel Manage My Databases](https://docs.cpanel.net/cpanel/databases/manage-my-databases/)
- [PHP password_hash](https://www.php.net/manual/en/function.password-hash.php)
- [Apache RewriteRule flags: F/403](https://httpd.apache.org/docs/2.4/rewrite/flags.html#flag_f)
- [Apache .htaccess / AllowOverride](https://httpd.apache.org/docs/2.4/howto/htaccess.html)
