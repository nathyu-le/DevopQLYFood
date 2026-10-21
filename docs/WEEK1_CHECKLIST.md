# Theo dõi checkpoint Week 1

Đối chiếu ảnh yêu cầu “DEVOPS FINAL PROJECT — WEEK 1: Infrastructure Setup & Initial Deployment” do người dùng cung cấp. Các checkbox chỉ phản ánh đúng phần hiện có trong repository, không tự đánh điểm thay giảng viên.

| Tiêu chí | Điểm theo đề | Hiện có | Còn cần làm |
|---|---:|---|---|
| Kiến trúc đủ thành phần và luồng truy cập | 0,5 | PNG + mô tả phương án client/server/network/app/DB/storage | Đối chiếu và cập nhật theo môi trường thật |
| Server setup/networking hoạt động và giải thích được | 0,5 | Hướng dẫn và mẫu thu thập cấu hình | Dựng server, cấu hình SSH/firewall/ports, lưu kết quả thật |
| Ứng dụng + DB chạy, thao tác dữ liệu thành công | 0,7 | Source PHP, SQL, kiểm tra source và kịch bản demo | Triển khai lên server nhóm; demo từ máy khách, chứng minh ghi/đọc |
| README, repository, phân công | 0,3 | README và bảng đầu việc dự kiến | Điền tên thành viên, xác nhận phân công và bổ sung hoạt động thật |
| **Tổng** | **2,0** | **Bộ repo chuẩn bị** | **Chưa đủ bằng chứng hoàn tất Week 1** |

## Đã chuẩn bị trong repository

- [x] Chọn đề tài và ghi phạm vi, nguồn code.
- [x] Source ứng dụng PHP có CRUD với MySQL.
- [x] SQL schema và dữ liệu mẫu tùy chọn.
- [x] `architecture.png` được gắn nhãn dự kiến.
- [x] README cài đặt thủ công ban đầu.
- [x] Bảng phân công theo đầu việc, chờ điền thành viên.
- [x] Kịch bản demo và danh mục minh chứng cần thu thập.

## Cần xác nhận bằng triển khai thật

- [ ] Môi trường server phù hợp đề; ghi OS và tài nguyên.
- [ ] User/SSH và firewall/security group đã cấu hình.
- [ ] App và DB hoạt động trên server nhóm.
- [ ] Truy cập từ máy khách và ghi/đọc một bản ghi thật.
- [ ] Có ảnh/log/video chứng minh các mục trên.
- [ ] Có tên và phân công chính thức của nhóm.
- [ ] Giảng viên truy cập được repo và các link minh chứng.

Docker/Compose và CI/CD triển khai thuộc Week 2. Monitoring, cảnh báo, backup/restore và playbook vận hành thuộc checkpoint sau; không ghi là đã hoàn thành trong Week 1.
