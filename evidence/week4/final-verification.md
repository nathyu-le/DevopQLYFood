# Kiểm chứng cuối kỳ Week 4

Source được kiểm tra và triển khai: **4b20e57e5e8d8fb94222fea256990f8643803068**.

[Run 42861794294](https://github.com/nathyu-le/DevopQLYFood/actions/runs/42861794294) hoàn tất cả ba job success. Post-deploy xác nhận health ok và revision đúng source SHA. [Tổng hợp JSON không chứa secrets hoặc dữ liệu live](final-verification.json).

| Hạng mục | Kết quả và nơi kiểm tra |
|---|---|
| container-checks 129991833446 | Build image mới, preflight PASS; 19 HTTP/DB/persistence checks qua; package/TLS gates và client regression qua |
| deploy-staging 129992902519 | Cùng image ID, 19 checks lại; 37 operations checks qua, firewall staging và demo isolation/recovery qua |
| deploy-cpanel 129994343366 | FTPS có xác minh TLS; health/revision đúng; 14 kiểm tra HTTP access qua |
| Health/revision live | Post-deploy capture ghi health ok, SHA 4b20e57; không coi docs-only commit sau đó là source runtime mới |
| Trang Week 4 | Anonymous chuyển login; admin staging render đúng revision; mốc hiển thị đổi từ lần 1 sang lần 2 qua commit/pipeline |
| Giữ Week 3 | Net diff app/public cũ không đổi; 26 file runtime/tài liệu Week 3 so byte giống mốc 2c3b9de; [manifest](week3-preservation.json) |

## Minh chứng từng hành vi

Preflight kiểm tra PHP 8.2+, pdo_mysql/pdo_sqlite/mbstring/session, config/hash, SELECT schema và private var. Đây là kiểm tra trong image sạch trên runner, không phải provision lại một tài khoản cPanel mới.

Staging chứng minh snapshot hỏng bị từ chối trước DELETE/INSERT, restore khớp toàn bộ dữ liệu/timestamps, dừng MariaDB thật gây 503 và alert firing, start lại về 200 và ok. Demo SQLite có backup/download/xóa/restore thật, ngắt file gây 503, reconnect về 200 và hai mẫu tốt xóa cảnh báo. Digest MariaDB trước/sau demo bằng nhau.

14 HTTP checks live: bốn assets 200; ba private API 401; ba trang admin chuyển login; bốn đường dẫn private bị chặn 403/404. Script không đọc nội dung config hoặc snapshot private. Các check này không là kiểm tra firewall OS của provider.

## Video và hồ sơ

[Run video 42860890403](https://github.com/nathyu-le/DevopQLYFood/actions/runs/42860890403) success; artifact week4-demo-video có MP4 400 giây cùng metadata, audio và video streams. Giọng máy được ghi rõ, dữ liệu thao tác là tổng hợp trên staging. File chưa phát lại cục bộ vì trình duyệt không tải được artifact; nhóm cần mở kiểm tra trước khi dùng/nộp. Đây không phải ghi hình thành viên tự thuyết minh.

Bản Word kịch bản 7 trang và báo cáo 8 trang đã render/kiểm tra từng trang, bàn giao riêng. [Báo cáo repo](../../docs/WEEK4_TECHNICAL_REPORT.md) · [Kịch bản repo](../../docs/WEEK4_DEMO.md) · [Cài mới](../../docs/FINAL_DEPLOYMENT.md).

## Phạm vi còn lại

Phiên admin live không được đăng nhập lại trong đợt này; kiểm tra admin mới chạy trong CI độc lập và có checkpoint live lịch sử Week 3. Không khai là vừa bấm demo trong phiên admin cPanel hiện tại. Khi báo cáo, đăng nhập sẵn và làm theo kịch bản; không đổi mật khẩu hoặc tắt auth để thuận tiện quay.

Firewall/root/SSH và policy DB live còn phụ thuộc provider; chưa có email/Slack, cron backup live đã xác nhận hoặc rollback DB tự động. Tình huống do giảng viên chọn, xác nhận đóng góp cá nhân và nộp bài vẫn là việc nhóm thực hiện tại buổi bảo vệ.
