<?php
/**
 * Admin meta boxes (schema-driven, whitelisted save).
 */
if (!defined('ABSPATH')) exit;

function nvr_direction_options() {
	$o = ['' => '— Chọn —'];
	foreach (['Đông', 'Tây', 'Nam', 'Bắc', 'Đông Nam', 'Đông Bắc', 'Tây Nam', 'Tây Bắc'] as $d) $o[$d] = $d;
	return $o;
}

function nvr_legal_options() {
	return ['' => '— Chọn —', 'Sổ hồng' => 'Sổ hồng', 'Sổ đỏ' => 'Sổ đỏ', 'Hợp đồng mua bán' => 'Hợp đồng mua bán', 'Hợp đồng thuê' => 'Hợp đồng thuê', 'Đang chờ sổ' => 'Đang chờ sổ'];
}

function nvr_project_options() {
	$o = ['' => '— Không thuộc dự án —'];
	$posts = get_posts(['post_type' => 'project', 'posts_per_page' => 200, 'post_status' => ['publish', 'draft'], 'orderby' => 'title', 'order' => 'ASC']);
	foreach ($posts as $p) $o[$p->ID] = $p->post_title;
	return $o;
}

function nvr_location_term_options() {
	$o = ['' => '— Không liên kết —'];
	$terms = get_terms(['taxonomy' => 'property_location', 'hide_empty' => false]);
	if (!is_wp_error($terms)) foreach ($terms as $t) $o[$t->slug] = $t->name;
	return $o;
}

/**
 * field => [type, label, hint/options]
 * types: text, number, select, checkbox, textarea, url, media
 */
function nvr_meta_schema($post_type) {
	switch ($post_type) {
		case 'property':
			return [
				'Giá & trạng thái' => [
					'price' => ['text', 'Giá hiển thị *', 'VD: 5.2 Tỷ  hoặc  15 Triệu/tháng'],
					'price_numeric' => ['number', 'Giá dạng số (VND)', 'Để trống: tự tính từ "Giá hiển thị" (dùng cho lọc & sắp xếp)'],
					'deal_type' => ['select', 'Hình thức', ['sale' => 'Mua bán', 'rent' => 'Cho thuê']],
					'status' => ['select', 'Trạng thái (hiện badge trên card)', ['Đang bán' => 'Đang bán', 'Đã bán' => 'Đã bán', 'Đang cho thuê' => 'Đang cho thuê', 'Đã cho thuê' => 'Đã cho thuê']],
					'featured' => ['checkbox', 'Tin nổi bật (hiện ở trang chủ)'],
				],
				'Thông số' => [
					'area' => ['text', 'Diện tích (m²) *', 'VD: 70 hoặc 69.1'],
					'area_numeric' => ['number', 'Diện tích dạng số', 'Để trống: tự tính'],
					'bedrooms' => ['number', 'Phòng ngủ (PN)', 'Đất nền: để trống'],
					'bathrooms' => ['number', 'Phòng tắm / WC', 'Đất nền: để trống'],
					'direction' => ['select', 'Hướng', nvr_direction_options()],
					'legal' => ['select', 'Pháp lý', nvr_legal_options()],
					'code' => ['text', 'Mã tin', 'Để trống: tự tạo NVR-xxxx'],
					'project_id' => ['select', 'Thuộc dự án', nvr_project_options()],
				],
				'Vị trí' => [
					'address' => ['text', 'Địa chỉ chi tiết *', 'Số nhà (nếu có), đường, phường/xã, quận/huyện'],
					'latitude' => ['text', 'Vĩ độ (lat)', 'VD: 10.7944 - dùng cho bản đồ ở trang danh sách'],
					'longitude' => ['text', 'Kinh độ (lng)', 'VD: 106.7218'],
					'map_url' => ['url', 'URL / iframe bản đồ Google (tùy chọn)', 'Để trống: tự tạo bản đồ từ địa chỉ. Có thể dán cả thẻ <iframe> từ Google Maps > Chia sẻ > Nhúng bản đồ'],
				],
				'Nội dung bổ sung' => [
					'amenities' => ['text', 'Tiện ích (cách nhau bằng dấu phẩy)', 'VD: Hồ bơi, Gym, Công viên'],
					'gallery' => ['media', 'Thư viện ảnh (chọn từ Media)'],
					'gallery_images' => ['textarea', 'Hoặc dán URL ảnh (mỗi dòng một URL)'],
					'tour_url' => ['url', 'URL tour 3D (Matterport/Kuula) - tùy chọn'],
					'contact_phone' => ['text', 'SĐT tư vấn riêng cho tin này (để trống = hotline chung)'],
				],
			];
		case 'project':
			return [
				'Thông tin dự án' => [
					'project_price' => ['text', 'Giá tham khảo *', 'VD: 50 Triệu/m²'],
					'project_status_text' => ['select', 'Trạng thái', ['Đang mở bán' => 'Đang mở bán', 'Sắp mở bán' => 'Sắp mở bán', 'Đã bàn giao' => 'Đã bàn giao']],
					'featured' => ['checkbox', 'Nổi bật (hiện ở mục "Dự án mới nổi bật" trang chủ)'],
					'project_developer' => ['text', 'Chủ đầu tư', 'Nhập đúng tên chủ đầu tư để liên kết trang chủ đầu tư'],
					'project_scale' => ['text', 'Quy mô'],
					'project_url' => ['url', 'Website dự án'],
				],
				'Vị trí' => [
					'project_area' => ['text', 'Khu vực hiển thị *', 'VD: TP. Thủ Đức, TP. Hồ Chí Minh'],
					'project_location' => ['text', 'Địa chỉ chi tiết'],
					'latitude' => ['text', 'Vĩ độ (lat)'],
					'longitude' => ['text', 'Kinh độ (lng)'],
					'project_map_url' => ['url', 'URL / iframe bản đồ Google (tùy chọn)'],
				],
				'Nội dung bổ sung' => [
					'project_amenities' => ['text', 'Tiện ích (cách nhau bằng dấu phẩy)'],
					'gallery' => ['media', 'Thư viện ảnh (chọn từ Media)'],
					'gallery_images' => ['textarea', 'Hoặc dán URL ảnh (mỗi dòng một URL)'],
				],
			];
		case 'developer':
			return [
				'Thông tin' => [
					'developer_url' => ['url', 'Website chính thức'],
				],
			];
		case 'neighborhood':
			return [
				'Thông tin khu dân cư' => [
					'neighborhood_district' => ['text', 'Quận / Huyện *', 'VD: Quận 7'],
					'neighborhood_city' => ['text', 'Tỉnh / Thành phố', 'VD: TP. Hồ Chí Minh'],
					'neighborhood_term' => ['select', 'Khu vực liên kết tin đăng', nvr_location_term_options()],
					'latitude' => ['text', 'Vĩ độ (lat)'],
					'longitude' => ['text', 'Kinh độ (lng)'],
				],
				'Nội dung chi tiết' => [
					'neighborhood_location' => ['textarea', 'Vị trí & giao thông'],
					'neighborhood_amenities' => ['textarea', 'Tiện ích nội khu'],
					'neighborhood_subareas' => ['textarea', 'Phân khu'],
					'neighborhood_market' => ['textarea', 'Đánh giá thị trường'],
					'gallery' => ['media', 'Thư viện ảnh (chọn từ Media)'],
					'gallery_images' => ['textarea', 'Hoặc dán URL ảnh (mỗi dòng một URL)'],
				],
			];
	}
	return [];
}

