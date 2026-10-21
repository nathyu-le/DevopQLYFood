# Minh chứng Week 1

**Cập nhật 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được sau khi cấu hình admin trên hosting cPanel. Đây là xác nhận tiến độ; chưa có bộ ảnh/video/SELECT để đánh dấu demo thành công.

| Hạng mục | Trạng thái |
|---|---|
| URL website | Người triển khai xác nhận truy cập được; kiểm tra độc lập trang đăng nhập HTTP 200 |
| Health endpoint trên hosting | HTTP 200, `{"status":"ok"}` — xem [log truy cập](../application/http-access-2026-10-21.md) |
| Đăng nhập, thêm món, tải lại và sửa giá | Chờ kiểm tra và lưu minh chứng |
| SELECT cùng bản ghi trong DB hosting | Chờ thực hiện và lưu kết quả |
| OS/tài nguyên, user/SSH, firewall/ports | Chờ thu thập, xác minh quyền của tài khoản hosting và đối chiếu đề |
| Sơ đồ hiện trạng hosting | Chờ xác nhận đường dẫn, web server, PHP và DB thực tế |

Bản source được gửi để upload: commit `ca9ef4fcd9214266af681963caf2076e44bc7767`. Cấu hình DB và admin được tạo riêng trên hosting, không lưu trong repo. Commit trên nhận diện bản source đã cung cấp, chưa xác nhận file đang chạy trên hosting trùng hoàn toàn nếu có chỉnh thêm thủ công.

- [Thông tin server và quyền hosting](../server/README.md).
- [Minh chứng app và DB cần thu](../application/README.md).
- [Kịch bản demo](../../docs/DEMO.md).
- [Hướng dẫn cPanel](../../docs/CPANEL_SETUP.md).
- [Checklist Week 1](../../docs/WEEK1_CHECKLIST.md).
- [Kế hoạch tiếp tục](../../docs/DEPLOYMENT_PLAN.md).

Hải phụ trách kiểm tra chức năng/đối chiếu DB; Tùng phụ trách tổng hợp ảnh/log/video theo [phân công Week 1](../../docs/TEAM.md). Đây là phân công, chưa ghi nhận hai thành viên đã thực hiện demo.

Mỗi minh chứng ghi ngày, môi trường, người thực hiện, file/link và commit tương ứng. Chưa có xác nhận của giảng viên về việc dùng hosting thay yêu cầu tự cài/cấu hình server hoặc thời hạn bổ sung.
