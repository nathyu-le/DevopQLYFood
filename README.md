# DevopQLYFood · Website quản lý thực đơn quán ăn

**DevOps Final Project 2026 · 4 tuần · Week 2: bộ đầu ra đã kiểm chứng**

Ứng dụng **Bếp Nhà** dùng PHP và MySQL/MariaDB để quản trị thực đơn. Cả bốn tuần tiếp tục phát triển trên cùng repository.

**Cập nhật 28/10/2026:** đã kiểm chứng luồng **push main → build/test → lưu image → deploy image vào staging tạm → deploy source lên cPanel → health/revision và ảnh trước/sau**. [Run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505) có cả ba job thành công ngay attempt 1. [Bộ minh chứng đầy đủ](evidence/week2/completion-2026-10-28.md) · [Kịch bản một người trình bày](docs/WEEK2_DEMO.md).

Docker staging chạy trên một runner mới, tải đúng image đã test và đối chiếu image ID trước khi chạy Compose. Website thật tiếp tục dùng PHP/DB trực tiếp trên **cPanel**; staging được dọn sau run. Không cần chuyển sang VPS để chạy luồng hiện tại.

| Tuần | Nội dung | Tiến độ |
|---|---|---|
| Week 1 | Triển khai app/DB ban đầu | Có ảnh thêm/sửa/SELECT và logs hosting; OS/user/SSH/firewall còn thiếu, xem [checklist](docs/WEEK1_CHECKLIST.md) |
| Week 2 | Docker/Compose và CI/CD | CI/CD cPanel đã chạy; image deploy vào staging tạm, có persistence, ảnh trước/sau và run test lỗi |
| Week 3 | Monitoring, bảo mật, backup/restore | Chưa triển khai, xem [checklist](docs/WEEK3_CHECKLIST.md) |
| Week 4 | Demo tổng thể, xử lý sự cố, báo cáo | Chưa thực hiện, xem [checklist](docs/WEEK4_CHECKLIST.md) |

**Nhóm:** Lê Đình Nhật Huy, Nguyễn Phước Hải, Lê Quang Tùng. [Phân công và việc cần nhóm kiểm tra](docs/TEAM.md).

## Week 2: mở các phần này

| Nội dung | Link |
|---|---|
| Chạy app + DB bằng Compose | [Docker setup](docs/DOCKER_SETUP.md) |
| Cấu hình và vận hành pipeline cPanel | [CI/CD setup](docs/CICD_SETUP.md) |
| Đối chiếu yêu cầu Week 2 | [Week 2 checklist](docs/WEEK2_CHECKLIST.md) |
| Kết quả thực tế, logs và artifacts | [Evidence Week 2](evidence/week2/README.md) |
| Hướng tiếp tục | [Deployment plan](docs/DEPLOYMENT_PLAN.md) · [Lộ trình 4 tuần](docs/ROADMAP.md) |

[Run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505), source commit `d18f6be9b79e1ede812d5450cb34cd8ec4e0e70a`: **14 validation checks**, **17 container checks** ở CI, regression và kiểm tra gói source qua; staging chạy lại **17 container checks**, giữ cùng bản ghi qua recreate; **deploy-cpanel success** và revision đúng SHA. [Source checks cùng commit](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997549) cũng success.

[Run thử lỗi thật](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40635078470) fail tại assertion từ chối giá `1000000000`; hai job deploy bị skipped. [PR #1](https://github.com/nathyu-le/DevopQLYFood/pull/1) đã đóng, không merge. [Ảnh trước/sau và dữ liệu persistence](evidence/week2/completion-2026-10-28.md) đã lưu trong repo, dùng được khi artifacts hết hạn.

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

**Website mới:** [khotrick.org/DevopQLYFood/public/](https://khotrick.org/DevopQLYFood/public/). Đã xác minh từ ngoài runner: login/health/version/CSS/JS HTTP 200; health `ok`, revision khớp source commit `d18f6be`, dòng mô tả mới xuất hiện và có ảnh Chrome sau deploy. [Kiến trúc Week 2](docs/WEEK2_ARCHITECTURE.md).

Bản Week 1: [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/). URL này không chứng minh bản pipeline mới đã được upload hoặc đang chạy Docker.

[CPANEL_SETUP.md](docs/CPANEL_SETUP.md) hướng dẫn upload/config DB ban đầu. `config/config.example.php` chỉ là mẫu; **không commit `config/config.local.php`**, `.env` hoặc `.secrets/`. Đặt DocumentRoot vào `public/`; layout cPanel có thư mục cha cần giữ quy tắc bảo vệ trong `.htaccess` như hướng dẫn.

Pipeline FTPS chỉ upload runtime source của đúng commit đã kiểm tra; không đưa config DB/admin, DB hoặc `.htaccess` vào gói cập nhật. Kết nối đã được cấu hình bằng GitHub Variables/Secrets và chạy thành công. [Cách tái triển khai và xử lý lỗi](docs/CICD_SETUP.md).

Đã đối chiếu lại ảnh đề Week 2: cần Docker/Compose, persistence, CI/CD tự động, test fail và minh chứng trước/sau; ảnh đề không ghi bắt buộc Docker chạy ngay trên cPanel. Bộ đầu ra hiện đã có minh chứng cho các mục này. Khi trình bày, nói đúng staging tạm và cPanel là hai môi trường riêng. Các mục OS/user/SSH/firewall còn thiếu được giữ ở checklist Week 1; Week 3–4 chưa thực hiện.

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
| `.github/workflows/container-pipeline.yml` | Build/test, artifact, deploy image staging, deploy cPanel và ảnh tự động |
| `tools/capture-evidence.py` | Chụp Chrome thật, kiểm tra health/revision và ghi metadata |
| `tests/` | Validation, HTTP/MySQL, container và kiểm tra gói source |
| `docs/`, `evidence/` | Hướng dẫn, checklist và kết quả thật theo tuần |

Source dùng PDO prepared statements, CSRF, kiểm tra dữ liệu, escape HTML và session phù hợp. `health.php` trả tình trạng kết nối DB; `version.php` chỉ trả revision. Cấu hình hosting và minh chứng vận hành được kiểm tra riêng.

## Xem lại Week 1

[Kiến trúc đã ghi nhận](docs/ARCHITECTURE.md) · [Sơ đồ PNG](architecture.png) · [Minh chứng server](evidence/server/README.md) · [App/DB](evidence/application/demo-2026-10-21.md) · [Log bảo vệ HTTP](evidence/server/http-protection-2026-10-21.md) · [Demo](docs/DEMO.md) · [Lời trình bày một người](docs/WEEK1_PRESENTATION.md).

Ảnh/sơ đồ này ghi nhận môi trường cũ ở Week 1. Chưa dùng chúng làm bằng chứng thông số gói cPanel mới. [Chỉ mục toàn dự án](evidence/README.md).

## Nguồn và đóng góp

Source khởi tạo cho đề tài, có hỗ trợ AI khi chuẩn bị code và tài liệu. Nhóm cần đọc hiểu, kiểm tra, chỉnh sửa, triển khai và ghi đóng góp thật; commit không thay bằng chứng mỗi thành viên thực hiện công việc. [Scope và nguồn](docs/SCOPE_AND_SOURCE.md).

Tham khảo: [PHP PDO](https://www.php.net/manual/en/book.pdo.php), [password_hash](https://www.php.net/manual/en/function.password-hash.php), [PHP Docker image](https://github.com/docker-library/docs/blob/master/php/README.md), [Docker Compose](https://docs.docker.com/compose/), [cPanel FTP Accounts](https://docs.cpanel.net/cpanel/files/ftp-accounts/).
