# Backup và restore database Week 3

Database ứng dụng hiện có một bảng dishes. `app/backup.php` xuất toàn bộ dữ liệu bảng này thành snapshot JSON có version định dạng, thời gian UTC, row count và SHA256. Schema và cấu hình quản trị được quản lý riêng; đây không phải bản dump toàn MySQL server, user grants hoặc cấu hình cPanel.

## Tạo backup trên cPanel

Đăng nhập website → **Vận hành** → **Tạo backup**. Thao tác yêu cầu session quản trị và CSRF; tối đa một lần mỗi 60 giây theo session. Nó chỉ đọc DB và ghi snapshot private, không thay đổi bản ghi.

Nếu dùng Terminal, từ thư mục gốc dự án:

```bash
php app/backup.php --keep=14
```

PHP CLI cần pdo_mysql và mbstring; dùng đúng PHP 8.2+ của hosting. Script đọc config/config.local.php, không nhận password qua command line và không in thông tin kết nối. Trên Docker, chạy bằng user www-data để cùng owner với web worker:

```bash
docker compose exec -T --user www-data app php app/backup.php --keep=14
```

Mặc định snapshot ở `var/backups`, tách runtime source, quyền thư mục 0700 và file 0600; `.htaccess` chặn HTTP. Source ZIP không chứa thư mục var. Khi cần vị trí ngoài vùng web, đặt biến môi trường MENU_BACKUP_DIR thành đường dẫn tuyệt đối do tài khoản sở hữu trước khi chạy CLI. Không đăng đường dẫn tài khoản thật hoặc file backup live vào Git.

## Chiến lược lưu trữ

Giữ 14 snapshot mới nhất trong mỗi directory; CLI cho phép `--keep` từ 1 đến 90. Chỉ các file menu-*.json do công cụ quản lý được xét xoay vòng, bỏ qua symlink. Ghi file tạm, kiểm tra đủ số byte, đặt quyền rồi rename; lỗi ghi không được báo thành công. Hỗ trợ tối đa 20.000 món và file 32 MiB; vượt giới hạn phải dùng công cụ backup database phù hợp, không cắt dữ liệu để tạo snapshot thiếu.

SHA256 dùng phát hiện sai lệch dữ liệu, không mã hóa backup. Cần giữ bản sao riêng ngoài hosting và kiểm tra phục hồi định kỳ. Lịch backup vận hành đề xuất là mỗi ngày và trước thay đổi schema. Nếu cấu hình Cron Jobs trên cPanel, dùng đường dẫn PHP và dự án thật do hosting cấp; **cron trên hosting chưa được tự cấu hình bởi pipeline source**. Có thể chạy tay/nút backup để đáp ứng phiên demo.

## Restore an toàn cho bài thực nghiệm

`app/restore.php` của MariaDB chính chỉ chạy CLI; ops-api.php không có restore action. Để không xóa dữ liệu người dùng thật, script yêu cầu database đích kết thúc `_test` hoặc `_ci` và xác nhận tên khớp hoàn toàn. Không chạy bài xóa/restore vào DB live.

Trên stack thử riêng đã import schema và cấu hình DB test:

```bash
docker compose exec -T --user www-data app php app/restore.php \
  --file=menu-YYYYMMDD-HHMMSS-xxxxxxxx.json \
  --confirm-db=restaurant_menu_ci
```

Thay filename bằng snapshot thực tế đã tạo. Chỉ nhận basename đúng mẫu trong private backup directory, từ chối traversal/symlink/file quá lớn. Trước khi ghi, kiểm tra định dạng, schema từng row, enum/giới hạn dữ liệu, ID trùng, row count và SHA256. Restore dùng transaction DELETE/INSERT với prepared statements, đối chiếu lại toàn bộ dữ liệu trước COMMIT; lỗi thì ROLLBACK. Không DROP/TRUNCATE hoặc tự sửa schema.

## Chuỗi kiểm chứng đã tự động hóa

`tests/operations_smoke.py` chỉ nhận Compose project và DB `_ci`:

1. Tạo một món thử và đọc toàn bộ rows/timestamps làm mốc.
2. Chạy backup, xác nhận row count/SHA256 và quyền file.
3. Xóa đúng bản ghi thử theo ID và tên, xác nhận dữ liệu đã mất.
4. Chứng minh xác nhận sai và snapshot hỏng bị từ chối trước khi ghi.
5. Restore snapshot hợp lệ, so toàn bộ ID, giá, trạng thái, tên và timestamps với mốc.
6. Thử retention và ghi JSON kết quả; không upload bản backup hoặc dữ liệu live.

Phần restore production chưa có cơ chế tự ghi đè DB live; checkpoint dùng DB thử riêng theo đề. Khi cần phục hồi thật phải có kế hoạch riêng, backup hiện trạng và quyền phù hợp. [Minh chứng](../evidence/week3/README.md) · [Playbook](TROUBLESHOOTING.md).

## Demo tương tác ngay trên cPanel

Trang `public/demo.php` dùng một database SQLite riêng tại `var/classroom-demo/demo.sqlite`. Quản trị bấm tạo món thử, backup, tải snapshot, xóa và restore trực tiếp. Đây là phạm vi demo riêng, không gọi `backup_restore()` của MariaDB chính. Snapshot demo có định dạng và demo_id riêng, chỉ nhận một fixture đúng schema với SHA256 khớp; snapshot hỏng bị chặn trước khi ghi. Restore dùng transaction, so digest trước khi commit.

Tải snapshot demo qua form POST tới demo-api.php, cần session quản trị và CSRF; không nhận đường dẫn file tùy ý hoặc file upload. Bộ mẫu này giữ snapshot mới nhất của lượt demo; chiến lược 14 snapshot vẫn áp dụng cho backup MariaDB chính. Ngắt kết nối bằng đổi tên file SQLite demo trong khóa độc quyền; khôi phục tên file để phục hồi. Health demo trả HTTP 503/200 thực tế, bộ cảnh báo dùng hai mẫu liên tiếp. Tự phục hồi sau 3 phút nếu bỏ quên. [Kịch bản một người](WEEK3_DEMO.md).
