# Week 3 Ops Monitor

Ops Monitor là trang giám sát trong ứng dụng PHP, dùng được trên cPanel không có Docker daemon. Đăng nhập quản trị rồi mở `public/operations.php` hoặc mục **Vận hành**. API `public/ops-api.php` yêu cầu cùng session quản trị; dữ liệu không được công khai qua health endpoint.

## Dashboard và nguồn số liệu

| Chỉ số | Nguồn và phạm vi |
|---|---|
| CPU % | Ưu tiên cgroup v2 `cpu.stat` và quota `cpu.max`, tính chênh lệch usage/time giữa hai mẫu. Nếu không có quota, dùng chênh lệch CPU counters trong `/proc/stat` |
| RAM | Ưu tiên `memory.current`/`memory.max`; tiếp theo là MemTotal và MemAvailable trong `/proc/meminfo` |
| PHP memory | `memory_get_usage(true)`; chỉ là bộ nhớ PHP cấp phát, không phải RSS hoặc quota cả tài khoản |
| DB health và latency | Kết nối PDO mới, đọc bảng dishes, đo thời gian probe; lỗi trả unavailable, không trả connection string |
| Revision | File REVISION của source/image đã deploy |

Trên Docker staging, app có quota 1 CPU và 256 MB; số liệu cgroup thuộc container app. Trên shared hosting, `/proc` có thể phản ánh hệ điều hành dùng chung hoặc bị nhà cung cấp giới hạn. Dashboard luôn ghi nguồn đo; **không gọi số liệu host dùng chung là quota cPanel**. Nếu không đọc được CPU, hiển thị Chưa có thay vì dựng số 0. RAM fallback được ghi rõ là PHP allocation.

## Cấu hình đang áp dụng

Cấu hình collector và alert nằm trong `app/operations.php`, hàm `ops_collect`, `ops_update_alert` và `ops_public_state`. Biểu đồ/browser polling ở `public/assets/operations.js`.

| Thiết lập | Giá trị |
|---|---|
| Browser polling và khoảng tối thiểu ghi mẫu | 5 giây |
| Lịch sử | Tối đa 360 mẫu; biểu đồ hiện 60 mẫu gần nhất |
| DatabaseUnavailable | Firing sau 2 probe lỗi liên tiếp; về OK sau 2 probe tốt |
| CPUHigh và MemoryHigh | Ngưỡng 85%, cùng quy tắc 2 mẫu; không đánh giá khi không có số đo |
| Kênh cảnh báo | Banner và trạng thái pending/firing/ok trong dashboard; sự kiện chuyển trạng thái trong log |
| Application log | JSONL, UTC; rotate khi vượt 1 MiB, giữ file hiện tại và tối đa 6 bản cũ |
| Log trên dashboard | 100 dòng cuối, lọc theo sự kiện/request ID và từ thời điểm; hiển thị giờ Việt Nam |

Health probe công khai luôn kiểm tra DB thật; việc ghi lịch sử được giới hạn tần suất. Log chỉ nhận trường được cho phép: route, method, status, duration, request ID, error class và metadata backup/alert. Không ghi query string, nội dung form, mật khẩu hoặc thông báo PDO đầy đủ.

## Giám sát khi không mở trang

Workflow `.github/workflows/uptime-monitor.yml` gọi public HTTPS health bằng `tools/check-uptime.py`, không có credentials quản trị. Lịch dự kiến là mỗi 5 phút, lệch phút đầu giờ; GitHub có thể trì hoãn scheduled runs. Mỗi lần probe lưu HTTP status/latency và fail job nếu health sai. Sau khi source Week 3 được deploy, health probe cũng kích hoạt collector trên cPanel.

Dashboard đang mở vẫn cập nhật mỗi 5 giây. Không mô tả lịch GitHub 5 phút là monitor liên tục từng giây. Nếu cần tần suất ổn định ngoài GitHub, có thể dùng cPanel Cron Jobs gọi health/collector bằng công cụ phù hợp; chưa tự ghi một cron hosting là đã cấu hình khi không có bằng chứng.

## Lưu trữ và quyền truy cập

State và logs ở `var/operations`, ngoài `public/`, không nằm trong Git hoặc source ZIP. Thư mục private 0700, state JSON 0600; có `.htaccess` chặn HTTP tại var. Với layout dưới public_html, phải giữ `.htaccess` của thư mục dự án. Docker dùng named volume `operations_data`; pipeline chỉ dọn volume của staging tạm.

Lỗi mất DB và phục hồi được thử bằng cách stop/start MariaDB của project staging `_ci`. Không có nút dừng DB hoặc tạo lỗi trên website live. [Minh chứng](../evidence/week3/README.md) · [Demo](../docs/WEEK3_DEMO.md).

Nguồn kỹ thuật: [PHP memory](https://www.php.net/manual/en/function.memory-get-usage.php), [cgroup v2](https://www.kernel.org/doc/html/latest/admin-guide/cgroup-v2.html), [GitHub workflow schedules](https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax), [cPanel Resource Usage](https://docs.cpanel.net/cpanel/metrics/cpu-and-concurrent-connection-usage/).
