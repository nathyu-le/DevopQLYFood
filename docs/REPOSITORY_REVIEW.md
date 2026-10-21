# Rà soát repository sau khi bổ sung minh chứng app/DB — 21/10/2026

Bản repo trước cập nhật: `b44951d9750a5c2830d329af040fb9a07159b169`.

| Nội dung kiểm tra | Kết quả và thay đổi |
|---|---|
| URL website | Dùng thống nhất https://thitruongmmo.com/DevopQLYFood/public/ |
| Minh chứng app + DB | Thêm 3 ảnh đã cắt được cho phép công khai; cùng ID 9, giá 45.000 → 50.000đ, SQL is_available 0 |
| Trạng thái README/checklist/evidence | Sửa các dòng cũ ghi chưa có kiểm tra DB; đánh dấu đúng phần ảnh đã chứng minh |
| Hướng dẫn cài đặt | README dẫn cPanel trước; Ubuntu là phương án tham chiếu, không ghi đã cài trên hosting |
| Kịch bản demo | Đề Week 1 cho nộp ảnh/log hoặc video; video liền mạch là lựa chọn bổ sung |
| Kiến trúc | Thêm bảng thông tin hosting đã biết; sơ đồ PNG Ubuntu vẫn được ghi rõ là tham chiếu, chưa phải sơ đồ hosting hoàn chỉnh |
| Week 2–4 | Checklist bám đề chi tiết; chưa ghi thực hiện Docker/CI/CD/monitoring. Playbook Week 3 ≥2 sự cố, Week 4 ≥3 |
| Phân công | Giữ Huy phụ trách hạ tầng, app/DB, tài liệu/GitHub; Hải kiểm tra, Tùng minh chứng. Chưa gán người thao tác ảnh nếu chưa xác nhận |
| Source và CI | Không thay source PHP/SQL/workflow trong lần cập nhật này; kết quả CI PHP/MySQL trước đó giữ nguyên phạm vi |
| Credentials | Không đưa config.local.php hoặc mật khẩu/hash quản trị thật vào bộ thay đổi |

## Phần Week 1 còn cần hoàn thiện

- OS/tài nguyên, phiên bản web server/PHP/DB, DocumentRoot và vị trí DB.
- Quyền user/SSH, firewall/ports; đối chiếu yêu cầu tự cài/cấu hình môi trường theo đề.
- Sơ đồ đúng môi trường hosting sau khi đủ thông tin.
- Ảnh tải lại/tìm kiếm và trạng thái web; ảnh hiện có xác nhận trạng thái 0 qua SQL.

[Xem minh chứng app/DB](../evidence/application/demo-2026-10-21.md) · [Checklist Week 1](WEEK1_CHECKLIST.md).
