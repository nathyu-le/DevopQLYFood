# Kiểm tra và giới hạn minh chứng

## Week 2: image staging và cPanel

[Run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505), source `d18f6be9b79e1ede812d5450cb34cd8ec4e0e70a`, event push, attempt 1: container-checks, deploy-staging và deploy-cpanel success. [Source checks cùng commit](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997549) success. [Ảnh/JSON/log và phạm vi](../evidence/week2/completion-2026-10-28.md).

CI build app image, lint PHP, chạy 14 validation checks và chứng minh bản copy cố ý làm sai validator bị test bắt. Stack Apache/PHP + MariaDB thực hiện 17 checks: HTTP, CSRF, validation, CRUD và recreate cả app/DB vẫn giữ cùng bản ghi trong volume.

Runner staging mới tải image artifact, đối chiếu ID đã xuất ở CI và chạy --no-build. Nó chạy lại 17 checks, lưu JSON persistence và ảnh Chrome thật. DB staging là dữ liệu thử riêng và được dọn sau job.

`tests/cpanel_release.py` xác minh gói không có config/secrets/SQL/.htaccess, đúng SHA, archive path an toàn và quyền 0644; HTTP gates từ chối status/health/revision sai. CPanel job cần hai job trên thành công; dùng FTPS có xác minh TLS, kiểm tra health/revision và ảnh sau deploy. Website thật dùng PHP/DB trực tiếp.

**Thử lỗi ở workflow thật:** [run 40635078470](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40635078470) từ PR có sửa validator để chấp nhận giá sai; validation báo `FAIL: reject invalid price: 1000000000` và exit 1. Hai job deploy skipped. PR cũng có branch guard không deploy; dependency test bảo vệ main. [PR #1](https://github.com/nathyu-le/DevopQLYFood/pull/1) đã đóng, không merge, source live giữ bản hợp lệ.

Public health xác nhận kết nối/truy vấn bảng dishes; bộ screenshot anonymous không đăng nhập admin và không sửa dữ liệu cPanel. Persistence JSON không phải chứng minh recreate DB cPanel. Phần OS/user/SSH/firewall Week 1 chưa được hoàn tất bởi các tests trên.

## Week 1: bộ kiểm tra source giữ lại

`.github/workflows/source-checks.yml` lint PHP, chạy validation và `tests/integration.py` với PHP development server/MySQL tạm. Bộ test chỉ chạy khi `RUN_INTEGRATION_TESTS=1`, tên DB kết thúc `_test`; từ chối ghi đè cấu hình có sẵn và dọn bản ghi thử sau khi chạy. Không dùng database triển khai.

[Run 39529399926](https://github.com/nathyu-le/DevopQLYFood/actions/runs/39529399926), commit `4baaaa1da776c714098e35ccc5e275c5495d7170`: PHP syntax hợp lệ, 14 validation và 19 HTTP/MySQL checks thành công. [Run 40604164849](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40604164849) của bản Week 2 cũng thành công.

Minh chứng app/DB và hosting Week 1 được giữ tại [evidence/week1](../evidence/week1/README.md). Kết quả CI không thay cho cấu hình OS/user/SSH/firewall hoặc truy cập môi trường triển khai.

## Chạy test

Trên máy có PHP phù hợp:

```bash
php tests/validation.php
```

Test integration cần checkout sạch và database test riêng, các biến `TEST_DB_HOST`, `TEST_DB_PORT`, `TEST_DB_NAME`, `TEST_DB_USER`, `TEST_DB_PASSWORD`. Không in credentials hoặc dùng DB hosting đang chạy.

Container tests chỉ dành cho stack Compose tạm có hậu tố `_ci` và `RUN_CONTAINER_TESTS=1`; workflow tự khởi tạo/dọn stack đó. Không chạy script recreate/cleanup CI với DB cần giữ. Xem [Docker setup](DOCKER_SETUP.md).
