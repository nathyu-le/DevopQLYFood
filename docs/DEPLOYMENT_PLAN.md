# Kế hoạch hoàn thiện Week 1 từ hosting và tiếp tục Week 2

**Cập nhật: 21/10/2026.** Người triển khai xác nhận website [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/) đã truy cập được trên hosting cPanel sau khi cấu hình admin. Đã đối chiếu 3 ảnh thêm/sửa và SELECT cùng món ID 9. [Minh chứng app + DB](../evidence/application/demo-2026-10-21.md). Tiếp theo bổ sung ảnh web thấy rõ trạng thái, bước tải lại/tìm kiếm hoặc video liên tục, đã bổ sung PHP 8.2 và quota tài khoản; `.htaccess` đã được upload và kiểm tra health/login/CSS 200, schema/README 403. Đã có ảnh giao diện SSH Access; còn xác minh distro/version OS, đăng nhập shell/quyền user và firewall theo đề. Chưa có VPS, chưa quyết định phải thuê thêm và chưa đánh dấu hoàn tất Week 1.

## Làm ngay trên hosting hiện có

1. Đăng nhập; thêm “Cơm gà Demo Week 1” giá 45.000đ, tải lại và tìm đúng món.
2. SELECT cùng bản ghi trong phpMyAdmin, đối chiếu ID/tên/giá/trạng thái.
3. Sửa thành 50.000đ và hết món; tải lại, SELECT lần nữa; chụp/quay các kết quả.
4. Đã thu Server Information, Domains, MultiPHP Manager, Statistics và SSH Access. Đã kiểm tra `.htaccess`: health/login/CSS 200, schema/README 403. Tiếp theo xác minh DB host/port và quyền đăng nhập shell/Terminal. Chưa coi IP Blocker là bằng chứng đã tự cấu hình firewall hệ điều hành.
5. Bổ sung các thông số còn thiếu vào sơ đồ hosting; đối chiếu yêu cầu cài Ubuntu Server/VM, EC2 hoặc VPS. Phần hosting không có quyền thực hiện cần bổ sung môi trường phù hợp hoặc được giảng viên chấp nhận thay thế.
6. Kiểm tra hosting có chạy được Docker/Compose và đáp ứng pipeline/monitoring hay không trước khi chọn môi trường Week 2.

[Hướng dẫn cPanel](CPANEL_SETUP.md) · [Kịch bản demo](DEMO.md).

## Phương án nếu cần bổ sung VM/VPS

| Bước | Công việc | Kết quả cần lưu |
|---|---|---|
| 1. Chuẩn bị VPS | Ghi OS/tài nguyên; tạo user/SSH; cấu hình firewall và ports; xác minh quyền truy cập | Thông tin và ảnh/log thật trong `evidence/server/` |
| 2. Bổ sung triển khai Week 1 | Cài Apache/PHP/MySQL theo [MANUAL_SETUP.md](MANUAL_SETUP.md), import schema, tạo cấu hình riêng, đặt DocumentRoot tại `public/` | Dịch vụ chạy, health check thành công và client truy cập được |
| 3. Ghi demo Week 1 | Theo [DEMO.md](DEMO.md): thêm món → tải lại → SELECT đúng bản ghi → sửa và đối chiếu DB; ghi URL/cổng và giải thích kiến trúc | Ảnh/video/log trong `evidence/application/`, liên kết ở `evidence/week1/` |
| 4. Chốt phần bổ sung | Cập nhật sơ đồ đúng hiện trạng, README, checklist và ngày/commit/minh chứng; đối chiếu với yêu cầu giảng viên | Bản Week 1 có thể xem lại bằng commit; chỉ ghi hoàn tất khi đủ bằng chứng |
| 5. Chuẩn bị chuyển sang Docker | Sao lưu DB/config ngoài Git và giữ bản triển khai cũ để đối chiếu; xác định cổng, network, volume và phương án dữ liệu | Ghi dữ liệu cần giữ và cách chuyển; không ghi đè DB chưa sao lưu |
| 6. Thực hiện Week 2 | Dockerfile/Compose, DB volume, secrets ngoài repo; pipeline checkout → build → test → tạo image → deploy tự động | Chạy và kiểm tra theo [WEEK2_CHECKLIST.md](WEEK2_CHECKLIST.md) |
| 7. Ghi demo Week 2 | Push thay đổi nhỏ → xem workflow → refresh app; kiểm chứng test fail và dữ liệu còn sau recreate container | Commit/run, ảnh trước/sau, log và video trong `evidence/week2/` |

Nếu chọn VM/VPS, dùng tiếp **cùng môi trường đó, cùng repository và cùng ứng dụng**. Khi chuyển từ Apache/MySQL cài trực tiếp sang Compose, xác định dịch vụ nào đang chiếm cổng 80/443/3306, cách chuyển dữ liệu và cách dừng/chuyển dịch vụ cũ trước khi dùng chung cổng. Không mặc định volume Docker tự chứa dữ liệu của MySQL cài trực tiếp. Chỉ chuyển sau khi đã lưu được bằng chứng Week 1 và kiểm tra bản sao dữ liệu cần giữ.

## Ghi minh chứng xuyên suốt

- Lưu bằng chứng ngay sau mỗi đầu việc: ngày thực hiện, người thực hiện, môi trường, commit SHA/run, các bước và kết quả quan sát.
- Week 1: `evidence/server/`, `evidence/application/`, chỉ mục `evidence/week1/README.md`.
- Week 2: `evidence/week2/`; Week 3: `evidence/week3/`; Week 4: `evidence/week4/`.
- Video có thể ghi xuyên suốt quá trình, nhưng chỉ mục từng tuần phải trỏ đến đúng đoạn hoặc tệp chứng minh tiêu chí đó. Kiểm tra quyền xem của các link nộp.
- Lưu bản source/commit của từng lần demo để xem lại sau khi nâng cấp. Không đổi ngày quay hoặc ghi một bước chưa chạy thành đã hoàn thành.
- Không đưa config thật, mật khẩu, private key, token, file dump database hoặc backup vào repo. Làm sạch thông tin nhạy cảm trong ảnh/log trước khi công khai.

## Vai trò và phần còn thiếu

Giữ phân công Week 1 đã thống nhất: **Lê Đình Nhật Huy** phụ trách kiến trúc/hạ tầng, app/DB và tài liệu/GitHub; **Nguyễn Phước Hải** kiểm tra chức năng và dữ liệu; **Lê Quang Tùng** tổng hợp minh chứng/video. Week 2–4 chưa chốt phân công, cập nhật vào [TEAM.md](TEAM.md) khi nhóm thống nhất.

Kế hoạch này không đánh dấu Week 1 hoàn tất và không thay cho môi trường chạy thật. CI source đã thành công được ghi riêng tại [VERIFICATION.md](VERIFICATION.md).
