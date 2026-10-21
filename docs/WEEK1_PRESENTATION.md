# Kịch bản trình bày Week 1 — một người trình bày

Bản ngày 21/10/2026. Một người nói và thao tác xuyên suốt; thời lượng gợi ý khoảng 7–9 phút, tùy tốc độ demo. Đây là báo cáo tiến độ triển khai app/DB trên cp1; phần tự cài/cấu hình OS, SSH và firewall đang tạm hoãn theo [kế hoạch](DEPLOYMENT_PLAN.md), chưa có xác nhận giảng viên chấp thuận thay thế tiêu chí.

**Cách dùng:** dòng **Mở / Thao tác** là chỉ dẫn, không đọc thành lời. Các đoạn trích sau **Lời đọc** là nội dung để đọc. Chỉ đọc lời xác nhận kết quả sau khi kết quả đó xuất hiện thật trên màn hình. Các câu hỏi ở cuối chỉ dùng khi thầy hỏi.

## Chuẩn bị trước giờ trình bày

Mở sẵn các tab theo thứ tự:

| Tab | Nội dung cần mở |
|---|---|
| 1 | [Repository và README](https://github.com/nathyu-le/DevopQLYFood) |
| 2 | [Sơ đồ architecture.png](https://github.com/nathyu-le/DevopQLYFood/blob/main/architecture.png) |
| 3 | [Thông tin hosting và ảnh cPanel](https://github.com/nathyu-le/DevopQLYFood/blob/main/evidence/server/README.md) |
| 4 | [Website](https://thitruongmmo.com/DevopQLYFood/public/) — đăng nhập admin trước |
| 5 | cPanel → phpMyAdmin → chọn đúng database ứng dụng → tab SQL; dùng tài khoản đang có, không ghi mật khẩu vào kịch bản |
| 6 | [Bộ ảnh app/DB dự phòng](https://github.com/nathyu-le/DevopQLYFood/blob/main/evidence/application/demo-2026-10-21.md) và [run CI đã thành công](https://github.com/nathyu-le/DevopQLYFood/actions/runs/39529399926) |

Mở kịch bản trên điện thoại/giấy hoặc màn hình riêng để đọc. Khi chiếu, chỉ hiển thị phần cần giải thích; đăng nhập trước, giữ mật khẩu, khóa và file cấu hình thật ngoài phần chiếu/chụp. Chạy thử chuỗi thêm → tải lại → SELECT → sửa → SELECT trước buổi; lần trình bày dùng một bản ghi mới và ghi nhớ ID thật. Giữ món demo lại để đối chiếu.

Dữ liệu nhập cho bản ghi demo trực tiếp:

| Trường | Giá trị |
|---|---|
| Tên món | **Cơm gà Demo trực tiếp** |
| Danh mục | Món chính |
| Giá ban đầu | Nhập **45000** |
| Tình trạng ban đầu | Còn món |
| Mô tả | Món dùng để minh họa đọc và ghi dữ liệu Week 1. |
| Giá sau khi sửa | Nhập **50000** |
| Tình trạng sau khi sửa | Hết món |

Chuẩn bị sẵn truy vấn trong phpMyAdmin, chạy hai lần ở bước 5C và 5E:

```sql
SELECT id, name, category, price, is_available, updated_at
FROM dishes
WHERE name = 'Cơm gà Demo trực tiếp'
ORDER BY id DESC
LIMIT 1;
```

Nếu đã tập trước và có trùng tên, đối chiếu đúng **ID vừa tạo trên web** với dòng SQL mới nhất. Không dùng cố định ID 9 cho demo mới; ID 9 thuộc bộ ảnh minh chứng đã lưu.

## 1. Giới thiệu đề tài và phân công — khoảng 40 giây

**Mở:** tab 1, đầu README. Hiển thị tên đề tài và nhóm; có thể mở thêm `docs/TEAM.md` khi nói phân công.

**Lời đọc:**

> Dạ em chào thầy. Em xin đại diện nhóm gồm Lê Đình Nhật Huy, Nguyễn Phước Hải và Lê Quang Tùng trình bày tiến độ Week 1 của đề tài website quản lý thực đơn quán ăn, tên ứng dụng là Bếp Nhà.
>
> Ứng dụng phục vụ người quản lý quán cập nhật món ăn, giá bán và tình trạng còn hoặc hết món. Phạm vi hiện tại gồm đăng nhập quản trị, thêm, xem, tìm kiếm, sửa và xóa món.
>
> Về phân công, Huy phụ trách kiến trúc, app/database và tài liệu GitHub; Hải phụ trách kiểm tra chức năng và đối chiếu dữ liệu; Tùng phụ trách tổng hợp minh chứng. Sau đây em trình bày kiến trúc, môi trường triển khai và demo thao tác dữ liệu.

Đây là phân công, không đọc thành khẳng định mọi đầu việc của từng người đã hoàn thành.

## 2. Giải thích kiến trúc — khoảng 55 giây

**Mở:** tab 2, sơ đồ PNG. Chỉ lần lượt vào máy khách → web server → PHP → MariaDB.

**Lời đọc:**

> Đây là sơ đồ môi trường đang dùng. Máy khách là trình duyệt trên laptop hoặc điện thoại. Người dùng truy cập website thitruongmmo.com/DevopQLYFood/public bằng HTTPS, sử dụng cổng 443.
>
> Request được web server trên hosting tiếp nhận và chuyển cho ứng dụng PHP xử lý. Khi xem danh sách, PHP đọc bảng dishes trong database MariaDB rồi trả giao diện về trình duyệt. Khi thêm hoặc sửa món, PHP kiểm tra dữ liệu, ghi vào database và đọc lại danh sách.
>
> Vì vậy trình duyệt làm việc với website, còn kết nối database được thực hiện ở phía PHP qua PDO. Nhóm chưa cấu hình reverse proxy riêng. Vị trí máy database, cổng database thực tế và đường dẫn lưu trữ cấp hệ điều hành chưa được nhóm xác minh, nên sơ đồ ghi rõ những phần đó.

Không đọc loopback `127.0.0.1:3306` hoặc `/var/lib/mysql` thành hiện trạng hosting; đó chỉ là giá trị trong phương án Ubuntu tham chiếu.

## 3. Môi trường hosting thực tế — khoảng 45 giây

**Mở:** tab 3. Hiển thị bảng thông số, sau đó ảnh PHP và Statistics; không cần cuộn đọc toàn bộ ảnh.

**Lời đọc:**

> Tuần này nhóm tận dụng hosting cPanel gói cp1 để triển khai và demo app cùng database. Thông tin cPanel cho biết hệ điều hành Linux, web server Apache 2.4.67, domain đang dùng PHP 8.2 và dịch vụ database là MariaDB 10.11.16.
>
> Giới hạn tài khoản đang dùng là CPU 100%, RAM 600 MB và dung lượng 1.000 MB. Đây là quota của tài khoản hosting, không phải cấu hình vật lý của toàn bộ máy chủ.
>
> cPanel hiển thị IP dùng chung 160.191.243.50 và Document Root của domain là public_html. Ứng dụng hiện được truy cập qua đường dẫn DevopQLYFood/public.

## 4. Repository, source và bảo vệ thư mục — khoảng 55 giây

**Mở:** tab 1, danh sách thư mục gốc. Nếu cần chỉ vào `public/`, `app/`, `database/`, `docs/`, `evidence/`. Chỉ mở file mẫu nếu cần; không mở cấu hình thật trên hosting.

**Lời đọc:**

> Repository dùng chung cho cả bốn tuần. Thư mục public là phần phục vụ website; app chứa xử lý PHP và kết nối database; database chứa schema và dữ liệu mẫu. Tài liệu triển khai ở docs, còn ảnh và log thực tế ở evidence.
>
> Source được khởi tạo riêng cho đề tài, có hỗ trợ AI; nguồn và phạm vi được khai báo trong tài liệu SCOPE_AND_SOURCE. Cấu hình database và tài khoản quản trị thật được tạo riêng trên hosting, không đưa lên GitHub.
>
> Với layout hiện tại, nhóm đã thêm htaccess để chặn truy cập web vào các thư mục ngoài public. Kết quả kiểm tra cho thấy URL schema và README trả 403, trong khi trang đăng nhập, health và CSS vẫn trả 200. Đây là bảo vệ vùng phục vụ web, không phải cấu hình firewall của hệ điều hành.

Có thể mở [log HTTP](../evidence/server/http-protection-2026-10-21.md) để chỉ bảng kết quả, hoặc [health endpoint](https://thitruongmmo.com/DevopQLYFood/public/health.php) để minh họa. Health chỉ xác nhận kết nối/đọc bảng; demo tiếp theo mới chứng minh ghi dữ liệu.

## 5. Demo trực tiếp app và database — khoảng 3 phút

### 5A. Mở website và thêm một món mới

**Mở:** tab 4, website đã đăng nhập.

**Lời đọc trước thao tác:**

> Đây là website đang chạy trên hosting, được truy cập từ máy khách qua Internet. Em sẽ thêm một món mới, kiểm tra sau khi tải lại, đối chiếu database rồi sửa cùng bản ghi để chứng minh luồng đọc và ghi.

**Thao tác:** bấm **Thêm món mới**, điền đúng bảng dữ liệu chuẩn bị. Bấm **Thêm vào thực đơn**. Tìm dòng mới nhất và ghi nhớ số `#ID` hiển thị cạnh danh mục.

**Lời đọc sau khi thấy lưu thành công:**

> Món Cơm gà Demo trực tiếp đã xuất hiện với giá 45.000 đồng và trạng thái còn món. Đây là ID của bản ghi vừa tạo; em sẽ dùng cùng ID này để đối chiếu các bước tiếp theo.

### 5B. Tải lại và tìm kiếm

**Thao tác:** bấm tải lại trang của trình duyệt. Nhập tên **Cơm gà Demo trực tiếp** vào ô tìm kiếm, bấm **Lọc**. Chỉ đúng ID vừa tạo.

**Lời đọc sau khi thấy đúng món:**

> Sau khi tải lại và tìm kiếm, bản ghi vừa tạo vẫn xuất hiện. Danh sách được PHP truy vấn lại từ database, không phải dữ liệu tạm chỉ còn trên giao diện. Tiếp theo em kiểm tra trực tiếp bản ghi trong database.

### 5C. Đối chiếu dữ liệu ban đầu bằng SQL

**Mở:** tab 5, phpMyAdmin của đúng database ứng dụng → **SQL**. Chạy truy vấn đã chuẩn bị.

**Thao tác:** chỉ cột `id` và đối chiếu với ID trên web, rồi chỉ `name`, `price` và `is_available`.

**Lời đọc sau khi kết quả khớp:**

> Trong bảng dishes, ID và tên món khớp với giao diện. Giá đang lưu là 45000 và is_available bằng 1, tương ứng với còn món. Như vậy thao tác thêm trên website đã ghi được một bản ghi thực tế vào database.

Nếu không có dòng hoặc ID không khớp, kiểm tra database/tên món/ID trước; không đọc lời xác nhận này khi kết quả chưa khớp.

### 5D. Sửa cùng món trên website

**Mở:** tab 4. Bấm biểu tượng bút **Sửa món** đúng dòng vừa tạo.

**Thao tác:** đổi giá thành **50000**, đổi **Tình trạng phục vụ → Hết món**, bấm **Lưu thay đổi**. Tải lại, tìm lại đúng món và ID.

**Lời đọc sau khi kết quả khớp:**

> Em đã sửa cùng món thành 50.000 đồng và chuyển sang hết món. Sau khi tải lại, giao diện vẫn giữ giá và tình trạng mới. Em sẽ chạy lại truy vấn để xác nhận thay đổi đã được ghi xuống database.

### 5E. Chạy lại SQL và đối chiếu sau cập nhật

**Mở:** tab 5. Chạy lại **đúng truy vấn cũ**; không chỉ nhìn kết quả của lần chạy trước.

**Thao tác:** đối chiếu `id` không đổi; `price` bằng **50000**; `is_available` bằng **0**.

**Lời đọc sau khi kết quả khớp:**

> Kết quả SQL vẫn là cùng ID, nhưng giá đã đổi thành 50000 và is_available bằng 0, tương ứng với hết món. Phần demo này chứng minh được thao tác thêm, đọc lại và cập nhật dữ liệu từ máy khách thông qua ứng dụng xuống database.

Giữ món mới để thầy đối chiếu. Nếu thầy yêu cầu demo xóa, chỉ xóa đúng món demo mới sau khi đã lưu minh chứng; không dùng bản ghi ID 9 của bộ ảnh cũ.

## 6. Minh chứng và kiểm tra source — khoảng 40 giây

**Mở:** tab 6, [bộ ảnh đã lưu](../evidence/application/demo-2026-10-21.md). Nếu thời gian cho phép, mở run CI đã chuẩn bị.

**Lời đọc:**

> Ngoài demo trực tiếp, nhóm đã lưu bộ ảnh và log trong repository. Bộ ảnh trước đó đối chiếu cùng bản ghi ID 9: thêm giá 45.000 đồng, sửa thành 50.000 đồng và SELECT xác nhận dữ liệu trong database.
>
> Source cũng đã có run GitHub Actions thành công, gồm kiểm tra cú pháp PHP, 14 kiểm tra validation và 19 kiểm tra tích hợp HTTP với MySQL. Đây là kiểm tra source trên môi trường tạm; database của hosting là MariaDB và đã có minh chứng thao tác riêng. Workflow hiện tại chưa phải pipeline tự động triển khai của Week 2.

Không đọc kết quả CI thành bằng chứng đã tự deploy lên hosting.

## 7. Phần còn thiếu, kế hoạch tiếp theo và kết thúc — khoảng 55 giây

**Mở:** [checklist Week 1](WEEK1_CHECKLIST.md), chỉ các dòng **Tạm hoãn**, sau đó [kế hoạch chuyển môi trường](DEPLOYMENT_PLAN.md).

**Lời đọc:**

> Nhóm em xin báo rõ phần còn thiếu. Gói cp1 hiện tại không hỗ trợ Terminal; nhóm chưa có minh chứng tự cài Ubuntu, đăng nhập SSH bằng user thực tế hoặc cấu hình firewall của hệ điều hành. Vì vậy những mục này vẫn để chưa hoàn thành trong checklist.
>
> Tuần này nhóm sử dụng cp1 để trình bày phần app và database đã triển khai. Nhóm xin ý kiến thầy về việc bổ sung phần hạ tầng khi chuyển môi trường trong giai đoạn Week 2.
>
> Sang Week 2, nhóm sẽ xem xét VPS hoặc hosting khác thực sự đáp ứng Docker, Compose và deploy tự động. Nhóm dự kiến dùng tiếp repository và source hiện tại, sao lưu dữ liệu trước khi chuyển, rồi thực hiện containerization và pipeline build, test, deploy.
>
> Đó là phần báo cáo tiến độ và demo Week 1 của nhóm em. Em cảm ơn thầy và xin nhận câu hỏi, góp ý.

Đây là đề xuất bổ sung của nhóm, không đọc thành “thầy đã cho phép”.

## Câu hỏi có thể gặp — chỉ đọc câu tương ứng khi được hỏi

**Vì sao dùng hosting thay vì Ubuntu VM/VPS theo đề?**

> Nhóm tận dụng hosting sẵn có để triển khai app và kiểm chứng đọc, ghi database trước. Nhóm hiểu phần đó chưa thay thế yêu cầu tự cài và cấu hình server, nên đã ghi phần thiếu và xin ý kiến thầy về kế hoạch bổ sung khi đổi môi trường.

**MariaDB hay MySQL?**

> Hosting đang báo MariaDB 10.11.16. Code dùng PDO với driver MySQL để kết nối; luồng thêm, sửa và SELECT đã được đối chiếu trên hosting. Bộ CI tự động riêng đang dùng MySQL 8.0.

**Dữ liệu có bị mất khi tải lại trang không?**

> Dữ liệu đã được ghi vào database. Khi tải lại trang, PHP chạy truy vấn đọc lại bảng dishes. Em đã đối chiếu cùng ID giữa website và SQL để chứng minh, thay vì chỉ dựa vào thông báo lưu trên giao diện.

**App và database nằm ở đâu, DB dùng cổng nào?**

> App được upload trong tài khoản hosting và phục vụ qua đường dẫn public của dự án. Database là dịch vụ MariaDB phía hosting. Vị trí máy DB, cổng kết nối thực tế và đường dẫn dữ liệu cấp hệ điều hành chưa có minh chứng trong repo, nên em chưa khẳng định các giá trị đó. URL website HTTPS đang dùng cổng 443.

**Đã cấu hình firewall chưa?**

> Chưa có minh chứng nhóm tự cấu hình firewall hệ điều hành. Htaccess hiện chặn truy cập một số đường dẫn web riêng; phần này khác với firewall. SSH và firewall đang được giữ là công việc chưa hoàn thành.

**Có SSH Access thì sao lại chưa có SSH?**

> Ảnh hiện tại chỉ cho thấy màn quản lý SSH keys. Nhóm chưa có kết quả đăng nhập shell thành công; Terminal của gói cp1 không được hỗ trợ theo thông tin nhóm đã kiểm tra. Vì vậy nhóm không dùng ảnh menu đó để đánh dấu hoàn thành SSH.

**Source có dùng AI không?**

> Có hỗ trợ AI khi khởi tạo source và tài liệu, được khai báo trong repository. Cấu hình ứng dụng và database thật được thực hiện trên hosting; kết quả kiểm tra và minh chứng đọc, ghi dữ liệu được lưu riêng. Nhóm cần hiểu và giải thích được phần source đang sử dụng.

**Week 2 sẽ làm gì, có phải đổi ứng dụng không?**

> Nhóm tiếp tục cùng ứng dụng và repository, chọn môi trường phù hợp rồi đưa app cùng database vào Compose, bảo đảm dữ liệu được lưu bền vững và thiết lập pipeline tự động build, test, deploy từ commit mới.

## Nếu demo trực tiếp gặp lỗi mạng hoặc lỗi kết nối

**Thao tác:** chuyển sang tab 6, bộ ảnh ID 9 và log HTTP đã lưu. Không đọc các lời xác nhận của bước live khi chưa chạy thành công.

**Lời đọc:**

> Hiện kết nối demo đang gián đoạn, em xin chuyển sang bộ minh chứng đã lưu. Đây là bộ ảnh thao tác ngày 21/10/2026, đối chiếu cùng bản ghi ID 9: thêm giá 45.000 đồng, sửa thành 50.000 đồng và SELECT xác nhận dữ liệu trong database. Đây là minh chứng của lần thao tác đã ghi lại, không phải kết quả live vừa hoàn thành.

Nếu app hoặc DB vẫn có lỗi, ghi nhận đúng lỗi để kiểm tra sau; không khẳng định hệ thống đang hoạt động bình thường chỉ từ ảnh cũ.

## Nếu chỉ có khoảng 5 phút

Giữ phần 1, 2, 3, toàn bộ demo 5A–5E và phần 7. Rút phần 4 xuống một câu về vị trí source/README và giữ khai báo nguồn; phần 6 chỉ mở ảnh/run khi thầy yêu cầu. Ưu tiên chứng minh cùng ID đọc/ghi DB và báo rõ phần hạ tầng tạm hoãn.
