# Minh chứng Week 1

**Cập nhật 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được sau khi cấu hình admin trên hosting cPanel. Đã có [3 ảnh thêm/sửa và SELECT đối chiếu](../application/demo-2026-10-21.md) cùng món ID 9. Bộ ảnh xác nhận phần ghi/đọc DB; chưa thay thế minh chứng hạ tầng hoặc toàn bộ kịch bản demo.

| Hạng mục | Trạng thái |
|---|---|
| URL website | Người triển khai xác nhận truy cập được; kiểm tra độc lập trang đăng nhập HTTP 200 |
| Health endpoint trên hosting | HTTP 200, `{"status":"ok"}` — xem [log truy cập](../application/http-access-2026-10-21.md) |
| Thêm món và sửa giá | Có ảnh thêm ID 9 giá 45.000đ và sửa cùng ID thành 50.000đ |
| Tải lại/tìm kiếm và trạng thái trên web | Cần bổ sung ảnh/bước liên tục; cột trạng thái đang ngoài phần ảnh chụp |
| SELECT cùng bản ghi trong DB hosting | ID 9, tên Cơm gà Demo Week 1, price 50000, is_available 0 |
| OS/runtime/Document Root | Có [ảnh cp1/Linux/Apache 2.4.67/MariaDB 10.11.16 và `/public_html`](../server/README.md); có thêm PHP 8.2 và quota CPU 100% / RAM 600 MB / disk 1.000 MB; chưa có distro/version OS |
| User/SSH, firewall/ports | Có giao diện quản lý SSH keys; chưa xác minh đăng nhập shell, quyền user và firewall/ports theo đề |
| Sơ đồ hiện trạng hosting | Đã cập nhật theo ảnh server/domain; đã thêm PHP 8.2/quota và log `.htaccess`; DB host/port và datadir chưa xác minh |

Bản source được gửi để upload: commit `ca9ef4fcd9214266af681963caf2076e44bc7767`. Cấu hình DB và admin được tạo riêng trên hosting, không lưu trong repo. Commit trên nhận diện bản source đã cung cấp, chưa xác nhận file đang chạy trên hosting trùng hoàn toàn nếu có chỉnh thêm thủ công.

- [Thông tin server và quyền hosting](../server/README.md).
- [Minh chứng app và DB cần thu](../application/README.md).
- [Kịch bản demo](../../docs/DEMO.md).
- [Hướng dẫn cPanel](../../docs/CPANEL_SETUP.md).
- [Checklist Week 1](../../docs/WEEK1_CHECKLIST.md).
- [Kế hoạch tiếp tục](../../docs/DEPLOYMENT_PLAN.md).

Hải phụ trách kiểm tra chức năng/đối chiếu DB; Tùng phụ trách tổng hợp ảnh/log/video theo [phân công Week 1](../../docs/TEAM.md). Đây là phân công, chưa ghi nhận hai thành viên đã thực hiện demo.

Mỗi minh chứng ghi ngày, môi trường, người thực hiện, file/link và commit tương ứng. Chưa có xác nhận của giảng viên về việc dùng hosting thay yêu cầu tự cài/cấu hình server hoặc thời hạn bổ sung.
