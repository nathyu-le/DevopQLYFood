# Kịch bản trình bày và live demo Week 4

Một người trình bày. Bố cục theo đúng bốn mục Nội dung trình bày và live demo của đề Week 4.

Bài trình bày gồm đúng bốn phần theo mục 3 của đề Week 4. Chỉ đọc lời ở nhãn NÓI và NÓI TIẾP. Đến LINK CHIẾU hoặc THAO TÁC thì dừng đọc để mở trang và bấm. CẦN THẤY là kết quả phải quan sát trước khi đọc tiếp.

| Phần theo đề | Thời lượng | Điều phải chứng minh |

|---|---|---|

| 1 Kiến trúc và trách nhiệm thành viên | 3 phút | Luồng hệ thống và phần việc của Huy Hải Tùng |

| 2 Demo cập nhật source code → pipeline → production app | 5–7 phút | Commit trong buổi và website thật có nội dung cùng revision mới |

| 3 Monitoring backup restore và security | 3–5 phút | Dashboard log dữ liệu sau restore và kiểm soát truy cập |

| 4 Live troubleshooting challenge và Q&A | 5–10 phút | Thầy chọn tình huống chẩn đoán nguyên nhân khắc phục và xác nhận |

### Chuẩn bị trước giờ báo cáo

Đăng nhập sẵn GitHub và website quản trị. Mở các tab: sơ đồ kiến trúc, trang Week 4, source week4.php, pipeline, Thực đơn, Dashboard và Demo trực tiếp. Các link cụ thể nằm ngay tại bước sử dụng trong bài.

Chuẩn bị tab sửa source: mở public/week4.php trên GitHub, bấm bút chì, tìm dòng id="release-note" và tăng số lần lên một. Giữ tab này, CHƯA commit. Đến phần 2 mới bấm Commit changes để thầy thấy cập nhật diễn ra trong buổi.

Trang demo phải ở HTTP 200 và cảnh báo OK trước khi bắt đầu; nếu đang ngắt kết nối thì bấm 6 Phục hồi kết nối và chờ hai mẫu tốt. Đến phần 3 mới bấm 1 Tạo dữ liệu thử. Không mở mật khẩu, GitHub Secrets hoặc cấu hình thật trên màn chiếu.

Thời lượng đề là khung dự kiến. Run kiểm chứng ngày 11/11 mất khoảng 6 phút 16 giây riêng pipeline; thời gian mới có thể khác. Tập trước thao tác commit và đối chiếu để giảm thời gian chuyển tab. Không dùng run cũ để nhận là lần cập nhật vừa thực hiện.

Week 4 tích hợp kết quả Week 1–3. Trang và file trình bày Week 3 được giữ riêng. Demo đang có nhãn Week 3 được dùng lại cho bài thử dữ liệu độc lập ngay trên cPanel.

## 1 Kiến trúc và trách nhiệm thành viên

Thời lượng theo đề 3 phút. Trong phần này giải thích hệ thống và phân công; chưa tạo sự cố.

**NÓI** Em chào thầy. Em đại diện nhóm gồm Lê Đình Nhật Huy, Nguyễn Phước Hải và Lê Quang Tùng trình bày Week 4 của đề tài quản lý thực đơn quán ăn, ứng dụng Bếp Nhà. Tuần này nhóm tích hợp phần ứng dụng, CI/CD, giám sát, bảo mật và backup restore từ ba tuần đầu thành bản triển khai cuối kỳ. Nhóm cũng hoàn thiện hướng dẫn triển khai, báo cáo kỹ thuật và playbook xử lý sự cố.

**LINK CHIẾU** [Sơ đồ kiến trúc cuối kỳ](https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/FINAL_ARCHITECTURE.md)

**THAO TÁC** Chỉ sơ đồ đầu: Trình duyệt → Web server và PHP trên cPanel → MariaDB thực đơn. Chỉ tiếp hai nhánh Private logs và backup, SQLite demo riêng. Sau đó chỉ sơ đồ commit → build test → staging → FTPS → health revision.

**NÓI TIẾP** Website thật chạy PHP và MariaDB trên cPanel, người dùng truy cập bằng HTTPS. PHP xử lý đăng nhập, kiểm tra dữ liệu và dùng PDO để đọc ghi thực đơn. Dashboard cùng nhật ký hỗ trợ theo dõi sức khỏe ứng dụng và điều tra lỗi. Database SQLite riêng phục vụ bài thử xóa dữ liệu và mất kết nối ngay trên hosting; dữ liệu thử này tách khỏi MariaDB thực đơn.

