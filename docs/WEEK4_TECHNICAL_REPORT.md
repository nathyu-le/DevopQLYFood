# Báo cáo kỹ thuật DevopQLYFood


Bản Word 8 trang bàn giao riêng.


Báo cáo kỹ thuật DevopQLYFood

Final Demonstration and Troubleshooting Challenge   Week 4   Tháng 11 năm 2026

Lê Đình Nhật Huy · Nguyễn Phước Hải · Lê Quang Tùng

## Abstract

Đồ án xây dựng quy trình triển khai và vận hành cho Bếp Nhà, ứng dụng quản lý thực đơn dùng PHP và MariaDB. Môi trường live là shared hosting cPanel; kiểm thử tích hợp và staging sử dụng Docker trên GitHub Actions. Thiết kế tách source khỏi cấu hình và dữ liệu, gắn revision vào bản triển khai và dùng các bước kiểm tra trước khi cập nhật hosting. Week 4 tích hợp đầu ra các tuần, bổ sung hướng dẫn tái tạo, hồ sơ phát hành và bài thực nghiệm xử lý sự cố.

Phương pháp đánh giá kết hợp kiểm thử HTTP, truy vấn database, tạo lại container giữ volume, sao lưu và phục hồi có đối chiếu, gây lỗi database trên môi trường thử và kiểm tra quyền truy cập. Trang demo cPanel dùng SQLite riêng để thực hiện tương tác mà không sửa MariaDB chính. Các kết quả được gắn với run và commit cụ thể; không suy từ bằng chứng staging sang cấu hình hạ tầng live. Hệ thống có giới hạn ở quyền shared hosting, sampling theo request, chưa có thông báo ngoài dashboard và chưa có chuyển release nguyên tử.

## Phạm vi hệ thống và sản phẩm

Chức năng nghiệp vụ gồm đăng nhập quản trị, thêm, xem, tìm kiếm, lọc, sửa, xóa và đổi tình trạng phục vụ của món. Hệ thống chưa triển khai đặt món, thanh toán hoặc giao hàng. Trọng tâm đồ án là chứng minh source được kiểm tra và đưa đến môi trường phục vụ, đồng thời có phương án quan sát và phục hồi khi lỗi.

Website: https://khotrick.org/DevopQLYFood/public/  Repository: https://github.com/nathyu-le/DevopQLYFood/

Source đã kiểm chứng: d46042c2f88d41f2422fcf6aee06546a498adf66. Tag week4-final-v1.0.1 có thể chứa thêm commit chỉ sửa hồ sơ sau source này; version.php phải khớp source runtime, không suy từ SHA mới nhất của main. Kết quả và lần gián đoạn uptime được ghi tại evidence/week4/audit-2026-11-11.md.

## Tổ chức báo cáo

Báo cáo trình bày mục tiêu và yêu cầu; kiến trúc; hiện thực triển khai; giám sát và bảo mật; thực nghiệm; phân tích sự cố; kết luận và tài liệu tham khảo. README và các hướng dẫn trong repo là tài liệu thao tác đi kèm, không thay cho bằng chứng chạy thực tế.

## 1 Introduction và yêu cầu

Bài toán vận hành xuất hiện khi nguồn chương trình, cấu hình của hosting và dữ liệu nghiệp vụ có vòng đời khác nhau. Chép toàn bộ thư mục mỗi lần sửa dễ ghi đè cấu hình; chỉ kiểm tra giao diện không phát hiện sai quyền hoặc mất dữ liệu khi tạo lại dịch vụ. Đồ án dùng quy trình kiểm tra trước deploy và đối chiếu sau deploy để giảm các sai lệch này.

Nhóm chọn tiếp tục cPanel đã có domain và hosting. Nhà cung cấp quản lý hạ tầng máy chủ; nhóm kiểm soát ứng dụng, database được cấp và luồng cập nhật. Docker được bổ sung để tạo môi trường thử tái lập được mà không yêu cầu Docker daemon trên hosting. Quyết định này giữ chi phí và công việc phù hợp phạm vi, đồng thời phải công bố rõ những tiêu chí hạ tầng chưa được kiểm chứng trên live.

| Yêu cầu Week 4 | Cách đáp ứng và xác minh |
|---|---|
| Tích hợp Week 1 đến 3 | Cùng source có app, CI/CD, giám sát, backup và demo riêng; kiểm tra lại bằng pipeline |
| Cài mới tái tạo được | FINAL_DEPLOYMENT.md, configure.php và preflight.php; clean Docker build trên runner |
| Commit đến website | Source push main, ba job có dependency, health và revision đúng SHA |
| Troubleshooting | Playbook ít nhất ba tình huống thực tế/thực nghiệm; demo DB riêng và xác nhận phục hồi |
| Báo cáo và bảo vệ | Báo cáo này, kịch bản một người, release/tag và video có ghi nguồn/môi trường |

