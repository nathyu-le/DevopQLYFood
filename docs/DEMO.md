# Kịch bản demo Week 1

**Cập nhật 21/10/2026:** ứng dụng đang được nhóm dùng trên [hosting cPanel](https://thitruongmmo.com/DevopQLYFood/public/). Đã có [3 ảnh thêm/sửa và SELECT cùng bản ghi ID 9](../evidence/application/demo-2026-10-21.md). Tài liệu này hướng dẫn toàn bộ trình tự demo; ảnh hiện có chưa thể hiện mọi bước, nhất là tải lại/tìm kiếm và trạng thái trên web.

**Bản trình bày cho một người:** [WEEK1_PRESENTATION.md](WEEK1_PRESENTATION.md) có lời đọc theo từng màn hình, dữ liệu demo và câu trả lời khi được hỏi.

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
- Web server/PHP và database chạy ở đâu? Ghi đúng loại dịch vụ thực tế của hosting; phương án Apache trên Ubuntu là tài liệu tham chiếu.
- Vì sao trình duyệt không cần và không được kết nối trực tiếp MySQL?
- Firewall cho phép dịch vụ nào, SSH dành cho ai?
- Dữ liệu nằm trên ổ đĩa nào; tải lại trang lấy dữ liệu từ đâu?
- Nhóm tự thực hiện phần nào; nguồn ban đầu và hỗ trợ AI được khai báo ở đâu?

## Bằng chứng cần lưu

Đề Week 1 cho phép video, ảnh hoặc log chạy app/database. Lưu ảnh trước/sau thêm, sau tải lại, kết quả SELECT và thông tin server/port/firewall; video ngắn liền mạch là một cách bổ sung rõ quá trình, không phải sản phẩm bắt buộc riêng nếu dùng ảnh/log theo đề. Gắn file/link thật trong `evidence/README.md`. Ảnh dựng sẵn và kết quả CI không thay cho minh chứng thao tác thật trên môi trường triển khai.
