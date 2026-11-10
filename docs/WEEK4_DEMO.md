# Kịch bản một người trình bày Week 4


Bản Word bàn giao riêng; trang Week 3 giữ nguyên.


Thuyết trình và demo Week 4

DevopQLYFood   Một người trình bày   Trình bày trực tiếp trên cPanel

Cách dùng: chỉ đọc phần NÓI và NÓI TIẾP. Đến LINK CHIẾU hoặc THAO TÁC thì dừng đọc để mở trang và bấm. Chỉ nói kết quả sau khi thấy thật. Bài chính khoảng 16 đến 20 phút, phần thử thách và hỏi đáp thêm 5 đến 10 phút theo yêu cầu của thầy.

## Chuẩn bị trước khi trình bày

Đăng nhập sẵn GitHub và tài khoản quản trị website. Mở các tab: README, kiến trúc cuối kỳ, trang Week 4, source week4.php, Actions, Dashboard, demo Week 3, health và version. Không chiếu màn hình nhập mật khẩu, GitHub Secrets hoặc config thật. Giữ file này ở điện thoại hoặc màn hình riêng.

Trang Week 3 và file trình bày Week 3 vẫn giữ nguyên. Week 4 dùng thêm trang week4.php. Bài backup và tạo lỗi vẫn thao tác trên SQLite thử riêng của trang demo cũ, không gây lỗi MariaDB chính.

## 1 Mở đầu và giới thiệu kết quả

LINK CHIẾU  Repository và hồ sơ cuối kỳ

https://github.com/nathyu-le/DevopQLYFood/

NÓI  Em chào thầy. Em đại diện nhóm gồm Lê Đình Nhật Huy, Nguyễn Phước Hải và Lê Quang Tùng trình bày Week 4 của đề tài quản lý thực đơn quán ăn, tên ứng dụng là Bếp Nhà. Trong tuần cuối, nhóm tích hợp ứng dụng, pipeline triển khai, giám sát, bảo mật và phục hồi thành một luồng demo thống nhất. Nhóm bổ sung hướng dẫn cài mới, báo cáo kỹ thuật, bản phát hành và playbook xử lý sự cố.

NÓI  Sau đây em trình bày kiến trúc và phân công, sau đó thay đổi một dòng source để chạy pipeline lên website. Tiếp theo em minh họa giám sát, backup và restore; cuối cùng là chẩn đoán một lỗi kết nối rồi xác nhận phục hồi.

## 2 Giải thích kiến trúc và phân công

LINK CHIẾU  Sơ đồ kiến trúc cuối kỳ

https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/FINAL_ARCHITECTURE.md

THAO TÁC  Chỉ sơ đồ trình duyệt → PHP cPanel → MariaDB; tiếp theo chỉ sơ đồ commit → kiểm tra → staging → FTPS → health và revision.

NÓI TIẾP  Người dùng truy cập website bằng HTTPS. PHP trên cPanel kiểm tra đăng nhập và dữ liệu, rồi dùng PDO để đọc hoặc ghi MariaDB. Dashboard và file log giúp quan sát vận hành. Một database SQLite riêng dành cho bài thử xóa dữ liệu và mất kết nối. Docker chạy ở staging tạm của GitHub Actions để kiểm thử; website cPanel chạy PHP trực tiếp.

NÓI  Theo phân công của nhóm, Huy phụ trách kiến trúc, ứng dụng, triển khai và tài liệu; Hải phụ trách kiểm tra chức năng, đối chiếu dữ liệu; Tùng phụ trách tổng hợp minh chứng và chuẩn bị demo. Một người trình bày xuyên suốt, các thành viên vẫn cần giải thích được phần được giao.

## 3 Thay đổi source ngay trong buổi demo

NÓI  Để chứng minh source mới đi đến website qua pipeline, em sẽ thay một dòng chữ riêng trên trang Week 4. Thay đổi này dễ quan sát, không đổi schema hoặc dữ liệu của ứng dụng. Em ghi nhận nội dung và revision hiện tại trước, rồi commit thay đổi.

