# COMI — môi trường phát triển mới

## Thành phần đã chuẩn bị

- `frontend/`: CMS Next.js với dữ liệu minh họa, chưa kết nối nghiệp vụ Laravel.
- `backend/`: Laravel API, Sanctum, CORS giới hạn origin, rate limit và PHPUnit.
- `.tools/bin/`: PHP 8.5.0 CLI và Composer 2.10.3 cài riêng, không sửa PATH toàn máy.
- Backend dùng SQLite riêng tại `backend/database/database.sqlite`; session/cache/queue dùng database, mail ghi log. Không kết nối hoặc import database PHP cũ.
- PHP có `pdo_mysql` để chuẩn bị chuyển sang MySQL, nhưng chưa cài MySQL server. SQLite chỉ là môi trường bootstrap; cần MySQL riêng và kiểm thử lại trước khi triển khai nghiệp vụ/nhập dữ liệu cũ.

## Chạy backend

Từ thư mục gốc project:

```bash
source modern/env.sh
cd modern/backend
composer dev
```

Backend: `http://127.0.0.1:8000`. `source modern/env.sh` chỉ áp dụng cho terminal hiện tại. Mở terminal mới cần chạy lại; không cần sửa `.bashrc`.

Các endpoint:

- `GET /up`: liveness Laravel.
- `GET /api/v1/health`: kiểm tra kết nối DB; trả 200 hoặc 503, không tiết lộ lỗi nội bộ.
- `GET /api/v1/me`: thông tin cơ bản của tài khoản đã đăng nhập.
- `POST /api/v1/auth/login`, `POST /api/v1/auth/logout`: đăng nhập / đăng xuất bằng session cookie.
- `GET /api/v1/auth/me`, `PUT /api/v1/auth/password`: hồ sơ, quyền và đổi mật khẩu.
- `GET/POST /api/v1/cms/users`, `PUT /api/v1/cms/users/{id}`: danh sách, tạo, cập nhật / khóa tài khoản.
- `GET/POST /api/v1/cms/roles`, `PUT /api/v1/cms/roles/{id}`: vai trò và danh mục quyền; chỉ quản trị hệ thống được thao tác.
- `GET /sanctum/csrf-cookie`: nền tảng CSRF cho SPA.

Next.js dùng cùng hostname `127.0.0.1` cho FE/BE và gửi `credentials: include`; chỉ origin FE cổng 3000/3001 được cấu hình. Request thay đổi dữ liệu lấy CSRF cookie và gửi `X-XSRF-TOKEN`. Nhóm API xác thực/CMS dùng middleware `web` một lần; không bật thêm `statefulApi()` để tránh khởi tạo session hai lần.

## Đăng nhập và phân quyền CMS

Mở `http://127.0.0.1:3001/login`. Tài khoản local ban đầu: `admin@comi.local`; mật khẩu ngẫu nhiên nằm trong `modern/.runtime/cms-admin-credentials.txt` (quyền file 600, không đưa vào Git). Bắt buộc đổi mật khẩu ở lần đăng nhập đầu; không dùng tài khoản local này cho production.

Vào **Tài khoản & phân quyền** để tạo vai trò tùy chỉnh, chọn quyền theo module, tạo tài khoản và gán vai trò. Chỉ quản trị hệ thống được quản lý tài khoản/quyền. Không có đăng ký công khai. Quên mật khẩu: liên hệ quản trị hệ thống đặt lại mật khẩu tạm thời qua màn hình chỉnh sửa tài khoản.

Mật khẩu phải có 8–72 ký tự gồm chữ hoa, chữ thường, số, ký tự đặc biệt. Khóa tài khoản hoặc đổi email/vai trò/mật khẩu sẽ thu hồi các phiên cũ. Không cho tự khóa hoặc hạ quyền quản trị của chính mình. Vai trò hệ thống được bảo vệ; quyền quản lý module phải kèm quyền xem. Quyền vai trò được đọc lại ở mỗi request backend; frontend làm mới phiên khi focus và mỗi phút.

