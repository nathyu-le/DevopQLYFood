# Chỉ mục minh chứng toàn dự án

| Tuần | Nơi cập nhật | Trạng thái |
|---|---|---|
| Week 1 | [week1](week1/README.md), [server](server/README.md), [application](application/README.md) | Có ảnh/log app + DB và môi trường hosting cũ; OS/user/SSH/firewall chưa hoàn tất |
| Week 2 | [week2](week2/README.md) | Docker/persistence trên CI qua; push → FTPS deploy → UI/revision mới đã xác minh, container trên hosting chưa có |
| Week 3 | [week3](week3/README.md) | Chưa có monitoring/security/backup/restore |
| Week 4 | [week4](week4/README.md) | Chưa có demo tổng thể/báo cáo cuối |

**Quyết định mới ngày 28/10/2026:** người triển khai đã mua gói cPanel mới có Terminal, không triển khai VPS. Chưa xác minh Docker/Compose/quyền daemon hoặc thông số gói mới. Minh chứng Week 1 thuộc môi trường trước đó; không suy ra cấu hình gói mới từ ảnh cũ.

Week 2 có [run tự động 40633267900](https://github.com/nathyu-le/DevopQLYFood/actions/runs/40633267900), commit `6583790`: cả container-checks và deploy-cpanel success ngay attempt 1. [Đối chiếu trước/sau](week2/deploy-2026-10-28.md) và log đã lưu trong Git.

Week 1 giữ nguyên ảnh và kết quả đã lưu: [demo app/DB](application/demo-2026-10-21.md), [thông tin hosting cũ](server/README.md), [bảo vệ truy cập HTTP](server/http-protection-2026-10-21.md). [Bộ source checks Week 1](https://github.com/nathyu-le/DevopQLYFood/actions/runs/39529399926) có 14 validation và 19 HTTP/MySQL checks; đây là runner tạm.

Mỗi minh chứng ghi ngày, môi trường, người thực hiện thực tế, commit/run và kết quả quan sát. Chỉ cập nhật checklist sau khi có kết quả. Không đưa config thật, mật khẩu, keys, token hoặc dump DB vào ảnh/log/repo. Video ngoài GitHub cần quyền xem phù hợp.

[Phân công đã xác nhận](../docs/TEAM.md) · [Kế hoạch cPanel](../docs/DEPLOYMENT_PLAN.md) · [Checklist Week 2](../docs/WEEK2_CHECKLIST.md).
