# Week 3 — Monitoring, Security & Reliability

**Trạng thái: chưa bắt đầu triển khai.** Đã đối chiếu ảnh đề chi tiết “DEVOPS FINAL PROJECT — WEEK 3: Monitoring, Security & Reliability” (`1000003654.jpg`), ngày 21/10/2026. Mục tiêu: **Operate and Secure the Application**. Điểm tối đa: **2,5/10**, demo nhóm và bảo vệ kỹ thuật.

## Mục tiêu

Chứng minh có thể quan sát tình trạng hệ thống, phát hiện vấn đề, bảo vệ truy cập và khôi phục dữ liệu.

## Logs và monitoring

- [ ] Thu thập application logs, chỉ ra cách tra cứu lỗi theo thời gian; bổ sung web/DB logs khi cần xử lý sự cố.
- [ ] Có dashboard thời gian thực cho CPU, RAM và health/status của Web/API; ghi công cụ và nguồn dữ liệu (Prometheus/Grafana, CloudWatch hoặc tương đương).
- [ ] Có ít nhất một cảnh báo được cấu hình và **kiểm thử thực tế**, lưu bằng chứng kích hoạt/khôi phục.
- [ ] Giải thích được nguồn metrics/logs và cách sử dụng khi xảy ra lỗi.

## Bảo mật cơ bản

- [ ] Xác minh ports, firewall/security group và quyền SSH trên môi trường thật; chỉ mở các cổng cần thiết, hạn chế truy cập database.
- [ ] Secrets không hard-code hoặc đưa vào logs/video/repo.
- [ ] Giải thích nơi lưu secrets và ai/dịch vụ nào có quyền truy cập.
- [ ] DB user được phân quyền phù hợp; MySQL không public ngoài nhu cầu đã giải thích.
- [ ] Ghi nhận cấu hình bảo mật đã áp dụng; không chỉ liệt kê kế hoạch.

## Backup, restore và sự cố

- [ ] Có script backup và restore database chạy được; file backup lưu ở đường dẫn riêng, có chiến lược lưu trữ/thời gian giữ và quyền truy cập.
- [ ] Backup dữ liệu thử nghiệm, thay đổi/xóa dữ liệu thử nghiệm, restore rồi đối chiếu bản ghi đã có trước đó để chứng minh phục hồi đúng.
- [ ] Có troubleshooting playbook draft ít nhất **2 sự cố ở Week 3**; mỗi mục có triệu chứng, xác minh, nguyên nhân, khắc phục và xác nhận phục hồi. Week 4 mới nâng lên tối thiểu 3 sự cố có thật hoặc thực nghiệm.
- [ ] Dừng app hoặc tạo lỗi có kiểm soát trên môi trường thử nghiệm, tra log theo thời gian và phục hồi dịch vụ.
- [ ] Lưu ảnh/log/video và kết quả restore vào `evidence/week3/`.

## Trình tự live demo

1. Mở dashboard khi hệ thống đang chạy, giải thích CPU/RAM và health check.
2. Tạo tình huống cảnh báo, cho thấy notification hoặc state của alert; khôi phục rồi kiểm tra trạng thái.
3. Dừng app hoặc tạo lỗi thử nghiệm, tìm log theo thời gian, xác định lỗi và phục hồi dịch vụ.
4. Backup, thay đổi/xóa bản ghi thử nghiệm, restore và đối chiếu dữ liệu trước/sau. Chỉ dùng dữ liệu thử nghiệm cho thao tác mô phỏng xóa.

## Hồ sơ nộp và rubric

| Tiêu chí | Điểm theo đề | Sản phẩm / minh chứng |
|---|---:|---|
| Monitoring/metrics/logs/dashboard/alert | 0,8 | Dashboard screenshots + config, logs có mốc thời gian, alert đã thử |
| Firewall, secrets, database access | 0,5 | Security/ports checklist và cấu hình đã xác minh |
| Backup và restore có bằng chứng dữ liệu | 0,8 | Backup/restore scripts + logs, đối chiếu bản ghi |
| Giải thích và xử lý sự cố | 0,4 | Incident playbook draft ≥2 sự cố, video/ảnh minh chứng demo |
| **Tổng** | **2,5** | Link sản phẩm và minh chứng truy cập được |

## Nơi cập nhật

Cấu hình monitoring dự kiến trong `monitoring/`; công cụ backup/restore trong `tools/`; playbook dự kiến ở `docs/TROUBLESHOOTING.md`. Các file này sẽ được tạo khi thực hiện, chưa có trong phiên bản hiện tại.

Xem [minh chứng Week 3](../evidence/week3/README.md) và bổ sung phân công tuần này vào [TEAM.md](TEAM.md) khi nhóm thống nhất.
