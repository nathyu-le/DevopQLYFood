# Demo trực tiếp Week 3 trên cPanel

Một người trình bày, thao tác trên website. Mở [Dashboard](https://khotrick.org/DevopQLYFood/public/operations.php) và [Demo trực tiếp](https://khotrick.org/DevopQLYFood/public/demo.php). Đăng nhập quản trị trước buổi demo.

## Mở đầu

“Em chào thầy. Trong Week 3, nhóm em đã bổ sung giám sát, nhật ký, cảnh báo và backup restore. Sau đây em xin thao tác trực tiếp trên website để trình bày.”

## 1 Xem dashboard

**Bấm:** mở Dashboard; chỉ DATABASE Hoạt động, CPU, RAM và thời điểm cập nhật.

**Nói:** “Đây là dashboard đang theo dõi hệ thống. Database hiện hoạt động và số liệu cập nhật mỗi 5 giây. CPU và RAM có thể thuộc máy chủ dùng chung, không phải mức dùng riêng của website.”

## 2 Tạo món thử

**Bấm:** vào Demo trực tiếp, bấm **1 Tạo dữ liệu thử**. Chỉ ID 1, tên Cơm gà demo Week 3, giá 45.000 đồng.

**Nói:** “Em dùng một món thử trong database riêng trên cùng hosting để kiểm tra backup và phục hồi dữ liệu. Em giữ lại ID, tên và giá để đối chiếu.”

## 3 Backup rồi xóa

**Bấm:** **2 Backup** → **Tải file backup** → **3 Xóa dữ liệu thử**. Chỉ bảng đã trống.

**Nói:** “Backup đã được tạo và tải về máy. Sau khi xóa dữ liệu thử, món đã biến mất khỏi bảng. Tiếp theo em phục hồi từ bản backup vừa tạo.”

## 4 Restore

**Bấm:** **4 Restore**. Đối chiếu món xuất hiện lại với cùng ID, tên và giá; chỉ **KHỚP BACKUP**.

**Nói:** “Món đã xuất hiện trở lại với dữ liệu ban đầu. Kết quả KHỚP BACKUP xác nhận toàn bộ dữ liệu phục hồi giống bản sao lưu.”

## 5 Tạo lỗi

**Bấm:** **5 Tạo lỗi kết nối**, chờ khoảng 5 đến 10 giây. Chỉ HTTP 503, database Mất kết nối, cảnh báo FIRING và nhật ký demo.

**Nói:** “Em vừa ngắt đường dẫn kết nối tới database demo. Health thực tế trả 503. Sau hai mẫu lỗi liên tiếp, cảnh báo chuyển sang FIRING và sự cố được ghi vào nhật ký.”

## 6 Phục hồi rồi kết thúc

**Bấm:** **6 Phục hồi kết nối**. Chờ HTTP 200, DATABASE Hoạt động và cảnh báo OK. Chỉ món vẫn còn.

**Nói:** “Sau khi phục hồi kết nối, health về 200 và cảnh báo về OK sau các mẫu tốt liên tiếp. Dữ liệu vừa restore vẫn được giữ nguyên. Em xin kết thúc phần demo và cảm ơn thầy.”

## Phạm vi kỹ thuật

Trang demo dùng SQLite riêng trong thư mục private `var/classroom-demo` trên cPanel. Thực đơn chính tiếp tục dùng MariaDB. Các nút xóa/restore chỉ thao tác bảng SQLite demo; không gọi script restore của database chính. File backup demo có scope, lượt demo và SHA256 riêng; tải qua POST có xác thực quản trị và CSRF. Bản backup chính vẫn nằm trong thư mục backup riêng của ứng dụng.

Tạo lỗi bằng cách tạm đổi tên file database demo, khiến health demo thật sự trả 503; khôi phục tên file để kết nối lại. Bộ cảnh báo lấy mẫu mỗi 5 giây, FIRING sau 2 mẫu lỗi và về OK sau 2 mẫu tốt. Demo tự phục hồi kết nối sau 3 phút nếu bỏ quên. Đây là lỗi của dịch vụ demo trên hosting, không phải thao tác dừng MariaDB của website chính.

[Test tích hợp](../tests/demo_smoke.py) kiểm tra auth/CSRF, backup/download/delete/restore, file lỗi bị chặn, HTTP 503/200, alert và dữ liệu MariaDB chính giữ nguyên. [Test nút bấm](../tests/demo_client.mjs) kiểm tra thao tác trong lúc trang tự cập nhật và chặn dữ liệu nền cũ ghi đè kết quả mới. Bài thử MariaDB staging trong CI vẫn được giữ để kiểm tra stack ứng dụng chính.
