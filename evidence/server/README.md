# Thu thập cấu hình server sau khi có môi trường thật

**Chưa thực hiện.** Đây là biểu mẫu và hướng dẫn, không phải kết quả chạy lệnh.

| Thông tin | Giá trị thực tế |
|---|---|
| Loại môi trường (VM/VPS/khác được chấp nhận) | Chưa xác định |
| OS / version | Chưa ghi nhận |
| CPU / RAM / disk | Chưa ghi nhận |
| Web server / PHP / MySQL version | Chưa ghi nhận |
| User quản trị / SSH method | Chưa ghi nhận |
| Cổng đang dùng và chính sách firewall | Chưa ghi nhận |
| Thư mục source và MySQL datadir | Chưa ghi nhận |
| Ngày, người xác minh | Chưa ghi nhận |

Trên Ubuntu có quyền phù hợp, có thể dùng các lệnh sau để kiểm tra. Chạy từng lệnh, lưu phần kết quả cần thiết sau khi loại thông tin không nên công khai:

```bash
cat /etc/os-release
lscpu
free -h
df -h
php --version
apache2 -v
mysql --version
ss -lnt
sudo ufw status verbose
sudo systemctl status apache2 --no-pager
sudo systemctl status mysql --no-pager
```

Kiểm tra MySQL `SELECT @@datadir;` bằng công cụ quản trị DB; không chụp cấu hình có password. Nếu dùng security group hoặc SSH port khác, lưu cấu hình thực tế tương ứng thay vì giả định luôn là UFW/22.
