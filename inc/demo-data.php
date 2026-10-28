<?php
/**
 * Sample (demo) content. Idempotent: every item has a `_nvr_demo_key`; re-running updates instead of duplicating.
 * All figures/addresses are ILLUSTRATIVE and must be verified/replaced before real publication.
 */
if (!defined('ABSPATH')) exit;

/**
 * Create or update one demo post.
 * @return array [post_id, created(bool)]
 */
function nvr_demo_upsert($post_type, $key, $title, $content, $meta = [], $extra = []) {
	global $wpdb;
	$id = (int) $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_nvr_demo_key' AND meta_value=%s LIMIT 1", $key));
	if ($id && get_post_type($id) !== $post_type) $id = 0;
	if (!$id && !empty($extra['legacy_titles'])) {
		foreach ((array) $extra['legacy_titles'] as $t) {
			$id = nvr_find_post_by_title($t, $post_type);
			if ($id) break;
		}
	}
	if (!$id) $id = nvr_find_post_by_title($title, $post_type);

	$postarr = ['post_type' => $post_type, 'post_title' => $title, 'post_content' => $content, 'post_status' => 'publish'];
	if (!empty($extra['slug'])) $postarr['post_name'] = $extra['slug'];
	if (!empty($extra['excerpt'])) $postarr['post_excerpt'] = $extra['excerpt'];
	if (!empty($extra['date'])) { $postarr['post_date'] = $extra['date']; $postarr['post_date_gmt'] = get_gmt_from_date($extra['date']); }

	$created = false;
	if ($id) {
		$postarr['ID'] = $id;
		wp_update_post($postarr);
	} else {
		$id = wp_insert_post($postarr);
		$created = true;
	}
	if (!$id || is_wp_error($id)) return [0, false];
	update_post_meta($id, '_nvr_demo_key', $key);
	foreach ($meta as $k => $v) update_post_meta($id, '_nvr_' . $k, is_scalar($v) ? (string) $v : '');
	return [(int) $id, $created];
}

function nvr_demo_terms($post_id, $taxonomy, $slugs, $names = []) {
	$ids = [];
	foreach ((array) $slugs as $slug) {
		$t = get_term_by('slug', $slug, $taxonomy);
		if (!$t) {
			$r = wp_insert_term($names[$slug] ?? $slug, $taxonomy, ['slug' => $slug]);
			if (!is_wp_error($r)) $ids[] = (int) $r['term_id'];
		} else {
			$ids[] = (int) $t->term_id;
		}
	}
	if ($ids) wp_set_object_terms($post_id, $ids, $taxonomy, false);
}

function nvr_demo_paragraphs($arr) {
	return implode("\n\n", array_map(function ($p) { return '<p>' . $p . '</p>'; }, $arr));
}

