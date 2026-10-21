# Kiểm tra truy cập HTTP trên hosting — 21/10/2026

## Môi trường và cách kiểm tra

- URL ứng dụng do người triển khai xác nhận: https://thitruongmmo.com/DevopQLYFood/public/
- Môi trường: hosting cPanel; phiên bản OS/web server/PHP/DB chưa được cung cấp đầy đủ.
- Thực hiện: kiểm tra kỹ thuật bằng HTTP client tự động của trợ lý, dùng Python urllib và xác minh TLS mặc định.
- Bản source đã cung cấp để upload: commit `ca9ef4fcd9214266af681963caf2076e44bc7767`; cấu hình thật được giữ riêng trên hosting.
- Đây là log truy cập thật, không gán kết quả này cho Hải/Tùng hoặc thay cho video demo của nhóm.

## Kết quả quan sát

| Request | HTTP | Content-Type | Kết quả |
|---|---:|---|---|
| GET https://thitruongmmo.com/DevopQLYFood/public/health.php | 200 | application/json; charset=utf-8 | `{"status":"ok"}` |
| GET https://thitruongmmo.com/DevopQLYFood/public/login.php | 200 | text/html; charset=UTF-8 | Có trang Đăng nhập và form với trường username/password |

Các request giữ nguyên URL đích HTTPS tương ứng khi hoàn tất.

## Phạm vi kết luận

Endpoint health trong source kết nối PDO đến DB và chạy `SELECT id FROM dishes LIMIT 1`. Status ok phù hợp với việc cấu hình DB đã cho phép kết nối và đọc bảng. Trang đăng nhập tải được và hiển thị form.

Các request này chưa đăng nhập hoặc thử INSERT/UPDATE/DELETE; chưa có kết quả SELECT đối chiếu bản ghi từ phpMyAdmin. Không suy ra nhóm đã tự cài OS, cấu hình firewall/SSH hoặc hoàn thành toàn bộ Week 1.

## Bước cần bổ sung

Theo [kịch bản demo](../../docs/DEMO.md): thêm “Cơm gà Demo Week 1” giá 45.000đ, tải lại/tìm kiếm, SELECT cùng bản ghi trong phpMyAdmin, sửa thành 50.000đ/hết món và đối chiếu lại. Lưu ảnh/video có URL, ID bản ghi, ngày và người thực hiện.
