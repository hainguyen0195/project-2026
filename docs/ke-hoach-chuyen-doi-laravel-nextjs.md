# Kế hoạch chuyển PHP thuần sang Laravel BE + Next.js FE

Ngày khảo sát: 15/09/2026. Trạng thái: đã dựng bản demo tương tác CMS Next.js tại `modern/frontend`, hỗ trợ sáng/tối/theo hệ thống; đã bootstrap Laravel tại `modern/backend` với Sanctum, CORS, API health, endpoint hồ sơ yêu cầu xác thực và SQLite riêng. Chưa kết nối nghiệp vụ/dữ liệu thật và chưa xác nhận tương đương chức năng với CMS cũ.

## 1. Mục tiêu và nguyên tắc

- Xây dựng ứng dụng riêng trong `modern/backend` và `modern/frontend`; giữ nguyên ứng dụng PHP hiện tại để đối chiếu và quay lui.
- Laravel xử lý nghiệp vụ và API; Next.js xử lý website khách hàng và giao diện quản trị. Laravel vẫn dùng PHP, Next.js dự kiến dùng TypeScript.
- Theo yêu cầu ngày 15/09/2026: thiết kế lại CMS/quản trị hiện đại, trực quan, hỗ trợ sáng/tối/theo hệ thống. Website khách hàng mặc định giữ giao diện; bảo toàn nội dung, URL và nghiệp vụ, không tự bổ sung cổng thanh toán hoặc thay đổi quy trình đơn hàng.
- Có file PHP không đồng nghĩa chức năng đang được bật: cần đối chiếu router, cấu hình type, menu, dữ liệu thực tế và chạy thử.
- Chỉ xác nhận “đầy đủ tính năng” sau khi hoàn thành ma trận nghiệp vụ và nghiệm thu từng luồng.

## 2. Ma trận phạm vi sơ bộ

| Nhóm | Bằng chứng trong mã nguồn | Hạng mục cần chuyển và kiểm chứng |
| --- | --- | --- |
| Trang chủ, bố cục | `sources/index.php`, `templates/`, `assets/` | Khối nội dung, responsive, menu, banner, footer |
| Sản phẩm | `sources/product.php`, `admin/sources/product.php` | Danh sách, chi tiết, phân trang, sắp xếp, quick view, sản phẩm liên quan/đã xem |
| Phân loại | `libraries/router.php`, bảng `table_product_*` | Danh mục 4 cấp, thương hiệu, tag, slug và type |
| Biến thể và khuyến mãi | `sources/product.php`, bảng `table_product_sale`, `table_product_flash_sale` | Màu/size, ảnh, giá thường/giá bán, thời gian flash sale |
| Tìm kiếm | `sources/search.php`, `api/load_ajax_product.php` | Tham số lọc, thứ tự, không có kết quả, phân trang |
| Giỏ hàng | `libraries/class/class.Cart.php`, `api/cart.php`, các API giỏ hàng | Thêm/sửa/xóa, biến thể, giỏ khách, tính lại giá phía BE |
| Đơn hàng | `sources/order.php`, `admin/sources/order.php` | Checkout, phí giao hàng, trạng thái, chi tiết, thông báo; xác minh phương thức thanh toán đang dùng |
| Địa chỉ và giao hàng | `api/district.php`, `api/ward.php`, `api/phivanchuyen.php` | Quan hệ địa giới và cách tính phí theo dữ liệu hiện có |
| Tài khoản khách | `sources/login.php`, `sources/user.php`, `sources/activated.php`, `sources/info_user.php` | Đăng nhập/đăng ký/kích hoạt/hồ sơ và quyền truy cập dữ liệu cá nhân |
| Quản trị và phân quyền | `admin/sources/user.php`, `admin/sources/phanquyen.php` | Tách tài khoản quản trị/khách, quyền theo hành động/module, nhật ký |
| Passkey/social login | `admin/api/passkey.php`, `libraries/passkey/`, `api/ajax_google.php`, `api/ajax_facebook.php` | Kiểm tra chức năng thực sự hoạt động, cấu hình domain/callback và phương án chuyển |
| CMS | `sources/news.php`, `sources/static.php`, `sources/project.php`, `sources/recruitment.php` | Tin tức, blog, dự án, tuyển dụng, chính sách, giới thiệu và type đang bật |
| Media | `sources/album.php`, `sources/video.php`, `admin/api/upload.php`, `libraries/router.php` | Album, video, upload, thư viện ảnh, thumbnail, watermark, đường dẫn cũ |
| Tương tác | `api/comment.php`, `api/ajax_rating.php`, `sources/contact.php`, `sources/nhanmail.php` | Bình luận/đánh giá, kiểm duyệt, liên hệ, newsletter, chống spam |
| SEO | `libraries/router.php`, `sitemap.php`, `admin/sources/seo*.php`, `admin/sources/redirect.php` | Metadata, canonical, sitemap, URL đa ngôn ngữ, redirect 301, mã trạng thái |
| Tiện ích quản trị | `admin/sources/import.php`, `admin/sources/export.php`, `admin/sources/history.php`, `admin/sources/trash.php` | Import/export, Word/Excel, lịch sử, thùng rác/khôi phục |
| Tích hợp khác | `admin/sources/gsc.php`, `admin/sources/pushOnesignal.php`, các template social/phone | Xác minh API bên thứ ba và tác vụ thực tế trước khi viết lại |
| Kho | `docs/ke-hoach-tinh-nang-quan-ly-kho-san-pham.md` | Hiện có tài liệu kế hoạch; chưa kết luận đã triển khai. Tách yêu cầu mới khỏi parity hiện tại |

