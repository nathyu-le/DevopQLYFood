# Theo dõi checkpoint Week 1

Đối chiếu ảnh yêu cầu “DEVOPS FINAL PROJECT — WEEK 1: Infrastructure Setup & Initial Deployment” do người dùng cung cấp. Các checkbox chỉ phản ánh đúng phần hiện có trong repository, không tự đánh điểm thay giảng viên.

**Bản nộp tiến độ, cập nhật 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được trên hosting cPanel sau khi cấu hình admin. Đã đối chiếu 3 ảnh thêm/sửa/SELECT cùng món ID 9; đã có ảnh Server Information/Domains và sơ đồ hosting, còn PHP/quota/SSH/firewall. [Bộ ảnh và phạm vi kiểm chứng](../evidence/application/demo-2026-10-21.md). Chưa đánh dấu hoàn tất Week 1 hoặc mặc định cần VPS. Xem [kế hoạch](DEPLOYMENT_PLAN.md).

| Tiêu chí | Điểm theo đề | Hiện có | Còn cần làm |
|---|---:|---|---|
| Kiến trúc đủ thành phần và luồng truy cập | 0,5 | PNG + mô tả hosting thật, các trường chưa biết được ghi rõ | Bổ sung PHP, DB host/port và vị trí lưu trữ khi có bằng chứng |
| Server setup/networking hoạt động và giải thích được | 0,5 | Ảnh cp1/Linux/Apache/MariaDB và Document Root | Đối chiếu môi trường phù hợp đề; bổ sung tài nguyên, user/SSH/firewall/ports |
| Ứng dụng + DB chạy, thao tác dữ liệu thành công | 0,7 | Có ảnh thêm ID 9 giá 45.000đ, sửa 50.000đ và SELECT cùng ID/giá, trạng thái 0 | Bổ sung ảnh web thấy rõ trạng thái; tải lại/tìm kiếm hoặc video liền mạch để hoàn thiện kịch bản |
| README, repository, phân công | 0,3 | README và phân công 3 thành viên đã cập nhật | Bổ sung kết quả và minh chứng hoạt động thật |
| **Tổng** | **2,0** | **Bộ repo chuẩn bị** | **Chưa đủ bằng chứng hoàn tất Week 1** |

## Đã chuẩn bị trong repository

- [x] Chọn đề tài và ghi phạm vi, nguồn code.
- [x] Source ứng dụng PHP có CRUD với MySQL.
- [x] SQL schema và dữ liệu mẫu tùy chọn.
- [x] `architecture.png` mô tả hosting theo ảnh đã cung cấp, ghi rõ thông số chưa xác minh.
- [x] README cài đặt thủ công ban đầu.
- [x] Phân công Week 1: Huy phụ trách 3 đầu việc chính; Hải và Tùng phụ trách kiểm tra/minh chứng.
- [x] Kịch bản demo và danh mục minh chứng cần thu thập.

## Cần xác nhận bằng triển khai thật

- [x] Có ảnh Server Information và Domains: cp1, Linux x86_64, Apache 2.4.67, MariaDB 10.11.16 và `/public_html`.
- [ ] Xác minh distro/version OS, tài nguyên và môi trường phù hợp yêu cầu server của đề.
- [ ] Upload `.htaccess` và kiểm tra chặn các URL ngoài `public/` trên hosting.
- [ ] User/SSH và firewall/security group đã cấu hình.
- [x] Người triển khai xác nhận website trên hosting cPanel đã truy cập được sau khi cấu hình admin (21/10/2026).
- [x] Kiểm tra HTTP trên hosting: trang login trả 200; health trả 200 với status ok, xác nhận kết nối và đọc bảng theo endpoint source. [Log](../evidence/application/http-access-2026-10-21.md).
- [x] Có ảnh thêm và sửa món trên hosting, SELECT đối chiếu cùng bản ghi ID 9 trong DB. [Minh chứng](../evidence/application/demo-2026-10-21.md).
- [x] Client hiển thị bản ghi đã thêm/sửa; SQL xác nhận tên/ID/giá và trạng thái 0.
- [ ] Bổ sung ảnh web thấy rõ trạng thái của ID 9 và bước tải lại/tìm kiếm hoặc video liên tục.
- [ ] Có ảnh/log hạ tầng OS/tài nguyên, user/SSH, firewall/ports và sơ đồ đúng môi trường thật.
- [x] Có tên và phân công Week 1 của 3 thành viên trong `TEAM.md`.
- [ ] Giảng viên truy cập được repo và các link minh chứng.

Khi thực hiện phần bổ sung, ghi ngày thật, môi trường và commit vào [minh chứng Week 1](../evidence/week1/README.md). Giữ bản source đã nộp theo commit để không lẫn với các cập nhật Week 2–4.

Docker/Compose và CI/CD triển khai thuộc Week 2. Monitoring, cảnh báo, backup/restore và playbook vận hành thuộc checkpoint sau; không ghi là đã hoàn thành trong Week 1.
