# Kiến trúc triển khai dự kiến — Week 1

**Trạng thái ngày 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được trên hosting cPanel. Phiên bản web server/PHP/DB, đường dẫn source/DocumentRoot và vị trí DB chưa được ghi nhận đầy đủ; chưa có sơ đồ hiện trạng được xác minh. Quyền cài Ubuntu, SSH và firewall vẫn cần đối chiếu đề.

Sơ đồ PNG và phần thiết kế dưới đây là **phương án Ubuntu/VM/VPS tham chiếu**. Không dùng các giá trị Apache, loopback DB, `/var/www` hoặc `/var/lib/mysql` bên dưới để mô tả hosting cPanel khi chưa kiểm chứng.

![Deployment architecture proposal](../architecture.png)

## Thành phần và luồng kết nối

| Thành phần | Vai trò | Vị trí dự kiến |
|---|---|---|
| Client | Trình duyệt để đăng nhập, xem và thêm món | Laptop/điện thoại ngoài server |
| Network | Đưa request từ client đến địa chỉ server | Internet hoặc mạng của VM, xác định khi triển khai |
| Firewall | Chỉ cho phép các cổng cần thiết | Hệ điều hành server hoặc security group |
| Apache | Nhận HTTP, phục vụ assets và chạy PHP | Ubuntu VM/VPS đề xuất |
| PHP app | Kiểm tra đầu vào, phân quyền, xử lý CRUD | Cùng server, source ngoài public được bảo vệ |
| MySQL | Lưu bảng `dishes` | Cùng server, kết nối loopback |
| Storage | Lưu source và các file dữ liệu DB | Ổ đĩa server |
| Management client | SSH bằng tài khoản có quyền phù hợp | Máy quản trị được cho phép |

Luồng chính: client gửi HTTP đến Apache, Apache/PHP xử lý và kết nối MySQL qua `127.0.0.1:3306`. MySQL đọc/ghi dữ liệu trên ổ đĩa, PHP trả trang HTML về client. Chưa sử dụng reverse proxy riêng trong Week 1.

## Cổng và lưu trữ

| Cổng | Mục đích | Chính sách thiết kế |
|---|---|---|
| TCP 80 | HTTP | Cho máy khách demo truy cập |
| TCP 22 | SSH | Chỉ máy quản trị / nguồn được phép; xác nhận port thực tế trước khi đặt firewall |
| TCP 3306 | MySQL | Chỉ loopback; không mở ra Internet |
| TCP 443 | HTTPS, khi cấu hình | Chưa xác nhận; chỉ thêm vào sơ đồ hiện trạng sau khi thực hiện |

Source dự kiến ở `/var/www/DevopQLYFood`, DocumentRoot chỉ đến `/var/www/DevopQLYFood/public`. MySQL thường dùng `/var/lib/mysql` trong phương án cài Ubuntu thông thường; xác minh `SELECT @@datadir;` trên server thật trước khi ghi là hiện trạng.

## Database và độ bền dữ liệu

`dishes` có khóa chính `id`, thông tin món, giá, tình trạng và timestamps. Các yêu cầu ghi dùng prepared statements. Tải lại trang chạy một truy vấn mới đến MySQL, không dùng mảng hoặc localStorage thay database.

Trong Week 1, dữ liệu nằm trên ổ đĩa của MySQL cài trực tiếp. Docker volumes, backup/restore và monitoring sẽ được thiết kế ở checkpoint tương ứng; sơ đồ này chưa chứng minh các phần đó đã hoàn thành.

## Khi có server thật

1. Xác định loại môi trường thực tế (VM/VPS/hosting được giảng viên chấp nhận).
2. Ghi OS, tài nguyên, IP/đường dẫn demo ở mức được phép công khai.
3. Chụp/lưu cấu hình SSH, firewall, port listening, Apache và MySQL.
4. Chỉnh sơ đồ từ “dự kiến” thành đúng các thành phần đã chạy; ghi ngày xác minh.
5. Liên kết minh chứng tương ứng trong `evidence/README.md`.
