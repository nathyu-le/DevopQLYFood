# Kiểm tra HTTP sau khi upload .htaccess — 21/10/2026

Người triển khai xác nhận đã upload file chặn vào hosting. Kiểm tra độc lập bằng Python urllib, GET qua HTTPS, không đăng nhập và không ghi dữ liệu.

URL gốc: `https://thitruongmmo.com/DevopQLYFood/`.

| Đường dẫn tương đối | HTTP | Quan sát |
|---|---:|---|
| `public/health.php` | 200 | JSON `{"status":"ok"}` |
| `public/login.php` | 200 | Trang đăng nhập truy cập được |
| `public/assets/app.css` | 200 | CSS được phục vụ |
| `database/schema.sql` | 403 | Không tải được schema qua HTTP |
| `README.md` | 403 | Không tải được tài liệu ở thư mục cha public |

Trước khi sửa, schema và README trả 200. Kết quả sau upload xác nhận hai URL kiểm tra đã bị chặn, các endpoint public được kiểm tra vẫn hoạt động. Không mở file cấu hình thật hoặc thu credentials.

Quy tắc được thêm vào repo ở commit `8ccfd83d95e880390ddf3d78a31d66a8109cc736`. Kiểm tra này ghi nhận hành vi HTTP, không đối chiếu byte của file đang triển khai và không kiểm tra mọi URL có thể có.

Chặn HTTP bằng Apache không chứng minh đã cấu hình firewall hệ điều hành, SSH hoặc security group. Những minh chứng đó được theo dõi riêng trong [thông tin server](README.md) và [checklist Week 1](../../docs/WEEK1_CHECKLIST.md).
