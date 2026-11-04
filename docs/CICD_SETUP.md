# Week 2 — CI/CD lên cPanel

**Đã kiểm chứng ngày 28/10/2026:** [run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505), event push, source `d18f6be`, attempt 1. Cả ba job success; có ảnh trước/sau và version đúng SHA. [Minh chứng](../evidence/week2/completion-2026-10-28.md) · [Kịch bản demo](WEEK2_DEMO.md).

## Luồng thực tế

1. **container-checks:** checkout → build image → lint/14 validation/regression → Compose/17 HTTP-DB-persistence checks → kiểm tra gói source → lưu image + ID và source ZIP.
2. **deploy-staging:** runner mới tải image artifact → load và so ID → chụp live trước cập nhật → Compose `--no-build` → 17 checks/recreate → ảnh staging + JSON persistence.
3. **deploy-cpanel:** nhận source artifact cùng commit → FTPS upload → health/revision đúng SHA → ảnh live sau deploy.

[Sơ đồ](WEEK2_ARCHITECTURE.md). Docker staging tạm được dọn sau job; cPanel chạy PHP/DB trực tiếp. Không triển khai VPS.

Staging chỉ chạy main, cần container-checks success. cPanel cần cả hai job success và `CPANEL_DEPLOY_ENABLED=true`. PR chỉ kiểm tra. Thay đổi docs/evidence/Markdown được paths-ignore; thay đổi source/config chạy tự động. Có thể Run workflow thủ công trên main khi cần demo lại.