function nvr_demo_properties() {
	// title, type, address, area, beds, baths, price text, price number, deal, direction, lat, lng, location slugs, highlight, project
	return [
		['The Urban Signature', 'can-ho', 'Nguyễn Hữu Cảnh, Phường 22, Bình Thạnh', '70', 2, 2, '5.2 Tỷ', 5200000000, 'sale', 'Đông Nam', 10.7944, 106.7218, ['binh-thanh'], 'Căn hộ nằm trên trục Nguyễn Hữu Cảnh, thuận tiện di chuyển vào trung tâm Quận 1 và sang khu Thảo Điền; xung quanh có siêu thị, trường học và công viên ven sông.', ''],
		['The Lumière Riverside', 'can-ho', 'Nguyễn Hữu Cảnh, Phường 22, Bình Thạnh', '85', 2, 2, '6.5 Tỷ', 6500000000, 'sale', 'Đông', 10.8001, 106.7210, ['binh-thanh'], 'Căn hộ diện tích rộng rãi, phòng khách thoáng và ban công đón gió từ hướng sông; phù hợp gia đình trẻ cần không gian sống yên tĩnh nhưng vẫn gần trung tâm.', ''],
		['Villa Garden Residence', 'nha-pho', 'Nguyễn Văn Hưởng, Phường Thảo Điền, TP. Thủ Đức', '120', 3, 3, '18 Tỷ', 18000000000, 'sale', 'Nam', 10.8062, 106.7340, ['thu-duc', 'quan-2'], 'Nhà phố trong khu Thảo Điền, nơi tập trung nhiều nhà hàng, quán cà phê, trường quốc tế và cộng đồng cư dân đa quốc gia; có sân vườn nhỏ và chỗ đậu ô tô.', ''],
		['Sunrise Riverside View Sông', 'can-ho', 'Nguyễn Hữu Thọ, Phước Kiển, Nhà Bè', '69.1', 2, 2, '3.8 Tỷ', 3800000000, 'sale', 'Nam', 10.7014, 106.7012, ['nha-be'], 'Căn hộ view sông trong khu vực Phước Kiển, kết nối nhanh với Phú Mỹ Hưng và trung tâm Quận 7 qua trục Nguyễn Hữu Thọ; không gian sống xanh và thoáng.', 'Sunrise Riverside'],
		['Vinhomes Grand Park Studio', 'can-ho', 'Nguyễn Xiển, Long Thạnh Mỹ, TP. Thủ Đức', '35', 1, 1, '1.2 Tỷ', 1200000000, 'sale', 'Đông', 10.8415, 106.8290, ['thu-duc'], 'Căn studio nhỏ gọn trong đại đô thị Vinhomes Grand Park, phù hợp người độc thân hoặc đầu tư cho thuê; nội khu có công viên, hồ bơi, trường học và trung tâm thương mại.', 'Vinhomes Grand Park'],
		['Masteri Thảo Điền', 'can-ho', 'Xa lộ Hà Nội, Thảo Điền, TP. Thủ Đức', '65', 2, 2, '4.5 Tỷ', 4500000000, 'sale', 'Đông Nam', 10.8028, 106.7340, ['thu-duc', 'quan-2'], 'Căn hộ cao tầng ngay trục Xa lộ Hà Nội, gần ga metro số 1 và khu Thảo Điền sôi động; thiết kế 2 phòng ngủ hợp lý, đón sáng tự nhiên tốt.', 'Masteri Thảo Điền'],
		['Nhà phố Him Lam Kênh Tẻ', 'nha-pho', 'Nguyễn Hữu Thọ, Tân Hưng, Quận 7', '100', 3, 4, '15 Tỷ', 15000000000, 'sale', 'Nam', 10.7400, 106.7050, ['quan-7'], 'Nhà phố 3 phòng ngủ, 4 phòng tắm trong khu dân cư Him Lam Kênh Tẻ, mặt tiền gần trục Nguyễn Hữu Thọ; thuận tiện ở kết hợp kinh doanh nhỏ.', ''],
		['Căn hộ Q7 Boulevard', 'can-ho', 'Nguyễn Lương Bằng, Phú Mỹ Hưng, Quận 7', '90', 3, 2, '7.2 Tỷ', 7200000000, 'sale', 'Đông Nam', 10.7248, 106.7215, ['quan-7'], 'Căn hộ 3 phòng ngủ tại Phú Mỹ Hưng - khu đô thị quy hoạch bài bản, nhiều cây xanh, gần trường quốc tế, bệnh viện và trung tâm thương mại.', ''],
		['Biệt thự Vinhomes Central Park', 'biet-thu', 'Nguyễn Hữu Cảnh, Phường 22, Bình Thạnh', '180', 4, 5, '35 Tỷ', 35000000000, 'sale', 'Đông', 10.7956, 106.7214, ['binh-thanh'], 'Biệt thự 4 phòng ngủ, 5 phòng tắm trong khu đô thị ven sông với công viên rộng, an ninh 24/7 và tiện ích nội khu đầy đủ.', ''],
		['Cho thuê Studio Quận 1', 'can-ho', 'Lê Thánh Tôn, Phường Bến Thành, Quận 1', '40', 1, 1, '15 Triệu/tháng', 15000000, 'rent', 'Đông', 10.7735, 106.6980, ['quan-1'], 'Studio ngay trung tâm Quận 1, đi bộ đến chợ Bến Thành và phố đi bộ Nguyễn Huệ; thích hợp chuyên gia hoặc cặp đôi cần thuê dài hạn.', ''],
		['Cho thuê Căn hộ Sunrise City', 'can-ho', 'Nguyễn Hữu Thọ, Tân Hưng, Quận 7', '75', 2, 2, '18 Triệu/tháng', 18000000, 'rent', 'Nam', 10.7410, 106.7015, ['quan-7'], 'Căn hộ 2 phòng ngủ cho thuê tại Quận 7, gần siêu thị, trường học và các tuyến đường lớn; có thể thuê theo hợp đồng 6 - 12 tháng.', ''],
		['Đất nền Nhà Bè', 'dat-nen', 'Lê Văn Lương, Nhơn Đức, Nhà Bè', '100', '', '', '4.5 Tỷ', 4500000000, 'sale', 'Bắc', 10.6505, 106.7010, ['nha-be'], 'Lô đất 100 m² tại Nhơn Đức, Nhà Bè, mặt đường Lê Văn Lương; phù hợp xây nhà ở hoặc giữ đất đầu tư dài hạn khi hạ tầng khu Nam Sài Gòn tiếp tục hoàn thiện.', ''],
		['Penthouse Thủ Thiêm', 'can-ho', 'Mai Chí Thọ, An Phú, TP. Thủ Đức', '150', 3, 3, '25 Tỷ', 25000000000, 'sale', 'Đông Nam', 10.7802, 106.7460, ['thu-duc', 'quan-2'], 'Penthouse 150 m² trên trục Mai Chí Thọ, tầm nhìn rộng ra khu Thủ Thiêm và sông Sài Gòn; thiết kế 3 phòng ngủ riêng tư với phòng khách tầng cao.', ''],
		['Nhà phố Quận 7', 'nha-pho', 'Huỳnh Tấn Phát, Phú Thuận, Quận 7', '80', 2, 2, '8.5 Tỷ', 8500000000, 'sale', 'Nam', 10.7210, 106.7380, ['quan-7'], 'Nhà phố 2 phòng ngủ trên trục Huỳnh Tấn Phát, kết nối Quận 7 với trung tâm thành phố và khu Nhà Bè; phù hợp ở hoặc làm văn phòng nhỏ.', ''],
		['Cho thuê Officetel Quận 2', 'van-phong', 'Song Hành, An Phú, TP. Thủ Đức', '45', 1, 1, '12 Triệu/tháng', 12000000, 'rent', 'Đông', 10.8020, 106.7465, ['thu-duc', 'quan-2'], 'Officetel 45 m² khu An Phú, vừa làm văn phòng vừa có thể ở; gần ga metro, nhiều quán ăn và dịch vụ tiện ích xung quanh.', ''],
	];
}

function nvr_demo_amenities($type) {
	switch ($type) {
		case 'nha-pho': case 'biet-thu': return 'Khu dân cư an ninh, Chỗ đậu ô tô, Gần trường học, Gần chợ - siêu thị, Công viên';
		case 'dat-nen': return 'Đường nhựa, Điện nước đầy đủ, Gần trường học, Khu dân cư hiện hữu';
		case 'van-phong': return 'Lễ tân, Bảo vệ 24/7, Chỗ đậu xe, Thang máy, Điều hòa trung tâm';
		default: return 'Hồ bơi, Phòng gym, Công viên nội khu, Bảo vệ 24/7, Chỗ đậu xe, Siêu thị tiện lợi';
	}
}

function nvr_demo_group($type) {
	return ['nha-pho' => 'house', 'biet-thu' => 'house', 'dat-nen' => 'land', 'van-phong' => 'office'][$type] ?? 'apartment';
}

