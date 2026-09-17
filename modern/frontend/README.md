# COMI Studio — bản thiết kế CMS

Next.js frontend độc lập, không thay đổi CMS PHP hiện tại. Đăng nhập, đổi mật khẩu, tài khoản, vai trò và module sản phẩm đã kết nối Laravel. Các nghiệp vụ còn lại vẫn là demo; cần nghiệm thu trước production.

## Chạy cục bộ

```bash
cd modern/frontend
npm ci
npm run dev
```

Mở `/login` tại cổng được in trong terminal (môi trường hiện tại: `http://127.0.0.1:3001/login`). Backend cần chạy tại `http://127.0.0.1:8000`; có thể đổi bằng `NEXT_PUBLIC_API_URL` trong `.env.local`. Xem README thư mục `modern` để lấy tài khoản local hoặc tạo admin mới.

## Đã có

- Đăng nhập cookie/CSRF, đăng xuất, bắt buộc đổi mật khẩu tạm thời, chặn phiên hết hạn.
- Tạo nhiều tài khoản, tìm kiếm/phân trang, gán vai trò, khóa/mở khóa và đặt lại mật khẩu.
- Vai trò tùy chỉnh theo module; chỉ quản trị hệ thống quản lý tài khoản/quyền; kiểm tra quyền ở Laravel.
- Dashboard, điều hướng sản phẩm/đơn hàng/khách hàng/nội dung/cài đặt.
- Chế độ sáng, tối, theo hệ thống; lưu lựa chọn vào localStorage.
- Menu mobile, bảng cuộn ngang, focus bàn phím, dialog đóng bằng Escape.
- Sản phẩm: tìm kiếm, lọc, phân trang, thêm/sửa, nhân bản, thùng rác và khôi phục; kiểm tra quyền xem/quản lý ở API.
- Danh mục tối đa 4 cấp, thương hiệu, kích thước; mã, giá gốc/khuyến mãi, trạng thái và cờ hiển thị.
- Ảnh đại diện, album có alt/chú thích/thứ tự; mô tả, nội dung và thông số VI/EN bằng Tiptap (trình soạn thảo trực quan tương tự CKEditor, không phải CKEditor).
- SEO, canonical, noindex; schema Product tự động, JSON tùy chỉnh hoặc tắt.
- Tab SEO cho AI (VI/EN): tóm tắt trả lời nhanh, đối tượng, tình huống sử dụng, tên gọi khác, tối đa 20 FAQ và 10 nguồn HTTP(S); preview nội dung và cảnh báo noindex. API lưu `ai_seo`; schema tự động bổ sung `alternateName` tiếng Việt. Đây là nội dung biên tập, không có chức năng sinh bài bằng AI hoặc cam kết xếp hạng. Cần tích hợp nội dung hiển thị và JSON-LD vào trang sản phẩm công khai khi xây storefront; chỉ lưu trong CMS chưa tạo khả năng được lập chỉ mục. Khi render, escape các trường văn bản và serialize JSON-LD an toàn; không đưa trực tiếp vào HTML bằng `dangerouslySetInnerHTML`.
- Chi tiết đơn và preview nội dung minh họa, biểu đồ chuyển khoảng thời gian.

Module sản phẩm lưu dữ liệu thật ở Laravel; chưa nhập dữ liệu PHP cũ. Dashboard, khách hàng, đơn hàng và nội dung vẫn dùng dữ liệu mẫu. Theme lưu trong localStorage. Ảnh upload là tài nguyên công khai, không dùng để lưu tài liệu riêng tư; giới hạn 8 MB, 5000×5000 px, chuyển WebP tĩnh (GIF chỉ giữ một khung hình). Gỡ ảnh không xóa file để tránh hỏng nội dung hoặc bản sao dùng chung.

## Kiểm tra

```bash
npm run typecheck
npm run build
```

Kiểm tra thủ công: đổi theme, resize mobile, tìm tên/mã, lọc trạng thái, thêm/sửa rồi tải lại, thử mã trùng, danh mục 4 cấp, upload ảnh/album, soạn nội dung, SEO/schema, nhân bản, xóa/khôi phục và tài khoản chỉ có quyền xem.

## Bước tích hợp tiếp theo

Tiếp tục tích hợp đơn hàng/khách hàng/nội dung; thiết kế công cụ nhập dữ liệu PHP cũ riêng. Chưa triển khai nghiệp vụ đánh giá khách hàng, flash sale hoặc AI của hệ thống cũ. Trước triển khai cần HTTPS, cookie secure, tắt debug và nghiệm thu bảo mật với cấu hình production.