LINK CHIẾU  Trang Week 4 trên website

https://khotrick.org/DevopQLYFood/public/week4.php

THAO TÁC  Đọc dòng “Mốc trình bày Week 4: lần 1” hoặc nội dung hiện tại. Ghi nhớ số lần đang hiển thị. Chỉ Revision ở cuối trang. Nếu đã tập và đang là lần 2, trong lần này đổi thành lần 3; mỗi buổi chọn số mới để dễ phân biệt.

LINK CHIẾU  Source chỉ dành cho trang Week 4

https://github.com/nathyu-le/DevopQLYFood/blob/main/public/week4.php

THAO TÁC  Trên GitHub bấm biểu tượng bút chì Edit this file. Dùng Ctrl+F tìm “Mốc trình bày Week 4:”. Chỉ đổi số lần hiện tại sang số tiếp theo trong dòng có id="release-note". Giữ nguyên dấu ngoặc, dấu nháy và cấu trúc PHP/HTML. Không sửa login.php hoặc demo.php của Week 3.

NÓI  Em chỉ thay nội dung hiển thị của trang cuối kỳ. Source đã được quản lý trong Git, vì vậy commit mới sẽ xác định chính xác thay đổi cần kiểm tra và triển khai. Không cần đăng nhập cPanel để chép file bằng tay trong bước cập nhật này.

THAO TÁC  Bấm Commit changes. Nhập thông điệp “Update Week 4 presentation marker”. Chọn commit trực tiếp lên main nếu giao diện cho phép theo cấu hình repo hiện tại, rồi xác nhận Commit changes. Nếu repo yêu cầu PR, thực hiện quy trình PR được cấp thay vì bỏ bảo vệ nhánh. Ghi 7 ký tự đầu SHA commit mới.

LINK CHIẾU  Pipeline triển khai cPanel

https://github.com/nathyu-le/DevopQLYFood/actions/workflows/container-pipeline.yml

THAO TÁC  Mở run vừa tạo có đúng thông điệp và SHA mới. Chỉ ba job container-checks, deploy-staging và deploy-cpanel. Không mở nhầm Source checks hoặc uptime monitor.

NÓI TIẾP  Job đầu build image và chạy kiểm tra. Job thứ hai tải đúng image đã qua kiểm tra sang runner mới, so image ID rồi thử các tình huống dữ liệu và vận hành. Khi hai job đều qua, job cuối gửi gói source cùng commit lên hosting bằng FTPS. Cấu hình và database live được giữ riêng.

NÓI  Pipeline có thể mất vài phút. Em sẽ chỉ xác nhận website cập nhật sau khi job deploy hoàn tất và revision khớp commit. Nếu có lỗi, em mở đúng bước lỗi để chẩn đoán, không sửa trạng thái hoặc bỏ kiểm tra cho run thành công.

Trong lúc chờ, có thể trình bày mục 5 và 6, rồi quay lại mục 4 để xác nhận. Nếu thầy muốn xem trọn pipeline trước, giải thích ba job và chờ kết quả, không bấm chạy nhiều run trùng nhau.

## 4 Xác nhận commit đã lên website

LINK CHIẾU  Run pipeline cuối kỳ đã kiểm tra

https://github.com/nathyu-le/DevopQLYFood/actions/runs/42861794294

THAO TÁC  Trong buổi demo phải dùng run của commit vừa tạo ở mục 3. Link trên là run bàn giao để tham khảo khi cần. Chờ cả ba job success. Mở deploy-cpanel, chỉ bước kiểm tra health và exact revision; tránh cuộn vào phần môi trường hoặc thông tin nhạy cảm.