function nvr_import_demo_content() {
	$new = 0;
	$legacy_titles = ['Căn hộ 2PN Sunrise Riverside', 'Nhà phố khu dân cư Jamona City', 'Căn hộ Vinhomes Grand Park', 'Căn hộ Eaton Park 2 phòng ngủ', 'Nhà phố Thủ Đức gần tiện ích', 'Đất nền khu dân cư hiện hữu', 'Căn hộ view sông Quận 7', 'Biệt thự compound ngoại khu', 'Căn hộ Sunrise Riverside cho thuê', 'Căn hộ Vinhomes Grand Park cho thuê', 'Nhà phố nguyên căn Quận 7', 'Căn hộ 1PN gần trung tâm', 'Căn hộ view sông Quận 7 cho thuê', 'Biệt thự compound cho thuê'];
	nvr_seed_terms();

	// 1) Hide the old placeholder listings/projects from earlier versions (only exact legacy titles, never real posts with demo key).
	foreach ($legacy_titles as $t) {
		$old = nvr_find_post_by_title($t, 'property');
		if ($old && !get_post_meta($old, '_nvr_demo_key', true) && get_post_status($old) === 'publish') wp_update_post(['ID' => $old, 'post_status' => 'draft']);
	}
	foreach (['Palm River', 'Eaton Park', 'Libera Nha Trang'] as $t) {
		$old = nvr_find_post_by_title($t, 'project');
		if ($old && !get_post_meta($old, '_nvr_demo_key', true) && get_post_status($old) === 'publish') wp_update_post(['ID' => $old, 'post_status' => 'draft']);
	}

	// 2) Projects (create first so properties can link to them)
	$projects = [
		['Vinhomes Grand Park', 'vinhomes-grand-park', '50 Triệu/m²', 'Đang mở bán', 'TP. Thủ Đức, TP. Hồ Chí Minh', 'Nguyễn Xiển, Long Thạnh Mỹ, TP. Thủ Đức', 'Vingroup', 'Đại đô thị quy mô hơn 270 ha', 10.8415, 106.8290, 'apartment', 0,
			['Vinhomes Grand Park là đại đô thị phía Đông TP. Hồ Chí Minh do Vingroup phát triển, gồm nhiều phân khu căn hộ, nhà phố và biệt thự.', 'Nội khu có công viên trung tâm, hồ bơi, trường học, trung tâm thương mại và hệ thống tiện ích phục vụ nhu cầu sinh sống hằng ngày. Dự án kết nối với trung tâm thành phố qua Xa lộ Hà Nội, đường Nguyễn Xiển và tuyến metro số 1.'], 'Công viên trung tâm, Hồ bơi, Trường học nội khu, Trung tâm thương mại, Phòng gym, Khu vui chơi trẻ em'],
		['Masteri Thảo Điền', 'masteri-thao-dien', '80 Triệu/m²', 'Đang mở bán', 'TP. Thủ Đức, TP. Hồ Chí Minh', 'Xa lộ Hà Nội, Thảo Điền, TP. Thủ Đức', 'Masterise Homes', 'Nhiều tòa căn hộ cao tầng', 10.8028, 106.7340, 'apartment', 3,
			['Masteri Thảo Điền là quần thể căn hộ cao tầng nằm ngay Xa lộ Hà Nội, thuộc khu Thảo Điền - nơi tập trung cộng đồng cư dân trong và ngoài nước.', 'Dự án có tiện ích nội khu như hồ bơi, phòng gym, khu thương mại và vị trí gần ga metro số 1, thuận tiện di chuyển về trung tâm Quận 1.'], 'Hồ bơi, Phòng gym, Khu thương mại, Công viên, Bảo vệ 24/7, Gần ga metro'],
		['Sunrise Riverside', 'sunrise-riverside', '45 Triệu/m²', 'Sắp mở bán', 'Nhà Bè, TP. Hồ Chí Minh', 'Nguyễn Hữu Thọ, Phước Kiển, Nhà Bè', 'Novaland', 'Khu căn hộ cao tầng ven sông', 10.7014, 106.7012, 'apartment', 6,
			['Sunrise Riverside là khu căn hộ ven sông tại Phước Kiển, Nhà Bè do Novaland phát triển, hướng đến cư dân cần không gian sống xanh nhưng vẫn kết nối nhanh với Quận 7.', 'Dự án thừa hưởng hạ tầng trục Nguyễn Hữu Thọ, gần Phú Mỹ Hưng, trường học, bệnh viện và các trung tâm thương mại lớn.'], 'Hồ bơi, Công viên ven sông, Phòng gym, Khu BBQ, Trường mầm non, Siêu thị'],
	];
	// Three additional curated demo projects so the project archive and homepage are populated on first import.
	$projects = array_merge($projects, [
		['The Global City', 'the-global-city', 'Giá tham khảo theo phân khu', 'Đang phát triển', 'TP. Thủ Đức, TP. Hồ Chí Minh', 'Đỗ Xuân Hợp, An Phú, TP. Thủ Đức', 'Masterise Homes', 'Khu đô thị quy mô lớn', 10.8030, 106.7500, 'city', 1, ['Khu đô thị được giới thiệu với định hướng phát triển không gian sống, thương mại và tiện ích đô thị. Nội dung trong website là dữ liệu minh họa để người dùng trải nghiệm danh mục.', 'Vui lòng xác minh tiến độ, giá và pháp lý từ chủ đầu tư trước khi ra quyết định.'], 'Không gian công cộng, thương mại, cảnh quan, tiện ích đô thị'],
		['Vinhomes Central Park', 'vinhomes-central-park', 'Giá tham khảo theo căn', 'Đang hoạt động', 'Bình Thạnh, TP. Hồ Chí Minh', 'Nguyễn Hữu Cảnh, Bình Thạnh', 'Vingroup', 'Khu đô thị ven sông', 10.7956, 106.7214, 'apartment', 2, ['Khu đô thị ven sông tại Bình Thạnh với công viên và nhiều loại hình nhà ở. Trang này sử dụng nội dung mẫu để hoàn thiện giao diện và điều hướng.', 'Thông tin căn hộ, giá bán và tình trạng giao dịch cần được xác minh theo từng sản phẩm cụ thể.'], 'Công viên, cảnh quan ven sông, khu thương mại, tiện ích nội khu'],
		['Phú Mỹ Hưng', 'phu-my-hung', 'Giá tham khảo theo sản phẩm', 'Đang hoạt động', 'Quận 7, TP. Hồ Chí Minh', 'Nguyễn Văn Linh, Tân Phú, Quận 7', 'Phú Mỹ Hưng', 'Khu đô thị quy hoạch', 10.7295, 106.7180, 'city', 3, ['Khu đô thị tại khu Nam TP. Hồ Chí Minh với hệ thống nhà ở, thương mại và tiện ích. Nội dung được dùng làm dữ liệu mẫu trình bày dự án.', 'Hãy đối chiếu thông tin pháp lý, giá và tình trạng từng sản phẩm với nguồn chính thức trước giao dịch.'], 'Công viên, trường học, thương mại, dịch vụ, không gian công cộng'],
	]);
	foreach ($projects as $p) {
		[$title, $slug, $price, $status, $area, $loc, $dev, $scale, $lat, $lng, $grp, $off, $paras, $amen] = $p;
		[$id, $c] = nvr_demo_upsert('project', 'project-' . $slug, $title, nvr_demo_paragraphs($paras), [
			'project_price' => $price, 'project_status_text' => $status, 'project_area' => $area, 'project_location' => $loc,
			'project_developer' => $dev, 'project_scale' => $scale, 'project_amenities' => $amen, 'featured' => 'yes',
			'latitude' => $lat, 'longitude' => $lng, 'gallery_images' => implode("\n", nvr_pool_urls($grp, 5, $off)),
		], ['slug' => $slug, 'date' => gmdate('Y-m-d H:i:s', time() - 3600 - array_search($p, $projects) * 600)]);
		if ($c) $new++;
	}

	// 3) Properties
	foreach (nvr_demo_properties() as $i => $p) {
		[$title, $type, $address, $area, $beds, $baths, $price, $pnum, $deal, $dir, $lat, $lng, $locs, $highlight, $project] = $p;
		$type_names = ['can-ho' => 'căn hộ', 'nha-pho' => 'nhà phố', 'biet-thu' => 'biệt thự', 'dat-nen' => 'lô đất', 'van-phong' => 'officetel'];
		$tn = $type_names[$type];
		$intro = $title . ' là ' . $tn . ($deal === 'rent' ? ' cho thuê' : ' đang được rao bán') . ' có diện tích ' . $area . ' m²';
		if ($beds !== '') $intro .= ', ' . $beds . ' phòng ngủ, ' . $baths . ' phòng tắm';
		$intro .= ', tọa lạc tại ' . $address . '. Mức giá ' . ($deal === 'rent' ? 'thuê' : 'bán') . ': ' . $price . '.';
		$content = nvr_demo_paragraphs([
			esc_html($intro),
			esc_html($highlight),
			'Đây là dữ liệu minh họa giao diện. Giá, tình trạng giao dịch, địa chỉ chi tiết và hồ sơ pháp lý cần được xác minh trước khi công bố hoặc giao dịch. Liên hệ NhàViệt để được hướng dẫn các bước kiểm tra thông tin.',
		]);
		$meta = [
			'price' => $price, 'price_numeric' => $pnum, 'area' => $area, 'area_numeric' => (float) $area,
			'bedrooms' => $beds, 'bathrooms' => $baths, 'address' => $address, 'deal_type' => $deal,
			'status' => $deal === 'rent' ? 'Đang cho thuê' : 'Đang bán', 'direction' => $dir,
			'legal' => $deal === 'rent' ? 'Theo hợp đồng thuê' : 'Cần xác minh hồ sơ',
			'code' => 'NVR-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT), 'featured' => $i < 12 ? 'yes' : '',
			'amenities' => nvr_demo_amenities($type), 'latitude' => $lat, 'longitude' => $lng,
			'gallery_images' => implode("\n", nvr_pool_urls(nvr_demo_group($type), 5, $i * 3)),
		];
		if ($project) {
			$pid = nvr_find_post_by_title($project, 'project');
			if ($pid) $meta['project_id'] = $pid;
		}
		[$id, $c] = nvr_demo_upsert('property', 'property-' . ($i + 1), $title, $content, $meta, ['date' => gmdate('Y-m-d H:i:s', time() - 3600 - $i * 600)]);
		if (!$id) continue;
		if ($c) $new++;
		nvr_demo_terms($id, 'property_type', [$type], ['can-ho' => 'Căn hộ', 'nha-pho' => 'Nhà phố', 'biet-thu' => 'Biệt thự', 'dat-nen' => 'Đất nền', 'van-phong' => 'Văn phòng']);
		nvr_demo_terms($id, 'property_location', $locs, ['quan-1' => 'Quận 1', 'quan-2' => 'Quận 2', 'quan-7' => 'Quận 7', 'thu-duc' => 'TP. Thủ Đức', 'binh-thanh' => 'Bình Thạnh', 'nha-be' => 'Nhà Bè']);
	}

	// 4) Developers
	$devs = [
		['Vingroup', 'https://vingroup.net', 'Tập đoàn đa ngành của Việt Nam, phát triển các thương hiệu bất động sản như Vinhomes, Vincom và nhiều đại đô thị tại các thành phố lớn.'],
		['Novaland', 'https://novaland.com.vn', 'Công ty phát triển bất động sản nhà ở, khu đô thị và nghỉ dưỡng; tại TP. Hồ Chí Minh nổi bật với các dự án khu Nam Sài Gòn và Thủ Đức.'],
		['Masterise Homes', 'https://masterisehomes.com', 'Thành viên Masterise Group, tập trung phân khúc căn hộ cao cấp với các dự án như Masteri Thảo Điền và Masteri An Phú.'],
		['Keppel Land', '', 'Đơn vị bất động sản thuộc Tập đoàn Keppel (Singapore), phát triển các dự án nhà ở và thương mại tại Việt Nam, trong đó có TP. Hồ Chí Minh.'],
		['Nam Long', 'https://namlongcorp.com.vn', 'Nam Long Group phát triển các khu đô thị và căn hộ quy mô lớn tại TP. Hồ Chí Minh và các tỉnh phía Nam.'],
		['Khang Điền', 'https://khangdien.com.vn', 'Doanh nghiệp bất động sản phát triển khu dân cư, nhà phố và căn hộ tại TP. Hồ Chí Minh và vùng lân cận.'],
		['Gamuda Land', 'https://gamudaland.com.vn', 'Thành viên Tập đoàn Gamuda (Malaysia), phát triển các khu đô thị tích hợp tại Việt Nam.'],
		['CapitaLand', '', 'Tập đoàn bất động sản đa quốc gia có trụ sở tại Singapore, hoạt động tại Việt Nam với nhiều dự án nhà ở và thương mại.'],
	];
	foreach ($devs as $i => $d) {
		[, $c] = nvr_demo_upsert('developer', 'developer-' . sanitize_title($d[0]), $d[0], '<p>' . esc_html($d[2]) . '</p><p>Thông tin mang tính tham khảo, vui lòng đối chiếu với nguồn chính thức của chủ đầu tư trước khi giao dịch.</p>', ['developer_url' => $d[1]]);
		if ($c) $new++;
	}

	// 5) Neighborhoods
	$new += nvr_import_neighborhoods();

	// 6) Blog posts
	$new += nvr_import_posts();

	// 7) System pages + reading settings
	$before = count(array_filter(array_map('get_page_by_path', array_keys(nvr_system_pages()))));
	nvr_configure_reading(nvr_ensure_pages());
	$new += count(nvr_system_pages()) - $before;

	return $new;
}

