# Theo dõi checkpoint Week 1

Đối chiếu ảnh yêu cầu “DEVOPS FINAL PROJECT — WEEK 1: Infrastructure Setup & Initial Deployment” do người dùng cung cấp. Các checkbox chỉ phản ánh đúng phần hiện có trong repository, không tự đánh điểm thay giảng viên.

**Bản nộp tiến độ, cập nhật 21/10/2026:** người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được trên hosting cPanel sau khi cấu hình admin. Cần kiểm tra CRUD/đối chiếu DB, thu ảnh/video và xác minh phần server/network theo đề. Chưa đánh dấu hoàn tất Week 1 hoặc mặc định cần VPS. Xem [kế hoạch](DEPLOYMENT_PLAN.md).

| Tiêu chí | Điểm theo đề | Hiện có | Còn cần làm |
|---|---:|---|---|
| Kiến trúc đủ thành phần và luồng truy cập | 0,5 | PNG + mô tả phương án client/server/network/app/DB/storage | Đối chiếu và cập nhật theo môi trường thật |
| Server setup/networking hoạt động và giải thích được | 0,5 | Hướng dẫn và mẫu thu thập cấu hình | Dựng server, cấu hình SSH/firewall/ports, lưu kết quả thật |
| Ứng dụng + DB chạy, thao tác dữ liệu thành công | 0,7 | Source đã qua CI; người triển khai xác nhận website mở được trên cPanel | Demo từ client: thêm/tải lại/sửa và SELECT đúng bản ghi trên hosting |
| README, repository, phân công | 0,3 | README và phân công 3 thành viên đã cập nhật | Bổ sung kết quả và minh chứng hoạt động thật |
| **Tổng** | **2,0** | **Bộ repo chuẩn bị** | **Chưa đủ bằng chứng hoàn tất Week 1** |

## Đã chuẩn bị trong repository

- [x] Chọn đề tài và ghi phạm vi, nguồn code.
- [x] Source ứng dụng PHP có CRUD với MySQL.
- [x] SQL schema và dữ liệu mẫu tùy chọn.
- [x] `architecture.png` được gắn nhãn dự kiến.
- [x] README cài đặt thủ công ban đầu.
- [x] Phân công Week 1: Huy phụ trách 3 đầu việc chính; Hải và Tùng phụ trách kiểm tra/minh chứng.
- [x] Kịch bản demo và danh mục minh chứng cần thu thập.

## Cần xác nhận bằng triển khai thật

- [ ] Môi trường server phù hợp đề; ghi OS và tài nguyên.
- [ ] User/SSH và firewall/security group đã cấu hình.
- [x] Người triển khai xác nhận website trên hosting cPanel đã truy cập được sau khi cấu hình admin (21/10/2026).
- [x] Kiểm tra HTTP trên hosting: trang login trả 200; health trả 200 với status ok, xác nhận kết nối và đọc bảng theo endpoint source. [Log](../evidence/application/http-access-2026-10-21.md).
- [ ] App và DB trên hosting qua kiểm tra ghi/đọc thực tế, có kết quả đối chiếu.
- [ ] Truy cập từ máy khách và ghi/đọc một bản ghi thật.
- [ ] Có ảnh/log/video chứng minh các mục trên.
- [x] Có tên và phân công Week 1 của 3 thành viên trong `TEAM.md`.
- [ ] Giảng viên truy cập được repo và các link minh chứng.

Khi thực hiện phần bổ sung, ghi ngày thật, môi trường và commit vào [minh chứng Week 1](../evidence/week1/README.md). Giữ bản source đã nộp theo commit để không lẫn với các cập nhật Week 2–4.

Docker/Compose và CI/CD triển khai thuộc Week 2. Monitoring, cảnh báo, backup/restore và playbook vận hành thuộc checkpoint sau; không ghi là đã hoàn thành trong Week 1.
