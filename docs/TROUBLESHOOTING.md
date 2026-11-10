# Incident playbook Week 3

Các thực nghiệm dưới đây dùng Docker staging riêng, không dừng dịch vụ hoặc xóa bản ghi của website cPanel. Dữ liệu trước/sau, ảnh và thời gian được liên kết trong [minh chứng Week 3](../evidence/week3/README.md). Chỉ đọc kết quả đã ghi nhận trong đúng run, không mô tả ảnh cũ là sự cố live vừa tạo.

## Sự cố 1 Database không khả dụng

**Triệu chứng:** health HTTP 503; dashboard báo Database gián đoạn; alert database_unavailable chuyển pending rồi firing sau hai mẫu lỗi. Log có database_unavailable và alert_transition với thời gian UTC.

**Xác minh:** mở health và dashboard; lọc log theo database_unavailable, đối chiếu thời điểm. Trong môi trường Compose riêng xem `docker compose ps` và `docker compose logs --since=10m db`. Không đăng thông báo lỗi kết nối đầy đủ vào evidence vì có thể chứa thông tin tài khoản/host.

**Nguyên nhân thực nghiệm:** test chạy `docker compose stop db`, khiến PDO không kết nối/truy vấn được. Đây là lỗi thật do dịch vụ thử đã dừng, không phải banner giả hoặc đổi JSON thành lỗi.

**Khắc phục:** trên project staging dùng `docker compose up -d --no-build --wait --wait-timeout 180 db`. Không xóa volume. Trên cPanel, nếu DB mất kết nối thật cần kiểm tra dịch vụ DB, cấu hình được cấp và trạng thái nhà cung cấp; không chạy lệnh Docker vào hosting.

**Xác nhận phục hồi:** HTTP 200/status ok, hai mẫu probe tốt đưa alert về ok, log có transition và cùng dữ liệu thử còn nguyên. Ảnh firing/recovered cùng lifecycle JSON ghi nhận kết quả.

**Phòng tránh:** theo dõi uptime độc lập, để credentials ngoài Git, giữ named volume và kiểm tra dashboard sau thay đổi cấu hình.

## Sự cố 2 Mất dữ liệu thử và phục hồi snapshot

**Triệu chứng:** món thử không còn trong SELECT sau thao tác xóa, trong khi health vẫn có thể ok. Health không thay bằng chứng dữ liệu nghiệp vụ.

**Xác minh:** so ID/tên/giá/trạng thái/timestamps đã ghi trước đó với bảng dishes. Xác định đúng DB test; kiểm tra snapshot có định dạng đúng, số rows và SHA256.

**Nguyên nhân thực nghiệm:** test xóa duy nhất món fixture bằng ID và tên trong DB `_ci`, sau khi đã tạo backup. Không mô phỏng xóa dữ liệu người dùng thật.

**Khắc phục:** chạy app/restore.php bằng CLI với filename snapshot và --confirm-db đúng DB thử. Snapshot hỏng hoặc xác nhận sai bị từ chối. Restore thực hiện trong transaction; không tự bỏ qua integrity check.

**Xác nhận phục hồi:** toàn bộ rows trước và sau restore giống nhau, gồm IDs và timestamps; digest khớp. JSON kết quả có before, after_delete, after_restore và all_rows_match=true. Log có restore_verified.

**Phòng tránh:** backup trước thay schema, retention rõ ràng, copy snapshot ra nơi riêng và thử restore định kỳ. Không coi một file backup tồn tại là đã có bằng chứng phục hồi.

## Sự cố 3 User app có quyền schema ngoài nhu cầu

**Triệu chứng đã gặp khi chạy Week 3:** test yêu cầu CREATE TABLE bị từ chối nhưng thao tác lại thành công; pipeline dừng ở staging và deploy-cpanel skipped.

**Xác minh:** dùng chính user app để thử DDL trên DB test. Đối chiếu entrypoint MariaDB và các mẫu quyền DB; không công khai SHOW GRANTS nguyên bản nếu có user/host.

**Nguyên nhân:** image MariaDB tạo quyền ALL trên mẫu tên DB đã escape dấu gạch dưới. Script cũ thu hồi ở mẫu khác; do đó quyền rộng hơn vẫn có hiệu lực. Việc source có một dòng GRANT CRUD chưa đủ chứng minh user chỉ có bốn quyền đó.

**Khắc phục:** với tài khoản riêng của app trong stack thử, REVOKE ALL PRIVILEGES, GRANT OPTION rồi GRANT đúng bốn quyền trên tên DB đã escape. Chạy lại toàn bộ kiểm tra; không bỏ assertion DDL. Volume đã tồn tại cần cập nhật grants bằng admin thay vì xóa dữ liệu.