function nvr_import_neighborhoods() {
	$new = 0;
	$hoods = [
		[
			'slug' => 'jamona-city', 'title' => 'Jamona City', 'legacy' => ['Jamona City - Quận 7'], 'district' => 'Quận 7', 'term' => 'quan-7', 'lat' => '10.7180', 'lng' => '106.7290', 'img' => 'house', 'off' => 0,
			'intro' => ['Jamona City là khu dân cư nằm ở phía Nam Quận 7, khu vực trục Đào Trí - Huỳnh Tấn Phát, nổi bật với không gian sống yên tĩnh, nhiều cây xanh và các dãy nhà phố, biệt thự liên kế.', 'Khu vực phù hợp gia đình muốn ở gần trung tâm Quận 7 và Phú Mỹ Hưng nhưng ưu tiên môi trường sống thoáng đãng, an ninh.'],
			'location' => 'Từ Jamona City có thể di chuyển ra đường Huỳnh Tấn Phát, Nguyễn Hữu Thọ và Nguyễn Văn Linh để kết nối Phú Mỹ Hưng, trung tâm Quận 7 và các tuyến đi Nhà Bè, Quận 4, Quận 1.',
			'amenities' => 'Công viên và đường dạo nội khu, trường học, siêu thị - cửa hàng tiện lợi, quán cà phê, khu thể thao và các tiện ích thương mại dọc các trục đường lớn lân cận.',
			'subareas' => 'Khu nhà phố liên kế, khu biệt thự, khu tiện ích - thương mại và các lô góc mặt tiền đường chính.',
			'market' => 'Giá nhà đất tại khu vực phụ thuộc vị trí (mặt tiền hay trong hẻm), hướng, diện tích và pháp lý từng căn. Nên so sánh ít nhất 3 tin đăng cùng khu và xem thực tế trước khi quyết định.',
		],
		[
			'slug' => 'hung-gia', 'title' => 'Hưng Gia', 'legacy' => ['Hưng Gia - Quận 7'], 'district' => 'Quận 7', 'term' => 'quan-7', 'lat' => '10.7290', 'lng' => '106.7100', 'img' => 'house', 'off' => 3,
			'intro' => ['Hưng Gia là khu dân cư nhà phố nằm trong khu vực Phú Mỹ Hưng, Quận 7, được nhiều gia đình lựa chọn nhờ vị trí gần Nguyễn Văn Linh, hạ tầng đồng bộ và môi trường sống xanh.', 'Các căn nhà phố tại đây thường có diện tích vừa phải, thiết kế 3 - 4 tầng, phù hợp ở lâu dài hoặc kết hợp kinh doanh nhỏ.'],
			'location' => 'Khu dân cư kết nối thuận tiện ra đại lộ Nguyễn Văn Linh, Nguyễn Lương Bằng, Nguyễn Đức Cảnh; di chuyển nhanh đến trung tâm Phú Mỹ Hưng, Quận 1 và khu Nam Sài Gòn.',
			'amenities' => 'Đường nội khu rộng, công viên - mảng xanh, trường học các cấp, bệnh viện, siêu thị và nhiều quán ăn, quán cà phê trong bán kính vài phút di chuyển.',
			'subareas' => 'Các dãy nhà phố liên kế, khu thương mại dịch vụ và khu công viên cây xanh nội khu.',
			'market' => 'Thị trường nhà phố khu Phú Mỹ Hưng và lân cận thường ổn định nhờ nhu cầu ở thực cao. Giá chênh lệch tùy vị trí lô góc, mặt tiền, tình trạng nhà và pháp lý; cần kiểm tra giấy tờ kỹ trước khi đặt cọc.',
		],
		[
			'slug' => 'sala-dai-quang-minh', 'title' => 'Sala Đại Quang Minh', 'legacy' => ['Sala Đại Quang Minh - Quận 2'], 'district' => 'Quận 2 (TP. Thủ Đức)', 'term' => 'thu-duc', 'lat' => '10.7720', 'lng' => '106.7230', 'img' => 'city', 'off' => 0,
			'intro' => ['Khu đô thị Sala do Đại Quang Minh phát triển, nằm tại phường An Lợi Đông, khu vực Thủ Thiêm (Quận 2 cũ, nay thuộc TP. Thủ Đức), đối diện trung tâm Quận 1 qua sông Sài Gòn.', 'Khu đô thị có nhiều phân khu như Sarimi, Sarica, Sadora, Sala Crown với các loại hình căn hộ, nhà phố và biệt thự, phù hợp cả nhu cầu ở và đầu tư.'],
			'location' => 'Từ Sala có thể sang Quận 1 qua hầm Thủ Thiêm, cầu Thủ Thiêm, cầu Ba Son; kết nối đường Mai Chí Thọ về Xa lộ Hà Nội, cao tốc TP. Hồ Chí Minh - Long Thành - Dầu Giây.',
			'amenities' => 'Công viên, đường dạo ven kênh, trường học quốc tế, trung tâm thương mại, nhà hàng - quán cà phê, khu thể thao và hệ thống an ninh quản lý khép kín.',
			'subareas' => 'Sarimi, Sarica, Sadora, Sala Crown và các khu nhà phố, biệt thự, căn hộ trong khu đô thị.',
			'market' => 'Sala nằm trong khu vực Thủ Thiêm nên giá thường cao hơn mặt bằng chung của TP. Thủ Đức. Mức giá tùy phân khu, loại hình và tầm nhìn; nên đối chiếu nhiều nguồn và xem pháp lý từng căn.',
		],
		[
			'slug' => 'thu-thiem', 'title' => 'Thủ Thiêm', 'legacy' => ['Thủ Thiêm - TP. Thủ Đức'], 'district' => 'TP. Thủ Đức', 'term' => 'thu-duc', 'lat' => '10.7770', 'lng' => '106.7210', 'img' => 'city', 'off' => 2,
			'intro' => ['Khu đô thị mới Thủ Thiêm có quy mô khoảng 657 ha, nằm bên kia sông Sài Gòn, đối diện trung tâm Quận 1. Đây là khu vực được quy hoạch để trở thành trung tâm tài chính - dịch vụ mới của thành phố.', 'Khu vực gồm các phường An Khánh, An Lợi Đông, Thạnh Mỹ Lợi, Bình Khánh, Bình Trưng... với nhiều dự án căn hộ, nhà phố, văn phòng và công trình công cộng.'],
			'location' => 'Thủ Thiêm kết nối Quận 1 qua hầm Thủ Thiêm, cầu Thủ Thiêm, cầu Ba Son và cầu Thủ Thiêm 2; trục Mai Chí Thọ và Xa lộ Hà Nội đi về phía Đông, cao tốc TP. Hồ Chí Minh - Long Thành - Dầu Giây.',
			'amenities' => 'Công viên ven sông, quảng trường trung tâm, bệnh viện, trường học, trung tâm thương mại và các công trình văn hóa - dịch vụ theo quy hoạch.',
			'subareas' => 'Khu trung tâm tài chính - thương mại, khu nhà ở cao tầng, khu nhà phố - biệt thự và khu công viên ven sông.',
			'market' => 'Thủ Thiêm thuộc nhóm khu vực có giá cao tại TP. Thủ Đức, đồng thời tiếp tục thay đổi theo tiến độ hạ tầng và quy hoạch. Người mua nên cập nhật thông tin quy hoạch mới nhất và kiểm tra kỹ pháp lý.',
		],
	];
	foreach ($hoods as $h) {
		[$id, $c] = nvr_demo_upsert('neighborhood', 'hood-' . $h['slug'], $h['title'], nvr_demo_paragraphs($h['intro']), [
			'neighborhood_district' => $h['district'], 'neighborhood_city' => 'TP. Hồ Chí Minh', 'neighborhood_term' => $h['term'],
			'neighborhood_location' => $h['location'], 'neighborhood_amenities' => $h['amenities'],
			'neighborhood_subareas' => $h['subareas'], 'neighborhood_market' => $h['market'],
			'latitude' => $h['lat'], 'longitude' => $h['lng'],
			'gallery_images' => implode("\n", nvr_pool_urls($h['img'], 5, $h['off'])),
		], ['slug' => $h['slug'], 'legacy_titles' => $h['legacy']]);
		if ($c) $new++;
	}
	return $new;
}

