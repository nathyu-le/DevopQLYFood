# Vận hành bản cuối

## Kiểm tra mỗi lần deploy

Mở health.php, version.php và trang đăng nhập tại website live. Health phải ok và revision đúng source SHA của run được báo thành công. Dashboard cần login, DB hoạt động và thời gian mẫu đổi sau 5 giây. Chạy chuỗi demo riêng sau cập nhật lớn; kết thúc ở HTTP 200/alert OK.

Dashboard chỉ lấy mẫu khi có request/trang đang mở; uptime workflow public probe riêng theo lịch 5 phút nhưng GitHub có thể trễ. Không coi scheduler là SLA hay daemon giám sát 24/7. Chưa cấu hình email/Slack; cảnh báo hiển thị trên dashboard và log.

## Backup và lưu trữ

Nút Tạo backup trên Vận hành tạo snapshot dishes MariaDB, tối đa một lần mỗi 60 giây/session. CLI `php app/backup.php --keep=14` cũng tạo snapshot trong var/backups. Bảo vệ directory 0700, file 0600; định dạng JSON kèm SHA256 phát hiện sai lệch, không mã hóa. Giữ một bản sao ngoài hosting và thử restore định kỳ vào DB `_test`/`_ci`.

Lịch đề xuất: hàng ngày và trước thay schema. Nếu có cPanel Cron Jobs, chọn PHP binary và đường dẫn project thực tế, ví dụ mẫu lệnh `/DUONG_DAN_PHP /DUONG_DAN_DU_AN/app/backup.php --keep=14`. Các đường dẫn này phải thay theo hosting. Không ghi đã cài cron nếu chưa xác minh. Không đưa password vào command, log hoặc video.

Nút Tải file backup ở Demo chỉ xuất snapshot SQLite của lượt demo; không phải bản dump MariaDB production. Restore MariaDB chính là CLI có guard DB thử; xử lý sự cố live cần kế hoạch phục hồi riêng.

## Điều tra sự cố

1. Xác định phạm vi: website chính hay demo SQLite, HTTP code, thời điểm và revision.
2. Đọc health/log tại đúng môi trường; không suy từ một banner sang trạng thái toàn máy chủ.
3. Đối chiếu thay đổi gần nhất và cấu hình cần thiết, giữ nguyên secrets trong mọi minh chứng.
4. Sửa nguyên nhân, kiểm tra lại health/dữ liệu; chờ 2 mẫu tốt nếu dùng alert.
5. Ghi incident timeline và cách phòng ngừa trong playbook, không chỉ ghi “đã hết lỗi”.

[Playbook có các tình huống đã kiểm chứng](TROUBLESHOOTING.md). Root/SSH/firewall của cPanel do provider quản lý; UFW runner không là minh chứng firewall live. Không dùng chmod 777, tắt TLS hoặc xóa volume để chữa lỗi.

## Quy trình đóng phiên demo

Phục hồi kết nối demo, chờ HTTP 200 và OK; giữ dữ liệu thử nếu cần đối chiếu. Không tạo lỗi MariaDB đang phục vụ website. Khi giảng viên chọn lỗi khác phạm vi thử, giải thích cách xác minh rồi dùng môi trường thử thích hợp; không gây sự cố cho các tài khoản khác trên shared host.
