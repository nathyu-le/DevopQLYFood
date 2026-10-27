# Kế hoạch tiếp tục trên cPanel

**Quyết định hiện tại ngày 28/10/2026: dùng gói cPanel mới đã mua, có Terminal theo xác nhận của người triển khai; không triển khai VPS.**

Website Week 1 đã chạy tại [thitruongmmo.com/DevopQLYFood/public/](https://thitruongmmo.com/DevopQLYFood/public/), có ảnh app/DB và thông tin hosting. cp1 là môi trường cũ của Week 1, khi đó không có Terminal. Gói mới có Terminal; ảnh mới cho thấy cả ba lệnh Docker đều trả `docker: command not found`. Chưa xác minh khả năng cấp quyền Docker/daemon, tên gói hoặc thông số mới. Các phương án VM/VPS trong tài liệu hướng dẫn cũ chỉ là tham chiếu, không phải kế hoạch đang thực hiện.

## Week 2 đang làm

| Phần | Môi trường / cách thực hiện | Trạng thái |
|---|---|---|
| Build app image | Docker trên runner GitHub Actions | Có cấu hình và run, xem evidence |
| App + MariaDB/network/volume | Compose trên runner, test CRUD và recreate container | Có kiểm tra thực tế; chưa xác minh chạy container trên gói mới |
| Kiểm tra lỗi chặn deploy | Test job là dependency của deploy; regression trên source copy | Đã có kiểm tra; còn demo run fail đầy đủ |
| Đóng gói source | ZIP của đúng commit đã qua kiểm tra | Không chứa config thật, DB, secrets hoặc .htaccess |
| Deploy tự động lên cPanel | FTPS nếu nhà cung cấp bật và tài khoản được cấu hình | Chưa kết nối; chưa có run deploy thành công |
| Website cPanel | PHP/MariaDB chạy trực tiếp | Giữ bản hiện tại, không tự chuyển DNS/DB |

Đã nhận output Terminal với `docker: command not found` cho cả ba lệnh. Thực hiện tiếp: kiểm tra **FTP Accounts** và explicit FTPS, xác định đúng thư mục đồ án, cấu hình GitHub Secrets/Variables rồi kiểm tra kết nối. Tiếp tục deploy source trên cPanel; chưa có bằng chứng Docker chạy trên hosting và chưa bật deploy khi chưa cấu hình kết nối. [Hướng dẫn CI/CD](CICD_SETUP.md).

## Dữ liệu và cấu hình cần giữ

Trước lần deploy đầu, sao lưu source đang chạy và export DB từ phpMyAdmin ra nơi riêng. Giữ nguyên `config/config.local.php`, `.htaccess` và DB đang dùng; bộ deploy cPanel không upload hay xóa các mục này. Không đưa mật khẩu/private key/config thật/dump DB vào Git hoặc ảnh/log công khai.

Compose có DB và volume riêng trên runner/máy chạy Docker. Nó không chứa sẵn dữ liệu cPanel và không tự chuyển dữ liệu hosting sang container. Không chạy lệnh xóa volume với dữ liệu cần giữ.

## Phần yêu cầu còn thiếu

- OS/user/SSH/firewall của Week 1 vẫn tạm hoãn/chưa có minh chứng; không coi quản lý SSH keys hay .htaccess là bằng chứng đã cấu hình firewall hệ điều hành.
- Chưa xác nhận gói cPanel mới hỗ trợ Docker/Compose và quyền daemon; hiện chỉ dùng Docker trên CI. Nếu đề yêu cầu container trên server triển khai, phần đó chưa đạt bằng cách deploy PHP trực tiếp lên cPanel.
- Chưa có run FTPS thành công hoặc demo source thay đổi tự động trên website.
- Cách triển khai hiện tại chưa được giảng viên xác nhận thay tiêu chí. Giữ các giới hạn này trong [checklist Week 2](WEEK2_CHECKLIST.md).

## Tài liệu và minh chứng

[Chạy Docker](DOCKER_SETUP.md) · [CI/CD cPanel](CICD_SETUP.md) · [Minh chứng Week 2](../evidence/week2/README.md) · [Minh chứng Week 1](../evidence/week1/README.md). Dùng chung repo và cùng ứng dụng cho cả bốn tuần. Chỉ cập nhật hoàn tất khi có kết quả thật.
