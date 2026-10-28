# Chỉ mục minh chứng toàn dự án

| Tuần | Nơi cập nhật | Trạng thái |
|---|---|---|
| Week 1 | [week1](week1/README.md), [server](server/README.md), [application](application/README.md) | Có ảnh/log app + DB và hosting cũ; OS/user/SSH/firewall còn thiếu |
| Week 2 | [Bộ checkpoint](week2/completion-2026-10-28.md), [chỉ mục](week2/README.md) | Đã kiểm chứng Docker/Compose, image staging, persistence, CI/CD cPanel, test fail và ảnh trước/sau |
| Week 3 | [week3](week3/README.md) | Chưa có monitoring/security/backup/restore |
| Week 4 | [week4](week4/README.md) | Chưa có demo tổng thể/báo cáo cuối |

**Week 2 ngày 28/10/2026:** [run 40634997505](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40634997505), source `d18f6be`: container-checks → deploy-staging → deploy-cpanel cùng success. Staging tải đúng image artifact trên runner mới, lưu persistence và ảnh rồi dọn stack. Live tiếp tục PHP/DB trực tiếp trên cPanel. [Run lỗi validation thật](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40635078470) có hai deploy skipped; PR đã đóng không merge.

Week 1 giữ nguyên ảnh/log: [demo app/DB](application/demo-2026-10-21.md), [hosting cũ](server/README.md), [bảo vệ HTTP](server/http-protection-2026-10-21.md). Không dùng thông số cp1 cũ để suy ra gói cPanel mới hoặc dùng DB CI thay DB live.

Mỗi minh chứng ghi ngày, môi trường, nguồn tạo, commit/run và kết quả thật. Không tự gán thao tác tự động cho thành viên nhóm; ghi đóng góp sau khi xác nhận công việc thực tế. Không đưa credentials/config thật hoặc dump DB vào ảnh/log/repo.

[Phân công](../docs/TEAM.md) · [Kịch bản Week 2](../docs/WEEK2_DEMO.md) · [Checklist Week 2](../docs/WEEK2_CHECKLIST.md).
