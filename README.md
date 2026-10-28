# DevopQLYFood · Website quản lý thực đơn quán ăn

**DevOps Final Project 2026 · 4 tuần · Week 2 đang thực hiện**

Ứng dụng **Bếp Nhà** dùng PHP và MySQL/MariaDB để quản trị thực đơn. Cả bốn tuần tiếp tục phát triển trên cùng repository.

**Cập nhật 28/10/2026:** đã hoàn thành luồng **push main → build/test Docker → đóng gói source → FTPS deploy lên cPanel → kiểm tra health/revision**. [Run 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900) tự chạy từ commit `6583790`, cả hai job thành công ngay attempt 1; website đã hiện dòng đăng nhập mới và đúng SHA. [Minh chứng trước/sau](evidence/week2/deploy-2026-10-28.md).

Docker/Compose và DB volume được kiểm tra trên runner GitHub Actions. Trên hosting hiện tại, ba lệnh Docker trả `command not found`; website chạy PHP/DB trực tiếp. Chưa chứng minh container chạy trên hosting và chưa coi đó là tiêu chí đã được giảng viên chấp thuận thay thế. Giữ hướng cPanel theo quyết định của nhóm.

| Tuần | Nội dung | Tiến độ |
|---|---|---|
| Week 1 | Triển khai app/DB ban đầu | Có ảnh thêm/sửa/SELECT và logs hosting; OS/user/SSH/firewall còn thiếu, xem [checklist](docs/WEEK1_CHECKLIST.md) |
| Week 2 | Docker/Compose và CI/CD | CI/CD source lên cPanel đã chạy tự động và kiểm chứng trước/sau; Docker/persistence đã chứng minh trên runner, chưa chạy container trên hosting |
| Week 3 | Monitoring, bảo mật, backup/restore | Chưa triển khai, xem [checklist](docs/WEEK3_CHECKLIST.md) |
| Week 4 | Demo tổng thể, xử lý sự cố, báo cáo | Chưa thực hiện, xem [checklist](docs/WEEK4_CHECKLIST.md) |

**Nhóm:** Lê Đình Nhật Huy, Nguyễn Phước Hải, Lê Quang Tùng. [Phân công đã xác nhận](docs/TEAM.md).

## Week 2: mở các phần này

| Nội dung | Link |
|---|---|
| Chạy app + DB bằng Compose | [Docker setup](docs/DOCKER_SETUP.md) |
| Kiểm tra Terminal gói mới, cấu hình pipeline cPanel | [CI/CD setup](docs/CICD_SETUP.md) |
| Yêu cầu và mục chưa hoàn thành | [Week 2 checklist](docs/WEEK2_CHECKLIST.md) |
| Kết quả thực tế, logs và artifacts | [Evidence Week 2](evidence/week2/README.md) |
| Hướng tiếp tục | [Deployment plan](docs/DEPLOYMENT_PLAN.md) · [Lộ trình 4 tuần](docs/ROADMAP.md) |

[Run 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900), commit `6583790`: build/Compose/PHP lint, **14 validation checks**, **17 container checks**, regression và gói source qua; **deploy-cpanel success**. [Source checks cùng commit](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267716) cũng success. Xem [log và giới hạn](evidence/week2/README.md).

**Kết quả Terminal đã nhận:** `docker --version`, `docker compose version` và `docker info --format '{{.ServerVersion}}'` đều trả `docker: command not found`. Shell hiện không tìm được Docker trong PATH của tài khoản; chưa thể kết luận Docker được phép cài/chạy hoặc daemon khả dụng. [Bản ghi đã lược thông tin tài khoản](evidence/week2/terminal-check-2026-10-28.md).

FTPS đã được cấu hình và xác minh bằng run từ push. Tiếp tục dùng [hướng dẫn CI/CD](docs/CICD_SETUP.md) để tái triển khai và xử lý lỗi; Docker/Compose hiện chỉ được chứng minh trên runner, chưa có container trên hosting.

## Chức năng và phạm vi

| Mã | Chức năng |
|---|---|
| F01 | Đăng nhập quản trị bằng mật khẩu hash |
| F02 | Xem, tìm kiếm và lọc thực đơn |
| F03 | Thêm món với danh mục, giá nguyên VNĐ, mô tả và tình trạng |
| F04 | Sửa thông tin món |
| F05 | Đổi còn món / hết món |
| F06 | Xóa món có bước xác nhận |

Phạm vi chưa bao gồm đặt món, thanh toán, giao hàng hoặc nhiều vai trò. Dùng PHP 8.2 trở lên, `pdo_mysql`, `mbstring` và session; HTML/CSS/JavaScript thuần, không cần npm/Composer để chạy app. Database có bảng `dishes`, charset utf8mb4/InnoDB; schema ở [database/schema.sql](database/schema.sql), tám món mẫu tùy chọn ở [database/seed.sql](database/seed.sql).

## Chạy Docker

Cần Git, Python 3, Docker đang chạy và Compose plugin. Trong checkout repo:

```bash
python3 tools/setup-containers.py
docker compose config --quiet
docker compose up -d --build --wait --wait-timeout 180
```

