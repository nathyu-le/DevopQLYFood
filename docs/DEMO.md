# Kịch bản demo Week 1 — thực hiện sau khi triển khai

**Trạng thái:** đây là hướng dẫn, chưa phải biên bản demo đã diễn ra.

## Chuẩn bị

- Có URL server truy cập được từ máy khách và tài khoản quản trị cấu hình riêng.
- Đã import schema, app kết nối được MySQL.
- Có quyền xem DB bằng công cụ quản trị trên môi trường thật.
- Không hiển thị mật khẩu, private key, token hoặc cấu hình chứa secret khi quay/chụp.
- Mở sẵn repository, `architecture.png`, README và minh chứng server.

## Trình tự

| Bước | Thực hiện | Kết quả phải quan sát được |
|---|---|---|
| 1 | Mở URL ứng dụng từ laptop/điện thoại đóng vai client | Trang đăng nhập từ server, không phải file HTML trên máy |
| 2 | Đăng nhập bằng tài khoản quản trị | Danh sách được tải từ MySQL |
| 3 | Thêm “Cơm gà Demo Week 1”, món chính, 45.000đ, còn món | Có thông báo lưu và dòng mới có ID |
| 4 | Tải lại trang hoặc mở lại URL | Dòng vừa thêm vẫn tồn tại |
| 5 | Tìm theo tên món | Lọc ra đúng món vừa tạo |
| 6 | Kiểm tra bản ghi trong MySQL | Tên, giá và tình trạng khớp với giao diện |
| 7 | Sửa giá thành 50.000đ, đổi sang hết món | Giao diện và DB cùng cập nhật |
| 8 | Nếu cần, xóa đúng món demo sau khi đã ghi hình | Món không còn trong kết quả truy vấn |

Truy vấn đọc minh chứng (chạy bằng tài khoản/công cụ DB phù hợp):

```sql
SELECT id, name, category, price, is_available, created_at, updated_at
FROM dishes
WHERE name = 'Cơm gà Demo Week 1';
```

## Nội dung giải thích

- Trình duyệt gửi yêu cầu đến địa chỉ nào, qua cổng nào?
- Apache/PHP và MySQL chạy ở đâu?
- Vì sao trình duyệt không cần và không được kết nối trực tiếp MySQL?
- Firewall cho phép dịch vụ nào, SSH dành cho ai?
- Dữ liệu nằm trên ổ đĩa nào; tải lại trang lấy dữ liệu từ đâu?
- Nhóm tự thực hiện phần nào; nguồn ban đầu và hỗ trợ AI được khai báo ở đâu?

## Bằng chứng cần lưu

Ảnh danh sách trước/sau thêm và sau tải lại; ảnh/trích kết quả SELECT; ảnh server/port/firewall; video ngắn ghi chuỗi thao tác. Gắn link hoặc tên file thật trong `evidence/README.md`. Ảnh giao diện dựng sẵn và kết quả CI không thay thế video trên server nhóm.
