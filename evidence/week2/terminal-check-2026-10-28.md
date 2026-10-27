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

**Hướng tiếp tục:** giữ cPanel theo quyết định của nhóm, kiểm tra FTP Accounts/explicit FTPS để cấu hình deploy source đã qua Docker CI. Chưa có deploy FTPS thành công; chưa ghi container đang chạy trên hosting hoặc Week 2 hoàn tất.

[CI/CD setup](../../docs/CICD_SETUP.md) · [Week 2 evidence](README.md)