Đề cho phép tình huống tương đương như database mất kết nối hoặc cấu hình sai và không yêu cầu gây sự cố hệ thống production thực tế. Vì vậy bài thử phá dữ liệu dùng fixture riêng. Việc giảng viên chọn lỗi và phần hỏi đáp là hoạt động tại buổi bảo vệ; tài liệu chuẩn bị không được ghi thành một đánh giá đã diễn ra.

Tiêu chí chấp nhận gồm: pipeline không triển khai khi bước cần thiết thất bại; source lên đúng revision; chức năng đọc ghi hoạt động; dữ liệu giữ qua recreate; snapshot khôi phục đúng toàn bộ trường; lỗi tạo health 503 và phục hồi 200; đường dẫn private không công khai. Không đặt mục tiêu SLA, tải đồng thời hoặc hiệu năng định lượng khi chưa có thí nghiệm tương ứng.

## 2 Architecture and Design

Luồng live bắt đầu từ trình duyệt qua HTTPS đến web server và PHP của cPanel. PHP kiểm tra session, xác thực dữ liệu, dùng prepared statements PDO truy vấn MariaDB rồi trả HTML hoặc JSON. Một bảng dishes chứa ID, tên, danh mục, giá, mô tả, tình trạng và timestamps. File cấu hình DB/admin ở config.local.php ngoài Git; app/public là phần source được cập nhật qua pipeline.

| Thành phần | Vai trò và ranh giới |
|---|---|
| cPanel live | Website PHP, MariaDB chính, private var; không chạy image Docker |
| Docker staging | App Apache/PHP 8.2, MariaDB 10.11, network và named volume riêng; runner tạm |
| Ops Monitor | Admin dashboard và API, mẫu tài nguyên/DB, alert và log allowlist |
| SQLite demo | Fixture độc lập, backup/xóa/restore, ngắt file và health riêng |
| GitHub Actions | Build/test, chuyển đúng image staging, đóng gói source, FTPS và post-deploy check |

Sơ đồ cuối trong docs/FINAL_ARCHITECTURE.md thể hiện riêng topology live và thứ tự triển khai. Dữ liệu MariaDB trên hosting không chuyển sang staging tự động. App staging chỉ bind loopback 8080; database không publish port 3306 ra host. Docker volumes giữ database và trạng thái qua recreate, nhưng stack CI được dọn sau khi lưu kết quả vì là dữ liệu thử.

Thiết kế demo tách biệt giúp trình bày trực tiếp trên cPanel: SQLite đặt trong var/classroom-demo, không dùng MariaDB chính. Snapshot có demo_id, định dạng riêng, số bản ghi và SHA256. Chỉ fixture theo schema được cho phép. Disconnect đổi tên file dưới khóa độc quyền; khi đường dẫn không có, health demo trả 503. Reconnect trả file lại và giữ dữ liệu. Hai mẫu tốt/lỗi điều khiển cảnh báo. Auto reconnect khi đã quá 180 giây cần request hoặc polling tiếp theo, không phải daemon chạy nền.

Đây là hai loại phục hồi khác nhau: MariaDB dishes backup bằng CLI hoặc nút dashboard, restore guarded vào DB thử; SQLite demo phục vụ bài trình bày có thể tải file và restore qua POST. Không dùng kết quả SQLite để khẳng định đã restore toàn bộ hosting hoặc user grants. Tách ranh giới này giúp giữ tính chính xác của minh chứng.

## 3 Implementation và triển khai

Ứng dụng dùng PHP thuần, CSS/JavaScript và PDO, không cần npm/Composer để chạy. validate_dish giới hạn tên, danh mục, giá nguyên, mô tả và trạng thái; output HTML được escape. Thao tác ghi có session quản trị và CSRF. Mật khẩu admin lưu dưới dạng hash. Exception handler trả lỗi tổng quát, chỉ ghi loại exception thay vì message có thể chứa kết nối.

Pipeline container-checks checkout source, sinh credentials dùng một lần, build image, lint PHP, validation và regression, chạy Compose, preflight và kiểm tra HTTP/DB/persistence. Gói runtime chỉ chứa PHP/CSS/JS app/public và REVISION; không chứa config thật, SQL, var, secrets hoặc .htaccess. Source artifact và Docker image cùng commit được lưu để chuyển đến bước sau.