function nvr_import_posts() {
	$new = 0;
	$cats = ['thong-tin-nha-dat' => 'Thông tin nhà đất', 'tin-tuc-kien-truc' => 'Tin tức / Kiến trúc'];
	$cat_ids = [];
	foreach ($cats as $slug => $name) {
		$t = get_term_by('slug', $slug, 'category');
		if (!$t) { $r = wp_insert_term($name, 'category', ['slug' => $slug]); $cat_ids[$slug] = is_wp_error($r) ? 0 : $r['term_id']; }
		else $cat_ids[$slug] = $t->term_id;
	}
	$posts = [
		['kinh-nghiem-mua-can-ho-lan-dau', 'Kinh nghiệm mua căn hộ lần đầu', 'thong-tin-nha-dat', 'apartment', 0,
			'Những bước cần làm để mua căn hộ lần đầu an toàn: xác định ngân sách, kiểm tra pháp lý, so sánh khu vực và tính toán chi phí phát sinh.',
			['Mua căn hộ lần đầu là quyết định tài chính lớn. Trước khi đi xem nhà, hãy xác định rõ ngân sách tối đa bao gồm vốn tự có, khoản vay dự kiến và các chi phí đi kèm như phí bảo trì, phí quản lý, thuế và lệ phí trước bạ.', 'Bước tiếp theo là chọn khu vực. Hãy cân nhắc quãng đường di chuyển đến nơi làm việc, trường học, bệnh viện và các tiện ích cần thiết. Đi xem căn hộ vào nhiều khung giờ khác nhau để đánh giá tiếng ồn, ánh sáng và tình trạng giao thông.', 'Cuối cùng, kiểm tra kỹ pháp lý: giấy phép xây dựng, tình trạng sổ hồng, hợp đồng mua bán và tiến độ thanh toán. Nên nhờ luật sư hoặc người có kinh nghiệm xem hợp đồng trước khi đặt cọc.']],
		['bang-gia-dat-quan-7-nhung-dieu-can-biet', 'Bảng giá đất Quận 7: những điều cần biết', 'thong-tin-nha-dat', 'house', 1,
			'Giải thích khác biệt giữa bảng giá đất nhà nước và giá thị trường, cách tra cứu và lưu ý khi định giá nhà đất tại Quận 7.',
			['Bảng giá đất do UBND thành phố ban hành dùng để tính thuế, phí và nghĩa vụ tài chính khi chuyển nhượng; không phải giá giao dịch thực tế trên thị trường. Giá thị trường thường cao hơn và thay đổi theo vị trí, hạ tầng và thời điểm.', 'Khi tìm hiểu giá đất tại Quận 7, hãy phân biệt các khu vực: trục Nguyễn Văn Linh, Phú Mỹ Hưng, Huỳnh Tấn Phát, Nguyễn Hữu Thọ... Mỗi khu có mặt bằng giá và nhu cầu khác nhau.', 'Để định giá chính xác hơn, bạn nên so sánh các tin đăng tương tự trong cùng khu vực, hỏi thêm môi giới địa phương và tham khảo giao dịch gần nhất.']],
		['checklist-phap-ly-truoc-khi-dat-coc-nha-dat', 'Checklist pháp lý trước khi đặt cọc nhà đất', 'thong-tin-nha-dat', 'land', 0,
			'Danh sách các giấy tờ và thông tin cần kiểm tra trước khi đặt cọc mua nhà đất để tránh rủi ro tranh chấp, quy hoạch.',
			['Trước khi đặt cọc, hãy yêu cầu bên bán cung cấp bản gốc giấy chứng nhận quyền sử dụng đất, quyền sở hữu nhà ở và tài sản gắn liền với đất. Đối chiếu thông tin chủ sở hữu với giấy tờ tùy thân.', 'Kiểm tra tình trạng quy hoạch tại cơ quan có thẩm quyền, xác nhận tài sản không bị thế chấp, kê biên hay đang có tranh chấp. Với căn hộ, cần xem thêm giấy phép xây dựng, hợp đồng mua bán và biên bản bàn giao.', 'Mọi thỏa thuận đặt cọc nên được lập thành văn bản, ghi rõ số tiền, thời hạn, điều kiện hoàn cọc và trách nhiệm của mỗi bên.']],
		['xu-huong-thiet-ke-can-ho-hien-dai', 'Xu hướng thiết kế căn hộ hiện đại', 'tin-tuc-kien-truc', 'apartment', 4,
			'Những xu hướng thiết kế nội thất căn hộ được ưa chuộng: không gian mở, vật liệu tự nhiên, lưu trữ thông minh và ánh sáng.',
			['Căn hộ hiện đại ưu tiên không gian mở, kết nối phòng khách, bếp và khu ăn uống thành một thể thống nhất để tạo cảm giác rộng rãi, đặc biệt với căn hộ diện tích vừa và nhỏ.', 'Vật liệu tự nhiên như gỗ, đá và vải lanh, kết hợp tông màu trung tính, giúp không gian ấm áp và dễ phối đồ. Hệ thống lưu trữ âm tường, giường kết hợp tủ và bàn gấp giúp tận dụng từng centimet.', 'Ánh sáng nhiều lớp - đèn trần, đèn hắt, đèn bàn - cho phép thay đổi không khí phòng theo từng thời điểm trong ngày.']],
		['toi-uu-khong-gian-song-cho-gia-dinh-tre', 'Tối ưu không gian sống cho gia đình trẻ', 'tin-tuc-kien-truc', 'house', 3,
			'Gợi ý cách bố trí căn hộ, nhà phố nhỏ cho gia đình trẻ có con: phân khu rõ ràng, an toàn, dễ vệ sinh và linh hoạt.',
			['Gia đình trẻ thường cần không gian vừa để sinh hoạt chung vừa để làm việc tại nhà. Hãy chia khu chức năng rõ ràng, dùng vách ngăn nhẹ hoặc kệ mở để tách khu làm việc khỏi khu vui chơi.', 'Chọn nội thất bo tròn góc, bề mặt dễ lau chùi và ổ điện có nắp che để an toàn cho trẻ. Tủ lưu trữ cao giúp cất đồ chơi gọn gàng và giữ sàn trống.', 'Ưu tiên ánh sáng và thông gió tự nhiên, vì đây là yếu tố ảnh hưởng lớn đến sức khỏe và tâm trạng cả nhà.']],
		['nhung-tien-ich-nen-co-trong-khu-do-thi', 'Những tiện ích nên có trong khu đô thị', 'tin-tuc-kien-truc', 'city', 0,
			'Các nhóm tiện ích quan trọng khi chọn khu đô thị: giáo dục, y tế, thương mại, công viên, giao thông và an ninh.',
			['Một khu đô thị đáng sống cần đáp ứng nhu cầu hằng ngày trong bán kính đi bộ hoặc vài phút di chuyển: trường học, phòng khám, siêu thị, quán ăn và khu vui chơi cho trẻ em.', 'Không gian xanh như công viên, đường dạo và hồ nước giúp tăng chất lượng sống và giá trị bất động sản về lâu dài. Giao thông kết nối - bãi đậu xe, trạm xe buýt, metro - cũng là yếu tố quyết định.', 'Cuối cùng, hãy chú ý đến an ninh, đơn vị quản lý vận hành và mức phí dịch vụ để tránh phát sinh ngoài dự kiến.']],
	];
	foreach ($posts as $i => $p) {
		[$slug, $title, $cat, $grp, $off, $excerpt, $paras] = $p;
		[$id, $c] = nvr_demo_upsert('post', 'post-' . $slug, $title, nvr_demo_paragraphs($paras), [
			'thumb_url' => nvr_pool_urls($grp, 1, $off, 900)[0],
		], ['slug' => $slug, 'excerpt' => $excerpt, 'date' => gmdate('Y-m-d H:i:s', time() - ($i + 1) * DAY_IN_SECONDS * 3 + 7 * HOUR_IN_SECONDS)]);
		if ($id) {
			if (!empty($cat_ids[$cat])) wp_set_post_categories($id, [(int) $cat_ids[$cat]]);
			if ($c) $new++;
		}
	}
	return $new;
}

