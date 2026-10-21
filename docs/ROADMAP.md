# Lộ trình DevopQLYFood — 4 tuần

## Một repository cho toàn bộ dự án

Cả Week 1–4 sử dụng [nathyu-le/DevopQLYFood](https://github.com/nathyu-le/DevopQLYFood). Source được phát triển tiếp trên cùng ứng dụng; tài liệu và minh chứng được cập nhật theo checkpoint. Không tạo bốn bản ứng dụng độc lập.

Hiện nhóm đang chuẩn bị **Week 1**. Source PHP/MySQL, tài liệu và phân công đã có; kiểm tra source trên GitHub Actions đã thành công. Triển khai server nhóm và minh chứng truy cập từ máy khách chưa hoàn thành. Week 2–4 dưới đây là kế hoạch theo brief chung, điều chỉnh tiếp khi có đề checkpoint chi tiết từ giảng viên.

## Các checkpoint

| Tuần | Trọng tâm theo đề | Đầu ra chính | Điểm theo brief chung | Trạng thái |
|---|---|---|---:|---|
| Week 1 | Infrastructure & Initial Deployment | Kiến trúc, server/network, app + DB chạy, README và phân công | 2,0 | Đang chuẩn bị; chưa đủ minh chứng triển khai |
| Week 2 | Containerization & CI/CD | Dockerfile, Compose, persistent volume, build–test–deploy từ commit mới | 2,5 | Chưa bắt đầu triển khai |
| Week 3 | Monitoring, Security & Reliability | Logs, CPU/RAM/trạng thái app, cảnh báo đã thử, bảo mật, backup/restore, playbook | 2,5 | Chưa bắt đầu triển khai |
| Week 4 | Final Demo & Troubleshooting | Demo end-to-end, xử lý sự cố, báo cáo, video và bảo vệ | 3,0 | Chưa thực hiện |

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

## Những file sẽ thêm ở các tuần sau

Các mục sau là **vị trí dự kiến**, chưa phải file cấu hình đã được thực hiện:

| Checkpoint | Cấu hình / công cụ dự kiến bổ sung |
|---|---|
| Week 2 | `Dockerfile`, `docker-compose.yml`, `.dockerignore`, workflow build–test–deploy trong `.github/workflows/` |
| Week 3 | Cấu hình monitoring trong `monitoring/`; công cụ backup/restore trong `tools/`; `docs/TROUBLESHOOTING.md` với ít nhất 3 tình huống |
| Week 4 | Báo cáo trong `report/`, tài liệu demo và link video trong `evidence/week4/` |

Chỉ tạo cấu hình thật sau khi chọn môi trường và triển khai tương ứng. Workflow **Source checks (not deployment)** hiện tại chỉ kiểm tra source với DB tạm, chưa hoàn thành CI/CD deploy Week 2.

## Cách cập nhật sau mỗi đầu việc

1. Chỉnh source/config/tài liệu liên quan trong cùng repo.
2. Chạy kiểm tra phù hợp với thay đổi, lưu kết quả thật.
3. Commit theo nội dung công việc; không sửa tác giả hoặc thời điểm để giả lập đóng góp.
4. Đặt ảnh/log hoặc link video vào thư mục evidence của tuần, có ngày, môi trường và người thực hiện.
5. Đánh dấu checklist khi đã có kết quả và minh chứng đáp ứng yêu cầu.
6. Cập nhật trạng thái và link ở README, điều chỉnh phân công trong `TEAM.md`; ghi người thực hiện cùng minh chứng của từng đầu việc.

Khi nộp checkpoint, ghi thêm commit SHA hoặc link commit trong chỉ mục minh chứng. Nhờ vậy giảng viên vẫn xem lại đúng bản Week 1 sau khi repo tiếp tục phát triển ở Week 2–4. Chỉ tạo tag checkpoint hoàn thành sau khi nhóm kiểm tra đủ sản phẩm; chưa gắn tag hoàn tất Week 1 ở thời điểm hiện tại.

## Chuyển từ Week 1 sang Week 2

Hoàn tất server, truy cập từ client và bằng chứng ghi/đọc DB của Week 1; ghi môi trường thực tế. Sau đó containerize cùng source này, cập nhật sơ đồ theo cấu trúc Docker, kiểm tra dữ liệu qua volume và xây dựng deployment tự động. Không coi việc có source hoặc có workflow test là đã triển khai thành công.