deploy-staging chạy trên runner mới, tải image artifact và so image ID trước khi chạy no-build. Staging kiểm tra lại revision và CRUD/persistence, kiểm tra firewall riêng của runner, thử backup/restore MariaDB, stop/start DB, alert lifecycle và demo SQLite. Hai job đầu phải success trước deploy-cpanel. PR chỉ kiểm tra, không deploy. Thay đổi chỉ tài liệu có thể được paths-ignore.

FTPS deploy dùng TLS có xác minh chứng chỉ trên control và data channels. Khi địa chỉ kết nối là alias khác certificate, CPANEL_FTP_TLS_HOST xác định danh tính dịch vụ đã biết; không tự chọn hostname từ lỗi hay tắt verify. Tài khoản FTP giới hạn thư mục ứng dụng. Script upload file tạm rồi rename, shared library trước controller và REVISION cuối. Sau đó kiểm tra health, revision và HTTP security routes.

Cài mới có quy trình riêng: chọn PHP/extensions cho domain, upload toàn bộ source, bảo vệ layout, tạo database/user, import schema bằng quyền quản trị, gán quyền CRUD cho app, tạo config local rồi kiểm tra. tools/preflight.php chỉ đọc cấu hình và SELECT schema; không in secrets hoặc đổi dữ liệu. Nó xác nhận runtime/extensions, hash admin, DB và quyền private var. HTTP access vẫn phải kiểm tra riêng.

Rollback source được thực hiện bằng commit hoàn tác qua cùng pipeline và giữ dữ liệu. Hệ thống chưa chuyển toàn bộ release nguyên tử hoặc rollback database tự động. Nếu có migration, cần kế hoạch tương thích, sao lưu và kiểm tra riêng. Điểm này là giới hạn triển khai, không được che bằng việc sửa REVISION tay.

## 4 Monitoring Security and Reliability

Ops Monitor lấy CPU/RAM theo cgroup hoặc OS tùy môi trường, DB health và latency. Dashboard lưu tối đa 60 mẫu, cập nhật khi có request và theo chu kỳ năm giây khi mở trang. Trên shared host, nhãn nguồn đo công bố khả năng số liệu OS dùng chung. Không suy ra CPU/RAM riêng của tài khoản từ số liệu đó. Khi nguồn không có, trả null hoặc nguồn PHP allocation có nhãn thay vì bịa phần trăm.

Cảnh báo database dùng hai mẫu lỗi liên tiếp để firing và hai mẫu tốt để về ok; CPU/RAM ngưỡng 85 phần trăm. Banner, trạng thái và log là kênh hiện tại. Chưa có email/Slack. Uptime workflow probe health công khai theo lịch năm phút nhưng có thể trễ theo GitHub scheduler; không thay daemon giám sát hoặc cam kết SLA. Bộ thử chủ động kiểm tra cảnh báo database; không nhận đã stress CPU/RAM nếu chưa thực hiện.

Log JSONL có thời gian UTC, loại sự kiện, route, status, request ID và trường allowlist. Giao diện hiển thị thời gian Việt Nam, cho lọc sự kiện/request ID và thời điểm. Không log form, query string hoặc PDO message. Log được giới hạn dung lượng và xoay vòng; dữ liệu log/backup nằm trong private var, quyền directory 0700 và file 0600. Quyền hệ thống phải phù hợp user web, không dùng chmod 777.

| Kiểm soát | Phạm vi đã thiết kế hoặc kiểm tra |
|---|---|
| Session và CSRF | Trang/API quản trị cần login; mutation/download cần token; cookie và headers bảo vệ |
| DB app staging | Chỉ SELECT INSERT UPDATE DELETE; test DDL phải bị từ chối |
| Mạng staging | Loopback app, DB không publish, UFW runner riêng |
| cPanel HTTP | Assets cần 200; API 401 khi anonymous; private paths 403 hoặc 404 |
| Secrets | GitHub Secrets và config local; không trong runtime ZIP, report hoặc video |

Firewall OS/SSH và network policy database của cPanel do provider quản lý, chưa có quyền root hoặc bằng chứng cấu hình tương đương. Bật Terminal không chứng minh quyền này. Các kiểm tra HTTP ứng dụng không được gọi là kiểm tra firewall máy chủ; báo cáo giữ rõ giới hạn để người đánh giá đối chiếu yêu cầu hạ tầng còn thiếu của Week 1.

