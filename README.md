# DevopQLYFood · Bếp Nhà

Website quản lý thực đơn quán ăn dùng PHP và MySQL/MariaDB. Một repository cho đồ án DevOps 4 tuần.

**Website live:** [khotrick.org/DevopQLYFood/public/](https://khotrick.org/DevopQLYFood/public/).

**Week 2 đã có bộ đầu ra kỹ thuật và minh chứng:** Docker/Compose, DB persistence, pipeline tự động và ảnh trước/sau. [Run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505) từ push source commit `d18f6be` có ba job success: **build/test → deploy image staging → deploy cPanel**. [Bộ minh chứng](evidence/week2/completion-2026-10-28.md) · [Checklist](docs/WEEK2_CHECKLIST.md) · [Kịch bản một người trình bày](docs/WEEK2_DEMO.md).

Docker staging chạy tạm trên một GitHub runner mới, tải đúng image đã test và so image ID trước khi chạy. Website thật dùng PHP/DB trực tiếp trên cPanel. Dữ liệu staging tách khỏi DB hosting và stack tạm được dọn sau run. [Sơ đồ Week 2](docs/WEEK2_ARCHITECTURE.md).

## Chạy và cập nhật

| Việc cần làm | Hướng dẫn |
|---|---|
| Cài app/DB lần đầu trên cPanel | [CPANEL_SETUP.md](docs/CPANEL_SETUP.md) |
| Cấu hình FTPS, chạy và xử lý lỗi pipeline | [CICD_SETUP.md](docs/CICD_SETUP.md) |
| Chạy app + MariaDB bằng Compose | [DOCKER_SETUP.md](docs/DOCKER_SETUP.md) |
| Xem test và phạm vi kiểm chứng | [VERIFICATION.md](docs/VERIFICATION.md) |

Sau khi hosting và GitHub Secrets/Variables đã được cấu hình, push source lên main sẽ chạy tests, deploy staging rồi cập nhật cPanel bằng FTPS có xác minh TLS. Pipeline kiểm tra health/revision và tự chụp ảnh. Config hosting, SQL, DB và `.htaccess` không nằm trong gói cập nhật; thay schema cần kế hoạch và backup riêng.

Để chạy Compose trên máy có Docker và Python 3:

```bash
python3 tools/setup-containers.py
docker compose config --quiet
docker compose up -d --build --wait --wait-timeout 180
```

Mở `http://127.0.0.1:8080/`, username mặc định `admin`; mật khẩu ngẫu nhiên trong `.secrets/admin_password`, đọc riêng trên máy. Không xóa named volume với dữ liệu cần giữ. [Các bước đầy đủ](docs/DOCKER_SETUP.md).

Cấu hình cPanel dùng `config/config.local.php` từ file mẫu; không commit config thật, `.env`, `.secrets/`, passwords hoặc tokens.

## Ứng dụng và source

App có đăng nhập quản trị, xem/tìm/lọc, thêm/sửa/xóa món và đổi còn/hết. Chưa bao gồm đặt món, thanh toán hoặc giao hàng. Runtime cần PHP 8.2 trở lên, pdo_mysql, mbstring và session; CSS/JS thuần, không cần npm/Composer. Database có bảng dishes; import [schema](database/schema.sql), [seed](database/seed.sql) tùy chọn.

| Đường dẫn | Vai trò |
|---|---|
| `app/`, `public/` | App PHP, assets, health/version endpoints |
| `config/config.example.php` | Mẫu cấu hình không có credentials thật |
| `database/` | Schema và seed |
| `Dockerfile`, `docker-compose.yml`, `docker/` | Image, app/DB, network, secrets và volume |
| `tools/` | Setup, package/deploy cPanel và chụp minh chứng |
| `.github/workflows/`, `tests/` | Source checks MySQL; build/test/deploy Docker staging và cPanel |
| `docs/`, `evidence/` | Hướng dẫn, checklist và minh chứng theo tuần |

## Tiến độ bốn tuần

| Tuần | Trạng thái và tài liệu |
|---|---|
| Week 1 | App/DB và ảnh hosting cũ đã có; OS/user/SSH/firewall còn thiếu. [Checklist](docs/WEEK1_CHECKLIST.md) · [Evidence](evidence/week1/README.md) · [Kịch bản](docs/WEEK1_PRESENTATION.md) |
| Week 2 | Bộ đầu ra đã kiểm chứng: image staging, persistence, CI/CD cPanel, test fail và ảnh trước/sau. [Evidence](evidence/week2/README.md) |
| Week 3 | Monitoring, bảo mật, backup/restore chưa triển khai. [Checklist](docs/WEEK3_CHECKLIST.md) |
| Week 4 | Demo tổng thể, xử lý sự cố và báo cáo chưa thực hiện. [Checklist](docs/WEEK4_CHECKLIST.md) |

[Lộ trình](docs/ROADMAP.md) · [Chỉ mục minh chứng](evidence/README.md) · [Phân công](docs/TEAM.md).

Minh chứng Week 1 thuộc cp1/domain cũ, không dùng để suy ra thông số hosting mới. [Kiến trúc Week 1](docs/ARCHITECTURE.md) và [sơ đồ đã lưu](evidence/week1/architecture.png) được giữ ở checkpoint đó.

## Thành viên và nguồn

Lê Đình Nhật Huy, Nguyễn Phước Hải, Lê Quang Tùng. Source được khởi tạo cho đề tài với hỗ trợ AI; nhóm cần đọc hiểu, kiểm tra, triển khai và ghi đóng góp thật. [Phạm vi và nguồn](docs/SCOPE_AND_SOURCE.md) · [Phân công theo tuần](docs/TEAM.md).
