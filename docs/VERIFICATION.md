# Kiểm tra và giới hạn minh chứng

## Week 2: Docker và gói source cPanel

[Run 40604164827](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40604164827), commit `145a500655e02178bdec386ed608f7d460310ebb`: build/Compose/PHP lint, 14 validation checks, 17 container checks, regression và kiểm tra gói source thành công. [Chi tiết, artifacts và log](../evidence/week2/README.md).

Container tests dùng Apache/PHP và MariaDB trên runner, có thao tác HTTP và PDO thực tế. Cả app/DB được tạo lại, cùng bản ghi còn trong named volume. Một source copy bị cố ý nới giới hạn giá phải fail đúng assertion; copy bị lỗi không được dùng làm image triển khai.

`tests/cpanel_release.py` kiểm tra gói source không chứa config/secrets/DB/.htaccess; từ chối sai SHA và đường dẫn archive không an toàn. `deploy-cpanel` có dependency vào job kiểm tra; nay đã chạy thành công trong [run 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900), event push, commit `6583790`, attempt 1. [UI/revision trước sau và public checks](../evidence/week2/deploy-2026-10-28.md).

Người triển khai chọn gói cPanel mới có Terminal; lệnh Docker trả command not found. Đã deploy source PHP/DB trực tiếp qua FTPS, chưa chứng minh container trên hosting. [Lệnh kiểm tra và CI/CD setup](CICD_SETUP.md).

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