NÓI TIẾP  Run đã qua kiểm tra và triển khai. Bước xác nhận còn lại là đối chiếu website thật. Em mở lại trang Week 4 và kiểm tra mã commit để tránh trường hợp pipeline thành công nhưng trình duyệt đang xem bản cũ hoặc nhầm môi trường.

LINK CHIẾU  Đối chiếu phiên bản trên hosting

https://khotrick.org/DevopQLYFood/public/version.php

THAO TÁC  So revision đầy đủ hoặc 7 ký tự đầu với SHA của run mới. Mở trang Week 4, tải lại Ctrl+R; dòng mốc phải đổi sang số vừa commit. Mở health chính bên dưới.

LINK CHIẾU  Health của MariaDB chính

https://khotrick.org/DevopQLYFood/public/health.php

CẦN THẤY  status là ok; version đúng commit mới; trang Week 4 có nội dung mới. Không chỉ dựa vào số thứ tự hoặc màu xanh của Actions.

NÓI TIẾP  Nội dung mới và mã revision đều khớp commit. Health chính vẫn tốt, nên em có thể xác nhận luồng source, commit, kiểm thử và triển khai đến website đã hoàn tất. Pipeline không thay database hoặc file cấu hình riêng của hosting trong lần cập nhật này.

## Nếu pipeline chưa thành công

THAO TÁC  Mở job đỏ → bước có dấu lỗi. Phân biệt build/test, kết nối FTPS, xác minh TLS và health sau deploy. Giữ nguyên dữ liệu. Nếu chỉ lỗi kết nối tạm và endpoint đã hoạt động lại, dùng Re-run failed jobs; nếu source sai thì sửa source bằng commit mới rồi để pipeline kiểm tra lại.

NÓI KHI CÓ LỖI  Hiện bước này chưa qua nên em chưa xác nhận triển khai thành công. Em sẽ dựa vào bước lỗi và thời điểm để xác định nguyên nhân, xử lý rồi đối chiếu lại health và revision. Bản đang phục vụ được ghi nhận theo revision thực tế, không suy ra từ commit mới nhất của repo.

## Điểm cần giải thích khi thầy hỏi

Vì sao không cần Docker trên cPanel: Docker dùng cho kiểm thử và staging tạm; hosting nhận source PHP. Vì sao biết đúng image: hai job so cùng image ID và staging chạy no-build. Vì sao không đưa DB vào gói source: cập nhật chương trình và thay đổi dữ liệu cần hai kế hoạch riêng, tránh ghi đè cấu hình hoặc mất dữ liệu.

## 5 Dashboard nhật ký và bảo mật

NÓI  Sau luồng triển khai, em chuyển sang vận hành. Mục tiêu là biết dịch vụ còn đọc được database hay không, quan sát tài nguyên theo thời gian và có nhật ký để điều tra khi xảy ra lỗi.

LINK CHIẾU  Dashboard Vận hành

https://khotrick.org/DevopQLYFood/public/operations.php

THAO TÁC  Chỉ DATABASE Hoạt động, CPU, RAM và ĐỘ TRỄ DB. Chờ khoảng 5 giây, chỉ thời điểm mẫu gần nhất thay đổi và biểu đồ. Đọc nhãn nguồn CPU/RAM dưới ô, không tự gọi là quota hosting.

NÓI TIẾP  Database đang hoạt động nghĩa là ứng dụng kết nối và đọc được bảng dishes. Độ trễ là thời gian probe thực tế. Khi mở trang, dashboard cập nhật mỗi năm giây. CPU và RAM có thể là số liệu hệ điều hành của máy chủ dùng chung, nên nhóm ghi rõ nguồn đo và không xem đó là tài nguyên riêng của tài khoản cPanel.

THAO TÁC  Cuộn đến Nhật ký ứng dụng. Nhập “http_request” vào ô Lọc sự kiện hoặc request ID, bấm Lọc. Chỉ thời gian, route, status và request ID của một dòng thật. Bấm Xóa lọc sau khi giải thích.

