# Week 2 — Containerization & CI/CD

**Trạng thái 28/10/2026:** CI/CD source lên cPanel đã chạy thành công. Docker/Compose/persistence được chứng minh trên runner; chưa chứng minh container trên hosting. Chưa đánh dấu toàn bộ Week 2 hoàn tất khi phần này chưa được giảng viên xác nhận.

Nguồn yêu cầu: ảnh đề Week 2 đã đối chiếu ngày 21/10/2026. Mục tiêu Automated Deployment Pipeline, tối đa 2,5/10; không dự đoán điểm đạt.

## Đã có kết quả

- [x] Ghi rõ hai môi trường CI và hosting trong [sơ đồ Week 2](WEEK2_ARCHITECTURE.md).
- [x] Dockerfile và .dockerignore; Compose có app, DB, network, secrets và named volume; build/run qua.
- [x] Cùng ID/tên/giá/trạng thái còn sau recreate cả app + DB trên runner, giữ volume: 17 container checks qua.
- [x] Secrets ngoài Git/build context; gói source không chứa config thật, DB hoặc .htaccess.
- [x] 14 validation checks, PHP lint và regression có ý nghĩa; tests của source package/HTTP gate qua.
- [x] Workflow build → test → source artifact → deploy FTPS → health/revision. Deploy phụ thuộc container-checks.
- [x] Kết nối hosting bằng tài khoản FTP riêng và TLS có xác minh certificate.
- [x] Push thay đổi giao diện nhỏ → run tự động → web có dòng mới và đúng SHA, không cập nhật source thủ công.
- [x] Run HTTP health check thất bại làm pipeline fail và dừng trước FTP; ghi đúng là lỗi pre-deploy health, không gọi là validation test thất bại.
- [x] README, hướng dẫn tái triển khai và log/minh chứng trước sau đã cập nhật.
- [x] Đã kiểm tra Terminal hosting: Docker command not found; ghi rõ giới hạn.

## Còn thiếu hoặc phụ thuộc xác nhận

- [ ] Chạy được Docker/Compose trên môi trường triển khai của nhóm nếu rubric yêu cầu điều đó; Docker trên CI chưa tự thay tiêu chí hosting.
- [ ] Có xác nhận của giảng viên nếu sử dụng cách Docker trên runner + PHP trực tiếp trên cPanel để đánh giá tiêu chí trên.
- [ ] Nhóm bổ sung ảnh/video trình bày, đóng góp thực tế và phần OS/user/SSH/firewall Week 1 còn thiếu.

## Kết quả để nộp / demo

[Run 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900), event **push**, commit `6583790e9850d7cdbdfa9aebac1bc896781dbcfb`, attempt 1: container-checks và deploy-cpanel success. Public login/health/version/CSS/JS trả 200; health ok, revision đúng commit, dòng mới xuất hiện. [Bộ minh chứng](../evidence/week2/deploy-2026-10-28.md).

| Rubric | Điểm tối đa | Minh chứng và giới hạn |
|---|---:|---|
| Dockerfile + Compose build/run | 0,7 | Run và source; hiện chạy trên runner |
| Volume và DB persistence | 0,3 | Cùng bản ghi qua recreate trên runner |
| CI/CD build/test/deploy tự động | 1,2 | Run push và FTPS deploy thành công |
| Commit → pipeline → app update | 0,3 | Dòng giao diện trước/sau và revision đúng SHA |
| Tổng | 2,5 | Phần Docker trên hosting vẫn cần đối chiếu tiêu chí |

[Docker setup](DOCKER_SETUP.md) · [CI/CD setup](CICD_SETUP.md) · [Evidence](../evidence/week2/README.md).
