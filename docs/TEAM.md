# Thành viên và phân công theo tuần

## Thành viên

| Thành viên | Vai trò chính ở Week 1 |
|---|---|
| Lê Đình Nhật Huy | Kiến trúc/hạ tầng, ứng dụng/database, tài liệu/GitHub |
| Nguyễn Phước Hải | Kiểm tra chức năng và đối chiếu dữ liệu |
| Lê Quang Tùng | Thu thập minh chứng và chuẩn bị video demo |

Phân công được cập nhật ngày 21/10/2026 theo thống nhất của nhóm. Đây là trách nhiệm được giao; kết quả hoàn thành phải được xác nhận bằng công việc, commit và minh chứng thực tế.

## Phân công theo đầu việc

| Đầu việc | Người phụ trách | Sản phẩm cần bàn giao |
|---|---|---|
| Kiến trúc và hạ tầng | Lê Đình Nhật Huy | Sơ đồ triển khai; lựa chọn môi trường; cấu hình OS, tài nguyên, user/SSH, firewall và ports; phối hợp bàn giao cho người kiểm tra |
| Ứng dụng và database | Lê Đình Nhật Huy | Source PHP, schema MySQL, cấu hình ứng dụng, khắc phục lỗi và triển khai ứng dụng |
| Kiểm tra và minh chứng | Nguyễn Phước Hải và Lê Quang Tùng | Bộ kiểm tra chức năng, đối chiếu bản ghi DB, ảnh/log cấu hình, ảnh thao tác từ client và video demo |
| Tài liệu và GitHub | Lê Đình Nhật Huy | README cài đặt, tổ chức source/docs/evidence, cập nhật tiến độ và lịch sử thay đổi của repo |

## Chia việc kiểm tra và minh chứng

| Thành viên | Việc cụ thể | Đầu ra |
|---|---|---|
| Nguyễn Phước Hải | Kiểm tra đăng nhập, thêm/xem/sửa/xóa món, tìm kiếm, còn/hết và dữ liệu không hợp lệ; đối chiếu bản ghi trong MySQL; ghi lỗi để Huy sửa và kiểm tra lại | Bảng kết quả thực hiện, mô tả lỗi, ảnh/kết quả SELECT và kết quả kiểm tra lại |
| Lê Quang Tùng | Truy cập ứng dụng từ máy khách; thu thập ảnh/log server, SSH/firewall/ports; ghi hình chuỗi thêm món → tải lại → đọc DB; kiểm tra link minh chứng mở được | Bộ ảnh/log đã loại secrets, video demo, chỉ mục file/link minh chứng |
| Hải và Tùng | Đối chiếu UI và DB, kiểm tra khả năng tái hiện và chuẩn bị minh chứng dự phòng | Bộ minh chứng Week 1 đầy đủ, có ngày, môi trường và người thực hiện |

**Cập nhật 21/10/2026:** nhóm dùng cp1 demo tạm Week 1; theo người triển khai gói này không hỗ trợ Terminal. Công việc OS/user/SSH/firewall và thu minh chứng tương ứng tạm hoãn, dự kiến bổ sung khi chọn môi trường Week 2. Phân công trên được giữ để theo dõi phần chưa làm, không ghi đã hoàn tất.

Huy chuẩn bị môi trường và quyền truy cập cần thiết cho Hải/Tùng; thông tin đăng nhập được chia sẻ bằng kênh riêng phù hợp, không đưa vào repository.

## Nơi cập nhật

- [Lộ trình cả 4 tuần](ROADMAP.md).
- [Checklist Week 1](WEEK1_CHECKLIST.md).
- [Minh chứng Week 1](../evidence/week1/README.md).
- Phân công trên áp dụng cho Week 1. Khi nhóm thống nhất công việc của tuần tiếp theo, bổ sung phần phân công tương ứng trong file này, giữ lại phân công của tuần trước.

Mỗi người cần hiểu cấu trúc hệ thống, vị trí app/database và giải thích được phần việc của mình khi bảo vệ.

## Week 2 — việc nhóm cần kiểm tra trước khi bảo vệ

Tiếp nối vai trò Week 1 để chuẩn bị; bảng này là phân công rà soát, **không xác nhận từng người đã thực hiện các thao tác trong pipeline**. Run tự động và ảnh do Chrome trên runner tạo được ghi đúng nguồn; nhóm bổ sung kết quả cá nhân sau khi làm thật.

| Thành viên | Phần cần nắm và rà soát | Sản phẩm kiểm tra |
|---|---|---|
| Lê Đình Nhật Huy | Docker/Compose, CI/CD, cấu hình cPanel và tài liệu | Giải thích được ba job, image ID, secrets và source deploy; rà README/checklist |
| Nguyễn Phước Hải | Validation, CRUD và DB persistence | Đọc run pass/fail, đối chiếu JSON cùng bản ghi trước/sau recreate, ghi kết quả rà soát thật |
| Lê Quang Tùng | Minh chứng và chuẩn bị demo | Kiểm tra ảnh/metadata, link run, bản dự phòng và kịch bản một người trình bày |

[Checklist Week 2](WEEK2_CHECKLIST.md) · [Bộ minh chứng](../evidence/week2/completion-2026-10-28.md) · [Kịch bản một người](WEEK2_DEMO.md). Một người trình bày theo lựa chọn của nhóm; cả ba vẫn cần hiểu phần phụ trách khi được hỏi.
