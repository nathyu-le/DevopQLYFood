# Kế hoạch tiếp tục trên cPanel

**Ngày 28/10/2026: CI/CD source lên hosting mới đã được kiểm chứng.** Không triển khai VPS theo quyết định của nhóm.

## Trạng thái hiện tại

| Phần | Kết quả |
|---|---|
| App/DB hosting mới | Login/health/version/CSS/JS trả 200; health ok |
| Docker và persistence | Build/test/Compose/recreate thành công trên runner |
| FTPS | Đã cấu hình tài khoản riêng và hostname khớp certificate; TLS được xác minh |
| Push → deploy | Run 40633267900 success ngay attempt 1, UI và revision thay đổi đúng commit |
| Data/config khi update | Không có config thật/SQL/DB/.htaccess trong source package |
| Docker trên hosting | Terminal báo command not found; chưa chứng minh container tại môi trường này |

[Trước/sau](../evidence/week2/deploy-2026-10-28.md) · [Kiến trúc](WEEK2_ARCHITECTURE.md) · [Cấu hình tái triển khai](CICD_SETUP.md).

## Vận hành tiếp

Tiếp tục thay đổi source trong cùng repo; push main sẽ test rồi deploy nếu đạt. Với lỗi host/network tạm, xác minh health và chỉ retry job lỗi; không bỏ test/TLS. Sao lưu dữ liệu/config riêng trước thay đổi DB hoặc migration; cập nhật source package không tự chuyển schema hoặc rollback DB.

Week 1 đã có minh chứng trên cp1 cũ. Ảnh/thông số đó không chứng minh cấu hình gói mới. Không tự thay DNS, tài nguyên hoặc gói hosting trong quá trình cập nhật source.

## Những mục còn thiếu

- Container trên môi trường triển khai nếu rubric yêu cầu: Docker trên CI chưa tự thay thế; cần khả năng hỗ trợ thực tế hoặc xác nhận cách đánh giá từ giảng viên.
- Minh chứng OS/user/SSH/firewall Week 1 vẫn chưa được hoàn tất.
- Nhóm bổ sung ảnh/video, xác nhận đóng góp thực tế; tiếp tục Week 3 theo checklist riêng sau khi chốt checkpoint.

[Week 2 checklist](WEEK2_CHECKLIST.md) · [Lộ trình](ROADMAP.md).
