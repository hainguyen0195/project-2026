# Kế hoạch: Quản lý nhập kho, xuất kho và tồn kho sản phẩm

## 1. Mục tiêu

Xây dựng tính năng quản lý kho cho sản phẩm, bảo đảm số lượng bán trên website luôn bám sát số lượng tồn thực tế và giúp admin nhận biết sớm sản phẩm đã hết hàng.

Kết quả cần đạt:

- Admin có thể nhập thêm, xuất bớt và điều chỉnh tồn kho.
- Hệ thống lưu được lịch sử từng lần thay đổi kho.
- Không cho khách thêm vào giỏ hoặc đặt hàng vượt số lượng tồn.
- Số lượng tồn được cập nhật đúng khi tạo đơn và khi đơn bị hủy hợp lệ.
- Sản phẩm tự chuyển sang trạng thái hết hàng khi tồn kho bằng `0`.
- Admin nhận được email khi sản phẩm chuyển từ còn hàng sang hết hàng.
- Không làm thay đổi hành vi bán hàng hiện tại đối với type sản phẩm chưa bật quản lý kho.

## 2. Phạm vi triển khai

### Giai đoạn đầu

- Áp dụng cho sản phẩm thường thuộc type `san-pham`.
- Quản lý một số lượng tồn tổng cho mỗi sản phẩm.
- Hỗ trợ nhập kho thủ công từ admin.
- Hỗ trợ xuất kho thủ công từ admin.
- Hỗ trợ xuất kho do bán hàng.
- Hỗ trợ hoàn kho khi đơn đã trừ kho nhưng bị hủy hợp lệ.
- Hỗ trợ cảnh báo email khi sản phẩm hết hàng.

### Chưa triển khai trong giai đoạn đầu

- Tồn kho riêng theo từng màu hoặc size.
- Quản lý nhiều kho, nhiều chi nhánh.
- Phiếu nhập/xuất có nhiều sản phẩm và trạng thái duyệt riêng.
- Đồng bộ với phần mềm kế toán hoặc đơn vị vận chuyển.

> Nếu sản phẩm có màu/size cần tồn riêng, phải mở rộng thiết kế thành tồn kho theo biến thể thay vì chỉ dùng một cột tồn trên `table_product`.

## 3. Cấu hình bật/tắt

Thêm cấu hình theo type sản phẩm, ví dụ:

```php
$config['product'][$nametype]['inventory'] = true;
```

Quy ước:

- `true`: hiển thị khối quản lý kho trong admin và áp dụng kiểm tra/trừ tồn khi bán.
- `false`: giữ hành vi hiện tại, không chặn giỏ hàng theo tồn và không tự trừ tồn.
- Giá trị mặc định nên là `false` trong lần phát hành đầu tiên để không làm sản phẩm cũ bị chuyển thành hết hàng ngoài ý muốn.

Có thể bổ sung thêm cấu hình:

```php
$config['product'][$nametype]['inventory_sale_timing'] = 'order';
$config['product'][$nametype]['inventory_alert'] = true;
$config['product'][$nametype]['inventory_alert_threshold'] = 0;
```

Trong đó:

- `inventory_sale_timing`: thời điểm trừ kho, dự kiến dùng `order` khi tạo đơn.
- `inventory_alert`: bật/tắt email cảnh báo hết hàng.
- `inventory_alert_threshold`: ngưỡng cảnh báo; giai đoạn đầu dùng `0` để chỉ cảnh báo khi hết hàng.

## 4. Thiết kế dữ liệu

### 4.1. Bổ sung cột cho `table_product`

Đề xuất thêm:

| Cột | Kiểu dữ liệu | Mặc định | Mục đích |
|---|---|---:|---|
| `stock_quantity` | `int unsigned` | `0` | Số lượng tồn hiện tại có thể bán |
| `stock_reserved` | `int unsigned` | `0` | Số lượng đang giữ cho đơn chưa hoàn tất, nếu dùng mô hình reserve |
| `stock_sold` | `int unsigned` | `0` | Tổng số lượng đã bán thành công, dùng cho báo cáo |
| `stock_status` | `varchar(30)` hoặc tính động | `in_stock` | Trạng thái hiển thị/cache; không dùng thay cho số lượng |
| `stock_alert_sent_at` | `int` nullable | `NULL` | Chống gửi lặp email khi sản phẩm vẫn đang hết hàng |

Khuyến nghị trạng thái bán được tính từ số lượng:

- `stock_quantity > 0`: `in_stock` / `Còn hàng`.
- `stock_quantity <= 0`: `out_of_stock` / `Hết hàng`.

