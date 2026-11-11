# DevopQLYFood · Bếp Nhà

Website quản lý thực đơn quán ăn dùng PHP và MySQL/MariaDB. Một repository cho đồ án DevOps 4 tuần.

**Website live:** [khotrick.org/DevopQLYFood/public/](https://khotrick.org/DevopQLYFood/public/).

## Bổ sung Week 4

[Trang Week 4 riêng](https://khotrick.org/DevopQLYFood/public/week4.php) · [Hồ sơ cuối kỳ](evidence/week4/README.md) · [Báo cáo](docs/WEEK4_TECHNICAL_REPORT.md) · [Checklist](docs/WEEK4_CHECKLIST.md).

Week 3 giữ nguyên để tiếp tục báo cáo: [demo cũ](https://khotrick.org/DevopQLYFood/public/demo.php), [kịch bản Week 3](docs/WEEK3_DEMO.md), [source đóng mốc](https://github.com/nathyu-le/DevopQLYFood/tree/week3-preserved). Week 4 thêm trang và tài liệu riêng. Đợt rà soát 11/11 sửa lỗi giữ backup mới trong thư viện dùng chung; giao diện và luồng demo Week 3 không đổi. [Kiến trúc cuối](docs/FINAL_ARCHITECTURE.md) · [Cài mới](docs/FINAL_DEPLOYMENT.md) · [Vận hành](docs/OPERATIONS.md) · [Kịch bản Week 4](docs/WEEK4_DEMO.md) · [Rà soát 11/11](evidence/week4/audit-2026-11-11.md).

Các mục bên dưới là lịch sử checkpoint Week 1–3. Việc trình bày, xác nhận đóng góp cá nhân và nộp bài do nhóm thực hiện; phần hạ tầng live chưa xác minh vẫn được ghi rõ.


**Week 3 ngày 4/11/2026:** đã có Ops Monitor, alert/logs, backup/restore và security/playbook; bài thực nghiệm staging đã kiểm chứng bằng ảnh và dữ liệu thật. Đã cập nhật source Week 3 lên cPanel bằng upload thủ công; kiểm tra lúc 11:20 ngày 04/11/2026 xác nhận health OK, revision d690bc0 và 7 kiểm tra đường dẫn HTTP đều qua. Đã xác minh dashboard, cập nhật số liệu, lọc log và tạo backup bằng phiên quản trị trên live lúc 11:34 ngày 04/11/2026. CPU/RAM live là số liệu OS có thể dùng chung, không phải quota riêng của tài khoản. Pipeline FTPS đã được khôi phục trong [run 41785933275](https://github.com/nathyu-le/DevopQLYFood/actions/runs/41785933275): cả ba job thành công; source `a0e5914`, health/revision và 7 kiểm tra HTTP đều qua. Xác minh TLS vẫn bật trên cả hai kênh FTP. [Minh chứng](evidence/week3/README.md) · [Checklist](docs/WEEK3_CHECKLIST.md) · [Demo một người](docs/WEEK3_DEMO.md).

[Bộ checkpoint Week 2](evidence/week2/completion-2026-10-28.md) và minh chứng Week 1 được giữ riêng theo ngày/source commit.

Docker staging chạy tạm trên một GitHub runner mới, tải đúng image đã test và so image ID trước khi chạy. Website thật dùng PHP/DB trực tiếp trên cPanel. Dữ liệu staging tách khỏi DB hosting và stack tạm được dọn sau run. [Sơ đồ Week 2](docs/WEEK2_ARCHITECTURE.md).

**Demo trực tiếp Week 3 trên cPanel:** [mở trang demo](https://khotrick.org/DevopQLYFood/public/demo.php) để bấm tạo món thử → backup/tải file → xóa → restore → tạo lỗi → phục hồi kết nối. Trang này dùng database SQLite thử riêng trên cùng hosting. [Kịch bản 6 bước](docs/WEEK3_DEMO.md).

## Chạy và cập nhật

| Việc cần làm | Hướng dẫn |
|---|---|
| Cài app/DB lần đầu trên cPanel | [CPANEL_SETUP.md](docs/CPANEL_SETUP.md) |
| Cấu hình FTPS, chạy và xử lý lỗi pipeline | [CICD_SETUP.md](docs/CICD_SETUP.md) |
| Chạy app + MariaDB bằng Compose | [DOCKER_SETUP.md](docs/DOCKER_SETUP.md) |
| Xem test và phạm vi kiểm chứng | [VERIFICATION.md](docs/VERIFICATION.md) |
| Giám sát vận hành | [Ops Monitor](monitoring/README.md) |
| Backup restore và xử lý sự cố | [Backup](docs/BACKUP_RESTORE.md) · [Playbook](docs/TROUBLESHOOTING.md) |

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
| `tools/` | Setup, package/deploy cPanel, uptime và chụp minh chứng |
| `monitoring/` | Cấu hình/nguồn đo và firewall staging |
| `.github/workflows/`, `tests/` | Source checks MySQL; build/test/deploy Docker staging và cPanel |
| `docs/`, `evidence/` | Hướng dẫn, checklist và minh chứng theo tuần |

## Tiến độ bốn tuần

| Tuần | Trạng thái và tài liệu |
|---|---|
| Week 1 | App/DB và ảnh hosting cũ đã có; OS/user/SSH/firewall còn thiếu. [Checklist](docs/WEEK1_CHECKLIST.md) · [Evidence](evidence/week1/README.md) · [Kịch bản](docs/WEEK1_PRESENTATION.md) |
| Week 2 | Bộ đầu ra đã kiểm chứng: image staging, persistence, CI/CD cPanel, test fail và ảnh trước/sau. [Evidence](evidence/week2/README.md) |
| Week 3 | Staging đã kiểm chứng; source đã lên cPanel bằng upload thủ công và qua health/revision/7 kiểm tra HTTP. Dashboard sau đăng nhập, lọc log và tạo backup trên hosting đã kiểm tra thành công. [Checklist](docs/WEEK3_CHECKLIST.md) · [Evidence](evidence/week3/README.md) |
| Week 4 | Đã bổ sung trang riêng, hướng dẫn tái tạo, báo cáo/playbook/kịch bản và kiểm tra cuối kỳ. [Hồ sơ](evidence/week4/README.md) · [Checklist](docs/WEEK4_CHECKLIST.md) |

[Lộ trình](docs/ROADMAP.md) · [Chỉ mục minh chứng](evidence/README.md) · [Phân công](docs/TEAM.md).

Minh chứng Week 1 thuộc cp1/domain cũ, không dùng để suy ra thông số hosting mới. [Kiến trúc Week 1](docs/ARCHITECTURE.md) và [sơ đồ đã lưu](evidence/week1/architecture.png) được giữ ở checkpoint đó.

## Thành viên và nguồn

Lê Đình Nhật Huy, Nguyễn Phước Hải, Lê Quang Tùng. Source được khởi tạo cho đề tài với hỗ trợ AI; nhóm cần đọc hiểu, kiểm tra, triển khai và ghi đóng góp thật. [Phạm vi và nguồn](docs/SCOPE_AND_SOURCE.md) · [Phân công theo tuần](docs/TEAM.md).