[Run lỗi validation thật](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40635078470) fail ở assertion giá vượt giới hạn; hai job deploy skipped. [PR #1](https://github.com/nathyu-le/DevopQLYFood/pull/1) đã đóng không merge. PR còn có branch guard chặn deploy; dependency vào tests cũng áp dụng với main.

## Cấu hình để tái triển khai

Trước lần deploy đầu, hosting phải có app + DB đã cấu hình, `config/config.local.php`, các thư mục app/public/config và health trả ok. Pipeline cập nhật source không thay việc cài mới hoặc chuyển database. Sao lưu source/config/DB ở nơi riêng trước khi chuyển môi trường.

Tạo FTP account riêng, giới hạn đúng thư mục đồ án. Dùng địa chỉ dịch vụ thực tế có DNS. Nếu nhà cung cấp dùng alias kết nối khác tên trên chứng chỉ, cấu hình thêm `CPANEL_FTP_TLS_HOST` bằng danh tính dịch vụ đã biết và có trong SAN của certificate. Nếu không đặt, tên xác minh mặc định là `CPANEL_FTP_HOST`. Cả kênh điều khiển và truyền file đều xác minh chứng chỉ; không tự lấy tên trong log để đổi đích hoặc tắt TLS. TLS phải xác minh được trước khi client gửi credentials.

Repo → Settings → Secrets and variables → Actions, dùng repository scope:

| Loại | Tên | Nội dung |
|---|---|---|
| Variable | CPANEL_DEPLOY_ENABLED | true sau khi cấu hình xong; false để tắt deploy |
| Variable | CPANEL_FTP_HOST | Địa chỉ kết nối hiện tại: ns22.dailysieure.com, không có https:// |
| Variable | CPANEL_FTP_TLS_HOST | Danh tính TLS của dịch vụ hiện tại: 103-153-64-233.cprapid.com; chỉ dùng với đúng hosting đã xác minh |
| Variable | CPANEL_FTP_PORT | 21 cho explicit FTPS của môi trường đang dùng |
| Variable | CPANEL_FTP_ROOT | / khi FTP account đã giới hạn đúng thư mục DevopQLYFood |
| Variable | CPANEL_BASE_URL | https://khotrick.org/DevopQLYFood/public/ |
| Secret | CPANEL_FTP_USER | Username FTP đầy đủ |
| Secret | CPANEL_FTP_PASSWORD | Mật khẩu FTP riêng, nhập trực tiếp trong GitHub Secrets |

Đặt flag ở repository scope để điều kiện job đọc được trước khi job chạy. Staging dùng environment staging, deploy hosting dùng environment cpanel; không đặt duyệt thủ công từng lần nếu cần demo tự động sau push.

Mở Actions → Docker checks and cPanel deploy → Run workflow → main cho lần kiểm tra đầu. Sau đó push thay đổi source sẽ tự chạy.

## Source và dữ liệu được xử lý thế nào?

Gói ZIP chỉ gồm runtime PHP/CSS/JS trong app/public và REVISION. Không chứa config thật, SQL/DB, .htaccess, backup hoặc secrets. Archive chuẩn hóa file quyền 0644; bộ test có fixture 0600 để tránh lặp lỗi CSS khi giải nén.

Script kiểm tra SHA của artifact, health trước upload và thư mục đích/config đã có. Nó upload từng file tạm rồi rename, REVISION cuối cùng; sau đó kiểm tra health và revision đúng SHA. Đây không phải chuyển nguyên release cùng lúc hoặc rollback DB tự động. Script không xóa file cũ trên hosting; khi thêm migration hoặc đổi cấu trúc phải có kế hoạch riêng.

## Kiểm tra sau deploy

- Mở login, health.php, version.php và assets/app.css trên URL public.
- Đối chiếu revision với commit của run, không suy ra chỉ từ màu xanh.
- Kiểm tra CI dùng User-Agent mô tả đúng ứng dụng DevopQLYFood-CI; TLS và health gate vẫn được giữ.
- Khi commit chỉ đổi docs, website có thể giữ revision của lần source deploy trước; xem đúng run được liên kết trong evidence.

## Các lỗi đã gặp và xử lý

| Lỗi | Xử lý đã kiểm chứng |
|---|---|
| HTTP 403 ở pre-deploy health | Client mặc định bị chặn; định danh ứng dụng bằng User-Agent mô tả rõ, health trả 200/ok |
| FTP hostname không phân giải | Dùng hostname dịch vụ có DNS thực tế |
| SSLCertVerificationError | Đối chiếu certificate/SAN và dùng hostname khớp; giữ xác minh TLS |
| HTTP 520 tại pre-check | Kiểm tra lại health, chỉ retry job lỗi sau khi endpoint về ok; không bỏ health gate |
| CSS bị 403 sau giải nén thủ công | Sửa quyền file và chuẩn hóa ZIP; CSS/JS đã được xác minh 200 sau FTPS deploy |

Client deploy xác minh certificate TLS trước khi gửi credentials; giữ hostname khớp certificate như phần cấu hình phía trên.

Với lỗi tạm và source/secrets đã đúng, có thể retry failed jobs của run; không cần chạy lại các job đã qua. Chỉ ghi deploy thành công khi job và public revision được xác minh.

## Lấy minh chứng và tái demo

Ở cuối run, tải các artifacts `week2-staging-evidence-<sha>` và `week2-live-evidence-<sha>`. Chúng chứa ảnh Chrome thật, health/revision và JSON persistence. Image/source giữ 7 ngày; log/ảnh giữ 14 ngày theo workflow. Bộ checkpoint hiện tại đã lưu ảnh/JSON/log chọn lọc trong [evidence Week 2](../evidence/week2/completion-2026-10-28.md).

Để demo mới: thay một câu nhỏ trong login, commit/push main, theo dõi ba job rồi refresh login và version.php. Không chỉnh sửa mã validator lỗi trên main; dùng run lỗi/PR đã đóng để giải thích fail gate. Không cần vào cPanel chạy lệnh update.

## Phạm vi checkpoint

Đã có Docker/Compose, persistence, deploy image staging, source deploy cPanel và ảnh trước/sau theo ảnh đề Week 2. Staging không tồn tại liên tục; cPanel không chạy image Docker. Không mô tả DB staging thành DB live. Điểm thực tế do giảng viên đánh giá; [checklist](WEEK2_CHECKLIST.md) đối chiếu từng đầu ra.

[GitHub Actions](https://docs.github.com/en/actions) · [cPanel FTP Accounts](https://docs.cpanel.net/cpanel/files/ftp-accounts/) · [Docker Compose](https://docs.docker.com/compose/).

## Các bước Week 3

Sau CRUD/persistence ở staging, workflow cấu hình/check UFW và chạy operations_smoke: backup → xóa fixture → restore → stop/start DB → alert lifecycle → ảnh Chrome thật. Deploy cPanel vẫn cần toàn bộ staging qua. Shared libraries/assets được upload trước bootstrap/controllers, REVISION cuối; đây chưa phải đổi release nguyên tử. Kết nối FTPS có retry TCP giới hạn, không retry sai credentials và không bỏ TLS.

Đã cập nhật source Week 3 lên cPanel bằng upload thủ công; kiểm tra lúc 11:20 ngày 04/11/2026 xác nhận health OK, revision d690bc0 và 7 kiểm tra đường dẫn HTTP đều qua. Đã xác minh dashboard, cập nhật số liệu, lọc log và tạo backup bằng phiên quản trị trên live lúc 11:34 ngày 04/11/2026. CPU/RAM live là số liệu OS có thể dùng chung, không phải quota riêng của tài khoản. Pipeline FTPS đã được khôi phục trong [run 41785933275](https://github.com/nathyu-le/DevopQLYFood/actions/runs/41785933275): cả ba job thành công; source `a0e5914`, health/revision và 7 kiểm tra HTTP đều qua. Xác minh TLS vẫn bật trên cả hai kênh FTP. [Theo dõi và minh chứng](../evidence/week3/README.md). Uptime workflow công khai chỉ probe health khoảng mỗi 5 phút, không có mật khẩu admin.
