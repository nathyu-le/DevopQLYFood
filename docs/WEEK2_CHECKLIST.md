# Week 2 — Containerization & CI/CD

**Trạng thái: chưa bắt đầu triển khai.** Checklist chuẩn bị theo brief chung; điều chỉnh theo yêu cầu Week 2 chi tiết khi giảng viên cung cấp. Điểm checkpoint theo brief: **2,5**.

## Mục tiêu

Đưa cùng ứng dụng PHP/MySQL vào Docker/Compose, bảo đảm dữ liệu bền vững và tự động build → test → deploy khi có commit mới.

## Việc cần hoàn thành

- [ ] Ghi môi trường chạy Docker và cập nhật sơ đồ triển khai thực tế.
- [ ] Có `Dockerfile`, `.dockerignore` và `docker-compose.yml` dùng được.
- [ ] App và MySQL chạy cùng nhau với network/phân quyền phù hợp.
- [ ] Database dùng persistent volume; chứng minh dữ liệu vẫn còn sau khi tạo lại container theo kịch bản kiểm tra không xóa volume.
- [ ] Secrets được cấu hình ngoài repository và không đóng vào image.
- [ ] Pipeline build ứng dụng/image từ source mới.
- [ ] Có ít nhất một automated test có ý nghĩa và bước kiểm tra lỗi chặn deploy.
- [ ] Deploy tự động từ commit mới lên môi trường đã xác định.
- [ ] Demo sửa một thay đổi nhỏ → commit → pipeline qua → ứng dụng trên server cập nhật.
- [ ] README có hướng dẫn tái triển khai và xử lý cấu hình.
- [ ] Có ảnh/log/video thật và link run tương ứng trong `evidence/week2/`.

## Hiện có và giới hạn

Ứng dụng đã có validation và kiểm tra CRUD với MySQL tạm trong GitHub Actions. Đây là phần có thể tái sử dụng; chưa có Docker/Compose hoặc pipeline deploy để đánh dấu checkpoint này hoàn tất.

## Nơi cập nhật

- Source dùng chung: `app/`, `public/`, `database/`, `config/`.
- Docker/Compose: thư mục gốc repo, tạo khi thực hiện.
- CI/CD: `.github/workflows/`.
- [Minh chứng Week 2](../evidence/week2/README.md).
- Phân công tuần này: bổ sung vào [TEAM.md](TEAM.md) khi nhóm thống nhất; hiện mới xác nhận phân công Week 1.
