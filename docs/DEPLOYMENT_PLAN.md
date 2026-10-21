# Kế hoạch hoàn thiện Week 1 từ hosting và tiếp tục Week 2

**Quyết định ngày 21/10/2026:** dùng hosting cPanel gói **cp1** để demo app + DB và trình bày tiến độ Week 1. Người triển khai xác nhận cp1 không hỗ trợ Terminal. Phần tự cài/cấu hình OS, user/SSH, firewall và ports **tạm hoãn bổ sung**, giữ trạng thái chưa hoàn thành.

Ứng dụng đang truy cập tại [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/). Đã có [ảnh thêm/sửa và SELECT cùng ID 9](../evidence/application/demo-2026-10-21.md), [thông số PHP/quota](../evidence/server/README.md) và [log chặn HTTP sau upload .htaccess](../evidence/server/http-protection-2026-10-21.md). Còn có thể bổ sung ảnh tải lại/tìm kiếm và cột trạng thái web khi demo.

## Week 1: trình bày trên cp1

1. Giữ website và database hiện tại để demo, giải thích sơ đồ và thao tác thêm/sửa/đọc dữ liệu theo [DEMO.md](DEMO.md).
2. Dùng bộ ảnh/log hiện có làm minh chứng; ghi rõ phần đã làm và phần hạ tầng tạm hoãn.
3. Tạm dừng tìm Terminal hoặc thử SSH trên cp1 theo quyết định của nhóm. Giao diện quản lý SSH keys chưa chứng minh có shell; khả năng SSH riêng ngoài Terminal chưa được kiểm chứng.
4. Phần yêu cầu tự cài/cấu hình server vẫn nằm trong checklist. Kế hoạch tạm hoãn của nhóm chưa phải xác nhận giảng viên đồng ý thay tiêu chí hoặc gia hạn.

## Week 2: xem xét môi trường mới

Chưa chọn hoặc mua VPS/gói hosting mới. Hai phương án đang xem xét:

| Phương án | Điều kiện cần xác nhận trước khi chọn |
|---|---|
| VPS | Quyền cài/chạy Docker và Compose, user/SSH, cấu hình firewall/ports, lưu dữ liệu bền vững và kết nối deploy từ runner |
| Gói hosting khác | Nhà cung cấp xác nhận tài khoản thực sự chạy được Docker/Compose, có quyền và cách kết nối để pipeline deploy tự động, có network/volume phù hợp; chỉ nâng gói có Terminal chưa đủ kết luận đáp ứng |

Sau khi chọn môi trường, bổ sung phần hạ tầng còn thiếu của Week 1 rồi triển khai Docker/Compose và pipeline theo [WEEK2_CHECKLIST.md](WEEK2_CHECKLIST.md). Dùng tiếp cùng repo, source và domain nếu phù hợp; sao lưu DB cùng cấu hình cục bộ ra nơi riêng trước khi chuyển. Không đưa credentials hoặc bản dump thật vào Git.

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
