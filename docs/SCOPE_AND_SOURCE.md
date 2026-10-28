# Phạm vi và nguồn code

## Ứng dụng được chọn

**Website quản lý thực đơn quán ăn** với tên giao diện Bếp Nhà. Đề tài nhỏ giúp tập trung vào các nội dung DevOps bắt buộc thay vì xây dựng hệ thống bán hàng lớn.

## Trong phạm vi

Đăng nhập một tài khoản quản trị cấu hình cục bộ; tạo/xem/sửa/xóa món; tìm kiếm theo tên hoặc mô tả; lọc danh mục/tình trạng; đổi còn/hết; lưu dữ liệu MySQL; kiểm tra trạng thái kết nối bằng endpoint `health.php`.

Các trường: tên món, danh mục, giá VNĐ, mô tả, tình trạng. Không upload ảnh hoặc nhận thanh toán trong phiên bản này.

## Ngoài phạm vi phiên bản đầu

Đặt món của khách, giỏ hàng, hóa đơn, thanh toán trực tuyến, giao hàng, nhiều nhà hàng, phân quyền nhiều cấp và quản lý ảnh. Không coi việc chưa có các chức năng này là lỗi triển khai Week 1.

## Nguồn và cách khai báo đóng góp

- Source được khởi tạo riêng cho repository này với hỗ trợ AI, không fork/copy một ứng dụng mã nguồn mở có sẵn.
- Thư viện chạy chính là các phần mở rộng PHP và MySQL; giao diện dùng CSS/JS thuần, biểu tượng SVG đơn giản do source định nghĩa.
- AI hỗ trợ chuẩn bị bản đầu của ứng dụng, schema, kiểm tra source và tài liệu. Thành viên phải review, hiểu, thử chạy và giải thích được nội dung trước khi bảo vệ.
- Nhóm tự ghi nhận các phần thực tế đã làm: cấu hình môi trường, chỉnh source, chạy thử, triển khai, chụp minh chứng và xử lý lỗi. Không ghi một việc là “đã làm” chỉ vì có hướng dẫn.
- Phân công Week 1 ghi tên thật trong `TEAM.md`; người thực hiện và kết quả được ghi cùng commit/minh chứng của từng công việc. Không tạo commit thay tên thành viên hoặc khai đóng góp chưa thực hiện.

## Tài liệu tham khảo kỹ thuật

1. PHP Manual — PDO: https://www.php.net/manual/en/book.pdo.php
2. PHP Manual — Prepared statements: https://www.php.net/manual/en/pdo.prepare.php
3. PHP Manual — Password hashing: https://www.php.net/manual/en/function.password-hash.php
4. PHP Manual — Session security: https://www.php.net/manual/en/session.security.php
5. MySQL 8.0 Reference Manual — CREATE TABLE: https://dev.mysql.com/doc/refman/8.0/en/create-table.html
6. Apache HTTP Server — .htaccess: https://httpd.apache.org/docs/2.4/howto/htaccess.html

Các nguồn này là tài liệu nền tảng triển khai, không phải nguồn của một website hoàn chỉnh được sao chép.
