<?php
/**
 * Theme setup, assets, activation (pages, permalinks, rewrite flush), misc filters.
 */
if (!defined('ABSPATH')) exit;

function nvr_theme_setup() {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
	add_theme_support('custom-logo', ['height' => 60, 'width' => 220, 'flex-width' => true, 'flex-height' => true]);
	add_theme_support('responsive-embeds');
	add_theme_support('automatic-feed-links');
	register_nav_menus(['primary' => 'Menu chính (để trống = menu mặc định có mega menu)', 'footer' => 'Menu chân trang']);
	add_image_size('nvr-card', 640, 440, true);
	add_image_size('nvr-wide', 1200, 675, true);
}
add_action('after_setup_theme', 'nvr_theme_setup');

function nvr_enqueue_assets() {
	$serif_fonts = is_front_page() || is_page('lien-he');
	if ($serif_fonts) wp_enqueue_style('nvr-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700;800&display=swap', [], null);
	wp_enqueue_style('nvr-style', get_stylesheet_uri(), $serif_fonts ? ['nvr-fonts'] : [], NVR_VERSION);
	wp_enqueue_script('nvr-main', NVR_URI . '/assets/js/main.js', [], NVR_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
	wp_localize_script('nvr-main', 'NVR', [
		'ajax'        => admin_url('admin-ajax.php'),
		'favPage'     => nvr_page_url('yeu-thich'),
		'thanks'      => is_page('cam-on'),
		'placeholder' => nvr_placeholder_url(),
		'isHome'      => is_front_page(),
		'maps'        => [
			'key'       => nvr_opt('google_maps_key'),
			'leafletJs' => NVR_URI . '/assets/vendor/leaflet/leaflet.js?ver=1.9.4',
			'leafletCss' => NVR_URI . '/assets/vendor/leaflet/leaflet.css?ver=1.9.4',
		],
	]);
}
add_action('wp_enqueue_scripts', 'nvr_enqueue_assets');

/* Trailing dots in headings (user request: no "." at the end of headings). */
function nvr_strip_title_dot($title) {
	if (is_admin()) return $title;
	return preg_replace('/[\s.]+$/u', '', (string) $title);
}
add_filter('the_title', 'nvr_strip_title_dot', 20);

/* Excerpt length/more */
add_filter('excerpt_length', function () { return 28; });
add_filter('excerpt_more', function () { return '…'; });

function nvr_body_classes($classes) {
	if (is_front_page()) $classes[] = 'is-front';
	return $classes;
}
add_filter('body_class', 'nvr_body_classes');

/* ---------------------------------------------------------------------
 * System pages
 * ------------------------------------------------------------------- */
function nvr_about_content() {
	return '<!--nvr-about-v2--><p class="lead">NhàViệt Realty là nền tảng giới thiệu và kết nối bất động sản tại TP. Hồ Chí Minh và các khu vực lân cận. Chúng tôi giúp người mua, người thuê và nhà đầu tư tìm đúng sản phẩm bằng thông tin rõ ràng, dễ so sánh và đội ngũ tư vấn đồng hành đến khi quyết định.</p>'
	. '<h2>Câu chuyện của chúng tôi</h2><p>Tìm nhà là một trong những quyết định lớn nhất của mỗi gia đình. Thông tin rải rác, giá không thống nhất và pháp lý khó kiểm chứng khiến nhiều người mất rất nhiều thời gian. NhàViệt ra đời với mục tiêu gom thông tin về một nơi: tin đăng có địa chỉ, diện tích, giá, hướng nhà, pháp lý; dự án với chủ đầu tư và tiện ích; khu dân cư với đặc điểm hạ tầng.</p>'
	. '<h2>Sứ mệnh &amp; tầm nhìn</h2><p><strong>Sứ mệnh:</strong> đơn giản hóa hành trình tìm kiếm bất động sản, giúp khách hàng quyết định dựa trên thông tin minh bạch.</p><p><strong>Tầm nhìn:</strong> trở thành địa chỉ tìm kiếm và tư vấn bất động sản đáng tin cậy tại khu vực phía Nam.</p>'
	. '<h2>Chúng tôi hỗ trợ bạn những gì?</h2><ul><li><strong>Mua bán:</strong> căn hộ, nhà phố, biệt thự, đất nền tại Quận 1, Quận 7, Bình Thạnh, Thủ Đức, Nhà Bè…</li><li><strong>Cho thuê:</strong> studio, căn hộ, officetel và văn phòng nhỏ theo hợp đồng 6–12 tháng.</li><li><strong>Dự án &amp; chủ đầu tư:</strong> tổng hợp thông tin các dự án đang được quan tâm và đơn vị phát triển.</li><li><strong>Khu dân cư:</strong> giới thiệu vị trí, tiện ích, kết nối giao thông từng khu.</li><li><strong>Tư vấn pháp lý, vay vốn:</strong> định hướng kiểm tra sổ hồng, quy hoạch và phương án tài chính.</li><li><strong>Dẫn xem nhà:</strong> đặt lịch xem thực tế theo thời gian phù hợp.</li></ul>'
	. '<h2>Quy trình làm việc</h2><ol><li>Tiếp nhận nhu cầu: ngân sách, khu vực, loại hình, mục đích ở hay đầu tư.</li><li>Chọn lọc sản phẩm phù hợp và gửi thông tin chi tiết.</li><li>Dẫn xem thực tế, cùng kiểm tra pháp lý và hiện trạng.</li><li>Hỗ trợ thương lượng, đặt cọc và hoàn tất thủ tục.</li><li>Chăm sóc sau giao dịch.</li></ol>'
	. '<h2>Giá trị cốt lõi</h2><ul><li><strong>Minh bạch:</strong> thông tin rõ ràng, nêu rõ những gì cần xác minh.</li><li><strong>Tận tâm:</strong> lắng nghe nhu cầu thực, không ép quyết định.</li><li><strong>Chuyên nghiệp:</strong> phản hồi nhanh, làm việc theo quy trình.</li><li><strong>Bảo mật:</strong> thông tin cá nhân chỉ dùng để tư vấn.</li></ul>'
	. '<h2>Lưu ý về thông tin</h2><p>Giá, diện tích và tình trạng pháp lý mang tính tham khảo tại thời điểm đăng. Vui lòng kiểm tra giấy tờ, quy hoạch và hợp đồng từ nguồn có thẩm quyền trước khi giao dịch.</p>'
	. '<h2>Liên hệ</h2><p>Gọi hotline, nhắn Zalo hoặc gửi yêu cầu tại trang <a href="/lien-he/">Liên hệ</a>; chúng tôi sẽ phản hồi trong thời gian sớm nhất.</p>';
}

function nvr_system_pages() {
	return [
		'trang-chu'           => ['Trang chủ', ''],
		'tin-tuc'             => ['Tin tức', ''],
		've-chung-toi'        => ['Về chúng tôi', nvr_about_content()],
		'ky-gui'              => ['Ký gửi bất động sản', ''],
		'tinh-vay'            => ['Tính khoản vay mua nhà', ''],
		'lien-he'             => ['Liên hệ', "Hãy để lại thông tin, đội ngũ tư vấn sẽ liên hệ lại với bạn trong thời gian sớm nhất. Bạn cũng có thể gọi hotline hoặc nhắn Zalo để được hỗ trợ ngay."],
		'yeu-thich'           => ['Bất động sản yêu thích', ''],
		'cam-on'              => ['Cảm ơn bạn đã liên hệ', "Chúng tôi đã nhận được thông tin của bạn. Chuyên viên tư vấn sẽ liên hệ trong thời gian sớm nhất (thường trong vòng 30 phút vào giờ hành chính)."],
		'chinh-sach-bao-mat'  => ['Chính sách bảo mật', nvr_legal_content('chinh-sach-bao-mat')],
		'dieu-khoan-su-dung'  => ['Điều khoản sử dụng', nvr_legal_content('dieu-khoan-su-dung')],
		'quy-che-hoat-dong'   => ['Quy chế hoạt động', nvr_legal_content('quy-che-hoat-dong')],
	];
}

/** Create any missing system page. Returns slug => ID. */
function nvr_ensure_pages() {
	$ids = [];
	foreach (nvr_system_pages() as $slug => $def) {
		$page = get_page_by_path($slug);
		if (!$page) {
			$id = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $def[0], 'post_content' => $def[1]]);
			$ids[$slug] = is_wp_error($id) ? 0 : $id;
		} else {
			if ($page->post_status !== 'publish') wp_update_post(['ID' => $page->ID, 'post_status' => 'publish']);
			if ($slug === 've-chung-toi' && (trim(wp_strip_all_tags($page->post_content)) === '' || (strpos($page->post_content, 'nvr-about-v2') === false && strpos($page->post_content, 'NhàViệt là nền tảng giới thiệu') !== false))) wp_update_post(['ID' => $page->ID, 'post_content' => $def[1]]);
			$ids[$slug] = $page->ID;
		}
	}
	return $ids;
}