Không nên cho admin chỉ sửa `stock_status` để tạo ra tình trạng “Còn hàng” khi số lượng thực tế bằng `0`.

### 4.2. Tạo bảng lịch sử kho

Tạo bảng, ví dụ `table_product_inventory`:

| Cột | Mục đích |
|---|---|
| `id` | Khóa chính |
| `id_product` | Sản phẩm bị thay đổi tồn |
| `type` | `import`, `export`, `sale`, `return`, `adjustment` |
| `quantity` | Số lượng thay đổi |
| `before_quantity` | Tồn trước thao tác |
| `after_quantity` | Tồn sau thao tác |
| `id_order` | Đơn hàng liên quan, có thể bằng `0` |
| `id_order_detail` | Chi tiết đơn liên quan, có thể bằng `0` |
| `note` | Ghi chú nhập/xuất/điều chỉnh |
| `id_admin` | Admin thực hiện thao tác, nếu có |
| `date_created` | Thời gian tạo bản ghi |
| `reference_code` | Mã thao tác để đối soát/idempotency |

Nên tạo index cho `id_product`, `id_order`, `type`, `date_created` và unique index cho `reference_code` nếu phù hợp.

### 4.3. Migration dữ liệu cũ

- Tạo migration SQL riêng, không chỉ sửa file dump.
- Xác định tồn ban đầu cho sản phẩm cũ trước khi bật tính năng.
- Phương án an toàn: thêm `inventory = false`, cho phép admin nhập tồn ban đầu, sau đó mới bật cấu hình.
- Không tự động coi toàn bộ sản phẩm cũ là `0` rồi bật bán theo tồn.
- Sao lưu database trước khi chạy migration.

### 4.4. Lưu ý về engine database

Schema hiện tại sử dụng `MyISAM` cho các bảng sản phẩm và đơn hàng. MyISAM không hỗ trợ transaction và khóa hàng như InnoDB. Trước khi triển khai trừ kho khi bán, cần chọn một trong hai phương án:

1. Chuyển các bảng liên quan sang `InnoDB` để dùng transaction và khóa an toàn.
2. Nếu chưa thể chuyển engine, dùng câu lệnh cập nhật nguyên tử kèm điều kiện tồn đủ, đồng thời bảo đảm mỗi đơn chỉ tạo một giao dịch kho bằng `reference_code` duy nhất.

Ưu tiên phương án 1 vì nhiều khách đặt cùng lúc có thể gây âm kho hoặc trừ kho hai lần nếu chỉ kiểm tra bằng PHP.

## 5. Khối quản lý kho trong admin

### 5.1. Form sản phẩm

Trong `admin/templates/product/man/man_add_tpl.php`, đặt khối “Quản lý kho” gần đầu form, trước phần nhập hình ảnh theo yêu cầu hiện tại.

Khối này chỉ hiển thị khi `$config['product'][$type]['inventory'] == true` và nên có:

- Tồn kho hiện tại, hiển thị rõ là số liệu hệ thống.
- Số lượng nhập thêm.
- Số lượng xuất bớt.
- Số lượng điều chỉnh trực tiếp.
- Loại thao tác.
- Ghi chú.
- Nút lưu thao tác.
- Link xem lịch sử kho của sản phẩm.

Không nên cho form sản phẩm âm thầm ghi đè tồn kho nếu admin không chọn rõ loại thao tác. Mọi thay đổi tồn phải tạo một bản ghi lịch sử.

### 5.2. Quy tắc nhập kho

- Chỉ nhận số nguyên dương.
- Nhập `0` không tạo giao dịch hoặc phải báo rõ không có thay đổi.
- Cộng số lượng vào `stock_quantity`.
- Ghi `before_quantity`, `after_quantity`, người thao tác và ghi chú.
- Nếu sản phẩm trước đó hết hàng và sau nhập có tồn, reset cờ cảnh báo để lần hết hàng tiếp theo có thể gửi email mới.

### 5.3. Quy tắc xuất kho thủ công

- Chỉ nhận số nguyên dương.
- Không cho xuất lớn hơn tồn hiện tại.
- Trừ tồn và ghi lịch sử với type `export`.
- Nếu tồn về `0`, chạy luồng cập nhật hết hàng và gửi cảnh báo theo cấu hình.

### 5.4. Quy tắc điều chỉnh tồn

- Khuyến nghị nhập “Tồn mới” thay vì nhập số chênh lệch để tránh nhầm dấu âm/dương.
- Tự tính chênh lệch và lưu type `adjustment`.
- Bắt buộc ghi lý do điều chỉnh để phục vụ đối soát.

