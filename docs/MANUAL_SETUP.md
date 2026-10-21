# Cài đặt thủ công ban đầu trên Ubuntu — Week 1

**Trạng thái:** quy trình chuẩn bị cho Ubuntu Server 24.04 LTS; chưa chạy trên server của nhóm. Không chạy các lệnh quản trị này trong shared hosting/cPanel thông thường. Chọn và xác minh môi trường thật trước khi dùng, rồi ghi khác biệt nếu có.

## 1. Server, user, SSH và firewall

Ghi OS/CPU/RAM/disk theo mẫu trong `evidence/server/README.md`. Dùng tài khoản quản trị được phép `sudo`, đăng nhập qua SSH; lưu bằng chứng phương thức đăng nhập nhưng không công khai key hoặc password. Nếu cần tạo người dùng trên một server mới:

```bash
sudo adduser deploy
sudo usermod -aG sudo deploy
```

Thêm public key của người quản trị vào `~deploy/.ssh/authorized_keys` với quyền phù hợp, hoặc dùng phương thức SSH do nhà cung cấp hỗ trợ. Kiểm tra đăng nhập được bằng tài khoản mới trước khi thay đổi cấu hình truy cập.

**Firewall:** xác minh SSH đang dùng port nào và địa chỉ nguồn quản trị. Ví dụ dưới đây dành cho SSH port 22; thay `IP_ADMIN_THUC_TE` bằng IP quản trị thật, không chạy nguyên placeholder:

```bash
sudo ufw allow from IP_ADMIN_THUC_TE to any port 22 proto tcp
sudo ufw allow 80/tcp
sudo ufw enable
sudo ufw status verbose
```

Giữ phiên SSH đang mở và kiểm tra một phiên SSH mới sau khi áp dụng. Không mở cổng 3306 ra Internet. Nếu có security group của nhà cung cấp, cấu hình tương ứng. Chỉ mở 443 khi đã thiết lập HTTPS. Nếu dùng VM, thay cách truy cập bằng cấu hình mạng VM thực tế và chứng minh client đến được server.

## 2. Cài các gói chạy ứng dụng

```bash
sudo apt update
sudo apt install -y git apache2 php libapache2-mod-php php-mysql php-mbstring mysql-server
sudo systemctl enable --now apache2 mysql
php --version
php -m
```

Kiểm tra có `PDO`, `pdo_mysql`, `mbstring`, `session`. Tài liệu dùng PHP 8.2+ và MySQL 8.0+; ghi phiên bản thực tế bằng kết quả lệnh.

## 3. Lấy source

Các lệnh này giả định thư mục đích mới/chưa có source. Không ghi đè một website đang dùng:

```bash
sudo mkdir -p /var/www/DevopQLYFood
sudo chown "$USER":www-data /var/www/DevopQLYFood
git clone https://github.com/nathyu-le/DevopQLYFood.git /var/www/DevopQLYFood
cd /var/www/DevopQLYFood
```

## 4. Tạo database và user ứng dụng

Mở MySQL bằng quyền quản trị. `MYSQL_HISTFILE=/dev/null` giúp không ghi câu lệnh chứa mật khẩu vào lịch sử client của phiên này:

```bash
sudo env MYSQL_HISTFILE=/dev/null mysql
```

Chạy SQL sau, **thay `MAT_KHAU_DB_TU_CHON` bằng mật khẩu mạnh của môi trường**, không đưa giá trị thật vào repo/ảnh/video:

```sql
CREATE DATABASE restaurant_menu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'menu_app'@'127.0.0.1' IDENTIFIED BY 'MAT_KHAU_DB_TU_CHON';
GRANT SELECT, INSERT, UPDATE, DELETE ON restaurant_menu.* TO 'menu_app'@'127.0.0.1';
EXIT;
```

Import schema bằng tài khoản quản trị, không cấp quyền tạo/xóa bảng cho tài khoản chạy app:

```bash
sudo mysql restaurant_menu < database/schema.sql
```

Tùy chọn, chỉ import dữ liệu mẫu một lần:

```bash
sudo mysql restaurant_menu < database/seed.sql
```

Kiểm tra MySQL chỉ lắng nghe loopback trong cấu hình của môi trường, ví dụ `bind-address = 127.0.0.1`. Khởi động lại dịch vụ nếu đã thay đổi và xác minh bằng `ss -lnt`. Không coi việc có firewall là lý do để công khai cổng DB.