Mở `http://127.0.0.1:8080/`. Username mặc định `admin`; mật khẩu nằm trong `.secrets/admin_password`, đọc trên máy riêng, không đưa vào ảnh/log. Setup tạo file ngẫu nhiên và giữ nguyên file đã có. Không chạy `down -v` hoặc xóa named volume với dữ liệu cần giữ. [Hướng dẫn đầy đủ](docs/DOCKER_SETUP.md).

Docker runtime và database tạm trên Actions tách khỏi database hosting; không tự chuyển hoặc thay dữ liệu cPanel.

## Deploy cPanel và cấu hình riêng

**Website mới:** [khotrick.org/DevopQLYFood/public/](https://khotrick.org/DevopQLYFood/public/). Đã xác minh từ ngoài runner: login/health/version/CSS/JS HTTP 200; health `ok`, revision khớp commit `6583790`, CSS/JS khớp source và dòng mô tả mới xuất hiện. [Kiến trúc Week 2](docs/WEEK2_ARCHITECTURE.md).

Bản Week 1: [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/). URL này không chứng minh bản pipeline mới đã được upload hoặc đang chạy Docker.

[CPANEL_SETUP.md](docs/CPANEL_SETUP.md) hướng dẫn upload/config DB ban đầu. `config/config.example.php` chỉ là mẫu; **không commit `config/config.local.php`**, `.env` hoặc `.secrets/`. Đặt DocumentRoot vào `public/`; layout cPanel có thư mục cha cần giữ quy tắc bảo vệ trong `.htaccess` như hướng dẫn.

Pipeline FTPS chỉ upload runtime source của đúng commit đã kiểm tra; không đưa config DB/admin, DB hoặc `.htaccess` vào gói cập nhật. Kết nối đã được cấu hình bằng GitHub Variables/Secrets và chạy thành công. [Cách tái triển khai và xử lý lỗi](docs/CICD_SETUP.md).

Nếu giảng viên yêu cầu container chạy trên máy triển khai, Docker trên runner và source PHP trực tiếp trên cPanel chưa tự thay thế được tiêu chí đó. Chưa đánh dấu hoàn tất Week 2.

## File chính trong repo

| Đường dẫn | Vai trò |
|---|---|
| `app/`, `public/` | PHP application, health/version endpoints, assets |
| `config/config.example.php` | Mẫu cấu hình cPanel không có credentials thật |
| `database/` | Schema và seed tùy chọn |
| `Dockerfile`, `docker-compose.yml`, `.dockerignore`, `docker/` | Runtime container, network, secrets, named volume |
| `tools/setup-containers.py` | Tạo config/secrets riêng, giữ file hiện có |
| `tools/package-cpanel.py`, `tools/deploy-cpanel.py` | Gói source có kiểm tra SHA và upload FTPS khi đã cấu hình |
| `.github/workflows/source-checks.yml` | Giữ bộ kiểm tra PHP/validation/HTTP với MySQL của Week 1 |
| `.github/workflows/container-pipeline.yml` | Docker build/test/persistence, artifact và deploy cPanel có điều kiện |
| `tests/` | Validation, HTTP/MySQL, container và kiểm tra gói source |
| `docs/`, `evidence/` | Hướng dẫn, checklist và kết quả thật theo tuần |

Source dùng PDO prepared statements, CSRF, kiểm tra dữ liệu, escape HTML và session phù hợp. `health.php` trả tình trạng kết nối DB; `version.php` chỉ trả revision. Cấu hình hosting và minh chứng vận hành được kiểm tra riêng.

## Xem lại Week 1

[Kiến trúc đã ghi nhận](docs/ARCHITECTURE.md) · [Sơ đồ PNG](architecture.png) · [Minh chứng server](evidence/server/README.md) · [App/DB](evidence/application/demo-2026-10-21.md) · [Log bảo vệ HTTP](evidence/server/http-protection-2026-10-21.md) · [Demo](docs/DEMO.md) · [Lời trình bày một người](docs/WEEK1_PRESENTATION.md).

Ảnh/sơ đồ này ghi nhận môi trường cũ ở Week 1. Chưa dùng chúng làm bằng chứng thông số gói cPanel mới. [Chỉ mục toàn dự án](evidence/README.md).

## Nguồn và đóng góp

Source khởi tạo cho đề tài, có hỗ trợ AI khi chuẩn bị code và tài liệu. Nhóm cần đọc hiểu, kiểm tra, chỉnh sửa, triển khai và ghi đóng góp thật; commit không thay bằng chứng mỗi thành viên thực hiện công việc. [Scope và nguồn](docs/SCOPE_AND_SOURCE.md).

Tham khảo: [PHP PDO](https://www.php.net/manual/en/book.pdo.php), [password_hash](https://www.php.net/manual/en/function.password-hash.php), [PHP Docker image](https://github.com/docker-library/docs/blob/master/php/README.md), [Docker Compose](https://docs.docker.com/compose/), [cPanel FTP Accounts](https://docs.cpanel.net/cpanel/files/ftp-accounts/).
