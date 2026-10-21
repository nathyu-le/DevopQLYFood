# Week 4 — Final Demo & Troubleshooting

**Trạng thái: chưa thực hiện.** Đã đối chiếu ảnh đề chi tiết “DEVOPS FINAL PROJECT — WEEK 4: Final Demonstration & Troubleshooting Challenge” (`1000003655.jpg`), ngày 21/10/2026. Mục tiêu: **Production Deployment & Final Defense**. Điểm tối đa: **3,0/10**.

## Demo tổng thể

- [ ] Tích hợp đủ Week 1–3 trên cùng bản triển khai ổn định, kiểm tra các link truy cập.
- [ ] Hoàn thiện deployment guide từ máy/server sạch, có bước hoặc script tái tạo được; ghi rõ cấu hình cần cấp riêng.
- [ ] Demo end-to-end: thay đổi source → commit → build/test/deploy → ứng dụng cập nhật.
- [ ] Demo thao tác thật với DB và dữ liệu bền vững.
- [ ] Giải thích dashboard, logs, một cảnh báo đã kiểm thử và cấu hình bảo mật.
- [ ] Chứng minh backup/restore bằng dữ liệu đối chiếu.
- [ ] Thực hiện tình huống troubleshooting do giảng viên chọn, xác định nguyên nhân gốc và cách khắc phục.
- [ ] Playbook tối thiểu **3 sự cố có thật hoặc thực nghiệm**, mỗi mục có triệu chứng, xác minh, nguyên nhân, khắc phục và phòng tránh.
- [ ] Có video/ảnh/log dự phòng khi mạng hoặc môi trường demo gặp vấn đề.

## Báo cáo và bảo vệ

- [ ] Báo cáo kỹ thuật có Abstract; Introduction; Architecture & Design; Implementation; Experiments & Evidence; Security/Reliability; Challenges; Conclusion; References.
- [ ] Technical report theo cấu trúc paper, **6–10 trang là độ dài đề xuất** trong đề.
- [ ] Video thuyết minh cuối kỳ **5–10 phút**.
- [ ] README, cấu hình, sơ đồ hiện trạng và toàn bộ link evidence đã cập nhật.
- [ ] Phân công và đóng góp đối chiếu được với công việc/commit/minh chứng; mỗi người giải thích được phần mình làm và sẵn sàng trả lời kỹ thuật.
- [ ] Final repository có release/tag của phiên bản dùng để nộp và bảo vệ.
- [ ] Chuẩn bị sửa nhỏ hoặc xử lý sự cố trực tiếp khi được yêu cầu.
- [ ] Nộp đúng link/file và thời hạn do giảng viên thông báo.

## Trình tự live demo theo đề

| Thời lượng | Nội dung |
|---|---|
| 3 phút | Kiến trúc và trách nhiệm thành viên |
| 5–7 phút | Cập nhật source → commit → pipeline → production app có phiên bản mới |
| 3–5 phút | Monitoring, backup/restore và security |
| 5–10 phút | Live troubleshooting challenge và Q&A; điều chỉnh theo sĩ số |

Video nộp 5–10 phút là sản phẩm riêng; không coi đó là tổng thời lượng của buổi bảo vệ trực tiếp.

Tình huống luyện tập có thể là container stopped, sai biến môi trường, dịch vụ không truy cập được, database mất kết nối hoặc lỗi proxy nếu có dùng proxy. Đề không yêu cầu tác động vào một hệ thống production thực tế; thao tác trên môi trường demo/thử nghiệm phù hợp.

## Hồ sơ nộp và rubric

| Tiêu chí | Điểm theo đề | Sản phẩm / minh chứng |
|---|---:|---|
| End-to-end hệ thống chạy, pipeline và giám sát | 0,8 | Final repo + release/tag; architecture final; deployment/operations README; CI/CD, monitoring, backup/restore proof |
| Troubleshooting: root cause và recovery | 0,8 | Playbook ≥3 entries và xử lý tình huống trực tiếp |
| Technical report: thiết kế, minh chứng, đánh giá | 0,6 | Technical paper, đề xuất 6–10 trang |
| Presentation, Q&A và đóng góp cá nhân | 0,8 | Video 5–10 phút, trình bày nhóm, giải thích phần phụ trách |
| **Tổng** | **3,0** | Repo và các link nộp được kiểm tra quyền xem |

## Nơi cập nhật

Báo cáo dự kiến đặt trong `report/` khi tạo; video và chỉ mục demo nằm tại [evidence/week4/](../evidence/week4/README.md). Các bản cấu hình/source vẫn được cập nhật trong cùng repo. Ghi commit được dùng để bảo vệ để có thể xem lại phiên bản đó.