/** Trash every demo item (those carrying a _nvr_demo_key). */
function nvr_remove_demo_content() {
	global $wpdb;
	$ids = $wpdb->get_col("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_nvr_demo_key'");
	$n = 0;
	foreach ($ids as $id) { if (wp_trash_post((int) $id)) $n++; }
	delete_option('nvr_demo_imported');
	return $n;
}

/* ---------------------------------------------------------------------
 * Legal page templates (sample text - needs legal review)
 * ------------------------------------------------------------------- */
function nvr_legal_content($slug) {
	$c = esc_html(nvr_opt('company'));
	$addr = esc_html(nvr_opt('address'));
	$hot = esc_html(nvr_hotline());
	$mail = esc_html(nvr_contact_email());
	$note = '<p><em>Lưu ý: đây là nội dung mẫu để website vận hành ban đầu. Vui lòng để luật sư hoặc bộ phận pháp chế của doanh nghiệp rà soát, chỉnh sửa trước khi áp dụng chính thức.</em></p>';
	switch ($slug) {
		case 'chinh-sach-bao-mat':
			return $note . "<h2>1. Mục đích thu thập thông tin</h2><p>$c thu thập họ tên, số điện thoại, email và nội dung nhu cầu mà khách hàng tự nguyện cung cấp qua các biểu mẫu liên hệ, nhằm tư vấn bất động sản, phản hồi yêu cầu và chăm sóc khách hàng.</p>"
				. "<h2>2. Phạm vi sử dụng thông tin</h2><p>Thông tin chỉ được sử dụng cho mục đích liên hệ, tư vấn, gửi thông tin bất động sản phù hợp và cải thiện chất lượng dịch vụ. $c không bán hoặc chia sẻ thông tin cá nhân cho bên thứ ba vì mục đích thương mại, trừ khi có sự đồng ý của khách hàng hoặc theo yêu cầu của cơ quan nhà nước có thẩm quyền.</p>"
				. "<h2>3. Thời gian lưu trữ</h2><p>Thông tin được lưu trữ trong thời gian cần thiết để thực hiện mục đích nêu trên hoặc cho đến khi khách hàng yêu cầu xóa.</p>"
				. "<h2>4. Cookie và công cụ phân tích</h2><p>Website có thể sử dụng cookie, Google Analytics và Meta Pixel để thống kê lượt truy cập và đo lường hiệu quả quảng cáo. Bạn có thể tắt cookie trong cài đặt trình duyệt.</p>"
				. "<h2>5. Quyền của khách hàng</h2><p>Khách hàng có quyền yêu cầu truy cập, chỉnh sửa hoặc xóa thông tin cá nhân của mình theo quy định pháp luật về bảo vệ dữ liệu cá nhân bằng cách liên hệ qua hotline $hot hoặc email $mail.</p>"
				. "<h2>6. Liên hệ</h2><p>$c - $addr.</p>";
		case 'dieu-khoan-su-dung':
			return $note . "<h2>1. Chấp nhận điều khoản</h2><p>Khi truy cập và sử dụng website của $c, bạn đồng ý tuân thủ các điều khoản dưới đây. Nếu không đồng ý, vui lòng ngừng sử dụng website.</p>"
				. "<h2>2. Nội dung thông tin</h2><p>Thông tin bất động sản (giá, diện tích, hình ảnh, pháp lý, tiện ích) được cung cấp nhằm mục đích tham khảo. Giá và tình trạng tin đăng có thể thay đổi mà không báo trước. Người dùng cần kiểm tra thực tế và xác minh pháp lý trước khi giao dịch.</p>"
				. "<h2>3. Quyền sở hữu trí tuệ</h2><p>Nội dung, hình ảnh, thiết kế trên website thuộc quyền sở hữu của $c hoặc đối tác cấp phép. Không sao chép, phân phối lại khi chưa được đồng ý bằng văn bản.</p>"
				. "<h2>4. Hành vi bị cấm</h2><p>Nghiêm cấm đăng tải thông tin sai sự thật, can thiệp trái phép vào hệ thống, thu thập dữ liệu hàng loạt hoặc sử dụng website cho mục đích vi phạm pháp luật.</p>"
				. "<h2>5. Giới hạn trách nhiệm</h2><p>$c nỗ lực cập nhật thông tin chính xác nhưng không đảm bảo tuyệt đối tính đầy đủ và kịp thời, và không chịu trách nhiệm đối với thiệt hại phát sinh từ việc sử dụng thông tin trên website khi chưa được kiểm chứng.</p>"
				. "<h2>6. Thay đổi điều khoản</h2><p>$c có quyền cập nhật điều khoản bất cứ lúc nào. Phiên bản mới có hiệu lực kể từ khi được đăng tải trên website.</p>";
		case 'quy-che-hoat-dong':
			return $note . "<h2>1. Giới thiệu</h2><p>Website của $c là nền tảng cung cấp thông tin và kết nối nhu cầu mua bán, cho thuê bất động sản. Địa chỉ: $addr. Hotline: $hot. Email: $mail.</p>"
				. "<h2>2. Nguyên tắc hoạt động</h2><p>Hoạt động trên nguyên tắc trung thực, minh bạch, tôn trọng quyền và lợi ích hợp pháp của khách hàng. Thông tin bất động sản được đăng tải phải rõ ràng về vị trí, diện tích, giá và tình trạng pháp lý.</p>"
				. "<h2>3. Quy trình tiếp nhận và tư vấn</h2><p>Khách hàng gửi thông tin qua biểu mẫu, hotline hoặc Zalo. Bộ phận tư vấn liên hệ xác nhận nhu cầu, cung cấp thông tin chi tiết, sắp xếp xem nhà và hỗ trợ thủ tục giao dịch.</p>"
				. "<h2>4. Quy trình giải quyết khiếu nại</h2><p>Mọi khiếu nại được tiếp nhận qua hotline $hot hoặc email $mail. $c phản hồi trong thời gian sớm nhất và phối hợp các bên thương lượng, hòa giải; trường hợp không thỏa thuận được, tranh chấp được giải quyết theo quy định pháp luật Việt Nam.</p>"
				. "<h2>5. Bảo mật thông tin</h2><p>Thông tin khách hàng được bảo mật theo Chính sách bảo mật đăng tải trên website.</p>";
	}
	return '';
}
