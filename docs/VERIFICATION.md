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

Sau khi source được đưa lên GitHub, mở [Actions](https://github.com/nathyu-le/DevopQLYFood/actions), chọn workflow **Source checks (not deployment)**, mở từng step và kiểm tra kết quả của đúng commit. Chỉ ghi “passed” sau khi run đã hoàn tất thành công.

Ở thời điểm đóng gói bản chuẩn bị: kiểm tra cú pháp JavaScript bằng `node --check` và cú pháp hai script Python bằng `py_compile` đã thành công. Môi trường soạn chưa có PHP/MySQL nên chưa thực thi PHP hoặc bài kiểm tra CRUD. Workflow được chuẩn bị để chạy các kiểm tra đó sau khi có quyền ghi repository; chưa có kết quả Actions để báo là passed.

| Loại kiểm chứng | Trạng thái / nơi đối chiếu |
|---|---|
| JavaScript syntax / Python syntax | Đã kiểm tra thành công trong môi trường soạn |
| PHP syntax và validation | Chưa thực thi; xem Actions sau khi upload source |
| CRUD với MySQL tạm | Chưa thực thi; xem step “Check real CRUD with an isolated MySQL database” sau khi upload |
| Server của nhóm | Chưa triển khai, chưa có minh chứng |
| Apache/SSH/firewall của nhóm | Chưa cấu hình, chưa có minh chứng |
| Demo từ client đến server nhóm | Chưa thực hiện |

Kiểm tra CI có thể chứng minh source hoạt động trong môi trường test. Nó **không chứng minh** đã dựng VPS/VM, không phải link website và chưa phải CI/CD build–test–deploy của Week 2.

## Chạy riêng validation trên máy có PHP

```bash
php tests/validation.php
```

Để chạy integration ngoài CI, dùng checkout sạch, database test riêng với quyền tạo bảng và CRUD, khai báo `TEST_DB_HOST`, `TEST_DB_PORT`, `TEST_DB_NAME`, `TEST_DB_USER`, `TEST_DB_PASSWORD`, rồi chạy `RUN_INTEGRATION_TESTS=1 python3 tests/integration.py`. Không chạy bằng database triển khai và không chụp/in các biến chứa mật khẩu.