### 5.5. Lịch sử kho

Thêm trang hoặc popup lịch sử với các bộ lọc:

- Khoảng thời gian.
- Loại giao dịch.
- Sản phẩm.
- Admin thực hiện.
- Mã đơn hàng.

Hiển thị tồn trước, số lượng thay đổi, tồn sau, ghi chú và thời gian. Không cho xóa lịch sử tùy tiện; nếu cần sửa phải tạo giao dịch điều chỉnh mới.

## 6. Luồng giỏ hàng và đặt hàng

### 6.1. Thêm vào giỏ

Tại `libraries/class/class.Cart.php` và API liên quan:

- Lấy tồn mới nhất từ database.
- Kiểm tra số lượng muốn thêm cộng với số lượng sản phẩm đã có trong giỏ.
- Không cho vượt `stock_quantity` khả dụng.
- Trả thông báo rõ số lượng còn lại.
- Không tin giá trị `quantity` hoặc `productid` do frontend gửi lên nếu chưa kiểm tra lại server.

### 6.2. Cập nhật số lượng giỏ hàng

Tại `api/cart.php`, `api/ajax_update_cart.php` và trang giỏ hàng:

- `min` và `max` trên giao diện chỉ để hỗ trợ người dùng.
- Server vẫn phải kiểm tra lại tồn.
- Nếu tồn đã giảm, tự hạ số lượng về mức hợp lệ và thông báo cho khách.
- Nếu sản phẩm đã hết hàng, không cho tiếp tục thanh toán.

### 6.3. Tạo đơn hàng

Tại `sources/order.php`:

1. Kiểm tra lại toàn bộ giỏ hàng ngay trước khi tạo đơn.
2. Kiểm tra tồn theo từng sản phẩm trên server.
3. Trừ tồn bằng thao tác nguyên tử hoặc transaction.
4. Chỉ tạo `order` và `order_detail` sau khi giữ/trừ tồn thành công.
5. Ghi một giao dịch `sale` cho từng sản phẩm.
6. Nếu bất kỳ sản phẩm nào không đủ tồn, hủy toàn bộ thao tác và báo khách cập nhật giỏ hàng.
7. Xóa giỏ hàng sau khi đơn và giao dịch kho được tạo thành công.

Ví dụ logic cập nhật nguyên tử cần hướng tới:

```sql
UPDATE table_product
SET stock_quantity = stock_quantity - :quantity,
    stock_sold = stock_sold + :quantity
WHERE id = :id_product
  AND stock_quantity >= :quantity;
```

Sau câu lệnh phải kiểm tra số dòng bị ảnh hưởng. Nếu bằng `0`, không được tiếp tục tạo đơn cho sản phẩm đó.

### 6.4. Tránh trừ kho hai lần

- Mỗi giao dịch bán hàng phải có `reference_code` duy nhất, ví dụ `order:{id_order}:product:{id_product}`.
- Khi retry request hoặc refresh trang, kiểm tra giao dịch đã tồn tại trước khi trừ lại.
- Không trừ kho trong cả `sources/order.php` và một hook khác nếu không có cơ chế idempotency.

## 7. Đồng bộ theo trạng thái đơn hàng

Trước khi code cần chốt chính sách thời điểm trừ kho. Đề xuất dùng chính sách sau:

- Đơn mới đặt (`1`): trừ tồn ngay khi tạo đơn vì đây là đơn bán hàng đã được ghi nhận.
- Đơn đã xác nhận (`2`) và đang giao (`3`): không trừ thêm.
- Đơn đã giao (`4`): giữ nguyên số đã bán.
- Đơn đã hủy (`5`): hoàn tồn đúng một lần nếu đơn trước đó đã trừ tồn.

Tùy quy trình kinh doanh, có thể thay bằng mô hình `stock_reserved`: tạo đơn chỉ giữ hàng, đến khi xác nhận mới chuyển từ `stock_reserved` sang `stock_sold`. Nếu chọn mô hình này, phải bổ sung kiểm tra và đối soát cho từng lần chuyển trạng thái.

Tại `admin/sources/order.php`:

- Khi lưu thay đổi `order_status`, lấy trạng thái cũ và trạng thái mới.
- Chỉ chạy nghiệp vụ kho khi có chuyển trạng thái hợp lệ.
- Không hoàn kho lần hai khi đơn đã ở trạng thái hủy.
- Không tự hoàn kho đơn đã giao nếu không có nghiệp vụ trả hàng riêng.
- Khi xóa đơn, không xóa trực tiếp nếu đơn đã trừ kho; phải yêu cầu hủy/hoàn kho trước hoặc chặn xóa.