NÓI TIẾP  Nhật ký có thời gian và mã yêu cầu để đối chiếu một request với sự cố. Bộ lọc giúp tìm đúng sự kiện; nhóm không ghi mật khẩu, nội dung biểu mẫu hoặc thông báo kết nối chứa credentials. Cảnh báo hiện ở dashboard và log; chưa cấu hình gửi email hoặc Slack.

LINK CHIẾU  Checklist bảo mật và cổng

https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/SECURITY.md

THAO TÁC  Chỉ mục Secrets và HTTP, không mở cấu hình thật. Nếu cần minh họa chặn thư mục, mở link schema bên dưới; trình duyệt phải báo 403 hoặc 404, không hiển thị SQL.

LINK CHIẾU  Kiểm tra đường dẫn ngoài public

https://khotrick.org/DevopQLYFood/database/schema.sql

NÓI TIẾP  Các trang quản trị cần đăng nhập; thao tác ghi có mã CSRF để kiểm tra phiên. Cấu hình thật và backup không nằm trong gói cập nhật. Thư mục riêng phải bị chặn qua HTTP. Firewall của máy chủ shared hosting do nhà cung cấp quản lý; UFW trong bài thử Docker là bằng chứng cho runner thử, không phải firewall cPanel.

NÓI  Sau đây em dùng trang demo riêng đã có từ Week 3 để thử phục hồi dữ liệu. Việc này được tách khỏi database thực đơn chính để thao tác trực tiếp mà không ảnh hưởng dữ liệu đang sử dụng.

## 6 Backup xóa restore và đối chiếu

LINK CHIẾU  Demo trực tiếp được giữ nguyên từ Week 3

https://khotrick.org/DevopQLYFood/public/demo.php

NÓI  Trang này sử dụng SQLite thử riêng ngay trên hosting. Các nút thực hiện thao tác thật với database thử. Em tạo một bản ghi, sao lưu, xóa, rồi phục hồi để kiểm tra dữ liệu có trở lại đúng không.

THAO TÁC  Bấm 1 Tạo dữ liệu thử. Chỉ dòng ID 1, tên Cơm gà demo Week 3, giá 45.000 đ. Tiếp theo bấm 2 Backup. Chỉ thời gian backup, số lượng 1 bản ghi và SHA256.

NÓI TIẾP  Đây là dữ liệu trước khi xóa. Backup lưu một snapshot kèm thời điểm và mã toàn vẹn. Snapshot tồn tại chưa đủ kết luận có thể phục hồi, nên nhóm cần thực hiện tiếp thao tác xóa và đối chiếu dữ liệu sau restore.

THAO TÁC  Bấm Tải file backup để trình duyệt tải file. Chỉ thông báo tải xuống, không cần mở JSON. Sau đó bấm 3 Xóa dữ liệu thử, chỉ bảng trống và sự kiện xóa trong Nhật ký demo.

NÓI TIẾP  File vừa tải là backup của database demo, không phải bản dump MariaDB chính. Dữ liệu thử hiện đã được xóa thật, nhưng kết nối vẫn có thể tốt. Vì vậy health tốt không đồng nghĩa mọi bản ghi nghiệp vụ vẫn còn đầy đủ.

THAO TÁC  Bấm 4 Restore. Chỉ cùng ID 1, tên món, giá 45.000 đ và ô DỮ LIỆU SAU RESTORE hiện KHỚP BACKUP. Cuộn nhẹ để chỉ sự kiện Restore khớp backup rồi quay lại các ô trạng thái.

NÓI TIẾP  Restore đã khôi phục cùng dữ liệu. Chương trình kiểm tra định dạng, lượt demo, số bản ghi và SHA256 trước khi ghi; sau đó đọc lại và đối chiếu toàn bộ trường trong giao dịch. Nếu snapshot sai hoặc hỏng, thao tác bị từ chối. Kết quả khớp backup là bằng chứng dữ liệu đã được phục hồi đúng trong bài thử.

