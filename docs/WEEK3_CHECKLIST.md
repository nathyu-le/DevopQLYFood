# Week 3 — Monitoring, Security & Reliability

**Trạng thái: chưa bắt đầu triển khai.** Checklist chuẩn bị theo brief chung; cập nhật theo đề Week 3 chi tiết. Điểm checkpoint theo brief: **2,5**.

## Mục tiêu

Chứng minh có thể quan sát tình trạng hệ thống, phát hiện vấn đề, bảo vệ truy cập và khôi phục dữ liệu.

## Logs và monitoring

- [ ] Thu thập/xem được logs của web/app và database theo nhu cầu xử lý sự cố.
- [ ] Có dashboard CPU, RAM và trạng thái ứng dụng.
- [ ] Có ít nhất một cảnh báo được cấu hình và **kiểm thử thực tế**, lưu bằng chứng kích hoạt/khôi phục.
- [ ] Giải thích được nguồn metrics/logs và cách sử dụng khi xảy ra lỗi.

## Bảo mật cơ bản

- [ ] Xác minh ports, firewall/security group và quyền SSH trên môi trường thật.
- [ ] Secrets không hard-code hoặc đưa vào logs/video/repo.
- [ ] DB user được phân quyền phù hợp; MySQL không public ngoài nhu cầu đã giải thích.
- [ ] Ghi nhận cấu hình bảo mật đã áp dụng; không chỉ liệt kê kế hoạch.

## Backup, restore và sự cố

- [ ] Có script backup database chạy được và vị trí lưu được xác định.
- [ ] Thực hiện restore vào môi trường phù hợp, đối chiếu các bản ghi để chứng minh khôi phục đúng.
- [ ] Có troubleshooting playbook ít nhất **3 mục**, gồm triệu chứng, cách kiểm tra, xử lý và xác nhận phục hồi.
- [ ] Thực hiện một kịch bản xử lý sự cố có minh chứng theo yêu cầu giảng viên.
- [ ] Lưu ảnh/log/video và kết quả restore vào `evidence/week3/`.

## Nơi cập nhật

Cấu hình monitoring dự kiến trong `monitoring/`; công cụ backup/restore trong `tools/`; playbook dự kiến ở `docs/TROUBLESHOOTING.md`. Các file này sẽ được tạo khi thực hiện, chưa có trong phiên bản hiện tại.

Xem [minh chứng Week 3](../evidence/week3/README.md) và bổ sung phân công tuần này vào [TEAM.md](TEAM.md) khi nhóm thống nhất.
