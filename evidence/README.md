# Chỉ mục minh chứng toàn dự án

| Checkpoint | Nơi cập nhật | Trạng thái |
|---|---|---|
| Week 1 | [Hướng dẫn Week 1](week1/README.md), [server](server/README.md), [application](application/README.md) | Có log HTTP và ảnh thêm/sửa/SELECT trên hosting; còn hạ tầng và sơ đồ hiện trạng |
| Week 2 | [week2/](week2/README.md) | Chưa có minh chứng Docker/CI/CD triển khai |
| Week 3 | [week3/](week3/README.md) | Chưa có minh chứng monitoring/security/backup/restore |
| Week 4 | [week4/](week4/README.md) | Chưa có demo tổng thể hoặc báo cáo cuối kỳ |

Week 1: Nguyễn Phước Hải kiểm tra chức năng và đối chiếu DB; Lê Quang Tùng tổng hợp ảnh/log/video demo; cả hai phối hợp hoàn thiện minh chứng. Xem [phân công](../docs/TEAM.md).

Người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) truy cập được ngày 21/10/2026. Thu minh chứng app/DB trên hosting hiện có trước, rồi xác minh phần hạ tầng và khả năng thực hiện Week 2; VM/VPS là phương án bổ sung nếu cần. Xem [kế hoạch](../docs/DEPLOYMENT_PLAN.md).

## Minh chứng Week 1

**Đã có minh chứng app + DB trên hosting:** [3 ảnh gốc và đối chiếu ID 9](application/demo-2026-10-21.md). Thông tin/cấu hình hạ tầng và video demo liền mạch vẫn cần bổ sung.

| Mã | Minh chứng cần có | Nơi lưu dự kiến | Trạng thái |
|---|---|---|---|
| E01 | OS, CPU, RAM, disk của server | `server/` | Chưa có |
| E02 | User/SSH, port listening, firewall/security group | `server/` | Chưa có |
| E03 | Web server/PHP và DB thực tế hoạt động | `server/` | Health HTTP 200/status ok; phiên bản/cấu hình dịch vụ chờ xác minh |
| E04 | Client mở được URL ứng dụng | `application/` | Trang login HTTP 200; có [log truy cập](application/http-access-2026-10-21.md) và ảnh app có món ID 9 |
| E05 | Thêm món và tải lại vẫn còn | `application/` | Có ảnh thêm/sửa cùng ID 9; bước tải lại riêng chưa thể hiện trong ảnh |
| E06 | SELECT xác nhận cùng bản ghi trong DB | `application/` | Có ảnh SELECT: ID 9, tên món khớp, price 50000, is_available 0 |
| E07 | Video demo hoặc bộ ảnh/log thay thế | Link video hoặc file trong `application/` | Có bộ 3 ảnh app/DB và log HTTP; chưa có video |

Khi thêm minh chứng, ghi ngày thực hiện, người thực hiện, môi trường, file/link thật và kết quả quan sát. Video để ngoài GitHub phải cấp quyền xem cho giảng viên. Không đưa credentials, private keys hoặc dữ liệu nhạy cảm vào ảnh/log/video.

Sơ đồ thiết kế nằm ở `../architecture.png`. Kết quả Actions là kiểm tra source trên runner, không thay cho E01–E07 trên server của nhóm.

## Kiểm tra source đã thực hiện

- Ngày xác minh: 21/10/2026.
- [GitHub Actions run 39529399926](https://github.com/nathyu-le/DevopQLYFood/actions/runs/39529399926).
- Commit được kiểm tra: `4baaaa1da776c714098e35ccc5e275c5495d7170`.
- Kết quả: PHP syntax hợp lệ; 14 validation checks và 19 HTTP/MySQL integration checks thành công.
- Môi trường: runner Ubuntu 24.04, PHP CLI 8.3.6, dịch vụ MySQL 8.0 tạm.
- Các request HTTP đi đến loopback của runner; đây là kiểm tra tích hợp thật với MySQL, **không phải** bằng chứng client truy cập server triển khai của nhóm.