## 5 Experiments and Evidence

Thực nghiệm dùng DB riêng và fixture tổng hợp, không xóa dữ liệu người dùng thật. Bằng chứng được định danh bằng commit, run, môi trường và assertions thay vì chỉ ảnh giao diện. Run cuối kỳ liên kết ở trang đầu; lịch sử Week 2 và Week 3 được giữ đúng checkpoint, không sửa ngày hoặc nhận thành kết quả mới.

| Bài kiểm tra | Điều quan sát hoặc tiêu chí qua |
|---|---|
| CRUD HTTP và validation | Dữ liệu hợp lệ ghi được; invalid price và CSRF sai không ghi; output escape |
| Volume persistence | Recreate cả app/DB, so cùng ID/tên/giá/trạng thái với trước recreate |
| MariaDB restore staging | Backup, xóa fixture, từ chối snapshot hỏng/xác nhận sai, restore toàn rows và timestamps khớp |
| DB outage staging | Dừng DB thật, health 503 và alert firing; start DB, health 200 và alert ok |
| SQLite demo | Tạo 1 row, backup/download, xóa, restore; ngắt file gây 503 và reconnect 200; digest MariaDB không đổi |
| Client race | Click action vẫn POST khi GET đang chờ; GET cũ không ghi đè phản hồi mới |
| FTPS và release | Đúng revision, gói loại secrets; invalid TLS không gửi credentials; live routes đúng trạng thái |

Run 42946473380 có 19 container checks ở mỗi môi trường, 39 operations checks và 14 HTTP access checks trên cPanel. Hai kiểm tra mới xác minh retention giữ snapshot vừa tạo và metadata trỏ đúng file có thể restore, kể cả khi tên file cũ xếp sau. Không cộng các suite thành một chỉ số chất lượng chung. Admin được kiểm tra trong staging; phiên admin live hiện tại chưa được đăng nhập lại trong đợt rà soát này.

Video 400 giây là checkpoint b8953e1: đầu là login/revision công khai của cPanel, sau đó demo dữ liệu tổng hợp trên staging; có nhãn môi trường và giọng máy. Rà soát 11/11 giải mã toàn bộ file không lỗi, kiểm tra hình của 10 chương và tín hiệu audio. Chưa đánh giá phát âm bằng nghe trực tiếp; nhóm nghe duyệt trước khi nộp. Kịch bản chính thao tác live. Nếu thầy yêu cầu tự thuyết minh, cần quay giọng thật; không gán giọng máy cho thành viên.

Nhóm chưa đo tải đồng thời, RTO/RPO cam kết hoặc uptime dài hạn theo SLA. Khoảng chờ 5–10 giây trong kịch bản là hướng dẫn theo chu kỳ mẫu của demo, không phải số đo phục hồi production. Đánh giá cuối dựa trên bằng chứng trong run và kết quả quan sát tại buổi bảo vệ.

## 6 Troubleshooting và bài học

Playbook dùng cấu trúc thống nhất: triệu chứng, xác minh, nguyên nhân, khắc phục, xác nhận và phòng tránh. Mục tiêu là trình bày được chuỗi suy luận từ tín hiệu đến nguyên nhân, thay vì chỉ khởi động lại mọi thứ khi thấy lỗi. Các sự cố dưới đây là đã gặp hoặc thực nghiệm trong phạm vi ghi rõ.

| Sự cố | Nguyên nhân và khôi phục |
|---|---|
| DB staging ngừng | compose stop db gây lỗi thật; xem health/logs/ps, start DB không xóa volume, chờ hai mẫu tốt |
| Mất fixture | Xóa đúng món thử sau backup; kiểm tra snapshot và restore transaction, so toàn bộ rows trước commit |
| Quyền DB quá rộng | Tên database có dấu gạch dưới làm thu hồi quyền sai mẫu; revoke đúng grants, cấp CRUD và chạy lại test DDL |
| FTPS timeout/TLS mismatch | Tách địa chỉ dịch vụ kết nối khỏi danh tính TLS đã biết; giữ cert verification và kiểm tra revision sau deploy |
| DB demo không truy cập | File SQLite bị đổi tên bởi thao tác thử; reconnect trả file, 503 về 200 và giữ dữ liệu |
| Click demo mất tác dụng | Polling/action dùng chung busy flag; tách cờ và generation để bỏ phản hồi stale; regression test |

