# Minh chứng Week 3 ngày 4 tháng 11 năm 2026

**Trạng thái:** Staging và source checks đã qua; deploy cPanel đang bị timeout kết nối FTPS. Website live chưa được xác nhận chạy source Week 3. Có ZIP cập nhật để upload bằng File Manager.

Source [d690bc0](https://github.com/nathyu-le/DevopQLYFood/commit/d690bc0295a00e709a98983a30e33523f8542765) · [Pipeline 41768981566](https://github.com/nathyu-le/DevopQLYFood/actions/runs/41768981566) · [Source checks](https://github.com/nathyu-le/DevopQLYFood/actions/runs/41768981563). Staging là môi trường Docker thử riêng trên runner, không phải DB cPanel.

## Sản phẩm và kết quả

| Yêu cầu Week 3 | Đã có |
|---|---|
| CPU RAM health dashboard | Ops Monitor PHP, charts/polling 5 giây, nguồn cgroup/OS/PHP được ghi rõ. [Cấu hình](../../monitoring/README.md) |
| Alert đã thử | Stop DB thật, health 503; alert pending/firing và về ok sau khi start lại |
| Logs theo thời gian | JSONL UTC/request ID/status/duration, filter trên dashboard; [log pipeline chọn lọc](pipeline-2026-11-04.log) |
| Security ports secrets | UFW staging default deny, DB không publish, app user bị chặn DDL, auth/CSRF/private files có tests. [Checklist](../../docs/SECURITY.md) |
| Backup và restore | Snapshot → xóa fixture → restore → IDs/values/timestamps khớp; file hỏng và confirmation sai bị chặn. [Scripts và hướng dẫn](../../docs/BACKUP_RESTORE.md) |
| Playbook ít nhất 2 sự cố | [3 trường hợp](../../docs/TROUBLESHOOTING.md): DB dừng, mất dữ liệu thử, quyền DB quá rộng |
| Ảnh minh chứng | Đã chụp Chrome thật ở trạng thái normal/firing/recovered; ảnh và JSON chi tiết bàn giao riêng cho chủ repo để xem trước |

[Test summary](results/test-summary.json) ghi **37 assertions**, gồm cả các probe lặp trong chuỗi sự cố, không phải 37 chức năng khác nhau. Source checks, 14 validation và 17 container/persistence checks cũng đã qua. Các bước thử chỉ tác động dữ liệu fixture trong DB `_ci`.

## Bộ ảnh và dữ liệu đối chiếu

Gói minh chứng bàn giao gồm dashboard-normal.png, dashboard-firing.png, dashboard-recovered.png, metadata từng ảnh, alert-lifecycle.json, backup-restore.json, application-events.json và staging-firewall.txt. Ảnh lấy trực tiếp từ Chrome của staging, không chỉnh sửa; JSON restore xác nhận toàn bộ rows trước/sau giống nhau. Không có snapshot hoặc dữ liệu của website live trong gói.

Ảnh dashboard đăng nhập và raw telemetry chưa được công khai trong Git. Chủ repo cần xem và cho phép công khai trước khi bổ sung chúng. Việc này không cản việc review source, cấu hình, log kiểm tra đã lược thông tin kết nối và test summary ở đây.

## Phạm vi hạ tầng và live

UFW đã được cấu hình/check trên runner staging riêng: default deny incoming, allow outgoing, loopback allowed và deny DB port; Compose không publish DB và bind app 127.0.0.1. Không gọi đó là firewall cPanel. Firewall/SSH/quota của hosting do nhà cung cấp quản lý; chưa có bằng chứng policy riêng từ gói hiện tại.

Staging và source checks đã qua; deploy cPanel đang bị timeout kết nối FTPS. Website live chưa được xác nhận chạy source Week 3. Có ZIP cập nhật để upload bằng File Manager.

Để cập nhật thủ công: upload ZIP runtime Week 3 vào đúng thư mục DevopQLYFood đang chứa app/config/public, Extract và ghi đè source. ZIP chỉ có app/public và REVISION; giữ config.local.php, database và .htaccess đang có. Sau upload, mở Vận hành, kiểm tra health/version và đối chiếu source commit ở đầu trang.

FTPS đã timeout trước đăng nhập qua các lần thử có giới hạn. Khi hosting khôi phục dịch vụ hoặc cấp endpoint đúng, cập nhật Variables/Secrets trực tiếp trong GitHub và retry job deploy; không gửi password qua issue/log, không tắt TLS. Upload tay là phương án đưa source lên trong lúc dịch vụ FTP gián đoạn, không được ghi thành pipeline deploy success.

Uptime workflow probe health khoảng 5 phút/lần; chưa tự cấu hình cron backup hosting. Backup có thể chạy CLI/nút trong dashboard sau khi source có trên hosting; restore bài thực nghiệm chỉ dành DB test/CI. [Checklist](../../docs/WEEK3_CHECKLIST.md) · [Demo một người](../../docs/WEEK3_DEMO.md).
