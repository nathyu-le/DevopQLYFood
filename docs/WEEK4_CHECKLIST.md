# Week 4 Final Demonstration and Troubleshooting

Mục tiêu: Production Deployment and Final Defense. Hướng thực hiện: cPanel live, Docker staging tạm. Week 3 giữ nguyên để nhóm báo cáo riêng; Week 4 chỉ bổ sung. Nhánh `week3-preserved` trỏ đúng source trước khi bắt đầu Week 4.

## Đầu ra kỹ thuật và hồ sơ

- [x] Trang Week 4 riêng, cùng ứng dụng đã có Week 1–3; không đổi giao diện hoặc logic demo Week 3.
- [x] Kiến trúc cuối tách cPanel, Docker staging và SQLite thử; [sơ đồ](FINAL_ARCHITECTURE.md).
- [x] Hướng dẫn cài từ source trên cPanel mới, config/DB/permissions/preflight và kiểm tra HTTP; [deployment guide](FINAL_DEPLOYMENT.md).
- [x] Pipeline tự động từ source main đến cPanel, image staging đúng ID và post-deploy revision; [kết quả từng run](../evidence/week4/final-verification.md).
- [x] Dashboard/log, HTTP auth/private paths, backup/restore và 503 → 200 được kiểm tra bằng các suite riêng, ghi đúng phạm vi.
- [x] [Playbook](TROUBLESHOOTING.md) có 7 sự cố đã gặp hoặc thực nghiệm, gồm đủ triệu chứng/xác minh/nguyên nhân/khắc phục/phòng tránh.
- [x] Báo cáo kỹ thuật theo cấu trúc paper; [Markdown](WEEK4_TECHNICAL_REPORT.md), bản Word 8 trang bàn giao riêng.
- [x] Kịch bản Word một người: lời đọc, link, thao tác, kết quả và hỏi đáp; [bản nội dung repo](WEEK4_DEMO.md).
- Release và tag của bản đóng mốc: [week4-final-v1.0.1](https://github.com/nathyu-le/DevopQLYFood/releases/tag/week4-final-v1.0.1). Trạng thái phát hành được đối chiếu trực tiếp trên GitHub.
- [x] Video dự phòng đã xuất 6 phút 40 giây, có audio/video, giọng máy và nhãn môi trường; [run/artifact](../evidence/week4/README.md). Rà soát 11/11 đã giải mã toàn bộ file không lỗi, kiểm tra ảnh của 10 chương và tín hiệu audio; nhóm nghe lại phát âm trước khi nộp.

Bản sửa 11/11: giữ snapshot vừa tạo khi retention chạy, kể cả tên file cũ xếp sau; thêm hai kiểm tra restore/metadata. Nhánh Week 3 gốc không thay đổi. [Kết quả rà soát](../evidence/week4/audit-2026-11-11.md).

## Những việc chỉ hoàn tất tại buổi bảo vệ hoặc khi nhóm xác nhận

- [ ] Chạy commit mới trực tiếp trong buổi và chỉ revision/nội dung mới cho giảng viên.
- [ ] Thực hiện tình huống giảng viên chọn và trả lời kỹ thuật.
- [ ] Cả ba thành viên đọc hiểu, xác nhận đóng góp cá nhân và trình bày được phần được giao.
- [ ] Nếu yêu cầu giọng thật của thành viên: quay/thay giọng thật cho video; không nhận giọng máy là giọng của nhóm.
- [ ] Nộp link/file đúng hình thức và hạn do giảng viên thông báo.

## Giới hạn vẫn cần công bố

Shared hosting không cung cấp quyền root cho nhóm; firewall OS/SSH và policy DB live chưa được xác minh tương đương tiêu chí hạ tầng Week 1. Không đánh dấu toàn bộ Week 1 hoàn tất vì đã có website hoặc UFW staging. Chưa có email/Slack, cron backup live được xác nhận hoặc rollback DB tự động. Snapshot dishes không là backup toàn tài khoản hosting. Trang demo SQLite không thay MariaDB chính.

## Trình tự theo đề và rubric

Kịch bản được căn chỉnh thành đúng bốn phần bên dưới: kiến trúc/phân công; cập nhật source qua pipeline đến production app; monitoring/backup/restore/security; tình huống troubleshooting do giảng viên chọn và hỏi đáp. Bản Word mới nhất vẫn giữ cùng tên; [nội dung đầy đủ](WEEK4_DEMO.md) và [mốc căn chỉnh tài liệu](../evidence/week4/presentation-alignment-2026-11-11.json). Đây là cập nhật kịch bản, không thay runtime hoặc file Week 3.

3 phút kiến trúc/phân công; 5–7 phút source → commit → pipeline → website; 3–5 phút monitoring/backup/security; 5–10 phút troubleshooting và Q&A. Kịch bản Word đi tuần tự, chỉ xác nhận triển khai khi run của commit mới hoàn tất. Video 5–10 phút là sản phẩm riêng.

| Tiêu chí | Điểm |
|---|---:|
| End-to-end hệ thống, pipeline và giám sát | 0,8 |
| Troubleshooting, root cause và recovery | 0,8 |
| Technical report | 0,6 |
| Presentation, Q&A, đóng góp cá nhân | 0,8 |
| Tổng | 3,0 |

Đánh giá thực tế do giảng viên quyết định; checklist hồ sơ không thay điểm hoặc xác nhận đã bảo vệ.
