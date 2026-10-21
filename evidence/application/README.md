# Minh chứng app + database

**Cập nhật 21/10/2026:** website tại [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/). Có kiểm tra truy cập HTTP và ảnh thao tác thực tế trên hosting.

| Bộ minh chứng | Kết quả |
|---|---|
| [Log HTTP](http-access-2026-10-21.md) | Health 200/status ok, trang đăng nhập 200 |
| [Ảnh thêm/sửa và SELECT](demo-2026-10-21.md) | Cùng món ID 9: thêm 45.000đ, sửa 50.000đ; SQL xác nhận price 50000, is_available 0 |

Bộ ảnh chứng minh thao tác thêm/sửa và dữ liệu đối chiếu trong DB. Cột trạng thái web nằm ngoài phần ảnh chụp; trạng thái 0 được xác nhận từ SQL. Bước tải lại/tìm kiếm riêng, xóa món và video liên tục chưa được thể hiện trong bộ ảnh này.

Tiếp theo: bổ sung ảnh web thấy rõ trạng thái của ID 9, bước tải lại/tìm kiếm hoặc video liền mạch theo [kịch bản demo](../../docs/DEMO.md); thu minh chứng hạ tầng tại [server/](../server/README.md). Ghi người thực hiện từng đầu việc theo công việc thật.