NÓI  Đối với MariaDB thực đơn chính, dashboard có chức năng tạo backup riêng và giữ mười bốn snapshot mới nhất. Restore MariaDB chính dùng CLI với database thử và xác nhận tên, không đưa thao tác ghi đè dữ liệu thật thành một nút demo trên web. Snapshot dữ liệu dishes cũng không thay thế sao lưu toàn bộ tài khoản cPanel.

NÓI  Tiếp theo, em giữ bản ghi vừa phục hồi và thử một lỗi kết nối để minh họa quy trình phát hiện, chẩn đoán và xác nhận dịch vụ hoạt động lại.

## 7 Thử thách lỗi kết nối và khắc phục

LINK CHIẾU  Trang demo và nhật ký trực tiếp

https://khotrick.org/DevopQLYFood/public/demo.php

NÓI  Để trình bày quy trình xử lý sự cố rõ ràng, nhóm chuẩn bị lỗi trên database thử độc lập. Em sẽ tạo lỗi, quan sát cảnh báo, đối chiếu health với log để xác định phạm vi và nguyên nhân, rồi phục hồi. Đây là bài luyện tập; trong phần thử thách em sẽ thực hiện tình huống thầy chọn trên môi trường được phép.

THAO TÁC  Bấm 5 Tạo lỗi kết nối. Chờ khoảng 5 đến 10 giây. Chỉ DATABASE DEMO Mất kết nối, HEALTH HTTP THỰC TẾ 503, cảnh báo PENDING rồi FIRING. Cuộn Nhật ký demo trực tiếp để chỉ sự kiện ngắt và đổi trạng thái.

NÓI TIẾP  Dịch vụ demo trả HTTP 503 thật. Sau hai mẫu lỗi, cảnh báo chuyển sang firing. Nhật ký cho thấy thời điểm ngắt kết nối xảy ra ngay trước cảnh báo. Bài thử đổi tên file SQLite nên ứng dụng không mở được đường dẫn database kỳ vọng. Đây là nguyên nhân của lỗi demo, không phải bằng chứng MariaDB hosting đã dừng.

LINK CHIẾU  Đối chiếu health website chính

https://khotrick.org/DevopQLYFood/public/health.php

THAO TÁC  Mở health chính, chỉ status ok. Quay lại tab demo. Bấm 6 Phục hồi kết nối, chờ 5 đến 10 giây; chỉ HTTP 200, DB Hoạt động, alert OK, dữ liệu đã restore vẫn còn và log phục hồi.

NÓI TIẾP  Website chính vẫn tốt, giúp giới hạn phạm vi sự cố vào database demo. Phục hồi trả file thử về đường dẫn đúng. Em xác nhận lại bằng HTTP 200, hai mẫu tốt đưa cảnh báo về OK và dữ liệu vẫn khớp backup. Như vậy việc khắc phục đã được kiểm tra ở cả dịch vụ và dữ liệu.

NÓI  Để phòng tránh, nhóm tách dữ liệu thử, giữ backup trước khi xóa, lưu nhật ký và đưa dịch vụ về hoạt động sau bài thử. Trang có cơ chế tự phục hồi sau ba phút nếu bỏ quên, nhưng khi trình bày em vẫn chủ động phục hồi và kiểm tra.

## Khi thầy chọn lỗi khác

NÓI  Dạ em sẽ xác định triệu chứng, phạm vi, thời điểm và thay đổi gần nhất trước. Sau đó em dùng health, logs và cấu hình tương ứng để kiểm chứng giả thuyết; sửa nguyên nhân rồi đọc lại health và dữ liệu. Em không tạo lỗi trên dịch vụ dùng chung của nhà cung cấp.

