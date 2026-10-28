<?php
/**
 * Custom post types & taxonomies.
 *  - property      Bất động sản (mua bán + cho thuê, phân biệt bằng meta deal_type)
 *  - project       Dự án
 *  - developer     Chủ đầu tư
 *  - neighborhood  Khu dân cư
 *  - nvr_lead      Khách hàng tiềm năng (private)
 */
if (!defined('ABSPATH')) exit;

function nvr_register_post_types() {
	$make_labels = function ($plural, $singular, $gender_new = 'mới') {
		return [
			'name' => $plural, 'singular_name' => $singular,
			'add_new' => 'Thêm ' . mb_strtolower($singular, 'UTF-8'), 'add_new_item' => 'Thêm ' . mb_strtolower($singular, 'UTF-8'),
			'edit_item' => 'Sửa ' . mb_strtolower($singular, 'UTF-8'), 'new_item' => $singular . ' ' . $gender_new,
			'view_item' => 'Xem ' . mb_strtolower($singular, 'UTF-8'), 'search_items' => 'Tìm ' . mb_strtolower($plural, 'UTF-8'),
			'not_found' => 'Chưa có dữ liệu', 'not_found_in_trash' => 'Thùng rác trống', 'all_items' => 'Tất cả ' . mb_strtolower($plural, 'UTF-8'),
			'menu_name' => $plural,
		];
	};

	register_post_type('property', [
		'labels' => $make_labels('Bất động sản', 'Bất động sản'),
		'public' => true, 'has_archive' => true, 'show_in_rest' => true,
		'rewrite' => ['slug' => 'bat-dong-san', 'with_front' => false],
		'menu_icon' => 'dashicons-admin-home', 'menu_position' => 5,
		'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions'],
	]);
	register_taxonomy('property_type', 'property', [
		'label' => 'Loại bất động sản', 'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true,
		'rewrite' => ['slug' => 'loai-bat-dong-san', 'with_front' => false],
	]);
	register_taxonomy('property_location', 'property', [
		'label' => 'Khu vực', 'public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true,
		'rewrite' => ['slug' => 'khu-vuc', 'with_front' => false],
	]);

	register_post_type('project', [
		'labels' => $make_labels('Dự án', 'Dự án'),
		'public' => true, 'has_archive' => true, 'show_in_rest' => true,
		'rewrite' => ['slug' => 'du-an', 'with_front' => false],
		'menu_icon' => 'dashicons-building', 'menu_position' => 6,
		'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
	]);

	register_post_type('developer', [
		'labels' => $make_labels('Chủ đầu tư', 'Chủ đầu tư'),
		'public' => true, 'has_archive' => true, 'show_in_rest' => true,
		'rewrite' => ['slug' => 'chu-dau-tu', 'with_front' => false],
		'menu_icon' => 'dashicons-groups', 'menu_position' => 7,
		'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
	]);

	register_post_type('neighborhood', [
		'labels' => $make_labels('Khu dân cư', 'Khu dân cư'),
		'public' => true, 'has_archive' => true, 'show_in_rest' => true,
		'rewrite' => ['slug' => 'khu-dan-cu', 'with_front' => false],
		'menu_icon' => 'dashicons-location-alt', 'menu_position' => 8,
		'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
	]);

	register_post_type('nvr_lead', [
		'labels' => ['name' => 'Khách hàng tiềm năng', 'singular_name' => 'Khách hàng', 'menu_name' => 'Khách hàng tiềm năng', 'all_items' => 'Khách hàng tiềm năng', 'edit_item' => 'Xem thông tin khách', 'search_items' => 'Tìm khách hàng', 'not_found' => 'Chưa có yêu cầu nào'],
		'public' => false, 'show_ui' => true, 'show_in_menu' => 'edit.php?post_type=property',
		'supports' => ['title', 'editor'], 'capability_type' => 'post',
		'capabilities' => ['create_posts' => 'do_not_allow'], 'map_meta_cap' => true,
	]);
}
add_action('init', 'nvr_register_post_types');