**NÓI** Khi source được commit lên main, GitHub Actions build và kiểm tra ứng dụng trong Docker. Staging tải đúng image đã kiểm tra để chạy các bài thử dữ liệu và phục hồi. Khi các bước đạt yêu cầu, pipeline gửi source cùng commit lên cPanel bằng FTPS, rồi đối chiếu health và revision. Docker phục vụ môi trường thử; website cPanel chạy PHP trực tiếp.

**NÓI** Theo phân công, Huy phụ trách kiến trúc, ứng dụng, triển khai và tài liệu. Hải phụ trách kiểm tra chức năng và đối chiếu dữ liệu. Tùng phụ trách tổng hợp minh chứng và chuẩn bị demo. Em trình bày xuyên suốt; các thành viên vẫn cần giải thích được phần phụ trách khi thầy hỏi.

**NÓI CHUYỂN PHẦN** Sau phần kiến trúc và phân công, em xin demo một cập nhật source đi qua pipeline rồi xuất hiện trên website thật.

**CẦN ĐẠT** Thầy biết website chạy ở đâu, Docker dùng ở đâu, hai database khác nhau và trách nhiệm từng thành viên. Chuyển sang phần 2 sau khoảng ba phút.

## 2 Demo cập nhật source code pipeline và production app

Thời lượng theo đề 5–7 phút. Thực hiện theo thứ tự source → commit mới → pipeline → website thật. Phần xác nhận website tiếp ở trang sau.

