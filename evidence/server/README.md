# Thu thập cấu hình hosting/server

**Cập nhật 21/10/2026:** website được người triển khai xác nhận mở được trên hosting cPanel. Thông số và quyền thực tế chưa được thu thập đủ; bảng dưới đây chưa phải kết quả chạy lệnh.

Trên hosting, thu ảnh mục Server Information, PHP configuration, phiên bản DB trong phpMyAdmin và DocumentRoot trong Domains. Kiểm tra SSH/Terminal và quyền thực tế riêng; các mục quảng cáo gói hosting không thay thế thông số tài khoản đang dùng. Chỉ dùng lệnh quản trị Ubuntu bên dưới nếu đang làm trên môi trường Ubuntu có quyền phù hợp.

| Thông tin | Giá trị thực tế |
|---|---|
| Loại môi trường (VM/VPS/khác được chấp nhận) | Hosting cPanel theo người triển khai; mức đáp ứng yêu cầu hạ tầng chờ đối chiếu |
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