## 8. Cập nhật trạng thái hết hàng trên website

Khi tồn thay đổi:

- Tồn `> 0`: hiển thị “Còn hàng”, cho chọn số lượng và cho thêm giỏ.
- Tồn `= 0`: hiển thị “Hết hàng”, vô hiệu hóa nút mua/thêm giỏ và bộ chọn số lượng.
- Tồn âm: coi là lỗi dữ liệu, không cho bán và ghi log để admin xử lý.

Cần cập nhật tại:

- Trang chi tiết sản phẩm.
- Danh sách sản phẩm.
- Sản phẩm nổi bật/bán chạy/flash sale nếu có.
- Giỏ hàng và bước xác nhận đơn.
- Dữ liệu structured data `Offer.availability`, dùng `OutOfStock` khi sản phẩm hết hàng.

Không nên phụ thuộc vào một giá trị status nhập tay trong admin; status hiển thị phải ưu tiên số tồn thực tế.

## 9. Email cảnh báo admin

### Điều kiện gửi

- Chỉ gửi khi sản phẩm chuyển từ `stock_quantity > 0` sang `stock_quantity = 0`.
- Không gửi lặp mỗi lần hệ thống cập nhật sản phẩm đang giữ nguyên trạng thái hết hàng.
- Khi nhập kho trở lại, reset cờ cảnh báo.
- Nếu sản phẩm lại về `0` ở lần sau, gửi email mới.
- Tôn trọng `$config['product'][$type]['inventory_alert']`.

### Nội dung email

- Tên sản phẩm.
- Mã sản phẩm.
- Tồn trước và tồn sau.
- Số lượng vừa xuất/bán.
- Mã đơn hàng nếu hết hàng do bán.
- Thời gian hết hàng.
- Link trực tiếp đến trang chỉnh sửa sản phẩm trong admin.

Tái sử dụng `libraries/class/class.Email.php` và cơ chế template hiện có. Tạo template riêng, ví dụ:

- `libraries/sample/mail/inventory/out_of_stock_vi.php`.
- `libraries/sample/mail/inventory/out_of_stock_en.php`.

Nếu gửi email lỗi, không rollback giao dịch kho chỉ vì email thất bại; cần ghi log để gửi lại hoặc xử lý thủ công.

## 10. Phân quyền và an toàn dữ liệu

- Chỉ tài khoản có quyền quản lý sản phẩm/kho mới được nhập, xuất hoặc điều chỉnh.
- Kiểm tra quyền ở server, không chỉ ẩn nút bằng JavaScript.
- Lưu `id_admin` trong mọi giao dịch thủ công.
- Escape/validate số lượng và ghi chú trước khi lưu.
- Dùng prepared statement hoặc API database hiện có với tham số bind.
- Không cho số lượng âm, số thập phân hoặc giá trị quá lớn.
- Ghi log lỗi khi phát hiện tồn âm, giao dịch trùng hoặc cập nhật không thành công.
- Sao lưu database trước khi chạy migration và trước khi bật kho trên dữ liệu thật.

## 11. Danh sách file dự kiến thay đổi

### Database

- `libraries/database/migrations/2026_xx_xx_add_product_inventory.sql`.
- `libraries/database/database.sql` nếu dự án yêu cầu cập nhật file dump mẫu.

### Cấu hình

- `libraries/type/config-type-product.php`.
- File cấu hình chung nếu cần danh sách email admin hoặc ngưỡng cảnh báo.

### Admin

- `admin/sources/product.php`: lưu nhập/xuất/điều chỉnh và lịch sử.
- `admin/templates/product/man/man_add_tpl.php`: khối quản lý kho đặt trước phần hình ảnh.
- Template danh sách sản phẩm: hiển thị tồn và trạng thái.
- `admin/sources/order.php`: xử lý hoàn kho khi hủy/chuyển trạng thái.
- Template lịch sử kho và giao diện lọc lịch sử.

### Frontend/API

- `libraries/class/class.Cart.php`: kiểm tra tồn khi thêm/cập nhật giỏ.
- `api/cart.php`.
- `api/ajax_update_cart.php`.
- `sources/order.php`: kiểm tra và trừ tồn khi tạo đơn.
- `templates/product/product_detail_overview.php`.
- Template danh sách sản phẩm và các block sản phẩm liên quan.
- `templates/layout/strucdata.php`: đồng bộ availability.

### Email

