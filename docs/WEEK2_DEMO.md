# Week 2 — Kịch bản một người trình bày

Thời lượng khoảng 5–7 phút. Phần **MỞ / THAO TÁC** là hướng dẫn cho người trình bày; phần **LỜI ĐỌC** có thể đọc trực tiếp. Một người nói, cả nhóm nắm phần phụ trách để trả lời câu hỏi.

## Chuẩn bị các tab

1. [README repository](../README.md).
2. [Dockerfile](../Dockerfile) và [Compose](../docker-compose.yml).
3. [Run thành công 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505).
4. [Bộ ảnh/persistence](../evidence/week2/completion-2026-10-28.md).
5. [Website](https://khotrick.org/DevopQLYFood/public/login.php), [health](https://khotrick.org/DevopQLYFood/public/health.php), [version](https://khotrick.org/DevopQLYFood/public/version.php).
6. [Run test lỗi thật](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40635078470).

Không mở màn hình config thật, GitHub Secrets hoặc lệnh đọc mật khẩu khi chia sẻ màn hình. Bộ ảnh/log đã lưu trong repo dùng làm dự phòng nếu mạng lỗi.

## 1. Giới thiệu — khoảng 30 giây

**MỞ / THAO TÁC:** README, bảng tiến độ Week 2.

**LỜI ĐỌC:**

“Nhóm em gồm Lê Đình Nhật Huy, Nguyễn Phước Hải và Lê Quang Tùng. Đề tài là website quản lý thực đơn quán ăn Bếp Nhà, dùng PHP và MySQL/MariaDB. Week 2 nhóm tập trung đóng gói bằng Docker và xây dựng pipeline tự động build, test rồi deploy từ commit mới. Website thật tiếp tục chạy trên cPanel.”

## 2. Docker và dữ liệu — khoảng 1 phút

**MỞ / THAO TÁC:** Dockerfile; Compose ở services app/db, networks và volumes.

**LỜI ĐỌC:**

“Dockerfile dùng Apache/PHP 8.2, cài các extension ứng dụng cần và chỉ copy source chạy app. Compose định nghĩa hai service app và MariaDB, cùng network riêng. Dữ liệu MariaDB nằm trong named volume db_data ở /var/lib/mysql. App chờ database healthy trước khi khởi động. Mật khẩu được đọc từ secrets, không đưa vào Git hoặc image.”

“Nhóm dùng Docker staging tạm trên GitHub runner để triển khai image. Website cPanel nhận source PHP của cùng commit qua FTPS; hai môi trường có database riêng.”

## 3. Pipeline tự động — khoảng 1–2 phút

**MỞ / THAO TÁC:** run thành công, chỉ ba job xanh; mở container-checks rồi deploy-staging và deploy-cpanel.

**LỜI ĐỌC:**

“Run này được kích hoạt bởi push commit d18f6be lên main. Job đầu checkout, build image, lint PHP, chạy 14 kiểm tra validation, regression và 17 kiểm tra HTTP, database và persistence. Sau khi qua tests, pipeline lưu image cùng image ID và gói runtime source.”

“Job staging chạy trên một runner mới. Nó tải image đã test, kiểm tra image ID rồi chạy Compose với --no-build, vì vậy không build lại một image khác. Staging chạy lại kiểm tra ứng dụng và lưu minh chứng. Job cPanel chỉ tiếp tục khi cả tests và staging thành công. Nó upload source qua FTPS, kiểm tra health và đối chiếu revision.”

“Trong lần cập nhật này nhóm chỉ push commit; không đăng nhập hosting chạy lệnh cập nhật source thủ công.”

**NẾU THẦY MUỐN PUSH LIVE:** mở public/login.php, sửa một câu mô tả nhỏ, commit lên main rồi mở run mới. Chờ cả ba job; refresh website và so version.php với SHA run mới. Không commit thay đổi chỉ ở docs để thử trigger vì workflow bỏ qua docs/evidence. Bộ ảnh hiện tại là bằng chứng của lần push đã thực hiện, không gọi là lần push vừa làm tại lớp.

## 4. Chứng minh app đã đổi — khoảng 1 phút

**MỞ / THAO TÁC:** ảnh trước/sau trong bộ evidence, rồi website live và version.php.

**LỜI ĐỌC:**

“Trước deploy, dòng login là ‘Đăng nhập để cập nhật món ăn và quản lý thực đơn của quán.’ Sau deploy là ‘Đăng nhập để cập nhật món, giá và tình trạng phục vụ.’ Hai ảnh do Chrome chụp tự động trong pipeline, kèm metadata health và revision. Version sau deploy khớp commit d18f6be; health trả ok.”

“Nếu HEAD chỉ có thêm tài liệu hoặc ảnh thì revision web vẫn là commit source cuối đã deploy. Vì vậy nhóm đối chiếu với commit của run, không lấy HEAD tài liệu để kết luận website sai phiên bản.”

## 5. Chứng minh persistence — khoảng 1 phút

**MỞ / THAO TÁC:** [persistence JSON](../evidence/week2/persistence-2026-10-28.json), hai object before_recreate/after_recreate; sau đó dòng PASS trong deploy-staging.

**LỜI ĐỌC:**

“Test tạo món qua HTTP, sửa giá thành 50.000 và trạng thái hết món, rồi đọc bản ghi từ MariaDB. Pipeline recreate cả app và database nhưng giữ named volume. Sau recreate, bản ghi vẫn có cùng ID 1, tên, giá 50000 và trạng thái 0; same_record_preserved bằng true. Web container mới cũng đọc được bản ghi đó.”

“Đây là database thử trong staging Docker, không phải database cPanel. Sau khi lưu minh chứng, pipeline dọn môi trường tạm.”

## 6. Khi test fail — khoảng 1 phút

**MỞ / THAO TÁC:** run 40635078470, bước Check input validation with the image runtime; chỉ FAIL và hai job deploy skipped. Mở PR #1 nếu cần.

**LỜI ĐỌC:**

“Nhóm có nhánh demo cố ý nới validator để nhận giá sai 1000000000. Test validation thật phát hiện, trả exit code 1 và pipeline thất bại. Cả hai job deploy bị bỏ qua. Pull request này đã đóng, không merge vào main, nên bản web đang chạy giữ validator hợp lệ. PR cũng có điều kiện không deploy; với main, các job deploy vẫn cần tests và staging thành công.”

## 7. Kết thúc — khoảng 20 giây

**MỞ / THAO TÁC:** checklist hoặc bảng hồ sơ trong bộ evidence.

**LỜI ĐỌC:**

“Hồ sơ Week 2 có Dockerfile, Compose, workflow, source tests, logs, ảnh trước/sau và hướng dẫn chạy pipeline. Nhóm tiếp tục monitoring, bảo mật và backup/restore ở Week 3. Các phần hạ tầng Week 1 chưa có minh chứng được giữ riêng trong checklist, không tính là đã hoàn thành bởi pipeline này.”

## Câu hỏi dễ gặp

| Câu hỏi | Trả lời theo hệ thống hiện tại |
|---|---|
| Docker có chạy trong cPanel không? | Không. Image chạy ở staging Docker tạm trên runner mới; cPanel chạy PHP/DB trực tiếp. |
| Vì sao biết image staging đúng image đã test? | Job tải artifact và so image ID, sau đó chạy --no-build. |
| Staging ở đâu sau run? | Được dọn sau kiểm chứng; ảnh/JSON/log đã lưu. Website public liên tục là cPanel. |
| Vì sao recreate không mất DB? | Container thay mới nhưng named volume db_data vẫn giữ /var/lib/mysql. |
| Làm sao tránh lộ mật khẩu? | File Docker secrets ngẫu nhiên, GitHub Secrets cho FTP và config local ngoài Git/package. |
| DB cPanel có bị xóa khi dọn CI không? | Không, Compose tạm trên runner dùng DB riêng; cPanel không nằm trong stack đó. |
| Có rollback/migration DB tự động chưa? | Chưa. Pipeline hiện cập nhật runtime source; thay schema cần kế hoạch và backup riêng. |