function nvr_configure_reading($ids) {
	if (get_option('show_on_front') !== 'page' && !empty($ids['trang-chu'])) {
		update_option('show_on_front', 'page');
		update_option('page_on_front', $ids['trang-chu']);
	}
	if (!get_option('page_for_posts') && !empty($ids['tin-tuc'])) update_option('page_for_posts', $ids['tin-tuc']);
}

/** Permalinks -> /%postname%/ (only if still on "Plain") and flush rules. */
function nvr_fix_permalinks() {
	global $wp_rewrite;
	if (!get_option('permalink_structure')) {
		$wp_rewrite->set_permalink_structure('/%postname%/');
	}
	flush_rewrite_rules();
}

function nvr_activate_theme() {
	nvr_register_post_types();
	nvr_seed_terms();
	nvr_configure_reading(nvr_ensure_pages());
	nvr_fix_permalinks();
	update_option('nvr_version', NVR_VERSION);
	// First install on an empty site: import sample content once.
	$has = get_posts(['post_type' => 'property', 'posts_per_page' => 1, 'post_status' => 'any', 'fields' => 'ids']);
	if (!$has && !get_option('nvr_demo_imported')) {
		nvr_import_demo_content();
		update_option('nvr_demo_imported', time());
		flush_rewrite_rules();
	}
}
add_action('after_switch_theme', 'nvr_activate_theme');

/** After a theme file update (version bump) refresh rewrite rules and missing pages once. */
function nvr_maybe_upgrade() {
	if (get_option('nvr_version') === NVR_VERSION) return;
	nvr_configure_reading(nvr_ensure_pages());
	// Idempotent seed: fills missing demo projects, listings, developers, neighborhoods and articles after theme updates.
	if (function_exists('nvr_import_demo_content')) { nvr_import_demo_content(); update_option('nvr_demo_imported', time()); }
	flush_rewrite_rules();
	update_option('nvr_version', NVR_VERSION);
}
add_action('init', 'nvr_maybe_upgrade', 99);