- `libraries/sample/mail/inventory/out_of_stock_vi.php`.
- `libraries/sample/mail/inventory/out_of_stock_en.php`.
- Có thể bổ sung helper trong `libraries/class/class.Email.php` nếu cơ chế template hiện tại chưa đủ.

## 12. Trình tự thực hiện

### Bước 1 - Chốt nghiệp vụ

- Chốt trừ kho lúc tạo đơn hay lúc xác nhận.
- Chốt cách xử lý đơn hủy, đơn đã giao và đơn bị xóa.
- Chốt sản phẩm màu/size dùng tồn tổng hay tồn theo biến thể.
- Chốt email người nhận và ngưỡng cảnh báo.

### Bước 2 - Migration và lớp nghiệp vụ

- Tạo cột tồn và bảng lịch sử.
- Chuẩn bị migration dữ liệu cũ.
- Viết hàm dùng chung cho nhập, xuất, hoàn kho, cập nhật trạng thái và gửi cảnh báo.
- Thêm cơ chế transaction hoặc atomic update phù hợp với engine database.

### Bước 3 - Admin

- Thêm cấu hình bật/tắt.
- Thêm khối quản lý kho trên form sản phẩm.
- Thêm lịch sử và phân quyền.
- Hiển thị tồn/trạng thái trong danh sách sản phẩm.

### Bước 4 - Giỏ hàng và bán hàng

- Kiểm tra tồn khi thêm/cập nhật giỏ.
- Kiểm tra lại ở bước tạo đơn.
- Trừ tồn và ghi lịch sử đúng một lần.
- Xử lý lỗi thiếu hàng và rollback.

### Bước 5 - Trạng thái đơn và email

- Đồng bộ hoàn kho khi hủy.
- Cập nhật trạng thái hết hàng ở tất cả giao diện.
- Tạo template email và chống gửi trùng.

### Bước 6 - Kiểm thử và phát hành

- Chạy migration trên môi trường test.
- Kiểm tra dữ liệu cũ.
- Kiểm thử đồng thời nhiều request.
- Bật `inventory` từng type/sản phẩm sau khi đã nhập tồn ban đầu.
- Theo dõi log và giao dịch kho sau khi phát hành.

## 13. Checklist kiểm thử bắt buộc

- Nhập `10` sản phẩm: tồn tăng đúng từ `x` lên `x + 10`.
- Xuất `3` sản phẩm: tồn giảm đúng và có lịch sử.
- Không cho xuất vượt tồn.
- Điều chỉnh tồn tạo đúng chênh lệch và ghi lý do.
- Không cho thêm vào giỏ vượt tồn.
- Không cho cập nhật giỏ vượt tồn.
- Request giả mạo số lượng lớn vẫn bị chặn ở server.
- Tạo đơn thành công trừ kho đúng một lần.
- Tạo đơn thiếu tồn không tạo đơn dở dang và không làm tồn âm.
- Hai khách đặt cùng lúc không làm tồn âm hoặc bán vượt số lượng.
- Hủy đơn đã trừ kho hoàn đúng một lần.
- Hủy lại đơn đã hủy không hoàn thêm.
- Đơn đã giao không tự hoàn kho khi chỉ đổi dữ liệu không hợp lệ.
- Tồn bằng `0` hiển thị “Hết hàng” ở trang chi tiết, danh sách, giỏ hàng và structured data.
- Nhập lại hàng chuyển về “Còn hàng” và có thể mua.
- Email chỉ gửi một lần cho mỗi lần chuyển sang hết hàng.
- Nhập hàng rồi hết hàng lần nữa có thể gửi email cảnh báo mới.
- Tắt `inventory` thì sản phẩm giữ hành vi cũ.
- Tắt `inventory_alert` thì không gửi email nhưng vẫn cập nhật tồn.
- Lịch sử hiển thị đúng admin, thời gian, loại giao dịch và mã đơn.

## 14. Tiêu chí hoàn thành

Tính năng được xem là hoàn thành khi:

- Migration chạy thành công trên database test và có backup trước khi chạy thật.
- Admin nhập/xuất/điều chỉnh được tồn và xem được lịch sử.
- Hệ thống không cho bán vượt tồn trong các API và request trực tiếp.
- Đơn hàng, hủy đơn và trạng thái hết hàng đồng bộ đúng với số tồn.
- Email hết hàng hoạt động, không gửi lặp và không ảnh hưởng đến việc lưu đơn.
- Các type chưa bật `inventory` không bị thay đổi hành vi.
- Hoàn tất checklist kiểm thử, kiểm tra log và đối soát tồn với một bộ dữ liệu mẫu.