function nvr_add_meta_boxes() {
	$titles = ['property' => 'Thông tin bất động sản', 'project' => 'Thông tin dự án', 'developer' => 'Thông tin chủ đầu tư', 'neighborhood' => 'Thông tin khu dân cư'];
	foreach ($titles as $pt => $title) {
		add_meta_box('nvr_details', $title, 'nvr_render_meta_box', $pt, 'normal', 'high');
	}
}
add_action('add_meta_boxes', 'nvr_add_meta_boxes');

function nvr_render_meta_box($post) {
	wp_nonce_field('nvr_save_meta', 'nvr_meta_nonce');
	foreach (nvr_meta_schema($post->post_type) as $group => $fields) {
		echo '<h4 class="nvr-group">' . esc_html($group) . '</h4><div class="nvr-fields">';
		foreach ($fields as $key => $def) {
			[$type, $label] = $def;
			$extra = $def[2] ?? null;
			$val = get_post_meta($post->ID, '_nvr_' . $key, true);
			$name = 'nvr_meta[' . esc_attr($key) . ']';
			$wide = in_array($type, ['textarea', 'media'], true) || in_array($key, ['address', 'map_url', 'amenities'], true);
			echo '<div class="nvr-field' . ($wide ? ' is-wide' : '') . '"><label><span>' . esc_html($label) . '</span>';
			switch ($type) {
				case 'select':
					echo '<select name="' . $name . '">';
					foreach ((array) $extra as $ov => $ol) echo '<option value="' . esc_attr($ov) . '" ' . selected((string) $val, (string) $ov, false) . '>' . esc_html($ol) . '</option>';
					echo '</select>';
					$extra = null;
					break;
				case 'checkbox':
					echo '<input type="hidden" name="' . $name . '" value=""><input type="checkbox" name="' . $name . '" value="yes" ' . checked($val, 'yes', false) . '>';
					break;
				case 'textarea':
					echo '<textarea rows="3" name="' . $name . '">' . esc_textarea($val) . '</textarea>';
					break;
				case 'number':
					echo '<input type="number" step="any" min="0" name="' . $name . '" value="' . esc_attr($val) . '">';
					break;
				case 'media':
					$ids = array_filter(array_map('absint', explode(',', (string) get_post_meta($post->ID, '_nvr_gallery_ids', true))));
					echo '<input type="hidden" class="nvr-gallery-ids" name="nvr_meta[gallery_ids]" value="' . esc_attr(implode(',', $ids)) . '"></label>';
					echo '<div class="nvr-gallery-preview">';
					foreach ($ids as $aid) echo '<span data-id="' . (int) $aid . '">' . wp_get_attachment_image($aid, 'thumbnail') . '</span>';
					echo '</div><p><button type="button" class="button nvr-gallery-pick">Chọn / sắp xếp ảnh</button> <button type="button" class="button-link nvr-gallery-clear">Xóa tất cả</button></p></div>';
					continue 2;
				default:
					echo '<input type="' . ($type === 'url' ? 'text' : 'text') . '" name="' . $name . '" value="' . esc_attr($val) . '">';
			}
			echo '</label>';
			if (is_string($extra) && $extra !== '') echo '<small>' . esc_html($extra) . '</small>';
			echo '</div>';
		}
		echo '</div>';
	}
}

