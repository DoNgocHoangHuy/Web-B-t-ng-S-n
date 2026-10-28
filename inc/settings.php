<?php
/**
 * Settings > Nhà Việt Realty : contact info, maps key, GA4, Pixel, sample data tools, status checklist.
 */
if (!defined('ABSPATH')) exit;

function nvr_settings_menu() {
	add_options_page('Nhà Việt Realty', 'Nhà Việt Realty', 'manage_options', 'nvr-settings', 'nvr_settings_page');
}
add_action('admin_menu', 'nvr_settings_menu');

function nvr_settings_fields() {
	return [
		'Thông tin liên hệ (hiện ở header, footer, nút nổi, trang liên hệ)' => [
			'company'      => ['Tên công ty / thương hiệu', 'text', 'Nhà Việt Realty'],
			'hotline'      => ['Hotline', 'text', '0900 000 000 - thay bằng số thật của bạn'],
			'zalo'         => ['Zalo (số điện thoại hoặc link)', 'text', 'Để trống = dùng số hotline. VD: 0900000000 hoặc https://zalo.me/0900000000'],
			'facebook'     => ['Link Facebook / Fanpage', 'url', 'https://www.facebook.com/ten-trang-cua-ban'],
			'email'        => ['Email hiển thị', 'email', ''],
			'notify_email' => ['Email nhận thông tin khách', 'email', 'Để trống = email quản trị WordPress'],
			'address'      => ['Địa chỉ văn phòng', 'text', ''],
			'hours'        => ['Giờ làm việc', 'text', 'VD: Thứ 2 – Thứ 7: 8:00 – 18:00 (hiện ở footer, trang Liên hệ, Về chúng tôi)'],
		],
		'Trang Về chúng tôi (để trống phần nào thì phần đó tự ẩn hoặc dùng nội dung mặc định)' => [
			'founded'      => ['Năm thành lập', 'text', 'VD: 2019. Có năm thì trang hiện thêm số liệu "X năm hoạt động"'],
			'stats_custom' => ['Số liệu nổi bật (tự nhập)', 'textarea', 'Mỗi dòng một số liệu, dạng: Giá trị|Nhãn. VD: 1.200+|Giao dịch đã hỗ trợ. Chỉ nhập số liệu thật. Để trống = tự đếm từ website (số tin đăng, dự án, chủ đầu tư, khu dân cư).'],
			'milestones'   => ['Mốc phát triển', 'textarea', 'Mỗi dòng: Năm|Tiêu đề|Mô tả ngắn. VD: 2019|Thành lập|Ra mắt văn phòng đầu tiên tại Quận 7. Để trống = ẩn phần này.'],
			'team'         => ['Đội ngũ tư vấn', 'textarea', 'Mỗi dòng: Họ tên|Chức danh|Số điện thoại|Link ảnh (có thể bỏ trống)|Giới thiệu ngắn. VD: Nguyễn Văn A|Chuyên viên tư vấn|0900123456||5 năm kinh nghiệm khu Nam Sài Gòn. Để trống = hiện 3 thẻ vai trò chung, không có tên người.'],
			'testimonials' => ['Nhận xét của khách hàng', 'textarea', 'Mỗi dòng: Tên khách|Giao dịch|Nội dung. VD: Chị Lan|Mua căn hộ Quận 7|Tư vấn rõ ràng, đi xem nhà đúng giờ. Chỉ nhập nhận xét thật và đã được khách đồng ý. Để trống = ẩn phần này.'],
		],
		'Bản đồ' => [
			'google_maps_key' => ['Google Maps API Key', 'text', 'Bật "Maps JavaScript API" và "Maps Embed API" trong Google Cloud, giới hạn key theo tên miền. Không có key: dùng OpenStreetMap ở trang danh sách, iframe không key ở trang chi tiết. Hoặc khai báo hằng NVR_GOOGLE_MAPS_KEY trong wp-config.php.'],
		],
		'Chia sẻ mạng xã hội' => [
			'og_image' => ['Ảnh chia sẻ mặc định (link)', 'url', 'Ảnh hiện khi dán link trang vào Zalo/Facebook, nên rộng 1200×630px. Để trống = dùng ảnh mẫu.'],
		],
		'Theo dõi chuyển đổi' => [
			'ga4_id'        => ['GA4 Measurement ID', 'text', 'G-XXXXXXXXXX'],
			'meta_pixel_id' => ['Meta (Facebook) Pixel ID', 'text', 'Dãy số, VD: 123456789012345'],
		],
	];
}

