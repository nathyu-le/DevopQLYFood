# Lộ trình DevopQLYFood — 4 tuần

## Một repository cho toàn bộ dự án

Cả Week 1–4 sử dụng [nathyu-le/DevopQLYFood](https://github.com/nathyu-le/DevopQLYFood). Source được phát triển tiếp trên cùng ứng dụng; tài liệu và minh chứng được cập nhật theo checkpoint. Không tạo bốn bản ứng dụng độc lập.

Hiện đang triển khai **Week 2**; Docker/Compose và pipeline đã được bổ sung, hướng hiện tại ngày 28/10/2026 là gói cPanel mới có Terminal theo người triển khai; cần xác minh Docker/Compose/quyền daemon. Docker đã kiểm tra trên CI, phương án FTPS đã chuẩn bị, không triển khai VPS. [Hướng dẫn Docker](DOCKER_SETUP.md) · [CI/CD](CICD_SETUP.md) · [Minh chứng](../evidence/week2/README.md).

Kết quả **Week 1**: Source PHP/MySQL, tài liệu và phân công đã có; kiểm tra source trên GitHub Actions đã thành công. Website đã có log HTTP 200 và [3 ảnh thêm/sửa/SELECT cùng bản ghi ID 9](../evidence/application/demo-2026-10-21.md) trên hosting cPanel; đã có ảnh server/domain và sơ đồ hosting, có PHP 8.2/quota và log chặn HTTP; OS/user/SSH/firewall tạm hoãn bổ sung khi chọn môi trường Week 2 vì cp1 không hỗ trợ Terminal theo người triển khai. Checklist Week 2–4 đã được đối chiếu với ba ảnh đề chi tiết ngày 21/10/2026; kết quả thực hiện mới được cập nhật trong checklist và evidence tương ứng.

Nguồn đối chiếu: đề Week 1 đã cung cấp trước đó; ảnh Week 2 `1000003653.jpg`, Week 3 `1000003654.jpg`, Week 4 `1000003655.jpg`. Các tên ảnh dùng để nhận diện đề đã đọc, không phải đường dẫn file đang lưu trong repo. Rubric và hồ sơ nộp cụ thể nằm trong checklist từng tuần.

## Các checkpoint

| Tuần | Trọng tâm theo đề | Đầu ra chính | Điểm tối đa / 10 | Trạng thái |
|---|---|---|---:|---|
| Week 1 | Infrastructure & Initial Deployment | Kiến trúc, server/network, app + DB chạy, README và phân công | 2,0 | Có minh chứng thêm/sửa và SELECT trên cPanel; có ảnh server/domain và sơ đồ hosting; có PHP 8.2/quota và log chặn HTTP; OS/user/SSH/firewall tạm hoãn bổ sung |
| Week 2 | Containerization & CI/CD | Dockerfile, Compose, persistent volume, build–test–deploy từ commit mới | 2,5 | Đã kiểm tra Docker trên runner; chuẩn bị deploy source lên cPanel, chưa kết nối FTPS |
| Week 3 | Monitoring, Security & Reliability | Logs theo thời gian, dashboard CPU/RAM/health, alert đã thử, bảo mật, backup/restore, playbook draft ≥2 sự cố | 2,5 | Chưa bắt đầu triển khai |
| Week 4 | Final Demo & Troubleshooting | Bản tích hợp Week 1–3, triển khai từ server sạch, release/tag, playbook ≥3 sự cố, paper và video | 3,0 | Chưa thực hiện |

## Mỗi tuần cập nhật ở đâu?

| Nội dung | Đường dẫn |
|---|---|
| Tổng quan hiện tại và link nộp | `README.md` |
| Thành viên và phân công theo tuần | `docs/TEAM.md` |
| Theo dõi Week 1 | `docs/WEEK1_CHECKLIST.md` và `evidence/week1/` |
| Theo dõi Week 2 | `docs/WEEK2_CHECKLIST.md` và `evidence/week2/` |
| Theo dõi Week 3 | `docs/WEEK3_CHECKLIST.md` và `evidence/week3/` |
| Theo dõi Week 4 | `docs/WEEK4_CHECKLIST.md` và `evidence/week4/` |
| Source ứng dụng dùng chung | `app/`, `public/`, `config/`, `database/` |
| Kiểm tra source / CI | `tests/`, `.github/workflows/` |
| Sơ đồ và hướng dẫn triển khai | `architecture.png`, `docs/ARCHITECTURE.md`, `docs/MANUAL_SETUP.md` |

Minh chứng Week 1 đã có chỗ dành cho `evidence/server/` và `evidence/application/`; `evidence/week1/README.md` dẫn đến hai thư mục đó để giữ nguyên các link hiện tại.

## Cấu hình theo các tuần

Week 2 đã có cấu hình dưới đây; Week 3–4 vẫn là vị trí dự kiến:

| Checkpoint | Cấu hình / công cụ dự kiến bổ sung |
|---|---|
| Week 2 | `Dockerfile`, `docker-compose.yml`, `.dockerignore`, workflow build–test–deploy trong `.github/workflows/` |
| Week 3 | Cấu hình monitoring trong `monitoring/`; script backup/restore trong `tools/`; `docs/TROUBLESHOOTING.md` với ít nhất 2 sự cố |
| Week 4 | Nâng playbook lên ít nhất 3 sự cố; báo cáo trong `report/`; deployment/operations guide hoàn chỉnh; release/tag và video trong chỉ mục `evidence/week4/` |

Cấu hình có thể chuẩn bị trước khi chọn server; chỉ ghi triển khai hoàn tất khi chạy và có minh chứng. Workflow **Source checks (not deployment)** vẫn kiểm tra source với DB tạm. Workflow **Week 2 - Docker checks and cPanel deploy** bổ sung kiểm tra Docker và cấu hình FTPS lên cPanel; deploy đang tắt. [Kết quả thực tế](../evidence/week2/README.md).

## Điều kiện chung khi nộp và demo

- Nộp link và minh chứng đúng hạn theo thông báo của giảng viên; kế hoạch bổ sung không tự thay đổi hạn nộp.
- Hệ thống và đường dẫn demo truy cập được; repository và các file minh chứng đã cập nhật.
- Mỗi thành viên hiểu hệ thống và giải thích được phần phụ trách.
- Không công khai mật khẩu, access key, token hoặc dữ liệu nhạy cảm trong source, logs và video.
- Có video, ảnh hoặc logs dự phòng nếu mạng gặp vấn đề. Week 3 chỉ dùng dữ liệu thử nghiệm khi mô phỏng xóa để thử restore.

## Cách cập nhật sau mỗi đầu việc

1. Chỉnh source/config/tài liệu liên quan trong cùng repo.
2. Chạy kiểm tra phù hợp với thay đổi, lưu kết quả thật.
3. Commit theo nội dung công việc; không sửa tác giả hoặc thời điểm để giả lập đóng góp.
4. Đặt ảnh/log hoặc link video vào thư mục evidence của tuần, có ngày, môi trường và người thực hiện.
5. Đánh dấu checklist khi đã có kết quả và minh chứng đáp ứng yêu cầu.
6. Cập nhật trạng thái và link ở README, điều chỉnh phân công trong `TEAM.md`; ghi người thực hiện cùng minh chứng của từng đầu việc.

Khi nộp checkpoint, ghi thêm commit SHA hoặc link commit trong chỉ mục minh chứng. Nhờ vậy giảng viên vẫn xem lại đúng bản Week 1 sau khi repo tiếp tục phát triển ở Week 2–4. Chỉ tạo tag checkpoint hoàn thành sau khi nhóm kiểm tra đủ sản phẩm; chưa gắn tag hoàn tất Week 1 ở thời điểm hiện tại.

## Chuyển từ Week 1 sang Week 2

Quyết định ngày 28/10/2026: dùng gói cPanel mới đã mua, có Terminal theo người triển khai, không triển khai VPS. Docker/Compose kiểm tra trên Actions; source của đúng commit đã qua test được đóng gói để deploy FTPS lên hosting PHP/MariaDB khi gói hosting hỗ trợ và kết nối được cấu hình; trước tiên kiểm tra khả năng chạy Docker trực tiếp trên Terminal gói mới. [Kế hoạch hiện tại](DEPLOYMENT_PLAN.md) · [CI/CD](CICD_SETUP.md).

Giữ minh chứng app/DB Week 1; OS/user/SSH/firewall chưa hoàn tất. Hiện chưa chứng minh container chạy trên server triển khai. Nếu rubric yêu cầu điều đó, phải ghi giới hạn và xin giảng viên xác nhận cách đánh giá; không suy ra Docker trên runner đã thay thế tiêu chí server. Không tự đánh dấu đủ Week 2 khi chỉ có CI xanh.