Mỗi dòng cần bổ sung: bật/tắt, vai trò, thao tác, input/output, quy tắc lỗi, bảng dữ liệu, màn hình, test case, trạng thái BE/FE/import/nghiệm thu.

## 3. Kiến trúc dự kiến

```text
modern/
  backend/                 Laravel API và nghiệp vụ
  frontend/                Next.js website và /admin
  contracts/               Đặc tả OpenAPI
  infrastructure/          Cấu hình triển khai
```

- API có version `/api/v1`; nhóm public, customer và admin có kiểm soát quyền riêng.
- Chọn một BE có các module rõ ràng, chưa chia microservice.
- FE không truy cập DB trực tiếp. Giá, quyền, trạng thái đơn và kiểm tra hợp lệ thuộc BE.
- Dự kiến dùng session cookie HttpOnly và CSRF cho web first-party; kiểm chứng thiết kế origin/proxy trước khi cấu hình xác thực.
- Trang nội dung/sản phẩm cần HTML có nội dung và metadata từ server; giỏ hàng và biểu mẫu dùng tương tác phía client.
- DB mới tách khỏi production, có migration; queue cho tác vụ gửi mail/xử lý nền khi cần, storage riêng cho media.
- Chốt phiên bản framework khi scaffold, theo môi trường triển khai và tài liệu chính thức; lưu lockfile. Không xem dependency chưa cài là đã kiểm thử.

## 4. Chuyển dữ liệu và tương thích

Nguồn đã thấy: `libraries/database/database.sql`, header ghi xuất ngày 09/08/2026; có `admin/schema.txt`. Chưa xác nhận đây là schema/data đang chạy thực tế.

1. Nhận bản sao DB gần nhất và media, ưu tiên ẩn danh dữ liệu khách hàng; không đưa credentials vào tài liệu hoặc frontend.
2. So sánh schema thực với dump và truy vấn trong code. Kiểm kê bảng, engine, index, khóa, cột JSON/text, trường ngôn ngữ và Unix timestamp.
3. Thiết kế schema mới và bảng ánh xạ ID. Lưu ID nguồn cho import chạy lại không nhân đôi dữ liệu.
4. Bảo toàn quan hệ danh mục/biến thể/đơn hàng, giá lịch sử, slug, tên ảnh và nội dung HTML. Quy định rõ xử lý bản ghi thiếu quan hệ.
5. Chuyển trạng thái chuỗi như `hienthi`, `thungrac` thành trường/quy tắc rõ ràng mà không làm hiện nội dung đã ẩn.
6. Mã đăng nhập có MD5 ở `sources/login.php` và dạng ghép secret/salt trong `admin/sources/user.php`. Không thể chuyển hash thành mật khẩu gốc: cần xác minh các luồng đang dùng, rồi chọn reset mật khẩu hoặc bộ xác minh chuyển tiếp giới hạn thời gian và rehash sau đăng nhập thành công.
7. Một số bảng dump dùng MyISAM; không giả định transaction của schema mới áp dụng được cho bảng cũ. Thiết kế bảng giao dịch mới phù hợp và kiểm thử rollback.
8. Đối soát số bản ghi, tổng tiền đơn, liên kết media, bản ghi mồ côi; import thử trên DB cách ly. Không import trực tiếp lên production.