function nvr_settings_page() {
	if (!current_user_can('manage_options')) return;
	$msg = '';
	if (!empty($_POST['nvr_action']) && check_admin_referer('nvr_settings_action')) {
		$action = sanitize_key(wp_unslash($_POST['nvr_action']));
		if ($action === 'save') {
			foreach (nvr_settings_fields() as $fields) {
				foreach ($fields as $key => $def) {
					$v = isset($_POST['nvr_' . $key]) ? wp_unslash($_POST['nvr_' . $key]) : '';
					$v = $def[1] === 'url' ? esc_url_raw(trim($v)) : ($def[1] === 'email' ? sanitize_email($v) : ($def[1] === 'textarea' ? sanitize_textarea_field($v) : sanitize_text_field($v)));
					update_option('nvr_' . $key, $v);
				}
			}
			$msg = 'Đã lưu cấu hình.';
		} elseif ($action === 'import') {
			$n = nvr_import_demo_content();
			update_option('nvr_demo_imported', time());
			flush_rewrite_rules();
			$msg = 'Đã nhập/cập nhật dữ liệu mẫu (' . (int) $n . ' mục mới).';
		} elseif ($action === 'reset_demo') {
			$n = nvr_remove_demo_content();
			$msg = 'Đã chuyển ' . (int) $n . ' mục dữ liệu mẫu vào thùng rác.';
		} elseif ($action === 'pages') {
			nvr_configure_reading(nvr_ensure_pages());
			nvr_fix_permalinks();
			$msg = 'Đã tạo các trang hệ thống còn thiếu và làm mới đường dẫn tĩnh (/%postname%/).';
		}
	}

	echo '<div class="wrap"><h1>Nhà Việt Realty</h1>';
	if ($msg) echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($msg) . '</p></div>';

	// Status checklist
	$checks = [];
	$checks[] = [(bool) get_option('permalink_structure'), 'Đường dẫn tĩnh đã đặt (khác "Plain")', 'Bấm "Tạo trang hệ thống & làm mới đường dẫn" bên dưới.'];
	$missing = [];
	foreach (array_keys(nvr_system_pages()) as $slug) if (!get_page_by_path($slug)) $missing[] = $slug;
	$checks[] = [!$missing, 'Đủ trang hệ thống (Liên hệ, Tin tức, Yêu thích, Cảm ơn, 3 trang pháp lý)', $missing ? 'Thiếu: ' . implode(', ', $missing) : ''];
	$checks[] = [nvr_opt('hotline') !== '0900 000 000' && get_option('nvr_hotline'), 'Đã nhập hotline thật', 'Đang dùng số mẫu 0900 000 000.'];
	$checks[] = [(bool) get_option('nvr_hours'), 'Đã nhập giờ làm việc', 'Đang dùng giờ mẫu: Thứ 2 – Thứ 7, 8:00 – 18:00.'];
	$checks[] = [(bool) get_option('nvr_team'), 'Đã nhập đội ngũ tư vấn thật (trang Về chúng tôi)', 'Đang hiện 3 thẻ vai trò chung, không có tên người.'];
	$checks[] = [(bool) get_option('nvr_testimonials'), 'Đã nhập nhận xét thật của khách hàng', 'Chưa có: phần nhận xét đang ẩn.'];
	$checks[] = [(bool) get_option('nvr_og_image'), 'Đã đặt ảnh chia sẻ mạng xã hội', 'Đang dùng ảnh mẫu khi dán link vào Zalo/Facebook.'];
	$checks[] = [(bool) nvr_opt('google_maps_key'), 'Đã nhập Google Maps API Key', 'Chưa có key: trang danh sách dùng OpenStreetMap.'];
	$checks[] = [nvr_valid_ga4(nvr_opt('ga4_id')), 'Đã nhập GA4 Measurement ID hợp lệ', ''];
	$checks[] = [nvr_valid_pixel(nvr_opt('meta_pixel_id')), 'Đã nhập Meta Pixel ID hợp lệ', ''];
	$checks[] = [defined('RANK_MATH_VERSION'), 'Plugin Rank Math đang hoạt động', 'Cài Rank Math SEO; theme tự gắn schema RealEstateListing + breadcrumb.'];
	$prop_count = wp_count_posts('property');
	$checks[] = [($prop_count->publish ?? 0) > 0, 'Có bất động sản đã xuất bản (' . (int) ($prop_count->publish ?? 0) . ')', 'Dùng nút "Nhập dữ liệu mẫu" bên dưới hoặc thêm tin mới.'];
	echo '<h2>Kiểm tra nhanh</h2><table class="widefat striped" style="max-width:860px"><tbody>';
	foreach ($checks as $c) {
		echo '<tr><td style="width:28px;font-size:18px;color:' . ($c[0] ? '#15803d' : '#b45309') . '">' . ($c[0] ? '✔' : '●') . '</td><td>' . esc_html($c[1]) . (!$c[0] && $c[2] ? '<br><small style="color:#646970">' . esc_html($c[2]) . '</small>' : '') . '</td></tr>';
	}
	echo '</tbody></table>';

	echo '<form method="post" style="max-width:860px">';
	wp_nonce_field('nvr_settings_action');
	echo '<input type="hidden" name="nvr_action" value="save">';
	foreach (nvr_settings_fields() as $section => $fields) {
		echo '<h2>' . esc_html($section) . '</h2><table class="form-table" role="presentation">';
		foreach ($fields as $key => $def) {
			$val = get_option('nvr_' . $key, '');
			$attr = $key === 'google_maps_key' && defined('NVR_GOOGLE_MAPS_KEY') ? ' disabled' : '';
			$fid = 'nvr_' . $key;
			echo '<tr><th scope="row"><label for="' . esc_attr($fid) . '">' . esc_html($def[0]) . '</label></th><td>';
			if ($def[1] === 'textarea') {
				echo '<textarea class="large-text" style="max-width:640px" rows="5" id="' . esc_attr($fid) . '" name="' . esc_attr($fid) . '">' . esc_textarea($val) . '</textarea><p class="description">' . esc_html($def[2]) . '</p>';
			} else {
				echo '<input class="regular-text" style="width:100%;max-width:480px" id="' . esc_attr($fid) . '" name="' . esc_attr($fid) . '" type="' . esc_attr($def[1] === 'url' ? 'url' : ($def[1] === 'email' ? 'email' : 'text')) . '" value="' . esc_attr($val) . '" placeholder="' . esc_attr(strlen($def[2]) < 70 ? $def[2] : '') . '"' . $attr . '>' . (strlen($def[2]) >= 70 ? '<p class="description">' . esc_html($def[2]) . '</p>' : '');
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}
	echo '<p><button class="button button-primary">Lưu cấu hình</button></p></form>';

	echo '<hr><h2>Công cụ</h2><form method="post" style="display:flex;gap:10px;flex-wrap:wrap">';
	wp_nonce_field('nvr_settings_action');
	echo '<button class="button" name="nvr_action" value="pages">Tạo trang hệ thống & làm mới đường dẫn</button>';
	echo '<button class="button button-primary" name="nvr_action" value="import">Nhập / cập nhật dữ liệu mẫu</button>';
	echo '<button class="button" name="nvr_action" value="reset_demo" onclick="return confirm(\'Chuyển toàn bộ dữ liệu mẫu vào thùng rác?\')">Xóa dữ liệu mẫu</button>';
	echo '</form><p class="description" style="max-width:860px">Dữ liệu mẫu gồm: 15 bất động sản (12 tin nổi bật), 6 dự án nổi bật, 8 chủ đầu tư, 4 khu dân cư, 6 bài viết và nội dung các trang hệ thống. Chạy lại chỉ cập nhật mục đã có, không tạo trùng. Đây là dữ liệu minh họa - hãy thay bằng thông tin thật đã xác minh trước khi chạy quảng cáo.</p></div>';
}

/** Admin notice: finish setup. */
function nvr_admin_notices() {
	if (!current_user_can('manage_options')) return;
	$screen = get_current_screen();
	if ($screen && $screen->id === 'settings_page_nvr-settings') return;
	if (!get_option('nvr_hotline')) {
		echo '<div class="notice notice-warning"><p><strong>Nhà Việt Realty:</strong> bạn chưa nhập hotline / Zalo / Google Maps Key. <a href="' . esc_url(admin_url('options-general.php?page=nvr-settings')) . '">Cấu hình ngay</a>.</p></div>';
	}
}
add_action('admin_notices', 'nvr_admin_notices');