## 5. Tạo cấu hình và tài khoản quản trị ứng dụng

Từ thư mục repository:

```bash
php tools/configure.php
```

Nhập host `127.0.0.1`, port `3306`, tên DB, DB user/password vừa tạo, tên đăng nhập và mật khẩu quản trị 12–72 byte. Script kiểm tra kết nối/bảng rồi tạo `config/config.local.php`, hash mật khẩu quản trị và từ chối ghi đè cấu hình đã có.

File cục bộ bị `.gitignore` bỏ qua. Nếu không có CLI, tham khảo `config/config.example.php` để cấu hình theo quyền của môi trường; không tạo trang công khai in password hoặc hash. Phương án cPanel chi tiết sẽ được bổ sung khi xác định quyền hosting.

Đặt quyền để Apache đọc được source/config, nhưng không được sửa chúng:

```bash
sudo chgrp -R www-data /var/www/DevopQLYFood
find /var/www/DevopQLYFood -type d -exec chmod 750 {} \;
find /var/www/DevopQLYFood -type f -exec chmod 640 {} \;
chmod 640 config/config.local.php
```

Không dùng `chmod 777`. Web server không cần quyền ghi thư mục source cho các chức năng hiện tại. PHP session được lưu trong vùng session của hệ thống.

## 6. Cấu hình Apache

Tạo `/etc/apache2/sites-available/devopqlyfood.conf` bằng quyền quản trị với nội dung:

```apache
<VirtualHost *:80>
    DocumentRoot /var/www/DevopQLYFood/public
    DirectoryIndex index.php

    <Directory /var/www/DevopQLYFood/public>
        Options -Indexes
        AllowOverride None
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/devopqlyfood-error.log
    CustomLog ${APACHE_LOG_DIR}/devopqlyfood-access.log combined
</VirtualHost>
```

Ví dụ này dành cho server demo riêng truy cập bằng IP. Nếu có nhiều website hoặc domain, thêm `ServerName`/VirtualHost phù hợp, không vô hiệu hóa site khác đang hoạt động. Với server demo mới chỉ chạy ứng dụng này:

```bash
sudo a2dissite 000-default
sudo a2ensite devopqlyfood
sudo apache2ctl configtest
sudo systemctl reload apache2
```

DocumentRoot **phải là `public/`**, không phải thư mục gốc repository. Cấu hình DB và SQL ở bên ngoài vùng phục vụ web.

## 7. Kiểm tra trước khi ghi nhận kết quả

1. Trên server: kiểm tra dịch vụ Apache/MySQL và `curl -i http://127.0.0.1/health.php` trả HTTP 200 với `{"status":"ok"}`.
2. Từ máy khác: mở `http://IP_SERVER_THUC_TE/`, đăng nhập và thêm món mới.
3. Tải lại, tìm món, sửa giá và đối chiếu bản ghi trong MySQL theo `DEMO.md`.
4. Kiểm tra URL `/config/config.local.php` và `/database/schema.sql` không phục vụ file thật. Không chụp secret nếu cấu hình sai; sửa DocumentRoot trước.
5. Cập nhật README, sơ đồ hiện trạng, thông tin server và thư mục evidence bằng kết quả thật.

HTTP được dùng trong ví dụ demo ban đầu. Nếu truy cập quản trị qua Internet, cấu hình HTTPS trước khi dùng credentials thật trên mạng công cộng. Chỉ ghi HTTPS hoạt động sau khi kiểm tra được chứng chỉ và URL.

## 8. Các lỗi thường gặp khi cài ban đầu

| Triệu chứng | Kiểm tra / xử lý |
|---|---|
| 503 “chưa được cấu hình” | Tạo `config.local.php` ngoài public, kiểm tra quyền đọc của Apache |
| 500 khi mở thực đơn | Xem error log đã loại thông tin nhạy cảm; kiểm tra DB host/user/password, schema và quyền CRUD |
| “could not find driver” ở công cụ CLI | Cài `php-mysql`, xác nhận CLI/web dùng đúng PHP và extension |
| Không truy cập từ client | Kiểm tra IP/mạng VM, dịch vụ, port listening, firewall và security group |
| Không thấy dữ liệu sau import | Kiểm tra tên DB trong config có đúng DB đã import; không chỉ nhìn file seed |

Đây là hướng dẫn cài ban đầu; playbook sự cố vận hành đầy đủ sẽ phát triển ở checkpoint tương ứng.
