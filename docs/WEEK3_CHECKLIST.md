# Week 3 Monitoring Security và Reliability

**Cập nhật ngày 4 tháng 11 năm 2026:** Đã cập nhật source Week 3 lên cPanel bằng upload thủ công; kiểm tra lúc 11:20 ngày 04/11/2026 xác nhận health OK, revision d690bc0 và 7 kiểm tra đường dẫn HTTP đều qua. Đã xác minh dashboard, cập nhật số liệu, lọc log và tạo backup bằng phiên quản trị trên live lúc 11:34 ngày 04/11/2026. CPU/RAM live là số liệu OS có thể dùng chung, không phải quota riêng của tài khoản. Pipeline FTPS vẫn lỗi.

Đối chiếu ảnh đề Week 3 đã cung cấp: monitoring CPU/RAM/health, dashboard và alert đã thử, logs theo thời gian, firewall/ports/secrets, backup/restore có đối chiếu và playbook ít nhất 2 sự cố. [Run kiểm chứng](https://github.com/nathyu-le/DevopQLYFood/actions/runs/41768981566) · [Ảnh và logs](../evidence/week3/README.md).

## Đã có source và minh chứng staging

- [x] Dashboard Ops Monitor có CPU/RAM/health/DB latency, biểu đồ và mốc cập nhật; ghi rõ nguồn cgroup/OS/PHP, không dựng số liệu khi bị giới hạn quyền.
- [x] Alert database_unavailable được kích hoạt bằng việc dừng MariaDB thật và phục hồi bằng khởi động lại; có ảnh normal/firing/recovered và lifecycle JSON.
- [x] JSONL log có UTC/request ID/route/status/duration; dashboard lọc theo event và thời điểm. Không ghi credentials, form hoặc query string.
- [x] UFW được cấu hình trên runner staging riêng; default deny incoming, loopback allowed, DB không publish. Có status thật và health check sau cấu hình.
- [x] User app staging chỉ có bốn quyền CRUD; test CREATE TABLE bị từ chối. Script xử lý đúng mẫu database có dấu gạch dưới.
- [x] Metrics API cần session admin; backup cần CSRF, có cooldown; restore không có HTTP action. File private ngoài public và không nằm trong source ZIP.
- [x] Script backup/restore chạy thật, có retention, SHA256, atomic writes và quyền hạn chế.
- [x] Backup món thử, xóa đúng fixture rồi restore; ID/values/timestamps và digest khớp. Confirmation sai/file hỏng bị chặn trước khi ghi.
- [x] Playbook có ít nhất 2 sự cố; hiện có 3 trường hợp đã thực nghiệm hoặc phát hiện qua test.
- [x] Hồ sơ có dashboard screenshots/config, security/ports checklist, scripts/logs và hướng dẫn một người demo.

Ảnh dashboard đã đăng nhập và raw JSON được bàn giao riêng để chủ repo xem trước; chưa công khai chúng trong Git khi chưa có xác nhận. Repo có source, log test chọn lọc và test summary.

## Môi trường live và việc cần xác nhận

- [x] Source Week 3 đã lên cPanel bằng upload thủ công; health/revision đúng và 7 kiểm tra HTTP qua: assets 200, metrics anonymous 401, dashboard về login, private paths 403. [Kết quả live](../evidence/week3/live-http-checks-2026-11-04.json).
- [x] Đã đăng nhập dashboard trên cPanel, thấy mẫu cập nhật/biểu đồ, DB hoạt động, lọc log theo backup_created và thời gian; bấm Tạo backup thành công và có log tương ứng. [Kiểm tra live](../evidence/week3/live-operations-checks-2026-11-04.json). CPU/RAM lấy từ OS có thể dùng chung, không phải mức dùng riêng của app/tài khoản.
- [ ] Xác minh policy firewall/SSH/quyền DB và nguồn quota của đúng gói cPanel nếu dùng các mục đó để bảo vệ hạ tầng live. UFW staging không chứng minh cấu hình firewall của nhà cung cấp.
- [ ] Nhóm mở thử đường dẫn bằng tài khoản nộp, tập demo, xác nhận đóng góp cá nhân thật và sẵn sàng giải thích nguồn metrics.

Cron backup cPanel là cấu hình vận hành đề xuất, chưa được tự thiết lập bởi pipeline; đề yêu cầu script/retention và chạy restore có bằng chứng, các mục này đã được test ở staging. Uptime workflow chỉ probe health, không tự backup database live.

## Rubric và hồ sơ nộp

| Tiêu chí | Tối đa theo đề | Minh chứng |
|---|---:|---|
| Monitoring metrics logs dashboard alert | 0,8 | Config, ba ảnh, lifecycle JSON và application events |
| Firewall secrets database access | 0,5 | UFW/Compose staging, DDL denied, session/CSRF/private files; scope cPanel ghi rõ |
| Backup và restore có dữ liệu đối chiếu | 0,8 | Scripts, log, JSON before/delete/restore và digest |
| Giải thích và xử lý sự cố | 0,4 | Playbook, run thực nghiệm và kịch bản demo |

Tổng tối đa 2,5/10 theo đề; không tự dự đoán điểm đạt hoặc ghi giảng viên đã chấp nhận cách triển khai. Chỉ dùng dữ liệu thử khi mô phỏng xóa. Không đánh dấu các mục OS/user/SSH/firewall Week 1 hoặc Week 4 đã hoàn tất từ kết quả này.

[Monitoring](../monitoring/README.md) · [Security](SECURITY.md) · [Backup restore](BACKUP_RESTORE.md) · [Playbook](TROUBLESHOOTING.md) · [Demo](WEEK3_DEMO.md).
