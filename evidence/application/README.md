# Minh chứng app + database

**Cập nhật 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được sau khi cấu hình admin trên hosting cPanel. Chưa có ảnh/video/SELECT của chuỗi CRUD thực tế để xác nhận demo hoàn tất.

[Log kiểm tra HTTP ngày 21/10/2026](http-access-2026-10-21.md): health HTTP 200/status ok và trang đăng nhập HTTP 200. Kết quả này xác nhận truy cập và đọc bảng qua health; thao tác ghi/sửa cùng SELECT đối chiếu còn cần thực hiện.

Theo `docs/DEMO.md`, cần bổ sung:

1. Ảnh client mở ứng dụng từ URL server.
2. Ảnh form thêm món và danh sách sau khi lưu.
3. Ảnh sau tải lại vẫn có bản ghi, có ID rõ ràng.
4. Kết quả SELECT cùng ID/tên/giá/tình trạng trong MySQL.
5. Video demo hoặc logs phù hợp, có ngày và người thực hiện.

Không dùng dữ liệu seed, hình mockup hoặc ảnh kết quả CI để ghi là đã triển khai server nhóm.
