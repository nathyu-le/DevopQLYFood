# Minh chứng Week 2 — CI/CD cPanel đã chạy

**Ngày xác minh: 28/10/2026.** [Run 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900), event push, commit `6583790e9850d7cdbdfa9aebac1bc896781dbcfb`, attempt 1: container-checks và deploy-cpanel **success**.

[Bộ trước/sau và cách xem](deploy-2026-10-28.md) · [Log chọn lọc](deploy-2026-10-28.log) · [Kiến trúc](../../docs/WEEK2_ARCHITECTURE.md).

| Nội dung | Kết quả thực tế |
|---|---|
| Dockerfile/Compose | Build và chạy app Apache/PHP + MariaDB trên runner |
| Validation | 14 checks qua với PHP trong image |
| HTTP/DB/persistence | 17 checks qua; cùng bản ghi tồn tại sau recreate app + DB |
| Kiểm tra gói source | Không có config/secrets/SQL; quyền archive 0644; SHA/path/HTTP gates qua |
| Deploy tự động | Run từ push, FTPS upload và health/revision đúng SHA |
| Thay đổi thấy trên app | Dòng mô tả đăng nhập cũ được thay bằng dòng mới |
| Đối chiếu ngoài runner | Login/health/version/CSS/JS 200; health ok, revision đúng commit; CSS/JS khớp source |
| Test/gate lỗi | Có run pre-deploy health 403/520 fail và dừng trước FTP; ghi đúng phạm vi lỗi |
| Docker trên hosting | Chưa có; Terminal trả docker: command not found |

FTPS đã kết nối bằng TLS có xác minh. Không đăng hostname dịch vụ, tài khoản, credentials hoặc ảnh FTP vào bộ minh chứng này.

## Lịch sử kiểm tra

- [Run 40604164827](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40604164827), commit 145a500: Docker CI qua, deploy skipped ở giai đoạn chưa cấu hình.
- [Run 40629412986](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40629412986), commit 751285c: thêm regression quyền file; CI qua, deploy skipped.
- [Run 40631292950](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40631292950): xác định pre-deploy health HTTP 403, dừng trước upload.
- [Run 40632576041](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40632576041): sau cấu hình hostname/TLS và retry lỗi health tạm, deploy thành công; public revision khớp aea127a.
- [Run 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900): thay đổi giao diện từ push, thành công ngay attempt 1, không thao tác cập nhật source trên hosting.
- [Source checks cùng commit cuối](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267716): success.

Image/source artifacts giữ 7 ngày, logs artifact giữ 14 ngày theo workflow. Kết quả/log chọn lọc đã lưu trong Git để còn đối chiếu sau khi artifacts hết hạn. [Log CI giai đoạn đầu](ci-2026-10-28.log).

## Phần nhóm cần bổ sung

Ảnh/video trình bày và đóng góp thực tế; xác nhận tiêu chí Docker trên server vì hosting hiện không chạy container. Chưa có bằng chứng OS/user/SSH/firewall Week 1 đã hoàn tất. Không dùng quota/ảnh cp1 cũ để suy ra cấu hình gói mới.

[Checklist](../../docs/WEEK2_CHECKLIST.md) · [CI/CD setup](../../docs/CICD_SETUP.md).
