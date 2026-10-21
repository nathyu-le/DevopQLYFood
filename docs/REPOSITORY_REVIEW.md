# Rà soát repository và hosting — 21/10/2026

Đợt app/DB dựa trên commit `b44951d`; bản chặn truy cập được thêm ở `8ccfd83`, hướng dẫn ở `526829b`; đợt thông tin hosting này dựa trên `526829b`.

| Nội dung kiểm tra | Kết quả và thay đổi |
|---|---|
| URL website | Dùng thống nhất https://thitruongmmo.com/DevopQLYFood/public/ |
| Minh chứng app + DB | Thêm 3 ảnh đã cắt được cho phép công khai; cùng ID 9, giá 45.000 → 50.000đ, SQL is_available 0 |
| Trạng thái README/checklist/evidence | Sửa các dòng cũ ghi chưa có kiểm tra DB; đánh dấu đúng phần ảnh đã chứng minh |
| Hướng dẫn cài đặt | README dẫn cPanel trước; Ubuntu là phương án tham chiếu, không ghi đã cài trên hosting |
| Kịch bản demo | Đề Week 1 cho nộp ảnh/log hoặc video; video liền mạch là lựa chọn bổ sung |
| Kiến trúc | Thay sơ đồ PNG/Mermaid bằng hiện trạng cp1/Linux/Apache/MariaDB theo ảnh; giữ hướng dẫn Ubuntu riêng; bổ sung PHP 8.2 và quota tài khoản; DB host/port chưa biết |
| Week 2–4 | Checklist bám đề chi tiết; chưa ghi thực hiện Docker/CI/CD/monitoring. Playbook Week 3 ≥2 sự cố, Week 4 ≥3 |
| Phân công | Giữ Huy phụ trách hạ tầng, app/DB, tài liệu/GitHub; Hải kiểm tra, Tùng minh chứng. Chưa gán người thao tác ảnh nếu chưa xác nhận |
| Source và CI | Không thay PHP/SQL/workflow; giữ kết quả CI MySQL 8.0 riêng với ảnh thao tác MariaDB hosting |
| Thông tin server | Thêm nguyên bản hai ảnh Server Information/Domains, xác nhận domain root `/public_html` và Force HTTPS Redirect Off |
| Bảo vệ thư mục riêng | GET schema và README trên hosting trả 200; thêm `.htaccess` cho layout hiện tại và hướng dẫn upload/rollback. Sau upload, HTTP xác nhận health/login/CSS 200, schema/README 403; có log thật, không đồng nhất chặn URL với firewall OS |
| Credentials | Không đưa config.local.php hoặc mật khẩu/hash quản trị thật vào bộ thay đổi |

## Phần Week 1 còn cần hoàn thiện

- Distro/version OS, đường dẫn OS đầy đủ và DB host/port/datadir; tài nguyên vật lý toàn máy không suy ra từ quota tài khoản.
- Phần OS/user/SSH/firewall tạm hoãn theo quyết định dùng cp1 demo Week 1. cp1 không hỗ trợ Terminal theo người triển khai; xem xét bổ sung khi chọn VPS hoặc hosting khác cho Week 2, chưa đánh dấu hoàn tất.
- Đã upload và kiểm tra `.htaccess` trên các URL ghi trong log. Tiếp tục bổ sung các trường chưa biết vào sơ đồ khi có bằng chứng.
- Ảnh tải lại/tìm kiếm và trạng thái web; ảnh hiện có xác nhận trạng thái 0 qua SQL.

[Xem minh chứng app/DB](../evidence/application/demo-2026-10-21.md) · [Checklist Week 1](WEEK1_CHECKLIST.md).
