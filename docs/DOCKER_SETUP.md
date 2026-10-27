# Week 2: chạy Bếp Nhà bằng Docker Compose

Dùng tiếp source PHP và bảng `dishes` của Week 1. Compose chạy hai service: `app` (Apache/PHP 8.2) và `db` (MariaDB 10.11). Cấu hình này tạo database riêng; không tự kết nối hay chuyển dữ liệu từ cPanel.

## Chạy trên máy có Docker

Cần Git, Python 3, Docker Engine hoặc Docker Desktop đang chạy và Docker Compose plugin hỗ trợ `up --wait` (khuyến nghị Compose 2.24 trở lên). Với Mac Apple Silicon, Docker build theo kiến trúc máy; CI/deploy hiện tạo image Linux amd64 trên runner Ubuntu. Docker trên Actions dùng amd64; cPanel nhận source PHP từ pipeline, không nhận image này.

```bash
git clone https://github.com/nathyu-le/DevopQLYFood.git
cd DevopQLYFood
python3 tools/setup-containers.py
docker compose config --quiet
docker compose up -d --build --wait --wait-timeout 180
docker compose ps
```

Mở `http://127.0.0.1:8080/`. Tài khoản mặc định là `admin`. Đọc mật khẩu một lần trên máy riêng:

```bash
cat .secrets/admin_password
```

Không chụp màn hình lệnh đọc mật khẩu khi ghi minh chứng. Script setup tạo mật khẩu ngẫu nhiên, không in ra log và không ghi đè file đã có. `.env` chỉ chứa cấu hình thông thường; `.secrets/` chứa ba mật khẩu, bị loại khỏi Git và build context. Entrypoint tạo cấu hình PHP cùng password hash bên trong container, ngoài DocumentRoot.

`APP_BIND=127.0.0.1` giới hạn truy cập từ máy chạy Docker. Cổng mặc định `APP_PORT=8080`, đổi trong `.env` nếu bị trùng. Website Week 1 dùng PHP/MariaDB trực tiếp. Gói cPanel mới có Terminal theo người triển khai; cần chạy kiểm tra trong [CI/CD setup](CICD_SETUP.md) để xác nhận Docker/Compose trước khi dùng cấu hình này trên hosting. Không yêu cầu VPS.

## File và cách hoạt động

| File/thành phần | Vai trò |
|---|---|
| `Dockerfile` | Build runtime Apache/PHP, cài `pdo_mysql` và `mbstring`, chỉ copy source cần chạy |
| `.dockerignore` | Chỉ cho runtime source và cấu hình Docker vào build context; loại credentials, Git, docs, evidence và tests |
| `docker/apache.conf` | DocumentRoot trỏ vào `public/`, không phục vụ `config/`, `database/` và `.secrets/` |
| `docker/entrypoint.php` | Đọc Docker secrets, tạo config PHP quyền 0640, hash mật khẩu quản trị |
| `docker-compose.yml` | App và DB trên network `menu`, chờ DB healthy, mount secrets và named volume |
| `db_data` | Lưu `/var/lib/mysql`, tồn tại khi container bị tạo lại |
| `database/schema.sql` | MariaDB import lần đầu khi volume còn trống; không tự chạy lại trên DB đã có |
| `docker/20-app-grants.sh` | Giới hạn user app ở quyền SELECT/INSERT/UPDATE/DELETE sau khi khởi tạo DB |
| `public/version.php` | Trả commit revision của image; image và gói source cPanel đều dùng revision; bản cPanel cũ chưa upload file này không tự cập nhật |

Không publish port 3306 lên host. App truy cập DB qua hostname `db` trong network Compose. Các thay đổi secrets hoặc tên DB sau lần khởi tạo **không tự cập nhật** user/database đang nằm trong volume; phải đổi credentials trong DB có kiểm soát và sao lưu trước.

## Kiểm tra và giữ dữ liệu

```bash
curl --fail http://127.0.0.1:8080/health.php
curl --fail http://127.0.0.1:8080/version.php
docker compose logs --tail=50 app db
```

Thêm món `Cơm gà Demo Week 2`, giá 45000; ghi ID. Sửa giá 50000 và tình trạng hết món. Tạo lại container:

```bash
docker compose up -d --no-build --force-recreate --wait --wait-timeout 180
```

Đăng nhập lại, tìm món và đối chiếu cùng ID/giá/trạng thái. Có thể dùng PDO trong container để SELECT, không cần mở port DB hoặc đưa mật khẩu vào lệnh:

```bash
docker compose exec -T app php -r '
$c=require "config/config.local.php"; $d=$c["db"];
$p=new PDO("mysql:host=".$d["host"].";dbname=".$d["name"].";charset=utf8mb4",$d["user"],$d["password"]);
$q=$p->prepare("SELECT id,name,price,is_available FROM dishes WHERE name=? ORDER BY id DESC LIMIT 1");
$q->execute(["Cơm gà Demo Week 2"]); echo json_encode($q->fetch(PDO::FETCH_ASSOC),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT),PHP_EOL;'
```

`docker compose down` giữ named volume. **Không dùng `down --volumes`, `down -v` hoặc `docker volume rm` với dữ liệu cần giữ.** Bước xóa volume trong workflow chỉ áp dụng stack CI tạm `devopqlyfood_ci`.

## Lỗi thường gặp

| Hiện tượng | Kiểm tra / xử lý |
|---|---|
| Không kết nối Docker daemon | Mở Docker Desktop hoặc khởi động Engine; chạy `docker info` |
| Port 8080 đã dùng | Đổi `APP_PORT`, rồi chạy lại Compose |
| DB/app unhealthy | Xem `docker compose logs --tail=100 app db`; kiểm tra secret files và tên DB/user, không xóa volume để chữa lỗi |
| Đổi mật khẩu file nhưng app không vào DB | User trong volume vẫn có mật khẩu cũ; cập nhật DB và secrets cùng nhau sau khi sao lưu |
| Image CI không chạy trên Mac M3 | Image CI là amd64; build tại Mac bằng Compose để có image arm64, hoặc chọn nền tảng amd64 có hỗ trợ giả lập |
| cPanel không có Docker/Terminal | Dùng Compose trong môi trường kiểm tra; deploy source lên cPanel theo [CI/CD](CICD_SETUP.md). Điều này chưa thay tiêu chí container chạy trên máy triển khai |

Tài liệu chính thức: [PHP Docker image](https://github.com/docker-library/docs/blob/master/php/README.md), [Compose dependency health](https://docs.docker.com/compose/how-tos/startup-order/), [Compose secrets](https://docs.docker.com/compose/how-tos/use-secrets/).