**Xác nhận:** CREATE TABLE bị từ chối nhưng các test CRUD, backup và restore vẫn qua; pipeline chỉ triển khai tiếp sau kết quả đó.

Nguồn đối chiếu: [MariaDB entrypoint](https://github.com/MariaDB/mariadb-docker/blob/master/docker-entrypoint.sh), [MariaDB GRANT](https://mariadb.com/docs/server/reference/sql-statements/account-management-sql-statements/grant).

# Bổ sung playbook Week 4

## Sự cố 4 FTPS timeout và tên TLS không trùng alias

**Triệu chứng thực tế ngày 04/11:** hai job container/staging qua, deploy-cpanel fail; TCP timeout trước đăng nhập. Sau khi đổi địa chỉ kết nối sang alias dịch vụ hiện tại, TLS báo hostname mismatch.

**Xác minh:** tách kết nối TCP, TLS và đăng nhập; đối chiếu địa chỉ dịch vụ hosting đã biết với danh tính trong chứng chỉ. Không đọc hoặc in FTP password. [Run khôi phục 41785933275](https://github.com/nathyu-le/DevopQLYFood/actions/runs/41785933275) là mốc xác minh sau sửa.

**Nguyên nhân:** địa chỉ kết nối cũ không còn là địa chỉ dịch vụ hiện tại; alias kết nối mới khác tên TLS của dịch vụ. Đây là hai tầng lỗi riêng, không phải mật khẩu sai.

**Khắc phục:** dùng CPANEL_FTP_HOST để kết nối và CPANEL_FTP_TLS_HOST cho danh tính dịch vụ đã xác minh. Giữ ssl.create_default_context/check_hostname trên cả hai kênh; TLS phải qua trước USER/PASS. Không tắt verify hoặc tự chọn tên từ lỗi.

**Xác nhận:** cả ba job qua, source SHA đúng ở version.php, health ok và các kiểm tra HTTP qua. Bộ tests/cpanel_release.py chứng minh TLS lỗi không gửi credentials.

**Phòng tránh:** ghi riêng host kết nối và TLS identity, kiểm tra với provider khi đổi gói; không retry sai credentials vô hạn.

## Sự cố 5 Database demo mất kết nối ngay trên cPanel

**Triệu chứng thực nghiệm:** demo-health trả 503, DATABASE DEMO Mất kết nối, alert PENDING rồi FIRING; website chính vẫn dùng được.

**Xác minh trực tiếp:** mở demo.php, tạo dữ liệu/backup trước rồi bấm 5 Tạo lỗi kết nối. Chờ hai mẫu, xem Nhật ký demo và health demo. Mở health.php chính để đối chiếu phạm vi. Không khẳng định MariaDB hosting đã dừng.

**Nguyên nhân:** thao tác được cho phép đổi tên file SQLite thử từ demo.sqlite sang demo.sqlite.offline dưới khóa độc quyền; dịch vụ demo không mở được đường dẫn DB kỳ vọng. Đây là lỗi kết nối thật của dịch vụ thử, không phải banner mô phỏng.

**Khắc phục:** bấm 6 Phục hồi kết nối để trả file về tên cũ, không tạo DB mới hoặc xóa backup. Có auto reconnect sau 180 giây nếu bỏ quên.

**Xác nhận:** health demo 200, DB hoạt động, sau 2 mẫu tốt alert OK; bản ghi restore giữ đúng ID/tên/giá. tests/demo_smoke.py kiểm tra digest MariaDB trước/sau bằng nhau.

**Phòng tránh:** phân biệt DB chính/DB thử, thao tác theo thứ tự, luôn kết thúc online; không áp dụng nút ngắt demo vào MariaDB live.

## Sự cố 6 Click demo bị mất khi refresh đang chạy

**Triệu chứng đã sửa:** người dùng bấm tạo dữ liệu nhưng nút dường như không chạy trong thời điểm polling GET, hoặc dữ liệu cũ ghi đè kết quả mới.

**Xác minh:** tạo GET chờ trong test client, bấm action và kiểm tra POST vẫn được gửi; trả GET cũ sau POST để thử thứ tự phản hồi. [Commit 83c269e](https://github.com/nathyu-le/DevopQLYFood/commit/83c269e1513c1d5239e6182719ab95749577eac3).

**Nguyên nhân:** action và polling từng dùng chung busy flag. **Khắc phục:** tách busy/refreshing, tăng generation sau action để bỏ phản hồi cũ. Download dùng form POST riêng với CSRF.

**Xác nhận:** tests/demo_client.mjs qua; demo live đã chạy đủ backup/delete/restore và 503 → 200. **Phòng tránh:** giữ regression test trong pipeline; không khôi phục lại một cờ khóa chung.
