# Kiến trúc Week 1: hosting đang dùng và phương án Ubuntu tham chiếu

**Trạng thái ngày 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được trên hosting cPanel. Phiên bản web server/PHP/DB, đường dẫn source/DocumentRoot và vị trí DB chưa được ghi nhận đầy đủ; chưa có sơ đồ hiện trạng được xác minh. Quyền cài Ubuntu, SSH và firewall vẫn cần đối chiếu đề.

## Thông tin hiện trạng đã có

| Thành phần | Đã xác nhận | Cần bổ sung |
|---|---|---|
| Client và đường truy cập | Website HTTPS tại `/DevopQLYFood/public/`; login và health trả HTTP 200 | Ảnh tải lại/tìm kiếm và trạng thái web đầy đủ |
| App | Có ảnh thêm/sửa cùng món ID 9 trên hosting cPanel | Đường dẫn source/DocumentRoot và phiên bản PHP thật |
| Database | SELECT cùng ID 9, giá 50000, is_available 0 trong phpMyAdmin | Engine/version, DB host/port và vị trí lưu dữ liệu thật |
| Quản trị và mạng | Có tài khoản cPanel được dùng để triển khai/đối chiếu DB | OS/tài nguyên, quyền SSH, firewall/ports và phạm vi quyền của tài khoản |

[Xem ảnh app/DB](../evidence/application/demo-2026-10-21.md) · [Log HTTP](../evidence/application/http-access-2026-10-21.md).

## Phương án Ubuntu tham chiếu

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

Trong phương án Ubuntu tham chiếu, dữ liệu nằm trên ổ đĩa của MySQL cài trực tiếp. Với hosting đang dùng, engine/version, DB host và đường dẫn lưu trữ cần được nhà cung cấp hoặc công cụ quản trị xác nhận. Docker volumes, backup/restore và monitoring sẽ được thiết kế ở checkpoint tương ứng; sơ đồ này chưa chứng minh các phần đó đã hoàn thành.

## Hoàn thiện thông tin môi trường thực tế

1. Xác định loại môi trường thực tế (VM/VPS/hosting được giảng viên chấp nhận).
2. Ghi OS, tài nguyên, IP/đường dẫn demo ở mức được phép công khai.
3. Chụp/lưu cấu hình SSH, firewall, port listening, Apache và MySQL.
4. Chỉnh sơ đồ từ “dự kiến” thành đúng các thành phần đã chạy; ghi ngày xác minh.
5. Liên kết minh chứng tương ứng trong `evidence/README.md`.
