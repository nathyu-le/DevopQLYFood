# Minh chứng Week 2 — Docker CI đã chạy, hosting đang xác minh

**Cập nhật 28/10/2026 (giờ Việt Nam):** người triển khai chọn **gói cPanel mới đã mua, có Terminal**; không triển khai VPS. Đã nhận ảnh kiểm tra Terminal: cả ba lệnh Docker đều trả `docker: command not found`. Tên gói/thông số, khả năng cấp quyền Docker và daemon vẫn chưa được xác minh. cp1 là môi trường cũ có minh chứng Week 1.

## Kết quả thực tế trên Actions

- Commit đã kiểm tra: [`145a500655e02178bdec386ed608f7d460310ebb`](https://github.com/nathyu-le/DevopQLYFood/commit/145a500655e02178bdec386ed608f7d460310ebb).
- [Run 40604164827 — Docker checks and cPanel deploy](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40604164827).
- `container-checks`: **success**. `deploy-cpanel`: **skipped**, chưa cấu hình/bật FTPS; run xanh không chứng minh đã deploy.
- Môi trường: GitHub-hosted Ubuntu 24.04, image PHP 8.2 Apache và MariaDB 10.11, network/volume Compose tạm. Không phải gói cPanel của nhóm.
- [Source checks 40604164849](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40604164849) của cùng commit cũng thành công.

| Minh chứng | Kết quả đã xác minh | Giới hạn |
|---|---|---|
| Build image + Compose config/run | Tất cả bước qua; app/DB healthy | Runner tạm |
| Validation | 14 kiểm tra qua với PHP trong image | Không thay cho cấu hình hosting |
| HTTP/DB | 17 kiểm tra container qua; đăng nhập, CSRF, CRUD, SQL và đường dẫn riêng không được phục vụ | App test riêng |
| Persistence | Tạo lại cả app và DB bằng `--force-recreate`, giữ volume; cùng ID/tên/giá/trạng thái không đổi, web đọc lại được | Chưa có video trên môi trường triển khai |
| Test bắt lỗi | Cố ý nới giới hạn giá trên bản sao source; validation trả exit 1 tại assertion mong đợi | Không phải một run tổng thể fail hoặc demo deploy bị chặn trên hosting |
| Gói source cPanel | 9 runtime files; loại config/secrets/DB/.htaccess; kiểm tra SHA và archive paths qua | Chưa upload lên hosting |
| Image/source artifacts và logs | Đã upload thành công, xem cuối trang run | Image/source giữ 7 ngày, logs giữ 14 ngày |
| Tự động cập nhật website | Chưa có | Cần xác minh Docker trên gói mới và chọn/kết nối deploy |

Log chọn lọc, không chứa credentials: [ci-2026-10-28.log](ci-2026-10-28.log).

## Artifacts trong run

- `cpanel-source-145a500655e02178bdec386ed608f7d460310ebb`: gói source cho phương án FTPS, artifact ID `12780196523`.
- `tested-image-145a500655e02178bdec386ed608f7d460310ebb`: image Docker đã qua kiểm tra, ID `12779846868`; không phải source upload lên File Manager.
- `container-test-results-145a500655e02178bdec386ed608f7d460310ebb`: status/logs services, ID `12779757039`.

## Cập nhật sau khi web mới chạy

Người triển khai xác nhận website mới đã chạy tại [khotrick.org/DevopQLYFood/public/](https://khotrick.org/DevopQLYFood/public/). Đây là xác nhận của người triển khai, chưa thay cho log health/CRUD độc lập hoặc demo tự động cập nhật từ commit. Ảnh FTP Accounts cho thấy tài khoản riêng đã được tạo; không đăng ảnh hoặc thông tin tài khoản vào repo.

Bản sửa quyền file trong source ZIP có [run 40629412986](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40629412986), commit `751285c3632a69f0a0c6a02b0cdb6fbab8339ac6`: container-checks success, gồm kiểm tra quyền archive 0644; deploy-cpanel skipped. [Source checks cùng commit](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40629413009) success. Cấu hình kết nối trên GitHub vẫn là bước tiếp theo.

## Còn phải làm trên cPanel

1. Đã chạy ba lệnh Docker trong Terminal: đều `command not found`. [Bản ghi lược thông tin tài khoản](terminal-check-2026-10-28.md); không đăng ảnh gốc.
2. Tiếp tục kiểm tra mục FTP Accounts và hỗ trợ explicit FTPS để cấu hình deploy source. Docker mới được kiểm chứng trên CI; tiêu chí container trên máy triển khai chưa có kết quả đáp ứng.
3. Chạy pipeline deploy thành công, lưu URL và SHA trước/sau cùng health/revision, demo thay đổi source xuất hiện trên web.
4. Thu run fail chứng minh lỗi test chặn deploy và video/ảnh đúng môi trường, bổ sung OS/user/SSH/firewall còn thiếu nếu đáp ứng được trên hosting.

Chưa đánh dấu Week 2 hoàn tất. Không gán việc thực hiện kiểm tra tự động cho thành viên cụ thể khi chưa có xác nhận đóng góp.

[Checklist](../../docs/WEEK2_CHECKLIST.md) · [Docker](../../docs/DOCKER_SETUP.md) · [CI/CD cPanel](../../docs/CICD_SETUP.md) · [Lộ trình](../../docs/ROADMAP.md)
