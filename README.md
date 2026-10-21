# DevopQLYFood · Website quản lý thực đơn quán ăn

**DevOps Final Project 2026 · Lộ trình 4 tuần · Checkpoint hiện tại: Week 1**

Ứng dụng PHP/MySQL giúp người quản lý thêm món, xem thực đơn, sửa giá, đổi tình trạng còn/hết và xóa món. Tên hiển thị trong ứng dụng: **Bếp Nhà**.

> **Trạng thái bàn giao:** Source và bộ tài liệu Week 1 đã được chuẩn bị. Chưa triển khai lên hosting/VPS của nhóm, chưa có URL ứng dụng và chưa có minh chứng cấu hình server. Sơ đồ bên dưới là **phương án thiết kế**, chưa phải hiện trạng triển khai. Các mục này cần được hoàn thành để đáp ứng đủ checkpoint Week 1.

**Đã kiểm tra source trên GitHub Actions:** PHP syntax, **14 validation checks** và **19 HTTP/MySQL integration checks** thành công với commit `4baaaa1`. [Xem run thành công](https://github.com/nathyu-le/DevopQLYFood/actions/runs/39529399926). Đây là môi trường test tạm, không phải server triển khai của nhóm.

## Tiến độ cả dự án

**Cả 4 tuần dùng chung repository này.** Source ứng dụng được cập nhật tiếp trong `app/`, `public/`, `database/` và `config/`; tiến độ, tài liệu và minh chứng được ghi theo từng checkpoint. [Xem lộ trình và nơi cập nhật](docs/ROADMAP.md).

| Tuần | Trọng tâm | Trạng thái hiện tại | Nơi theo dõi |
|---|---|---|---|
| Week 1 | Hạ tầng và triển khai ban đầu | Source/tài liệu/phân công đã có; kiểm tra source qua; còn triển khai và minh chứng server | [Checklist](docs/WEEK1_CHECKLIST.md) · [Minh chứng](evidence/week1/README.md) |
| Week 2 | Docker/Compose và CI/CD build–test–deploy | Chưa bắt đầu triển khai; đã chuẩn bị checklist | [Checklist](docs/WEEK2_CHECKLIST.md) · [Minh chứng](evidence/week2/README.md) |
| Week 3 | Monitoring, bảo mật, backup/restore và xử lý sự cố | Chưa bắt đầu triển khai; đã chuẩn bị checklist | [Checklist](docs/WEEK3_CHECKLIST.md) · [Minh chứng](evidence/week3/README.md) |
| Week 4 | Demo tổng thể, troubleshooting, báo cáo và bảo vệ | Chưa thực hiện; đã chuẩn bị checklist | [Checklist](docs/WEEK4_CHECKLIST.md) · [Minh chứng](evidence/week4/README.md) |

**Nhóm:** Lê Đình Nhật Huy, Nguyễn Phước Hải, Lê Quang Tùng. [Xem phân công Week 1](docs/TEAM.md).

## Đi nhanh đến phần cần xem

| Nội dung | Tài liệu |
|---|---|
| Kiến trúc đề xuất | [Sơ đồ PNG](architecture.png) · [Giải thích kiến trúc](docs/ARCHITECTURE.md) |
| Cài đặt thủ công ban đầu | [Hướng dẫn Ubuntu](docs/MANUAL_SETUP.md) |
| Phạm vi và nguồn code | [Scope & source](docs/SCOPE_AND_SOURCE.md) |
| Phân công nhóm | [Thành viên và phân công Week 1](docs/TEAM.md) |
| Kịch bản demo ghi/đọc DB | [Demo runbook](docs/DEMO.md) |
| Trạng thái theo rubric | [Week 1 checklist](docs/WEEK1_CHECKLIST.md) |
| Minh chứng cần bổ sung | [Evidence index](evidence/README.md) |
| Kiểm tra source | [Hướng dẫn và giới hạn kiểm tra](docs/VERIFICATION.md) · [GitHub Actions](https://github.com/nathyu-le/DevopQLYFood/actions) |

## 1. Mục tiêu và chức năng

| Mã | Chức năng | Dữ liệu / kết quả |
|---|---|---|
| F01 | Đăng nhập quản trị | Tài khoản được cấu hình cục bộ; mật khẩu lưu bằng hash |
| F02 | Xem, tìm kiếm và lọc món | Đọc danh sách từ MySQL; lọc danh mục và tình trạng |
| F03 | Thêm món | Tên, danh mục, giá nguyên VNĐ, mô tả, còn/hết |
| F04 | Sửa món | Cập nhật thông tin của một món đã có |
| F05 | Đổi tình trạng phục vụ | Chuyển còn món / hết món và lưu trong DB |
| F06 | Xóa món | Xóa bản ghi được chọn; giao diện có bước xác nhận |

Phạm vi chưa bao gồm đặt món, thanh toán, giao hàng và nhiều vai trò người dùng. Mục tiêu học phần tập trung vào triển khai và vận hành, không mở rộng chức năng nghiệp vụ quá mức.

## 2. Công nghệ và yêu cầu chạy

- PHP **8.2 trở lên**, bật `pdo_mysql`, `mbstring` và session.
- MySQL **8.0 trở lên** với bảng `dishes`, charset `utf8mb4`, InnoDB.
- HTML/CSS/JavaScript thuần; không cần Node.js, npm, Composer hoặc framework để chạy ứng dụng.
- Phương án Week 1: Apache + PHP + MySQL trên Ubuntu Server/VM/VPS. Môi trường thật sẽ được xác định sau.

![Kiến trúc dự kiến của DevopQLYFood](architecture.png)

**Luồng đọc:** trình duyệt → HTTP → Apache/PHP → SELECT MySQL → trang HTML.

**Luồng ghi:** form quản trị → kiểm tra đăng nhập, CSRF và dữ liệu → INSERT/UPDATE/DELETE → tải lại danh sách từ MySQL.

## 3. Cài đặt thủ công ban đầu

Xem hướng dẫn có đầy đủ câu lệnh tại [docs/MANUAL_SETUP.md](docs/MANUAL_SETUP.md).

Tóm tắt:

1. Chuẩn bị server, user/SSH, mạng và firewall.
2. Cài Apache, PHP, phần mở rộng PHP và MySQL.
3. Clone repository, tạo database và import `database/schema.sql`.
4. Tạo DB user chỉ có quyền `SELECT`, `INSERT`, `UPDATE`, `DELETE` cho database ứng dụng.
5. Chạy `php tools/configure.php` để tạo cấu hình và tài khoản quản trị. Script hỏi mật khẩu, tạo hash và kiểm tra kết nối DB; không in mật khẩu ra kết quả.
6. Cấu hình DocumentRoot của web server trỏ vào **`public/`**.
7. Kiểm tra từ máy khách và lưu minh chứng thật.

`config/config.example.php` chỉ là mẫu. **Không commit `config/config.local.php`.** Chỉ `public/` được phục vụ ra web; `app/`, `config/`, `database/`, `tests/` và `docs/` phải nằm ngoài DocumentRoot.

Sau khi đã có cấu hình và database, có thể kiểm tra nhanh trên máy phát triển bằng:

```bash
php -S 127.0.0.1:8080 -t public
```

Mở `http://127.0.0.1:8080`. Đây là PHP development server để kiểm tra cục bộ, không thay cho triển khai Apache và minh chứng truy cập server từ máy khách.

## 4. Database

- [database/schema.sql](database/schema.sql): tạo bảng, khóa chính, chỉ mục và ràng buộc.
- [database/seed.sql](database/seed.sql): tám món mẫu tùy chọn; chỉ import một lần nếu cần dữ liệu minh họa.
- `seed.sql` không phải minh chứng nhóm đã triển khai hoặc thao tác DB thành công.

Giá được lưu bằng `DECIMAL(12,0)`; ứng dụng chấp nhận số nguyên từ 0 đến 999.999.999 VNĐ. Danh mục có bốn lựa chọn: món chính, khai vị, đồ uống, tráng miệng.

## 5. Cấu trúc repository

| Đường dẫn | Vai trò |
|---|---|
| `public/` | Điểm vào web, đăng nhập, quản lý món, đăng xuất, health endpoint và assets |
| `app/bootstrap.php` | Kết nối PDO, session, CSRF, kiểm tra dữ liệu và escape HTML |
| `config/config.example.php` | Mẫu cấu hình không có thông tin đăng nhập thật |
| `database/` | Schema và dữ liệu mẫu |
| `docs/` | Kiến trúc, cài đặt, phân công, demo, lộ trình và checklist Week 1–4 |
| `evidence/` | Chỉ mục chung và các mục week1–week4; bổ sung ảnh/log/video thật theo tuần |
| `tests/` | Kiểm tra validation và CRUD HTTP với MySQL test riêng |
| `tools/configure.php` | Công cụ cấu hình bằng CLI |
| `.github/workflows/source-checks.yml` | Kiểm tra source trên GitHub Actions; **không triển khai ứng dụng** |

## 6. Kiểm tra và bảo mật cơ bản trong source

- PDO prepared statements cho các câu truy vấn có dữ liệu đầu vào.
- Kiểm tra quyền quản trị và CSRF trước khi ghi dữ liệu.
- Escape nội dung khi xuất HTML; kiểm tra danh mục, giá, tên và ID.
- Hash mật khẩu, đổi session ID sau đăng nhập; cookie HttpOnly và SameSite.
- Không có mật khẩu triển khai thật trong repository; file cấu hình cục bộ bị Git bỏ qua.
- `health.php` chỉ trả `ok`/`unavailable`, kiểm tra kết nối và bảng ứng dụng, không trả credentials.

Những biện pháp này nằm trong code. Cấu hình firewall, SSH, HTTPS và phân quyền trên server thật vẫn phải thực hiện và chứng minh riêng.

Workflow hiện tại chỉ lint PHP, kiểm tra validation và thử CRUD trong database tạm. **Đây chưa phải CI/CD build–test–deploy của Week 2.** Kết quả thật được xem trong tab Actions, không suy ra chỉ từ việc đã có file workflow.

## 7. Link và minh chứng triển khai

| Mục | Trạng thái |
|---|---|
| Repository | [nathyu-le/DevopQLYFood](https://github.com/nathyu-le/DevopQLYFood) |
| Website | Chưa triển khai — bổ sung sau |
| Môi trường server / OS / CPU / RAM / disk | Chưa cấu hình — dùng biểu mẫu trong `evidence/server/README.md` |
| Demo ghi/đọc dữ liệu từ máy khách | Chưa thực hiện trên server của nhóm |
| Video / ảnh cấu hình / logs | Chưa thu thập — xem `evidence/README.md` |
| Thành viên và phân công Week 1 | Đã cập nhật 3 thành viên trong [docs/TEAM.md](docs/TEAM.md); nhật ký thực hiện cập nhật sau khi có kết quả |

## 8. Nguồn và đóng góp

Source khởi tạo cho đề tài này, có hỗ trợ AI khi chuẩn bị code và tài liệu; không sao chép một ứng dụng mẫu bên ngoài. Nhóm cần đọc hiểu, kiểm tra, chỉnh sửa, triển khai và ghi lại đóng góp thật. Commit khởi tạo không thay cho bằng chứng mỗi thành viên tự thực hiện phần việc.

Tài liệu kỹ thuật tham khảo: [PHP PDO](https://www.php.net/manual/en/book.pdo.php), [password_hash](https://www.php.net/manual/en/function.password-hash.php), [PHP sessions](https://www.php.net/manual/en/book.session.php), [MySQL CREATE TABLE](https://dev.mysql.com/doc/refman/8.0/en/create-table.html). Tham chiếu đầy đủ hơn và phạm vi nhóm thực hiện nằm trong [SCOPE_AND_SOURCE.md](docs/SCOPE_AND_SOURCE.md).