function nvr_sanitize_meta($type, $key, $value, $extra = null) {
	$value = is_string($value) ? wp_unslash($value) : '';
	switch ($type) {
		case 'number':
			$value = trim($value);
			return is_numeric($value) ? (string) (0 + $value) : '';
		case 'checkbox':
			return $value === 'yes' ? 'yes' : '';
		case 'textarea':
			return sanitize_textarea_field($value);
		case 'select':
			$opts = array_map('strval', array_keys((array) $extra));
			return in_array((string) $value, $opts, true) ? $value : '';
		case 'url':
			if (stripos($value, '<iframe') !== false && preg_match('/src=["\']([^"\']+)["\']/i', $value, $m)) $value = html_entity_decode($m[1]);
			return esc_url_raw(trim($value));
		case 'media':
			return implode(',', array_filter(array_map('absint', explode(',', $value))));
		default:
			return sanitize_text_field($value);
	}
}

function nvr_save_meta_box($post_id, $post) {
	if (!isset($_POST['nvr_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nvr_meta_nonce'])), 'nvr_save_meta')) return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;
	$schema = nvr_meta_schema($post->post_type);
	if (!$schema) return;
	$posted = isset($_POST['nvr_meta']) && is_array($_POST['nvr_meta']) ? $_POST['nvr_meta'] : [];
	foreach ($schema as $fields) {
		foreach ($fields as $key => $def) {
			$type = $def[0];
			if ($type === 'media') {
				$val = nvr_sanitize_meta('media', 'gallery_ids', $posted['gallery_ids'] ?? '');
				$key = 'gallery_ids';
			} else {
				if (!array_key_exists($key, $posted)) continue;
				$val = nvr_sanitize_meta($type, $key, $posted[$key], $def[2] ?? null);
			}
			if ($val === '') delete_post_meta($post_id, '_nvr_' . $key);
			else update_post_meta($post_id, '_nvr_' . $key, $val);
		}
	}
	if ($post->post_type === 'property') nvr_property_autofill($post_id);
}
add_action('save_post', 'nvr_save_meta_box', 10, 2);

/** Fill numeric price/area & listing code if the editor left them empty. */
function nvr_property_autofill($post_id) {
	$price_text = get_post_meta($post_id, '_nvr_price', true);
	if (get_post_meta($post_id, '_nvr_price_numeric', true) === '' && $price_text !== '') {
		$n = nvr_parse_vnd($price_text);
		if ($n) update_post_meta($post_id, '_nvr_price_numeric', (string) $n);
	}
	$area = get_post_meta($post_id, '_nvr_area', true);
	if (get_post_meta($post_id, '_nvr_area_numeric', true) === '' && $area !== '') {
		$n = nvr_parse_area($area);
		if ($n) update_post_meta($post_id, '_nvr_area_numeric', (string) $n);
	}
	if (get_post_meta($post_id, '_nvr_code', true) === '') {
		update_post_meta($post_id, '_nvr_code', 'NVR-' . str_pad((string) $post_id, 4, '0', STR_PAD_LEFT));
	}
	if (get_post_meta($post_id, '_nvr_deal_type', true) === '') update_post_meta($post_id, '_nvr_deal_type', 'sale');
}

function nvr_admin_assets($hook) {
	if (!in_array($hook, ['post.php', 'post-new.php'], true)) return;
	$screen = get_current_screen();
	if (!$screen || !in_array($screen->post_type, ['property', 'project', 'developer', 'neighborhood'], true)) return;
	wp_enqueue_media();
	wp_enqueue_script('nvr-admin', NVR_URI . '/assets/js/admin.js', ['jquery', 'jquery-ui-sortable'], NVR_VERSION, true);
	wp_add_inline_style('wp-admin', '
		.nvr-group{margin:18px 0 8px;padding-bottom:6px;border-bottom:1px solid #dcdcde;font-size:14px}
		.nvr-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 18px}
		.nvr-field.is-wide{grid-column:1/-1}.nvr-field label span{display:block;font-weight:600;margin-bottom:4px}
		.nvr-field input[type=text],.nvr-field input[type=number],.nvr-field select,.nvr-field textarea{width:100%}
		.nvr-field small{display:block;color:#646970;margin-top:3px}
		.nvr-gallery-preview{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px}.nvr-gallery-preview span{display:block;width:78px;height:78px;border-radius:6px;overflow:hidden;cursor:move;border:1px solid #dcdcde}
		.nvr-gallery-preview img{width:100%;height:100%;object-fit:cover}
	');
}
add_action('admin_enqueue_scripts', 'nvr_admin_assets');
