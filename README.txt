NHÀ VIỆT REALTY - WordPress Theme v5.4.0
=========================================

BẢN 5.4.0 - TRANG MỚI & NỘI DUNG DOANH NGHIỆP
- Trang "Về chúng tôi" (page-ve-chung-toi.php): câu chuyện, số liệu, sứ mệnh, dịch vụ, quy trình, đội ngũ, giá trị,
  thông tin doanh nghiệp, câu hỏi thường gặp, form tư vấn. Có dữ liệu có cấu trúc AboutPage + RealEstateAgent + FAQPage.
  Template dùng thẳng theo đường dẫn /ve-chung-toi/ nên không phụ thuộc nội dung cũ lưu trong database.
- Trang "Ký gửi bất động sản" (/ky-gui/): form cho chủ nhà (bán/cho thuê, loại, địa chỉ, diện tích, giá), lưu vào Khách hàng tiềm năng.
- Trang "Tính khoản vay mua nhà" (/tinh-vay/): trả đều/gốc đều, lãi suất ưu đãi giai đoạn đầu, lịch trả nợ theo năm.
  Trang chi tiết bất động sản bán có nút "Tính khoản vay cho căn này" tự điền giá (?price=...).
- Giờ làm việc hiện ở footer, trang Liên hệ, trang Về chúng tôi.
- Ảnh chia sẻ mặc định (Open Graph) cho trang chủ, trang con, trang danh sách; mô tả meta riêng cho 3 trang trên.
- Cài đặt > Nhà Việt Realty có thêm: giờ làm việc, năm thành lập, số liệu tự nhập, mốc phát triển, đội ngũ, nhận xét khách hàng, ảnh chia sẻ.
  Các ô nhiều dòng dùng dấu | để ngăn cột (có ví dụ ngay dưới mỗi ô). Chỉ nhập thông tin THẬT; để trống thì phần tương ứng tự ẩn
  (nhận xét, mốc phát triển) hoặc dùng nội dung chung không có tên người (đội ngũ).
- Sau khi cập nhật theme: vào Cài đặt > Nhà Việt Realty > "Tạo trang hệ thống & làm mới đường dẫn" nếu trang /ky-gui/ hoặc /tinh-vay/ báo 404.

CÀI ĐẶT (3 bước)
1. Giao diện > Giao diện > Thêm mới > Tải theme lên > chọn file .zip > Kích hoạt.
   Khi kích hoạt, theme TỰ ĐỘNG: đặt đường dẫn tĩnh /%postname%/ (nếu đang "Plain") và làm mới rewrite rules,
   tạo các trang hệ thống (Trang chủ, Tin tức, Liên hệ, Yêu thích, Cảm ơn, Chính sách bảo mật, Điều khoản sử dụng,
   Quy chế hoạt động), đặt Trang chủ/Trang bài viết, và nhập dữ liệu mẫu nếu website chưa có bất động sản nào.
2. Cài đặt > Nhà Việt Realty: nhập Hotline, Zalo, Facebook, email, Google Maps API Key, GA4 ID, Meta Pixel ID.
   Mục "Kiểm tra nhanh" ở đầu trang cho biết còn thiếu gì.
3. Cài plugin Rank Math SEO (xem mục SEO bên dưới).

DỮ LIỆU MẪU (Cài đặt > Nhà Việt Realty > Công cụ)
- 15 bất động sản (Bình Thạnh, TP. Thủ Đức/Quận 2, Nhà Bè, Quận 7, Quận 1), đủ diện tích/PN/WC/giá/địa chỉ, có tọa độ.
- 3 dự án nổi bật (Vinhomes Grand Park, Masteri Thảo Điền, Sunrise Riverside), 8 chủ đầu tư,
  4 khu dân cư (Jamona City, Hưng Gia, Sala Đại Quang Minh, Thủ Thiêm), 6 bài viết, nội dung các trang hệ thống.
- Chạy lại chỉ cập nhật, không tạo trùng. "Xóa dữ liệu mẫu" chuyển toàn bộ mục mẫu vào thùng rác.
- Đây là dữ liệu MINH HỌA. Ảnh mẫu lấy từ Unsplash (đổi sang ảnh của bạn: Media hoặc ô "Thư viện ảnh").
  Mô tả khu dân cư/chủ đầu tư/trang pháp lý là nội dung tham khảo - cần xác minh/luật sư rà soát trước khi chạy thật.

TÍNH NĂNG
- CPT: Bất động sản (Mua bán/Cho thuê qua trường "Hình thức"), Dự án, Chủ đầu tư, Khu dân cư, Khách hàng tiềm năng.
- Trạng thái BĐS: Đang bán / Đã bán / Đang cho thuê / Đã cho thuê -> badge trên card.
- Card BĐS: tên in đậm, địa chỉ chi tiết, "70 m² | 2 PN | 2 WC", giá cụ thể, nút tim góc phải trên.
  Đất nền tự ẩn PN/WC.