/** Default terms so filters/menus work before any listing is added. */
function nvr_seed_terms() {
	if (get_option('nvr_terms_seeded')) return;
	$types = ['Căn hộ' => 'can-ho', 'Nhà phố' => 'nha-pho', 'Biệt thự' => 'biet-thu', 'Đất nền' => 'dat-nen', 'Văn phòng' => 'van-phong'];
	foreach ($types as $name => $slug) {
		if (!term_exists($slug, 'property_type')) wp_insert_term($name, 'property_type', ['slug' => $slug]);
	}
	$locations = ['Quận 1' => 'quan-1', 'Quận 2' => 'quan-2', 'Quận 7' => 'quan-7', 'TP. Thủ Đức' => 'thu-duc', 'Bình Thạnh' => 'binh-thanh', 'Nhà Bè' => 'nha-be'];
	foreach ($locations as $name => $slug) {
		if (!term_exists($slug, 'property_location')) wp_insert_term($name, 'property_location', ['slug' => $slug]);
	}
	update_option('nvr_terms_seeded', 1);
}
add_action('init', 'nvr_seed_terms', 20);

/* Admin list columns ------------------------------------------------- */
function nvr_property_columns($cols) {
	$new = [];
	foreach ($cols as $k => $v) {
		$new[$k] = $v;
		if ($k === 'title') {
			$new['nvr_thumb'] = 'Ảnh';
			$new['nvr_price'] = 'Giá';
			$new['nvr_specs'] = 'Thông số';
			$new['nvr_status'] = 'Trạng thái';
		}
	}
	return $new;
}
add_filter('manage_property_posts_columns', 'nvr_property_columns');
function nvr_property_column_content($col, $id) {
	if ($col === 'nvr_thumb') {
		$u = nvr_card_image_url($id, 'thumbnail');
		echo $u ? '<img src="' . esc_url(nvr_img_url($u, 120, 80)) . '" alt="" style="width:80px;height:54px;object-fit:cover;border-radius:6px">' : '—';
	} elseif ($col === 'nvr_price') {
		echo esc_html(nvr_meta('price', $id, '—'));
		if (nvr_is_featured($id)) echo ' <span title="Nổi bật" style="color:#d97706">★</span>';
	} elseif ($col === 'nvr_specs') {
		echo esc_html(implode(' | ', nvr_specs($id)) ?: '—');
	} elseif ($col === 'nvr_status') {
		echo esc_html(nvr_status($id)[0]);
	}
}
add_action('manage_property_posts_custom_column', 'nvr_property_column_content', 10, 2);

function nvr_project_columns($cols) {
	$cols['nvr_price'] = 'Giá';
	$cols['nvr_status'] = 'Trạng thái';
	return $cols;
}
add_filter('manage_project_posts_columns', 'nvr_project_columns');
add_action('manage_project_posts_custom_column', function ($col, $id) {
	if ($col === 'nvr_price') echo esc_html(nvr_meta('project_price', $id, '—')) . (nvr_is_featured($id) ? ' <span title="Nổi bật" style="color:#d97706">★</span>' : '');
	if ($col === 'nvr_status') echo esc_html(nvr_meta('project_status_text', $id, '—'));
}, 10, 2);

add_filter('manage_nvr_lead_posts_columns', function ($cols) {
	return ['cb' => $cols['cb'], 'title' => 'Khách hàng', 'nvr_phone' => 'Điện thoại', 'nvr_source' => 'Quan tâm', 'date' => 'Thời gian'];
});
add_action('manage_nvr_lead_posts_custom_column', function ($col, $id) {
	if ($col === 'nvr_phone') {
		$p = get_post_meta($id, '_nvr_lead_phone', true);
		echo $p ? '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $p)) . '">' . esc_html($p) . '</a>' : '—';
	}
	if ($col === 'nvr_source') {
		$ref = (int) get_post_meta($id, '_nvr_lead_ref', true);
		echo $ref ? '<a href="' . esc_url(get_edit_post_link($ref)) . '">' . esc_html(get_the_title($ref)) . '</a>' : esc_html(get_post_meta($id, '_nvr_lead_source', true) ?: '—');
	}
}, 10, 2);
