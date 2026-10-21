# Week 2 — Containerization & CI/CD

**Trạng thái: chưa bắt đầu triển khai.** Đã đối chiếu ảnh đề chi tiết “DEVOPS FINAL PROJECT — WEEK 2: Containerization & CI/CD Automation” (`1000003653.jpg`), ngày 21/10/2026. Mục tiêu: **Automated Deployment Pipeline**. Điểm tối đa: **2,5/10**, demo nhóm và bảo vệ kỹ thuật.

**Môi trường chưa chốt:** Week 1 đang demo trên cp1; người triển khai xác nhận gói này không hỗ trợ Terminal và tạm hoãn phần OS/user/SSH/firewall. Khi bắt đầu Week 2 sẽ xem xét VPS hoặc hosting khác. Môi trường được chọn phải thực sự chạy được Docker/Compose và cho pipeline deploy tự động; chỉ có Terminal/SSH chưa đủ kết luận đáp ứng. [Kế hoạch chuyển môi trường](DEPLOYMENT_PLAN.md).

## Mục tiêu

Đưa cùng ứng dụng PHP/MySQL vào Docker/Compose, bảo đảm dữ liệu bền vững và tự động build → test → deploy khi có commit mới.

## Việc cần hoàn thành

- [ ] Ghi môi trường chạy Docker và cập nhật sơ đồ triển khai thực tế.
- [ ] Có `Dockerfile` build application image, hạn chế lớp không cần thiết; thêm `.dockerignore` để loại secrets và file không cần khỏi build context.
- [ ] Có `docker-compose.yml` định nghĩa app, database, network và persistent volume; build/run thành công.
- [ ] Database dùng persistent volume; chứng minh dữ liệu vẫn còn sau khi tạo lại container theo kịch bản kiểm tra không xóa volume.
- [ ] Secrets được cấu hình ngoài repository và không đóng vào image.
- [ ] Workflow thể hiện đủ checkout → build → test → tạo image → deploy từ source mới.
- [ ] Có ít nhất một automated test có ý nghĩa và bước kiểm tra lỗi chặn deploy.
- [ ] Deploy tự động từ commit mới lên môi trường đã xác định; không cần đăng nhập server để chạy lệnh cập nhật thủ công.
- [ ] Xác định cách runner kết nối VPS. Nếu chuyển sang VM local, có self-hosted runner hoặc kết nối hợp lệ; đề không bắt buộc public IP.
- [ ] Demo sửa một thay đổi nhỏ → commit → pipeline qua → ứng dụng trên server cập nhật.
- [ ] README có hướng dẫn tái triển khai và xử lý cấu hình.
- [ ] Có ảnh/log/video thật và link run tương ứng trong `evidence/week2/`.

## Trình tự live demo

1. Push một thay đổi nhỏ lên nhánh triển khai, ghi commit SHA và phiên bản đang chạy trước đó.
2. Mở từng bước workflow, cho thấy build/test/tạo image/deploy thành công.
3. Refresh trang/API trên server, xác nhận thay đổi của đúng commit đã xuất hiện.
4. Thử hoặc giải thích một trường hợp test thất bại; chứng minh lỗi test làm pipeline fail và chặn deploy.
5. Tạo bản ghi thử, recreate container nhưng giữ volume, đối chiếu lại bản ghi. Lưu trạng thái trước/sau và lệnh đã dùng.

## Hồ sơ nộp và rubric

| Tiêu chí | Điểm theo đề | Sản phẩm / minh chứng |
|---|---:|---|
| Dockerfile + Compose build/run đúng | 0,7 | `Dockerfile`, `docker-compose.yml`, log build và trạng thái services |
| Volume và DB persistence được chứng minh | 0,3 | Cùng bản ghi còn sau khi recreate container |
| CI/CD đủ build/test/deploy, hoạt động tự động | 1,2 | Workflow `.github/workflows/*.yml`, test source và workflow logs |
| Demo commit → pipeline → app update rõ ràng | 0,3 | Video/ảnh phiên bản trước và sau deploy, commit và run tương ứng |
| **Tổng** | **2,5** | README hướng dẫn chạy pipeline và các link minh chứng |

## Hiện có và giới hạn

Ứng dụng đã có validation và kiểm tra CRUD với MySQL tạm trong GitHub Actions. Đây là phần có thể tái sử dụng; chưa có Docker/Compose hoặc pipeline deploy để đánh dấu checkpoint này hoàn tất.

## Nơi cập nhật

- Source dùng chung: `app/`, `public/`, `database/`, `config/`.
- Docker/Compose: thư mục gốc repo, tạo khi thực hiện.
- CI/CD: `.github/workflows/`.
- [Minh chứng Week 2](../evidence/week2/README.md).
- Phân công tuần này: bổ sung vào [TEAM.md](TEAM.md) khi nhóm thống nhất; hiện mới xác nhận phân công Week 1.