DB/config sai: kiểm tra đúng config local, extension và quyền DB, giữ secrets kín. Container dừng: chỉ trên Docker thử, xem compose ps/logs rồi up dịch vụ, giữ volume. FTPS lỗi: xác định TCP/TLS/auth; giữ verify TLS. Proxy: hiện không có proxy riêng do nhóm cấu hình; chỉ xử lý trong môi trường thử có proxy thực tế, không nói đã thử nếu chưa làm.

## 8 Bàn giao và kết thúc

LINK CHIẾU  Hồ sơ Week 4 và kết quả kiểm tra

https://github.com/nathyu-le/DevopQLYFood/tree/main/evidence/week4

LINK CHIẾU  Bản phát hành cuối kỳ

https://github.com/nathyu-le/DevopQLYFood/releases/tag/week4-final-v1.0.0

THAO TÁC  Chỉ release/tag, hướng dẫn cài mới, báo cáo kỹ thuật và playbook. Để trang demo kết thúc ở HTTP 200 và OK. Không cần mở ảnh hoặc artifact trong bài trình bày chính.

NÓI TIẾP  Nhóm bàn giao source có tag, sơ đồ kiến trúc cuối, hướng dẫn cài từ đầu và vận hành, báo cáo kỹ thuật, video dự phòng và playbook. Các tình huống đã ghi gồm database không khả dụng, mất dữ liệu thử, quyền database vượt nhu cầu, lỗi FTPS và lỗi đồng thời ở giao diện demo. Mỗi mục có triệu chứng, xác minh, nguyên nhân, khắc phục và phòng tránh.

NÓI  Qua buổi demo, nhóm đã minh họa chuỗi cập nhật từ commit đến website, theo dõi vận hành, phục hồi dữ liệu và xử lý một sự cố có đối chiếu. Các giới hạn còn lại là quyền hạ tầng shared hosting, chưa có gửi cảnh báo ngoài hệ thống và chưa có rollback nguyên tử toàn bộ release. Nhóm ghi rõ những điểm này trong báo cáo để tránh đánh đồng với phần đã kiểm chứng. Em xin kết thúc và sẵn sàng trả lời câu hỏi của thầy.

## Câu trả lời ngắn chỉ dùng khi được hỏi

## Vì sao dùng SQLite cho demo

Dạ ứng dụng chính vẫn dùng MariaDB. SQLite chỉ là database thử riêng để bấm xóa và tạo lỗi trực tiếp trên cPanel; không thay kiến trúc dữ liệu chính.

## Cảnh báo có gửi email không

Dạ hiện có banner, trạng thái và log. Chưa cấu hình email hoặc Slack; nhóm không trình bày như đã có notification bên ngoài.

## Vì sao hai mẫu lỗi mới cảnh báo

Dạ giúp tránh một lần lỗi thoáng qua làm bật cảnh báo ngay. Sau hai mẫu tốt mới về OK. Khi mở trang, chu kỳ lấy mẫu là năm giây.

## SHA256 có bảo mật backup không

Dạ mã này giúp kiểm tra dữ liệu có thay đổi; không mã hóa và không thay thế phân quyền hoặc giữ bản sao riêng.

## Thay source xong dữ liệu có mất không

Dạ gói deploy không chứa database/config/var. DB giữ riêng. Thay schema vẫn cần backup và kế hoạch migration, không tự được giải quyết bằng gói cập nhật.

## Nhóm đã tự cấu hình firewall cPanel chưa

Dạ firewall OS và quyền root của shared hosting do provider quản lý. Nhóm kiểm tra HTTP access của app và có UFW trên runner thử; hai phạm vi này được ghi riêng.

Video nộp 5 đến 10 phút là sản phẩm riêng, không phải toàn bộ thời lượng bảo vệ. Nếu dùng bản giọng máy bàn giao, giữ nhãn nguồn giọng và môi trường; nếu thầy yêu cầu thành viên tự thuyết minh, dùng kịch bản này để quay giọng thật, không nhận giọng máy là giọng của nhóm.
