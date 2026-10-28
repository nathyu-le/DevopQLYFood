# Week 2 — Containerization & CI/CD

**Checkpoint 28/10/2026: bộ đầu ra kỹ thuật đã được kiểm chứng và sẵn sàng nộp/demo.** [Run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505) từ push main, source `d18f6be9b79e1ede812d5450cb34cd8ec4e0e70a`, attempt 1: cả ba job success. [Bộ minh chứng](../evidence/week2/completion-2026-10-28.md) · [Kịch bản một người](WEEK2_DEMO.md).

Nguồn đối chiếu: ảnh đề gốc Week 2 `1000003653.jpg`, đọc lại ngày 28/10/2026. Đề yêu cầu Docker/Compose và deploy tự động; không ghi bắt buộc Docker phải chạy trên hosting cPanel. Tài liệu cũ đã diễn giải thêm điều kiện này; checklist hiện ghi đúng kiến trúc thực hiện. Docker được triển khai vào staging tạm trên runner mới; live cPanel nhận source PHP của cùng commit.

## Yêu cầu kỹ thuật

- [x] Dockerfile build được app; gom cài extensions và dọn dependency/cache trong cùng RUN, chỉ copy runtime source.
- [x] Compose có app Apache/PHP, MariaDB, network `menu`, named volume `db_data`, health checks và secrets.
- [x] Recreate cả app và DB vẫn giữ cùng ID/tên/giá/trạng thái: [persistence JSON](../evidence/week2/persistence-2026-10-28.json).
- [x] Workflow checkout → build → test → lưu image/source → deploy. Staging tải image artifact, xác minh image ID rồi chạy `--no-build` trên runner mới.
- [x] Test có ý nghĩa: 14 validation checks; 17 HTTP/DB/CRUD/persistence checks ở mỗi stack CI và staging; kiểm tra regression và biên gói source.
- [x] Lỗi validation thật làm pipeline fail: [run 40635078470](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40635078470), assertion `reject invalid price: 1000000000`, exit 1; hai job deploy skipped. PR demo đã đóng không merge.
- [x] Secrets/env ngoài Git: Docker dùng file secrets ngẫu nhiên; FTP credentials ở GitHub Secrets; config hosting không nằm trong gói cập nhật.
- [x] Push commit mới tự cập nhật website qua FTPS có xác minh TLS; không đăng nhập server để chạy lệnh update.
- [x] Public health ok và version đúng SHA; có ảnh thật trước/sau và metadata đi kèm.

## Hồ sơ nộp

- [x] [Dockerfile](../Dockerfile) và [docker-compose.yml](../docker-compose.yml).
- [x] [Workflow](../.github/workflows/container-pipeline.yml).
- [x] [Tests](../tests/) và [workflow log chọn lọc](../evidence/week2/completion-2026-10-28.log), kèm link run gốc.
- [x] Ảnh phiên bản [trước](../evidence/week2/screenshots/live-before-login.png) và [sau](../evidence/week2/screenshots/live-after-login.png) deploy; ảnh [container staging](../evidence/week2/screenshots/staging-login.png).
- [x] [README](../README.md), [Docker setup](DOCKER_SETUP.md) và [CI/CD setup](CICD_SETUP.md) có cách chạy pipeline.

Đề cho phép **video hoặc ảnh** trước/sau; bộ này dùng ảnh và logs thật. Nhóm vẫn cần đọc hiểu, tập trình bày và truy cập các link bằng tài khoản dùng khi nộp.

## Đối chiếu rubric

| Tiêu chí theo đề | Điểm tối đa | Minh chứng |
|---|---:|---|
| Dockerfile + Compose build/run | 0,7 | Source, job container-checks và deploy-staging; cùng image ID |
| Volume và DB persistence | 0,3 | Bản ghi trước/sau recreate giống nhau, same_record_preserved=true |
| CI/CD build/test/deploy tự động | 1,2 | Run push có ba job success; run validation fail có deploy skipped |
| Commit → pipeline → app update | 0,3 | Commit d18f6be, ảnh hai phiên bản, version public đúng SHA |
| Tổng | 2,5 | Mức tối đa theo đề; điểm thực tế do giảng viên đánh giá |

## Phạm vi và việc của nhóm

Staging tạm được dọn sau run; không có URL staging hoạt động liên tục. Live cPanel chạy PHP/DB trực tiếp và không chạy Docker. Persistence JSON thuộc DB staging, không phải DB cPanel. Pipeline cập nhật source, chưa tự migration/rollback DB.

OS/user/SSH/firewall còn thiếu thuộc [Week 1](WEEK1_CHECKLIST.md), không được tự đánh dấu hoàn tất bởi kết quả Week 2. [Week 3](WEEK3_CHECKLIST.md) và [Week 4](WEEK4_CHECKLIST.md) tiếp tục ở checkpoint riêng. Đóng góp cá nhân cần nhóm xác nhận theo công việc thật trong [TEAM](TEAM.md).