Bài thử trực tiếp trên cPanel đi từ dữ liệu đã restore sang ngắt kết nối demo, quan sát 503 và firing, đối chiếu health chính còn ok, sau đó reconnect và xác nhận dữ liệu/alert. Cách này minh họa cô lập phạm vi và phục hồi mà không dừng MariaDB dùng chung. Thầy có thể chọn tình huống khác; người trình bày phải giải thích công cụ và môi trường phù hợp, không nhận lỗi mẫu đã bao phủ mọi dạng sự cố.

Khi cấu hình thật sai, kiểm tra PHP/extensions, config local, prefix DB và quyền được cấp trước; tránh in credentials. Khi deploy lỗi, xác định bước TCP, TLS, auth hay post-deploy health. Với thay đổi source, commit sửa và chạy lại pipeline; với mất dữ liệu, giữ hiện trạng và chọn snapshot đúng. Không dùng tắt TLS, xóa volume hoặc sửa revision để làm trạng thái “xanh”.

Một bài học quan trọng là kiểm tra hành vi thực tế. Source có dòng GRANT CRUD chưa chứng minh DDL bị cấm; file backup tồn tại chưa chứng minh phục hồi đúng; run success chưa chứng minh trình duyệt xem đúng revision. Các assertion và bước đối chiếu cuối được chọn để kiểm tra trực tiếp những khác biệt này.

## 7 Conclusion và hướng phát triển

Đồ án tổ chức được ứng dụng thực đơn, CI/CD đến cPanel, Docker staging, dashboard/log/cảnh báo và thử nghiệm sao lưu phục hồi. Week 4 bổ sung hồ sơ và trang riêng. Giao diện, demo và tài liệu Week 3 được giữ; nhánh week3-preserved đóng mốc gốc. Đợt rà soát sửa riêng lỗi retention trong thư viện backup dùng chung, không thay quy trình trình bày Week 3.

Giới hạn còn lại gồm hạ tầng cPanel do provider quản lý; chưa xác minh firewall/root/SSH live theo tiêu chí hạ tầng ban đầu; chưa có gửi cảnh báo ngoài dashboard; lấy mẫu phụ thuộc request và scheduler; backup snapshot dishes chưa bao phủ toàn tài khoản; upload source chưa chuyển nguyên tử hoặc tự rollback DB. Độ bền và tính sẵn sàng production cần thêm vận hành dài hạn, off-site backup, diễn tập phục hồi và phép đo hiệu năng trước khi cam kết.

Ưu tiên tiếp theo là thu policy/ports từ provider, xác minh quyền DB live, cài lịch backup có theo dõi lỗi và bản sao ngoài hosting, thêm notification, cải thiện triển khai release theo thư mục và kế hoạch migration. Các cải tiến này được ghi là công việc tiếp theo, không tính vào kết quả đã chạy. Đóng góp cá nhân cần nhóm xác nhận bằng phần rà soát và thao tác thực sự, không suy từ tài khoản đứng tên commit tự động.

## References

1 Repository và source của đồ án
[https://github.com/nathyu-le/DevopQLYFood/](https://github.com/nathyu-le/DevopQLYFood/)

2 Run kiểm chứng cuối kỳ
[https://github.com/nathyu-le/DevopQLYFood/actions/runs/42946473380](https://github.com/nathyu-le/DevopQLYFood/actions/runs/42946473380)

3 Hướng dẫn cài mới và kiến trúc trong repo
[https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/FINAL_DEPLOYMENT.md](https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/FINAL_DEPLOYMENT.md)

4 GitHub Actions Deployment environments
[https://docs.github.com/en/actions/concepts/workflows-and-actions/deployment-environments](https://docs.github.com/en/actions/concepts/workflows-and-actions/deployment-environments)

5 Docker Compose Secrets
[https://docs.docker.com/compose/how-tos/use-secrets/](https://docs.docker.com/compose/how-tos/use-secrets/)

6 cPanel Manage My Databases
[https://docs.cpanel.net/cpanel/databases/manage-my-databases/](https://docs.cpanel.net/cpanel/databases/manage-my-databases/)

7 PHP PDO Transactions
[https://www.php.net/manual/en/pdo.transactions.php](https://www.php.net/manual/en/pdo.transactions.php)

8 Playbook và minh chứng theo môi trường
[https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/TROUBLESHOOTING.md](https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/TROUBLESHOOTING.md)

Tài liệu chính thức được đối chiếu trong tháng 11/2026. Mô tả hiện thực và kết quả dựa trên source cùng các run của đồ án; tài liệu nhà cung cấp dùng để giải thích cơ chế, không thay thế minh chứng triển khai của nhóm.
