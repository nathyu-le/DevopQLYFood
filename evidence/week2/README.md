# Minh chứng Week 2 — Docker, staging và cPanel

**Bộ checkpoint đã kiểm chứng ngày 28/10/2026.** [Xem bộ đầy đủ](completion-2026-10-28.md) · [Log chọn lọc](completion-2026-10-28.log) · [Kịch bản một người](../../docs/WEEK2_DEMO.md).

[Run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505), event push, source `d18f6be9b79e1ede812d5450cb34cd8ec4e0e70a`, attempt 1: cả ba job success.

| Nội dung | Kết quả và file |
|---|---|
| Build/test | Dockerfile/Compose qua; 14 validation, regression và 17 container checks |
| Deploy image | Runner staging mới tải artifact và so [image ID](tested-image-id-2026-10-28.txt), chạy --no-build |
| Persistence | 17 checks staging qua; [cùng bản ghi trước/sau recreate](persistence-2026-10-28.json) |
| Ảnh container | [Staging login](screenshots/staging-login.png), [metadata](screenshots/staging-check.json) |
| cPanel tự cập nhật | FTPS và health/revision đúng SHA; [ảnh trước](screenshots/live-before-login.png), [ảnh sau](screenshots/live-after-login.png) |
| Test fail thật | [Run 40635078470](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40635078470) fail validation, hai deploy skipped; PR #1 đóng không merge |
| Hướng dẫn chạy | [Docker setup](../../docs/DOCKER_SETUP.md), [CI/CD setup](../../docs/CICD_SETUP.md) |

Ảnh Chrome và JSON được tải trực tiếp từ artifacts run trên rồi đưa vào Git. Screenshot không chỉnh sửa, không đăng nhập admin. Source/image giữ 7 ngày và log/ảnh artifacts giữ 14 ngày theo workflow; các file checkpoint trong Git vẫn xem được sau đó.

## Phạm vi bộ nộp

Bộ checkpoint dùng run thành công và run validation lỗi được liên kết phía trên. Bộ kết quả cuối thay các bản log cấu hình ban đầu; có thể xem lại các lần thử trước trong lịch sử Git/GitHub Actions. Terminal cPanel từng trả docker command not found, vì vậy image được deploy ở staging tạm theo [kiến trúc](../../docs/WEEK2_ARCHITECTURE.md).

Staging tạm không có URL public lâu dài; live chạy PHP/DB trực tiếp. JSON persistence thuộc staging, không phải cPanel. Ảnh đề Week 2 không yêu cầu Docker phải chạy ngay trên cPanel; [checklist](../../docs/WEEK2_CHECKLIST.md) đã sửa diễn giải cũ và đối chiếu từng mục. OS/user/SSH/firewall Week 1 và Week 3–4 vẫn theo checklist riêng.