- Trang danh sách + Trang kết quả tìm kiếm: sidebar lọc bên trái (từ khóa, nhu cầu, loại, khu vực, giá, diện tích, PN,
  hướng, pháp lý, trạng thái, sắp xếp), lưới/danh sách/bản đồ. Không có kết quả -> gợi ý BĐS nổi bật.
- Bản đồ: nút "Bản đồ" ở trang danh sách (marker theo kết quả lọc); có API key = Google Maps, không có = OpenStreetMap
  (Leaflet đã đóng gói sẵn trong theme). Trang chi tiết: khối "Vị trí" nhúng iframe theo địa chỉ.
- Yêu thích (lưu trên trình duyệt) + trang /yeu-thich/. Chia sẻ, Zalo, Hotline.
- Sticky header thu gọn khi cuộn, thanh tìm kiếm thu gọn (trang chủ: tự hiện sau khi cuộn qua ô tìm kiếm ở hero).
- Nút liên hệ nổi: Desktop = cột dọc mép phải; Mobile = thanh cố định đáy chia 3 nút bằng nhau (Hotline, Zalo, Facebook).
- Form liên hệ: lưu lead trong WordPress (Bất động sản > Khách hàng tiềm năng) + gửi email + chuyển sang trang /cam-on/.
  Chống spam: honeypot, bẫy thời gian, giới hạn tần suất theo IP.

SEO & TRACKING
- Schema RealEstateListing (trang BĐS) + BreadcrumbList (trang con). Có Rank Math: theme gắn schema qua filter
  rank_math/json_ld và dùng breadcrumb của Rank Math. Không có plugin SEO: theme tự in JSON-LD, meta description, Open Graph.
- Trang lọc/tìm kiếm/cảm ơn/yêu thích tự gắn noindex.
- Rank Math: sau khi cài, vào Rank Math > Setup Wizard; bật Breadcrumbs (Rank Math > General Settings > Breadcrumbs);
  Titles & Meta > Bất động sản/Dự án/Khu dân cư: để trạng thái "Index". Sitemap tự gồm các CPT.
- GA4 + Meta Pixel: nhập ID ở trang cài đặt. SDK tải trễ (khi người dùng tương tác hoặc ~3,5 giây) để giữ điểm PageSpeed.
  Sự kiện: click_hotline, click_zalo, click_facebook, submit_form (khi gửi form), generate_lead / Lead (ở trang /cam-on/).
  Mọi sự kiện cũng đẩy vào dataLayer (dùng được cho GTM).
- Hiệu năng: ảnh Unsplash mẫu dùng WebP + srcset + width/height; ảnh tải lên được tự chuyển WebP (WP 6.1+);
  lazy-load, JS defer, bỏ emoji/embed, bản đồ chỉ tải khi bấm nút "Bản đồ".
  Để PageSpeed > 80 vẫn cần: hosting tốt, plugin cache (LiteSpeed/WP Rocket), CDN, ảnh thật đã nén. Hãy đo lại sau khi lên host.

CẦN BẠN TỰ LÀM (theme không thể làm thay)
- Nhập Google Maps API Key (tạo trong Google Cloud, bật Maps JavaScript API + Maps Embed API, giới hạn theo tên miền).
- Nhập hotline/Zalo/Facebook/email thật; GA4 ID; Pixel ID.
- Cài Rank Math, cấu hình SMTP (WP Mail SMTP) để email lead chắc chắn gửi được, cài cache/backup/bảo mật.
- Thay ảnh mẫu bằng ảnh có bản quyền; xác minh toàn bộ thông tin BĐS; để luật sư rà soát 3 trang pháp lý.
- Banner đồng ý cookie (nếu cần theo quy định) - dùng plugin riêng.

THÊM / SỬA DỮ LIỆU
- Bất động sản > Thêm tin mới: nhập đủ Giá hiển thị, Diện tích, PN, WC, Địa chỉ; Giá/Diện tích dạng số tự tính nếu để trống.
- Tick "Tin nổi bật" để hiện ở trang chủ. Dự án: tick "Nổi bật" để hiện ở mục "Dự án mới nổi bật".
- Ảnh: Ảnh đại diện + "Thư viện ảnh" (chọn từ Media). Tọa độ lat/lng để hiện trên bản đồ danh sách.
- Menu: Giao diện > Menu > gán vào "Menu chính" nếu muốn tự quản lý; để trống = menu mặc định có mega menu.

CẤU TRÚC FILE
functions.php + inc/ (helpers, post-types, meta-boxes, settings, query, leads, ajax, seo, tracking, performance, demo-data, setup)
template-parts/ (property-card, project-card, property-filter, listing, gallery, contact-box, floating-contact)
assets/js/main.js, assets/js/admin.js, assets/vendor/leaflet (BSD-2), assets/img/placeholder.svg
