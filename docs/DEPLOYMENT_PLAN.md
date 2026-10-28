# Kế hoạch tiếp tục trên cPanel

**Checkpoint Week 2 ngày 28/10/2026 đã có bộ minh chứng kỹ thuật.** Tiếp tục hosting cPanel hiện có theo quyết định của nhóm.

| Phần | Kết quả |
|---|---|
| CI | Build image, lint, 14 validation, regression, 17 container checks và package checks qua |
| Docker staging | Runner mới tải image artifact, so image ID, chạy --no-build; CRUD/persistence qua và có ảnh |
| Website cPanel | FTPS tự động từ push, health ok, revision d18f6be, có ảnh trước/sau |
| Test thất bại | Run validation lỗi thật fail; hai job deploy skipped; PR demo đóng không merge |
| Config và dữ liệu hosting | Không nằm trong source package; không chuyển dữ liệu từ CI sang hosting |

[Run thành công](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505) · [Bộ minh chứng](../evidence/week2/completion-2026-10-28.md) · [Kiến trúc](WEEK2_ARCHITECTURE.md) · [Runbook](CICD_SETUP.md).

## Khi tiếp tục sửa app

Push main tự chạy tests, deploy staging rồi mới cập nhật cPanel. Đối chiếu version.php với source commit của run. Commit chỉ đổi docs có thể giữ revision lần source deploy trước. Với lỗi mạng tạm, xác minh health và retry job lỗi; không bỏ test hoặc TLS.

Pipeline chỉ cập nhật runtime source, chưa migration schema/rollback DB tự động. Sao lưu config và DB riêng trước khi thay đổi cấu trúc dữ liệu. Staging được dọn sau run; không dùng nó làm dịch vụ public chạy lâu dài.

## Checkpoint tiếp theo

- Nhóm tập [kịch bản một người Week 2](WEEK2_DEMO.md), kiểm tra quyền mở repo và đường dẫn khi nộp.
- Phần OS/user/SSH/firewall Week 1 còn thiếu giữ ở [checklist Week 1](WEEK1_CHECKLIST.md); không dùng Docker CI thay minh chứng hosting.
- Week 3 triển khai monitoring, bảo mật, backup/restore và playbook theo [checklist](WEEK3_CHECKLIST.md). Chỉ đánh dấu sau khi chạy thật.
- Week 4 tích hợp, demo và báo cáo theo [checklist](WEEK4_CHECKLIST.md).

Ảnh cp1 Week 1 là lịch sử, không suy ra tài nguyên gói cPanel mới. Không tự thay gói hosting hoặc DNS trong đợt cập nhật này.
