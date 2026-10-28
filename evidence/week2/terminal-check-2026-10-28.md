# Kiểm tra Terminal cPanel — 28/10/2026

Nguồn: ảnh Terminal do người triển khai cung cấp trong phiên làm việc. Chỉ ghi lại lệnh/kết quả; không đăng ảnh gốc hoặc thông tin tài khoản, hostname/IP.

```text
$ docker --version
bash: docker: command not found
$ docker compose version
bash: docker: command not found
$ docker info --format '{{.ServerVersion}}'
bash: docker: command not found
```

**Quan sát:** Terminal chạy được các lệnh shell; shell không tìm được lệnh `docker` trong PATH của tài khoản ở thời điểm kiểm tra. Chưa kiểm chứng Docker daemon, Compose hoặc quyền chạy/cài Docker. Kết quả không đủ để khẳng định nhà cung cấp tuyệt đối không hỗ trợ Docker.

**Tại thời điểm kiểm tra:** chưa cấu hình FTPS. **Cập nhật sau đó cùng ngày:** đã có run deploy image staging trên runner mới và FTPS source lên cPanel thành công, kèm ảnh trước/sau. [Bộ checkpoint mới](completion-2026-10-28.md). Kết quả này không có nghĩa Docker đã được cài/chạy trong cPanel; kiến trúc giữ hai môi trường riêng.

[CI/CD setup](../../docs/CICD_SETUP.md) · [Week 2 evidence](README.md)
