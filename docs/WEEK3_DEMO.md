# Week 3 Kịch bản một người trình bày

Thời lượng khoảng 7 đến 10 phút. Mỗi phần có link chiếu, thao tác và lời đọc. Source Week 3 đã lên hosting bằng upload thủ công; health/revision và 7 kiểm tra đường dẫn HTTP đã qua. Đã đăng nhập Vận hành, kiểm tra số liệu/biểu đồ, lọc log và tạo backup thành công trên live ngày 4/11/2026. CPU/RAM hiện mang nhãn OS visible to PHP; may be shared host, không phải quota riêng của tài khoản. Trước buổi demo mở lại trang; dùng bộ minh chứng staging để trình bày cảnh báo và restore có đối chiếu. Pipeline FTPS vẫn đang lỗi.

Ba ảnh dashboard và JSON gốc nằm trong gói `Week3_MinhChung.zip` bàn giao riêng; tải và giải nén trước buổi. Chỉ mục trong repo dẫn tới kết quả CI và log chọn lọc. Không dừng DB hoặc thử xóa dữ liệu live để minh họa.

## 1 Dashboard và phạm vi

**Link chiếu:** [Website Vận hành](https://khotrick.org/DevopQLYFood/public/operations.php) · [Nguồn metrics và cấu hình](../monitoring/README.md).

**Thao tác:** đăng nhập quản trị, mở Vận hành. Chỉ CPU, RAM, DB health/latency và giờ mẫu cập nhật; chờ khoảng 5 giây để thấy mẫu mới. Chỉ dòng nguồn dữ liệu bên dưới cards.

**Lời đọc:** “Week 3 nhóm bổ sung công cụ giám sát, bảo vệ truy cập và kiểm chứng phục hồi dữ liệu. Dashboard này cập nhật mỗi 5 giây khi mở, theo dõi CPU, RAM và kết nối database. Nguồn số liệu được hiển thị rõ: cgroup thuộc container ở staging, còn trên shared hosting có thể là OS dùng chung hoặc PHP allocation. Nhóm không dùng số liệu đó để suy ra quota cPanel.”

## 2 Cảnh báo đã thử

**Link chiếu:** [Bộ minh chứng Week 3](../evidence/week3/README.md).

**Thao tác:** mở ảnh dashboard bình thường, ảnh firing, ảnh recovered; mở alert lifecycle JSON. Chỉ time, health unavailable và state firing/ok. Nếu cần chạy lại, mở Actions → Docker checks and cPanel deploy → Run workflow trên main; job staging tự chạy bài lỗi với DB riêng.

**Lời đọc:** “Nhóm đã dừng MariaDB ở staging riêng. Health trả 503 và sau hai mẫu lỗi liên tiếp, cảnh báo database không khả dụng chuyển sang firing. Khi khởi động lại DB, health về ok; hai mẫu tốt làm cảnh báo phục hồi. Banner trên ảnh do dashboard đọc trạng thái thực tế, không phải ảnh dựng. Website cPanel không bị dừng trong bài này.”

## 3 Tra log theo thời gian

**Link chiếu:** dashboard ở phần 1 và application events trong [evidence](../evidence/week3/README.md).

**Thao tác:** lọc database_unavailable/alert_transition trong bộ log sự cố; đối chiếu UTC trong JSON với giờ Việt Nam trên UI. Trên dashboard live có thể lọc http_request hoặc request ID để minh họa công cụ tìm kiếm.

**Lời đọc:** “Log ứng dụng được ghi JSON theo thời gian, có event, route, HTTP status, thời gian xử lý và request ID. Nhóm dùng mốc thời gian và sự kiện chuyển trạng thái để tìm lỗi và kiểm tra lúc phục hồi. Log không chứa mật khẩu, nội dung form hoặc query string.”

## 4 Backup và restore có đối chiếu

**Link chiếu:** [Hướng dẫn và script backup restore](BACKUP_RESTORE.md) · JSON backup restore trong [evidence](../evidence/week3/README.md).

**Thao tác:** chỉ bản ghi trước xóa, after_delete rỗng, after_restore giống hệt và all_rows_match=true. Có thể bấm Tạo backup trên dashboard live để minh họa thao tác chỉ đọc; không chạy restore/xóa vào DB live.

**Lời đọc:** “Script backup lưu dữ liệu bảng dishes vào thư mục riêng, file quyền 0600, giữ 14 snapshot và có SHA256. Bài test tạo một món thử, backup, xóa đúng món đó rồi restore. Sau restore, ID, tên, giá, trạng thái và timestamps đều khớp. Script từ chối file hỏng và xác nhận DB sai trước khi ghi; restore dùng transaction, chỉ cho DB thử kết thúc test hoặc ci.”

## 5 Security và xử lý sự cố

**Link chiếu:** [Security và ports](SECURITY.md) · [Playbook](TROUBLESHOOTING.md).

**Thao tác:** chỉ UFW staging, DB không publish port, API anonymous 401 và private paths bị chặn. Mở hai sự cố đầu trong playbook; nếu được hỏi thêm, trình bày bug quyền DB đã được test bắt và sửa.

**Lời đọc:** “Trên runner staging riêng, nhóm đặt firewall default deny incoming, giữ loopback để demo và không publish port database. User app chỉ có bốn quyền CRUD; test chứng minh không tạo được bảng. Trên cPanel đã kiểm tra metrics anonymous trả 401, dashboard chuyển về login và các đường dẫn private trả 403. Dashboard sau đăng nhập và tạo backup đã được thử thành công; nguồn CPU/RAM live là OS có thể dùng chung, không phải quota tài khoản. Firewall hệ điều hành của hosting do nhà cung cấp quản lý, không được gọi UFW của runner là firewall cPanel.”

“Playbook có hướng xác minh, nguyên nhân, khắc phục và xác nhận phục hồi cho lỗi DB không khả dụng và mất dữ liệu thử. Hồ sơ nộp gồm dashboard screenshots/config, security checklist, scripts/logs backup restore và minh chứng xử lý sự cố. Các phần đánh giá vẫn cần trình bày đúng môi trường và nguồn số liệu.”

## Câu hỏi cần nắm

- Cảnh báo có gửi email không? Hiện dùng banner/state và log chuyển trạng thái; uptime workflow có kết quả pass/fail trên GitHub, chưa cấu hình kênh email/Slack riêng.
- Có backup tự động theo ngày trên cPanel chưa? Script và retention đã có; không ghi cron hosting đã cấu hình khi chưa có bằng chứng. Có thể chạy CLI hoặc nút backup; lịch GitHub hiện chỉ probe health.
- Vì sao không restore DB live khi demo? Đề yêu cầu chỉ dùng dữ liệu thử khi mô phỏng xóa. Script có guard DB test/CI và không có restore action qua HTTP.
- Có phải app chạy Docker trên cPanel không? Không. Docker staging ở GitHub runner; live cPanel tiếp tục PHP/DB trực tiếp.

## Minh chứng live sau đăng nhập

[Biên bản kiểm tra dashboard và backup](../evidence/week3/live-operations-checks-2026-11-04.json). Ảnh chụp live được bàn giao riêng, chưa công khai trong repo. CPU/RAM hiển thị lớn hơn giới hạn gói cPanel có thể do phạm vi OS dùng chung; khi trình bày phải chỉ đúng nhãn nguồn đo và không gọi đó là mức tiêu thụ của website.
