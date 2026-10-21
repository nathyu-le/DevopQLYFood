# Chỉ mục minh chứng toàn dự án

| Checkpoint | Nơi cập nhật | Trạng thái |
|---|---|---|
| Week 1 | [Hướng dẫn Week 1](week1/README.md), [server](server/README.md), [application](application/README.md) | Chưa có minh chứng triển khai server nhóm |
| Week 2 | [week2/](week2/README.md) | Chưa có minh chứng Docker/CI/CD triển khai |
| Week 3 | [week3/](week3/README.md) | Chưa có minh chứng monitoring/security/backup/restore |
| Week 4 | [week4/](week4/README.md) | Chưa có demo tổng thể hoặc báo cáo cuối kỳ |

Week 1: Nguyễn Phước Hải kiểm tra chức năng và đối chiếu DB; Lê Quang Tùng tổng hợp ảnh/log/video demo; cả hai phối hợp hoàn thiện minh chứng. Xem [phân công](../docs/TEAM.md).

Nhóm dự kiến thuê VPS để bổ sung triển khai/demo Week 1, sau đó làm tiếp Week 2 trên cùng hệ thống. Lưu minh chứng ngay trong quá trình thực hiện và dẫn về đúng checkpoint; phần bổ sung chưa được ghi nhận là đã được giảng viên chấp thuận. Xem [kế hoạch](../docs/DEPLOYMENT_PLAN.md).

## Minh chứng Week 1

**Chưa thu thập minh chứng triển khai thật.** Không có ảnh server, video hoặc logs triển khai được dựng giả trong thư mục này.

| Mã | Minh chứng cần có | Nơi lưu dự kiến | Trạng thái |
|---|---|---|---|
| E01 | OS, CPU, RAM, disk của server | `server/` | Chưa có |
| E02 | User/SSH, port listening, firewall/security group | `server/` | Chưa có |
| E03 | Dịch vụ Apache/PHP và MySQL hoạt động | `server/` | Chưa có |
| E04 | Client mở được URL ứng dụng | `application/` | Chưa có |
| E05 | Thêm món và tải lại vẫn còn | `application/` | Chưa có |
| E06 | SELECT xác nhận cùng bản ghi trong MySQL | `application/` | Chưa có |
| E07 | Video demo hoặc bộ ảnh/log thay thế | Link video hoặc file trong `application/` | Chưa có |

Khi thêm minh chứng, ghi ngày thực hiện, người thực hiện, môi trường, file/link thật và kết quả quan sát. Video để ngoài GitHub phải cấp quyền xem cho giảng viên. Không đưa credentials, private keys hoặc dữ liệu nhạy cảm vào ảnh/log/video.

Sơ đồ thiết kế nằm ở `../architecture.png`. Kết quả Actions là kiểm tra source trên runner, không thay cho E01–E07 trên server của nhóm.

## Kiểm tra source đã thực hiện

- Ngày xác minh: 21/10/2026.
- [GitHub Actions run 39529399926](https://github.com/nathyu-le/DevopQLYFood/actions/runs/39529399926).
- Commit được kiểm tra: `4baaaa1da776c714098e35ccc5e275c5495d7170`.
- Kết quả: PHP syntax hợp lệ; 14 validation checks và 19 HTTP/MySQL integration checks thành công.
- Môi trường: runner Ubuntu 24.04, PHP CLI 8.3.6, dịch vụ MySQL 8.0 tạm.
- Các request HTTP đi đến loopback của runner; đây là kiểm tra tích hợp thật với MySQL, **không phải** bằng chứng client truy cập server triển khai của nhóm.