**LINK CHIẾU** [Trang Week 4 trước cập nhật](https://khotrick.org/DevopQLYFood/public/week4.php)

**NÓI** Đây là bản đang phục vụ trên cPanel. Em sẽ tăng số lần trong dòng mốc trình bày để có thay đổi dễ nhìn thấy. Em ghi nhận nội dung và revision trước khi commit, sau đó đối chiếu lại khi pipeline triển khai xong.

**THAO TÁC** Chỉ “Mốc trình bày Week 4: lần 2” và revision cuối trang. Nếu đã tập, đọc số thực tế đang hiện. Bản cập nhật sẽ dùng số kế tiếp, ví dụ lần 2 đổi thành lần 3.

**LINK CHIẾU** [Source public week4 php trên GitHub](https://github.com/nathyu-le/DevopQLYFood/blob/main/public/week4.php)

**THAO TÁC** Chuyển sang tab sửa source đã chuẩn bị. Chỉ dòng id="release-note" cùng số mới. Nếu chưa chuẩn bị, bấm bút chì Edit this file, Ctrl+F tìm “Mốc trình bày Week 4:” và tăng số lên một. Giữ nguyên cấu trúc PHP và HTML.

**NÓI** Thay đổi giao diện nhỏ này giúp đối chiếu chính xác nội dung trước và sau triển khai. Em sẽ commit lên main để pipeline tự kiểm tra rồi cập nhật hosting.

**THAO TÁC** Bấm Commit changes. Nhập “Update Week 4 presentation marker”, chọn commit lên main theo quyền hiện có và xác nhận. Ghi lại bảy ký tự đầu SHA mới. Nếu nhánh yêu cầu PR thì dùng quy trình PR; không bỏ bảo vệ nhánh.

**LINK CHIẾU** [Pipeline triển khai cPanel](https://github.com/nathyu-le/DevopQLYFood/actions/workflows/container-pipeline.yml)

**THAO TÁC** Mở run vừa xuất hiện, đối chiếu thông điệp commit và SHA. Chỉ ba job container-checks, deploy-staging và deploy-cpanel. Giữ trang run này để xem đến khi hoàn tất.

**NÓI TIẾP** Job container-checks build image và chạy kiểm tra cú pháp, dữ liệu và hành vi ứng dụng. Job deploy-staging dùng lại đúng image để kiểm tra CRUD, dữ liệu qua recreate, backup restore và phục hồi sự cố. Job deploy-cpanel chỉ chạy sau các job cần thiết đã thành công, gửi gói source bằng FTPS có xác minh TLS. Cấu hình và database của hosting được giữ riêng.

**TRONG LÚC CHỜ** Chỉ tiến độ từng job và giải thích luồng trên. Không đọc trước rằng deploy đã thành công. Run mới phải có đủ kết quả rồi mới chuyển sang bước xác nhận ở trang sau. Nếu chậm hơn khung giờ, dùng hướng dẫn xử lý chậm ở cuối tài liệu.

### Xác nhận bản mới trên production app

**THAO TÁC** Khi ba job của run mới success, mở deploy-cpanel và chỉ bước kiểm tra health cùng exact commit revision. Không dùng một run lịch sử thay cho run vừa tạo.

**NÓI** Pipeline của commit vừa tạo đã qua. Bây giờ em mở website thật để xác nhận nội dung và revision, vì màu xanh của pipeline cần được đối chiếu với bản đang phục vụ.

**LINK CHIẾU** [Phiên bản hiện tại trên cPanel](https://khotrick.org/DevopQLYFood/public/version.php)

**THAO TÁC** So revision trong JSON với SHA của run mới. Phải trùng commit đó; không lấy commit tài liệu mới nhất của main làm giá trị so sánh.

**LINK CHIẾU** [Trang Week 4 sau cập nhật](https://khotrick.org/DevopQLYFood/public/week4.php)

**THAO TÁC** Tải lại trang bằng Ctrl+R. Chỉ dòng mốc đã chuyển sang số vừa commit, ví dụ lần 3, và revision cuối trang.

**LINK CHIẾU** [Ứng dụng Thực đơn đang phục vụ](https://khotrick.org/DevopQLYFood/public/index.php)

**THAO TÁC** Mở trang Thực đơn. Chỉ ứng dụng chính tải thành công với dữ liệu hiện có và các chức năng quản lý. Không xóa món thật để minh họa ở bước này.

**LINK CHIẾU** [Health của ứng dụng và MariaDB chính](https://khotrick.org/DevopQLYFood/public/health.php)

**CẦN THẤY** Ba job của đúng commit mới success; version khớp SHA; mốc hiển thị đổi đúng số; trang Thực đơn mở được; health chính trả status ok.

**NÓI TIẾP** Website thật đã hiển thị nội dung mới và revision khớp commit. Trang Thực đơn vẫn truy cập được, health chính trả ok. Như vậy em đã chứng minh cập nhật source đi qua kiểm thử và pipeline để tới ứng dụng trên cPanel.

**NÓI CHUYỂN PHẦN** Tiếp theo em trình bày monitoring, backup restore và các kiểm soát bảo mật trong khoảng ba đến năm phút.

**NẾU BƯỚC NÀO CHƯA QUA** Dừng lời xác nhận thành công. Mở đúng job hoặc endpoint lỗi, nêu trạng thái thực tế rồi chẩn đoán theo phần 4. Không sửa REVISION tay hoặc bỏ test để làm run xanh.

## 3 Monitoring backup restore và security

Thời lượng theo đề 3–5 phút. Dự kiến một phút Dashboard và log, một đến hai phút backup restore, khoảng một phút bảo mật. Chưa bấm tạo lỗi kết nối ở phần này.

**LINK CHIẾU** [Dashboard vận hành và nhật ký](https://khotrick.org/DevopQLYFood/public/operations.php)

**NÓI** Dashboard theo dõi CPU, RAM, kết nối database và độ trễ. Khi mở trang, dữ liệu được cập nhật mỗi năm giây. Trên shared hosting, số liệu hệ điều hành có thể dùng chung nên nhóm hiển thị rõ nguồn đo.

**THAO TÁC** Chỉ DATABASE Hoạt động, CPU, RAM, nguồn đo và mẫu gần nhất. Cuộn tới Nhật ký ứng dụng; nhập http_request vào ô lọc, bấm Lọc, chỉ thời gian route status request ID của một dòng, rồi bấm Xóa lọc.

**NÓI TIẾP** Log có thời gian và mã yêu cầu để đối chiếu khi lỗi. Cảnh báo hiện trên dashboard và nhật ký; nhóm chưa cấu hình gửi email hoặc Slack.

**LINK CHIẾU** [Demo backup và restore với dữ liệu thử riêng](https://khotrick.org/DevopQLYFood/public/demo.php)

**NÓI** Em dùng lại demo đã có từ Week 3 để thử trực tiếp trên cPanel. Database SQLite này tách khỏi MariaDB thực đơn. Em tạo dữ liệu, sao lưu, xóa rồi phục hồi để xác nhận backup dùng được.

**THAO TÁC** Bấm 1 Tạo dữ liệu thử, chỉ ID 1, Cơm gà demo Week 3, giá 45.000 đ. Bấm 2 Backup, chỉ số lượng một bản ghi và SHA256. Bấm Tải file backup, chỉ thông báo tải xuống. Bấm 3 Xóa dữ liệu thử và chỉ bảng trống. Cuối cùng bấm 4 Restore.

**CẦN THẤY** Dữ liệu từ có một bản ghi → bảng trống → có lại cùng ID tên giá. Ô DỮ LIỆU SAU RESTORE hiện KHỚP BACKUP; HTTP 200 và cảnh báo OK. File tải về là backup SQLite demo.

**NÓI TIẾP** Dữ liệu đã phục hồi khớp snapshot. Chương trình kiểm tra định dạng và SHA256 trước khi ghi, rồi đọc lại để đối chiếu. MariaDB chính có cơ chế backup riêng; bài thử này không được dùng để nhận đã restore toàn bộ hosting.

**LINK CHIẾU** [Kiểm tra chặn đường dẫn ngoài public](https://khotrick.org/DevopQLYFood/database/schema.sql)

**THAO TÁC** Mở link schema; cần bị chặn 403 hoặc 404 và không hiện SQL. Không mở hoặc chiếu nội dung config thật.

**NÓI TIẾP** Trang quản trị yêu cầu đăng nhập, thao tác ghi có CSRF, cấu hình và backup riêng không đưa vào gói deploy. Firewall hệ điều hành của cPanel do nhà cung cấp quản lý; kiểm tra đường dẫn HTTP này chỉ xác minh phạm vi ứng dụng.

**NÓI CHUYỂN PHẦN** Em đã trình bày monitoring, backup restore và security. Sau đây em mời thầy chọn tình huống cho phần live troubleshooting và hỏi đáp.

## 4 Live troubleshooting challenge và hỏi đáp

Thời lượng theo đề 5–10 phút, điều chỉnh theo thầy. Thầy chọn hoặc chấp thuận tình huống trước khi nhóm gây lỗi. Các bước dưới là nhánh thao tác nếu thầy chọn mất kết nối database thử.

**NÓI** Dạ, đến phần thử thách xử lý sự cố, em mời thầy chọn tình huống để nhóm kiểm tra trực tiếp. Môi trường demo riêng trên cPanel đang hỗ trợ thử mất kết nối database và phục hồi dữ liệu. Nhóm sẽ xác định triệu chứng, đối chiếu bằng chứng, tìm nguyên nhân, khắc phục rồi xác nhận lại dịch vụ.

**THAO TÁC** Dừng và chờ thầy giao tình huống. Chỉ đi tiếp nhánh dưới nếu thầy chọn hoặc đồng ý mất kết nối database thử. Nếu thầy chọn tình huống khác, dùng hướng dẫn ở trang sau; không tự đổi tình huống của thầy.

**LINK CHIẾU** [Trang demo và nhật ký để chẩn đoán](https://khotrick.org/DevopQLYFood/public/demo.php)

**GÂY LỖI THEO TÌNH HUỐNG** Giữ bản ghi vừa restore. Bấm 5 Tạo lỗi kết nối; chờ khoảng 5–10 giây. Chỉ DATABASE DEMO Mất kết nối, HTTP thực tế 503 và cảnh báo PENDING rồi FIRING.

**NÓI** Triệu chứng là dịch vụ demo trả 503 và không truy cập được database thử. Em sẽ kiểm tra website chính để xác định phạm vi, rồi đối chiếu nhật ký theo thời điểm lỗi.

**LINK CHIẾU** [Đối chiếu health của website chính](https://khotrick.org/DevopQLYFood/public/health.php)

**THAO TÁC** Xem status ok nếu đúng thực tế. Quay lại demo, cuộn Nhật ký demo trực tiếp; chỉ “Đã ngắt kết nối DB demo” và sự kiện “Cảnh báo đổi trạng thái” cùng thời điểm.

**NÓI TIẾP** Health chính vẫn ok trong khi demo lỗi, nên phạm vi nằm ở database thử. Log ghi nhận mất kết nối ngay sau thao tác ngắt. Thao tác thử đã đổi tên file SQLite, khiến dịch vụ không mở được đường dẫn database kỳ vọng. Đây là nguyên nhân của tình huống này; em sẽ trả file về đường dẫn đúng để phục hồi.

**KHẮC PHỤC** Bấm 6 Phục hồi kết nối. Chờ hai mẫu tốt, khoảng 5–10 giây. Chỉ HTTP 200, DATABASE DEMO Hoạt động, cảnh báo OK, log “Đã phục hồi kết nối” và bản ghi đã restore còn nguyên.

**NÓI TIẾP** Sau khắc phục, health demo trở lại 200, cảnh báo về OK và dữ liệu vẫn còn đúng. Em xác nhận dịch vụ cùng dữ liệu đã phục hồi. Để phòng tránh và xử lý nhanh, nhóm tách dữ liệu thử, sao lưu trước khi xóa và giữ log theo thời gian. Em xin mời thầy đặt câu hỏi thêm.

**CẦN ĐẠT** Có tình huống được thầy chọn hoặc chấp thuận; chỉ được triệu chứng, phạm vi, log, nguyên nhân, cách sửa và bằng chứng phục hồi. Kết thúc ở HTTP 200 và OK. Nếu health chính cũng lỗi thì dừng kết luận phạm vi, xác minh sự cố thật trước.

### Khi thầy chọn tình huống khác trong phần 4

**NẾU THẦY CHỌN MẤT DỮ LIỆU THỬ** Mở demo.php. Nếu chưa có snapshot hợp lệ thì tạo dữ liệu thử và backup trước. Sau khi thầy giao tình huống, bấm 3 Xóa dữ liệu thử. Chỉ bảng trống và log xóa; đối chiếu snapshot một bản ghi. Bấm 4 Restore, chỉ cùng ID tên giá và KHỚP BACKUP. Giải thích nguyên nhân là bản ghi thử bị xóa, health có thể vẫn 200; phục hồi dữ liệu phải được kiểm chứng riêng.

**NẾU THẦY CHỌN CONTAINER BIẾN MÔI TRƯỜNG HOẶC PROXY** Nêu đúng phạm vi: cPanel không có Docker daemon hoặc proxy riêng do nhóm quản lý. Trao đổi với thầy về môi trường thử phù hợp hoặc tình huống tương đương được chấp thuận. Chỉ dùng lệnh hay sửa cấu hình trong môi trường có quyền và đã chuẩn bị; không nhận demo SQLite là đã xử lý lỗi container hoặc proxy.

**NÓI KHI CẦN XÁC ĐỊNH MÔI TRƯỜNG** Dạ website này chạy PHP trực tiếp trên shared hosting. Với lỗi thầy vừa chọn, em xin xác định môi trường nhóm được thao tác trước. Nếu thử trên cPanel, nhóm có thể thực hiện lỗi database độc lập; nếu cần container hoặc proxy thì cần môi trường thử tương ứng. Em sẽ trình bày đúng thao tác và kết quả thực tế trong phạm vi đó.

### Câu trả lời ngắn khi thầy hỏi

**VÌ SAO WEEK 4 DÙNG DEMO CÓ NHÃN WEEK 3** Dạ Week 4 yêu cầu tích hợp các tuần trước. Nhóm giữ nguyên thành phần Week 3 để dùng lại, bổ sung trang cập nhật và hồ sơ cuối kỳ riêng.

**VÌ SAO DEMO DÙNG SQLITE** Dạ ứng dụng chính dùng MariaDB. SQLite là database thử riêng để xóa và ngắt kết nối trực tiếp trên hosting mà không làm gián đoạn dữ liệu thực đơn.

**VÌ SAO CHƯA GỬI EMAIL CẢNH BÁO** Dạ hiện nhóm có banner, trạng thái cảnh báo và log. Email hoặc Slack chưa được cấu hình; nhóm ghi rõ phạm vi hiện có.

**NẾU PIPELINE CHẬM HOẶC THẤT BẠI** Dạ em chưa xác nhận deploy thành công. Em mở job đang chờ hoặc bước lỗi để xác minh. Nếu thầy đồng ý, em trình bày phần monitoring trong lúc chờ rồi quay lại đối chiếu đúng commit mới; không dùng run cũ thay cho kết quả lần này.

### Kết thúc phần 4 và bài trình bày

**LINK CHIẾU** [Hồ sơ và checklist cuối kỳ](https://github.com/nathyu-le/DevopQLYFood/blob/main/docs/WEEK4_CHECKLIST.md)

**THAO TÁC** Giữ demo ở HTTP 200 và OK. Khi thầy hỏi hồ sơ, mở checklist để chỉ báo cáo, deployment guide, playbook và release; không cần mở ảnh trong phần live demo.

**NÓI** Nhóm đã trình bày kiến trúc và trách nhiệm thành viên; demo cập nhật source qua pipeline đến website; minh họa monitoring, backup restore và security. Phần thử thách vừa rồi đã được thực hiện theo tình huống thầy giao và kết quả quan sát thực tế. Nhóm xin kết thúc và cảm ơn thầy.

Chỉ đọc câu xác nhận thử thách ở trên nếu đã thực hiện tình huống của thầy. Nếu chưa làm được hoặc chưa phục hồi, báo đúng bước còn vướng. Mọi thành viên vẫn cần sẵn sàng trả lời phần việc của mình.
