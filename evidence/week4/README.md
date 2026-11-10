# Hồ sơ cuối kỳ Week 4

Mốc source cuối kỳ và kết quả pipeline được ghi tại [final-verification.md](final-verification.md). Chỉ ghi thành công khi run, health và revision được đối chiếu.

## Link trình bày trực tiếp

- [Website](https://khotrick.org/DevopQLYFood/public/)
- [Health chính](https://khotrick.org/DevopQLYFood/public/health.php) và [revision](https://khotrick.org/DevopQLYFood/public/version.php)
- [Dashboard](https://khotrick.org/DevopQLYFood/public/operations.php) và [demo riêng](https://khotrick.org/DevopQLYFood/public/demo.php), cần login
- [Pipeline cập nhật source](https://github.com/nathyu-le/DevopQLYFood/actions/workflows/container-pipeline.yml)
- [Bản phát hành cuối](https://github.com/nathyu-le/DevopQLYFood/releases/tag/week4-final-v1.0.0)

## Tài liệu bàn giao

[Kiến trúc](../../docs/FINAL_ARCHITECTURE.md) · [Cài mới](../../docs/FINAL_DEPLOYMENT.md) · [Vận hành](../../docs/OPERATIONS.md) · [Playbook](../../docs/TROUBLESHOOTING.md) · [Kịch bản](../../docs/WEEK4_DEMO.md) · [Báo cáo](../../docs/WEEK4_TECHNICAL_REPORT.md).

Video và bản Word được bàn giao riêng cho chủ dự án; không public screenshot/telemetry của phiên quản trị hoặc backup thật lên repo. Video tự động nếu dùng staging phải ghi đúng phạm vi và nguồn thuyết minh, không gán thành ghi hình do ba thành viên tự thực hiện. Link/file video hoàn thành được ghi vào hồ sơ sau khi kiểm tra thực tế.

Các artifact cũ có retention; chạy lại workflow nếu đã hết hạn. Không coi link artifact hết hạn là bằng chứng còn tải được. Minh chứng lịch sử Week 1–3 được giữ đúng ngày/môi trường.

## Video dự phòng đã xuất

[Run video 42860890403](https://github.com/nathyu-le/DevopQLYFood/actions/runs/42860890403), artifact **week4-demo-video**, file **Week4_Demo_ThuyetMinh.mp4**, 400 giây (6 phút 40 giây), có video và audio. Giọng Việt tạo tự động, không phải giọng thành viên. Đầu video là login/revision công khai của cPanel; phần thao tác dùng dữ liệu tổng hợp trên Docker staging tạm và có nhãn môi trường. Không có OS metrics hoặc dữ liệu MariaDB live.

Artifact đã được workflow xác minh định dạng/thời lượng và upload thành công. Trình duyệt của phiên làm việc không tải được artifact về nên chưa kiểm tra phát lại cục bộ; nhóm mở file trước khi dùng làm bản nộp. GitHub yêu cầu đăng nhập để tải artifact và retention là 30 ngày; lưu bản sao riêng khi tải. Khi thầy yêu cầu thành viên tự thuyết minh, dùng kịch bản Word để quay giọng thật.
