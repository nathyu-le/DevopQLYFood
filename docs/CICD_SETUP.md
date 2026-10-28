# Week 2 — CI/CD lên cPanel

**Đã chạy thành công ngày 28/10/2026:** [run 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900), event push, commit `6583790`, attempt 1. Cả build/test và deploy qua; website có dòng mới và đúng revision. [Minh chứng](../evidence/week2/deploy-2026-10-28.md).

## Luồng thực tế

Push main → checkout → build Docker image → PHP lint/validation/regression → Compose HTTP/DB/persistence → kiểm tra gói source → artifact → FTPS upload → health/revision. [Sơ đồ](WEEK2_ARCHITECTURE.md).

Docker chạy trên runner. cPanel chạy PHP và DB trực tiếp; ba lệnh Docker trong Terminal hosting trả command not found. Không triển khai VPS theo quyết định của nhóm.

Job deploy chỉ chạy trên main khi `CPANEL_DEPLOY_ENABLED=true`, có `needs: container-checks`. PR chỉ kiểm tra. Commit docs/evidence/Markdown được bỏ qua bởi container workflow; có thể Run workflow thủ công nếu cần. Thay đổi source/config chạy tự động.

## Cấu hình để tái triển khai

Trước lần deploy đầu, hosting phải có app + DB đã cấu hình, `config/config.local.php`, các thư mục app/public/config và health trả ok. Pipeline cập nhật source không thay việc cài mới hoặc chuyển database. Sao lưu source/config/DB ở nơi riêng trước khi chuyển môi trường.

Tạo FTP account riêng, giới hạn đúng thư mục đồ án. Dùng **hostname dịch vụ thực tế có DNS và khớp SAN trong certificate**; tên FTP do giao diện gợi ý chưa chắc đã có bản ghi DNS. TLS phải xác minh được trước khi client gửi credentials.

Repo → Settings → Secrets and variables → Actions, dùng repository scope:

| Loại | Tên | Nội dung |
|---|---|---|
| Variable | CPANEL_DEPLOY_ENABLED | true sau khi cấu hình xong; false để tắt deploy |
| Variable | CPANEL_FTP_HOST | Hostname dịch vụ đã xác minh với certificate, không có https:// |
| Variable | CPANEL_FTP_PORT | 21 cho explicit FTPS của môi trường đang dùng |
| Variable | CPANEL_FTP_ROOT | / khi FTP account đã giới hạn đúng thư mục DevopQLYFood |
| Variable | CPANEL_BASE_URL | https://khotrick.org/DevopQLYFood/public/ |
| Secret | CPANEL_FTP_USER | Username FTP đầy đủ |
| Secret | CPANEL_FTP_PASSWORD | Mật khẩu FTP riêng, nhập trực tiếp trong GitHub Secrets |

Đặt flag ở repository scope để điều kiện job đọc được trước khi job chạy. Job dùng environment cpanel; không đặt duyệt thủ công từng lần nếu cần demo tự động sau push.

Mở Actions → Week 2 - Docker checks and cPanel deploy → Run workflow → main cho lần kiểm tra đầu. Sau đó push thay đổi source sẽ tự chạy.

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

Có workflow Hosting connection diagnostics để đọc certificate công khai qua AUTH TLS, **không dùng FTP credentials hoặc truyền file**. Việc deploy dùng client riêng có xác minh certificate đầy đủ.

Với lỗi tạm và source/secrets đã đúng, có thể retry failed jobs của run; không cần chạy lại các job đã qua. Chỉ ghi deploy thành công khi job và public revision được xác minh.

## Giới hạn rubric

Docker/Compose và volume persistence đã có kết quả trên runner. Source lên cPanel đã tự động cập nhật từ push. Chưa chứng minh container chạy trên hosting; phần yêu cầu đó cần môi trường đáp ứng hoặc xác nhận cách đánh giá từ giảng viên. Chưa tự đánh dấu toàn bộ Week 2 hoàn tất.

[GitHub Actions](https://docs.github.com/en/actions) · [cPanel FTP Accounts](https://docs.cpanel.net/cpanel/files/ftp-accounts/) · [Docker Compose](https://docs.docker.com/compose/).