## 5. Thứ tự triển khai và điều kiện hoàn thành

### Giai đoạn 1 — Chốt baseline

- Chạy ứng dụng cũ trên DB sao chép; xác định menu/type thực sự bật, ghi lại ảnh giao diện và các luồng chính.
- Hoàn thiện ma trận ở mục 2 với dữ liệu mẫu và kết quả mong đợi.
- Đầu ra: phạm vi nghiệm thu, schema nguồn đã xác minh, quyết định giữ giao diện.

### Giai đoạn 2 — Nền tảng

- Scaffold Laravel và Next.js độc lập, cấu hình DB thử nghiệm, env mẫu không chứa secrets.
- Thiết lập API error/pagination, đăng nhập, CSRF, phân quyền, logging và test nền tảng.
- Đầu ra: cả BE/FE chạy được, health check, truy cập trái quyền bị từ chối.

### Giai đoạn 3 — Lát cắt sản phẩm hoàn chỉnh

- Import danh mục/sản phẩm/media thử nghiệm; API danh sách/chi tiết; FE và CRUD admin.
- Đối chiếu ảnh, giá, slug, ẩn/hiện, thùng rác, danh mục 4 cấp, biến thể và SEO.
- Đầu ra: người quản trị sửa dữ liệu và website hiển thị đúng; không dùng mock thay cho nghiệm thu.

### Giai đoạn 4 — Bán hàng và tài khoản

- Giỏ hàng, checkout, vận chuyển, flash sale, đơn hàng, thông báo và tài khoản.
- Kiểm thử gửi trùng checkout, giá bị sửa ở client, flash sale hết hạn, truy cập đơn người khác, cập nhật trạng thái không hợp lệ.
- Nếu kho được xác nhận trong phạm vi: kiểm thử mua đồng thời, không âm tồn và hoàn kho đúng một lần.

### Giai đoạn 5 — CMS và phần quản trị còn lại

- Chuyển nội dung, media, đánh giá, SEO, đa ngôn ngữ, import/export và các tích hợp đã xác minh.
- Đối chiếu từng quyền admin và thao tác bị từ chối; không chỉ kiểm tra màn hình tồn tại.

### Giai đoạn 6 — Nghiệm thu và chuyển hệ thống

- Chạy test API/integration/E2E, kiểm thử responsive, tải, dữ liệu và crawl URL cũ.
- Chạy staging bằng bản sao dữ liệu, người dùng xác nhận toàn bộ checklist.
- Backup trước cutover; có cửa sổ dừng ghi hoặc cơ chế đồng bộ đã kiểm thử để không mất đơn phát sinh.
- Import cuối, đối soát, chuyển traffic; lưu điểm rollback và cách xử lý dữ liệu phát sinh sau cutover. Không quay lui bằng cách phục hồi DB cũ mù quáng.

## 6. Môi trường khảo sát và thông tin còn thiếu

- Shell có Node.js `v22.22.2`; đã cài PHP 8.5.0 và Composer 2.10.3 riêng tại `modern/.tools/bin`. Dùng `source modern/env.sh` để đưa vào PATH của terminal hiện tại; chưa cài Docker/MySQL server.
- Thư mục hiện tại không được `git status` nhận diện là Git repository.
- Đã chạy bản demo Next.js và Laravel độc lập; Laravel có SQLite local, migration và 9 bài test qua kiểm tra. Chưa chạy website cũ hay kết nối DB cũ, chưa triển khai luồng login/logout/phân quyền và API nghiệp vụ.
- Cần xác nhận dump hiện có có phải bản đang dùng, giao diện giữ nguyên hay đổi, host triển khai và các tích hợp đang bật. Mặc định giữ giao diện và dùng DB/media sao chép, không tác động hệ thống thật.

## 7. Tài liệu framework đã tham khảo

- Laravel installation: https://laravel.com/docs/13.x/installation
- Next.js installation: https://nextjs.org/docs/app/getting-started/installation

Tài liệu này là kế hoạch chuyển đổi, không phải chứng nhận hoàn thành sản phẩm.
