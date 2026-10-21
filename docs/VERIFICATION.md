# Kiểm tra source và giới hạn minh chứng

## Những gì workflow kiểm tra

File `.github/workflows/source-checks.yml` dùng runner Ubuntu và MySQL tạm để:

1. Kiểm tra cú pháp tất cả file PHP.
2. Kiểm tra validation tên/giá/danh mục/trạng thái và escape HTML.
3. Khởi động PHP development server trên loopback của runner.
4. Thử đăng nhập, CSRF, thêm, tải lại/tìm kiếm, sửa, đổi tình trạng, xóa và đăng xuất qua HTTP.
5. Kiểm tra giá/trạng thái bằng một truy vấn PDO độc lập vào MySQL.

`tests/integration.py` chỉ chạy khi `RUN_INTEGRATION_TESTS=1`, yêu cầu database có tên kết thúc bằng `_test`, từ chối ghi đè cấu hình có sẵn và dọn bản ghi thử nghiệm sau khi chạy. Các mật khẩu trong workflow chỉ dùng cho dịch vụ tạm của CI, không dùng để triển khai thật.

## Cách xem kết quả thật

Mở [Actions](https://github.com/nathyu-le/DevopQLYFood/actions), chọn workflow **Source checks (not deployment)**, mở từng step và kiểm tra kết quả của đúng commit. Chỉ ghi “passed” sau khi run đã hoàn tất thành công.

**Kết quả đã xác minh ngày 21/10/2026:** [run 39529399926](https://github.com/nathyu-le/DevopQLYFood/actions/runs/39529399926), commit `4baaaa1da776c714098e35ccc5e275c5495d7170`, kết luận **success**. Runner Ubuntu 24.04, PHP CLI 8.3.6 và dịch vụ MySQL 8.0 tạm. Log xác nhận tất cả file PHP hợp lệ, **14 validation checks passed** và **19 HTTP/MySQL integration checks passed**.

Kiểm tra cú pháp JavaScript bằng `node --check` và cú pháp hai script Python bằng `py_compile` cũng đã thành công trong môi trường soạn. PHP/MySQL của bộ test được thực thi trên runner GitHub Actions. Ngày 21/10/2026 người triển khai xác nhận website đã mở được trên hosting cPanel; đã đối chiếu 3 ảnh thêm/sửa và SELECT của món ID 9 trên hosting. [Bộ minh chứng thủ công](../evidence/application/demo-2026-10-21.md).

| Loại kiểm chứng | Trạng thái / nơi đối chiếu |
|---|---|
| JavaScript syntax / Python syntax | Đã kiểm tra thành công trong môi trường soạn |
| PHP syntax và validation | Thành công — tất cả file PHP hợp lệ và 14 kiểm tra validation qua |
| CRUD với MySQL tạm | Thành công — 19 kiểm tra HTTP/MySQL qua |
| Website trên hosting cPanel | Trang đăng nhập HTTP 200; health HTTP 200/status ok ngày 21/10/2026 — [log kiểm tra](../evidence/application/http-access-2026-10-21.md) |
| OS/web server/SSH/firewall trên môi trường thật | Có ảnh Linux x86_64, Apache 2.4.67, MariaDB 10.11.16, domain root `/public_html`; domain PHP 8.2, quota CPU 100%, RAM 600 MB, disk 1.000 MB; có ảnh giao diện SSH Access, chưa có đăng nhập SSH/firewall. [Log chặn HTTP](../evidence/server/http-protection-2026-10-21.md). [Ảnh server](../evidence/server/README.md) |
| Demo client → app → ghi/đọc DB hosting | Ảnh xác nhận thêm ID 9, sửa 45.000 → 50.000đ và SELECT cùng ID/giá/trạng thái 0; cột trạng thái web chưa hiện trong ảnh |

Kiểm tra CI có thể chứng minh source hoạt động trong môi trường test. Nó **không chứng minh** đã dựng VPS/VM, không phải link website và chưa phải CI/CD build–test–deploy của Week 2.

## Chạy riêng validation trên máy có PHP

```bash
php tests/validation.php
```

Để chạy integration ngoài CI, dùng checkout sạch, database test riêng với quyền tạo bảng và CRUD, khai báo `TEST_DB_HOST`, `TEST_DB_PORT`, `TEST_DB_NAME`, `TEST_DB_USER`, `TEST_DB_PASSWORD`, rồi chạy `RUN_INTEGRATION_TESTS=1 python3 tests/integration.py`. Không chạy bằng database triển khai và không chụp/in các biến chứa mật khẩu.