Máy mới: sau migrate, chạy `php artisan cms:create-admin email@congty.vn --name="Quản trị viên"`. Lệnh tạo các vai trò mặc định và in mật khẩu ngẫu nhiên một lần, không ghi đè tài khoản đã có. Bảo quản đầu ra như thông tin mật; không đưa mật khẩu vào source.

Frontend đọc `NEXT_PUBLIC_API_URL` (mặc định `http://127.0.0.1:8000`). Cấu hình trong `.env.local` và khởi động lại Next.js khi cần đổi địa chỉ.

## Chạy frontend ở terminal khác

```bash
cd modern/frontend
npm run dev
```

Xem cổng được in trong terminal; hiện bản demo dùng `http://127.0.0.1:3001/admin`.

## Kiểm tra backend

```bash
source modern/env.sh
cd modern/backend
composer validate --strict
composer check-platform-reqs
php artisan test --compact
php artisan migrate:status
php artisan route:list --path=api
```

PHPUnit dùng SQLite `:memory:` độc lập. Không chạy `migrate:fresh` hoặc import SQL vào database cũ.

## Thiết lập lại từ source

`.tools`, `.env`, vendor và DB SQLite không đưa vào Git. Máy mới cần PHP/Composer tương thích với `composer.lock`, sau đó chạy `composer setup` trong backend. Script cài dependency, tạo `.env` khi thiếu, chỉ tạo key khi `APP_KEY` trống và migrate database được cấu hình; kiểm tra `.env` trước khi chạy. Backend không cần npm/Vite vì FE nằm riêng.

Runtime PHP tải từ Herd Lite do script php.new tham chiếu; Composer installer được xác minh SHA-384. Runtime PHP đang tải được là 8.5.0, không khẳng định là bản vá mới nhất và chỉ dùng phát triển local. Khi triển khai, dùng runtime được cập nhật bản vá, HTTPS, `APP_DEBUG=false`, secure cookie, secrets riêng và database MySQL cách ly đã nghiệm thu.

## Phần tiếp theo

Sản phẩm đã lưu dữ liệu thật trong Laravel: danh mục tối đa 4 cấp (PHP cũ bật 3 cấp), thương hiệu/kích thước, ảnh/album, mô tả/nội dung/thông số VI/EN, giá/mã, SEO/schema, nhân bản và thùng rác/khôi phục. Trình soạn thảo dùng Tiptap, không phải bản phân phối CKEditor. Chưa nhập sản phẩm cũ; đơn hàng/nội dung/khách hàng và dashboard vẫn là dữ liệu mẫu. Đánh giá khách hàng, flash sale và AI chưa được chuyển đổi.

API dưới `/api/v1`: `GET/POST /cms/products`, `GET/PUT/DELETE /cms/products/{product}`, `POST /cms/products/{id}/restore`, `POST /cms/products/{product}/duplicate`, `GET/POST /cms/product-categories`, `PUT/DELETE /cms/product-categories/{category}` và `POST /cms/product-media`. Quyền `products.view` cho đọc, `products.manage` cho thay đổi; API kiểm tra quyền độc lập với menu.

Ảnh được phục vụ công khai qua `GET /catalog/media/{media}`; không upload tài liệu mật. Nhận JPEG/PNG/WebP/GIF tối đa 8 MB và 5000×5000 px, reencode WebP tĩnh để loại metadata (GIF mất hoạt ảnh). File ảnh dùng chung được giữ khi gỡ ảnh/xóa mềm, chưa có tác vụ dọn file mồ côi. HTML được lọc allowlist; schema tùy chỉnh nhận JSON Product, không nhận thẻ script.

Sau cập nhật, chạy `source modern/env.sh` rồi khởi động lại backend để nạp `modern/php-conf.d/uploads.ini` (upload 8 MB, POST 12 MB, memory 256 MB). Production cần cấu hình giới hạn tương ứng ở PHP-FPM và reverse proxy. Tiếp tục tích hợp các module còn lại và xây dựng công cụ nhập dữ liệu cũ riêng, không import SQL đè database.
