# Week 2 — Containerization & CI/CD

**Trạng thái: đang thực hiện, chưa hoàn tất.** Đã đối chiếu ảnh đề chi tiết “DEVOPS FINAL PROJECT — WEEK 2: Containerization & CI/CD Automation” (`1000003653.jpg`), ngày 21/10/2026. Mục tiêu: **Automated Deployment Pipeline**. Điểm tối đa: **2,5/10**, demo nhóm và bảo vệ kỹ thuật.

**Môi trường theo xác nhận mới nhất 28/10/2026:** người triển khai đã mua gói **cPanel mới có Terminal**, không chọn VPS. Docker/Compose đã kiểm tra thành công trên Actions; chưa có output xác minh Docker/Compose/quyền daemon trên gói mới. Pipeline source FTPS đã chuẩn bị nhưng chưa kết nối. Có Terminal chưa xác nhận được Docker daemon hoạt động. [Kiểm tra và cấu hình cPanel](CICD_SETUP.md) · [Kết quả CI](../evidence/week2/README.md).

## Mục tiêu

Đưa cùng ứng dụng PHP/MySQL vào Docker/Compose, bảo đảm dữ liệu bền vững và tự động build → test → deploy khi có commit mới.

## Việc cần hoàn thành

Các dấu đã làm dưới đây ghi rõ phạm vi **runner CI**, chưa phải đã deploy container trên cPanel.

- [ ] Xác minh Docker/Compose/quyền daemon trên Terminal gói cPanel mới, ghi thông số thực tế và cập nhật sơ đồ.
- [x] Có `Dockerfile` build runtime app và `.dockerignore` chỉ cho source cần thiết vào context.
- [x] Có `docker-compose.yml` định nghĩa app, MariaDB, network, secrets và persistent volume; build/run đã qua trên Actions.
- [x] Chứng minh cùng bản ghi còn sau recreate app + DB, giữ named volume trong môi trường CI tạm.
- [x] Secrets ngoài repository/build context; setup giữ file hiện có; app đọc secret files lúc khởi động.
- [x] Có automated tests validation/HTTP/DB/persistence; kiểm tra regression cố ý bị assertion từ chối.
- [x] Có workflow build → test → tạo artifact; job deploy phụ thuộc test và chỉ bật khi cấu hình.
- [x] Có hướng dẫn chạy Docker, cấu hình cPanel FTPS và giới hạn minh chứng.
- [ ] Chốt cách runner kết nối hosting (container nếu Docker được phép; source FTPS là phương án đã chuẩn bị).
- [ ] Deploy tự động từ commit mới lên cPanel đã xác định; không cần thao tác upload/cập nhật thủ công.
- [ ] Nếu rubric yêu cầu container trên máy triển khai, chứng minh Docker trên hosting hoặc có xác nhận của giảng viên về cách đánh giá khác. Docker trên CI chưa tự thay thế tiêu chí này.
- [ ] Demo sửa nhỏ → commit → pipeline → website cập nhật đúng SHA.
- [ ] Run test fail chặn deploy và website giữ phiên bản trước; thu ảnh/log/video thật trên môi trường phù hợp.


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

[Run 40604164827](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40604164827), commit `145a500`: build/Compose, PHP lint, **14 validation checks**, **17 container checks**, regression và gói source cPanel đều qua. `deploy-cpanel` **skipped**, chưa có kết nối FTPS, chưa xác minh Docker trên hosting mới. Kết quả/artefacts/log được ghi trong [evidence Week 2](../evidence/week2/README.md). Chưa hoàn thành toàn bộ rubric.

## Nơi cập nhật

- Source dùng chung: `app/`, `public/`, `database/`, `config/`.
- Docker/Compose: `Dockerfile`, `docker-compose.yml`, `.dockerignore`, `docker/`.
- CI/CD: `.github/workflows/`.
- [Minh chứng Week 2](../evidence/week2/README.md).
- Phân công tuần này: bổ sung vào [TEAM.md](TEAM.md) khi nhóm thống nhất; hiện mới xác nhận phân công Week 1.
