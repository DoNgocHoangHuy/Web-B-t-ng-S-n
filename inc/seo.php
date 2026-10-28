<?php
/**
 * SEO: RealEstateListing + BreadcrumbList schema, Open Graph/meta description fallback.
 *
 * - If Rank Math is active, the listing schema is injected through its `rank_math/json_ld` filter
 *   and breadcrumbs are rendered by rank_math_the_breadcrumbs().
 * - If no SEO plugin is active, the theme prints its own JSON-LD, meta description and Open Graph tags.
 */
if (!defined('ABSPATH')) exit;

function nvr_split_address($address) {
	$parts = array_map('trim', explode(',', (string) $address));
	return [
		'street'   => $parts[0] ?? '',
		'locality' => count($parts) > 1 ? end($parts) : 'TP. Hồ Chí Minh',
	];
}

/** RealEstateListing schema for one property. */
function nvr_property_schema($id, $with_context = true) {
	$address = nvr_meta('address', $id);
	$split = nvr_split_address($address);
	$price = (float) nvr_meta('price_numeric', $id, 0);
	$is_rent = nvr_meta('deal_type', $id) === 'rent';
	$status = nvr_status($id)[1];
	$sold = in_array($status, ['sold', 'rented'], true);
	$desc = get_the_excerpt($id);
	if (!$desc) $desc = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $id)), 35);

	$images = array_slice(array_map(function ($u) { return nvr_img_url($u, 1200); }, nvr_gallery($id)), 0, 6);

	$about_type = 'Apartment';
	if (has_term(['nha-pho', 'biet-thu'], 'property_type', $id)) $about_type = 'House';
	if (has_term('dat-nen', 'property_type', $id)) $about_type = 'Place';

	$about = [
		'@type' => $about_type,
		'address' => ['@type' => 'PostalAddress', 'streetAddress' => $address, 'addressLocality' => $split['locality'], 'addressRegion' => 'TP. Hồ Chí Minh', 'addressCountry' => 'VN'],
	];
	if (($area = nvr_parse_area(nvr_meta('area', $id))) > 0) $about['floorSize'] = ['@type' => 'QuantitativeValue', 'value' => $area, 'unitCode' => 'MTK'];
	if (!nvr_is_land($id)) {
		$about['numberOfBedrooms'] = (int) nvr_meta('bedrooms', $id, 0);
		$about['numberOfBathroomsTotal'] = (int) nvr_meta('bathrooms', $id, 0);
	}
	$lat = nvr_meta('latitude', $id); $lng = nvr_meta('longitude', $id);
	if (is_numeric($lat) && is_numeric($lng)) $about['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float) $lat, 'longitude' => (float) $lng];

	$schema = [
		'@type' => 'RealEstateListing',
		'@id' => get_permalink($id) . '#listing',
		'url' => get_permalink($id),
		'name' => get_the_title($id),
		'description' => $desc,
		'datePosted' => get_the_date('c', $id),
		'dateModified' => get_the_modified_date('c', $id),
		'about' => $about,
	];
	if ($images) $schema['image'] = $images;
	if ($price > 0) {
		$offer = ['@type' => 'Offer', 'price' => $price, 'priceCurrency' => 'VND', 'url' => get_permalink($id), 'availability' => $sold ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock'];
		if ($is_rent) $offer['priceSpecification'] = ['@type' => 'UnitPriceSpecification', 'price' => $price, 'priceCurrency' => 'VND', 'unitText' => 'MON'];
		$schema['offers'] = $offer;
	}
	if ($with_context) $schema = ['@context' => 'https://schema.org'] + $schema;
	return $schema;
}

/* Fallback output (no SEO plugin) ------------------------------------ */
function nvr_print_schema() {
	if (is_admin() || nvr_seo_plugin_active()) return;
	$out = [];
	if (is_singular('property')) {
		$out[] = nvr_property_schema(get_queried_object_id());
	}
	if (is_front_page()) {
		$out[] = ['@context' => 'https://schema.org', '@type' => 'RealEstateAgent', 'name' => nvr_opt('company'), 'url' => home_url('/'), 'telephone' => nvr_hotline(), 'address' => ['@type' => 'PostalAddress', 'streetAddress' => nvr_opt('address'), 'addressCountry' => 'VN']];
		$out[] = ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => get_bloginfo('name'), 'url' => home_url('/'), 'potentialAction' => ['@type' => 'SearchAction', 'target' => nvr_archive_url('property') . '?keyword={search_term_string}', 'query-input' => 'required name=search_term_string']];
	}
	foreach ($out as $s) echo '<script type="application/ld+json">' . wp_json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
}
add_action('wp_head', 'nvr_print_schema', 30);

/* Rank Math integration ---------------------------------------------- */
function nvr_rankmath_jsonld($data, $jsonld) {
	if (is_singular('property')) {
		$data['nvr_listing'] = nvr_property_schema(get_queried_object_id(), false);
	}
	return $data;
}
add_filter('rank_math/json_ld', 'nvr_rankmath_jsonld', 99, 2);

/* Meta description + Open Graph fallback ------------------------------ */
/** Hand-written descriptions for the system pages (independent of stored page content). */
function nvr_page_description() {
	$brand = nvr_opt('company') ?: 'Nhà Việt Realty';
	if (is_page('ve-chung-toi')) return $brand . ' là nền tảng giới thiệu và kết nối bất động sản tại TP. Hồ Chí Minh: mua bán, cho thuê, dự án, khu dân cư, tư vấn pháp lý và dẫn xem nhà. Tìm hiểu câu chuyện, quy trình và giá trị của chúng tôi.';
	if (is_page('ky-gui')) return 'Ký gửi bán hoặc cho thuê nhà đất tại TP. Hồ Chí Minh cùng ' . $brand . '. Gửi thông tin bất động sản, chuyên viên liên hệ tư vấn và định hướng giá.';
	if (is_page('tinh-vay')) return 'Công cụ tính khoản vay mua nhà: nhập giá trị, tỷ lệ vay, lãi suất và thời hạn để ước tính số tiền trả mỗi tháng, tổng lãi và lịch trả nợ theo năm.';
	return '';
}
add_filter('rank_math/frontend/description', function ($d) { $x = nvr_page_description(); return $x ? $x : $d; });

function nvr_meta_description() {
	$pd = nvr_page_description();
	if ($pd) return $pd;
	if (is_singular()) {
		$id = get_queried_object_id();
		$d = get_the_excerpt($id);
		if (!$d) $d = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $id)), 30);
		if (is_singular('property')) {
			$d = trim(get_the_title($id) . ' - ' . nvr_price_text($id) . ' - ' . implode(' | ', nvr_specs($id)) . '. ' . nvr_meta('address', $id) . '. ' . $d);
		}
		return $d;
	}
	if (is_front_page()) return get_bloginfo('description') ?: 'Mua bán, cho thuê căn hộ, nhà phố, biệt thự, đất nền tại TP. Hồ Chí Minh. Thông tin rõ ràng, giá cụ thể, tư vấn miễn phí.';
	if (is_post_type_archive('property')) return 'Danh sách nhà đất mua bán và cho thuê tại TP. Hồ Chí Minh: căn hộ, nhà phố, biệt thự, đất nền. Lọc theo khu vực, giá, diện tích.';
	return '';
}

function nvr_head_meta() {
	if (is_admin() || nvr_seo_plugin_active()) return;
	$desc = wp_strip_all_tags(nvr_meta_description());
	if ($desc) echo '<meta name="description" content="' . esc_attr(wp_trim_words($desc, 32, '')) . '">' . "\n";
	if (is_singular(['property', 'project', 'neighborhood', 'post'])) {
		$id = get_queried_object_id();
		$img = nvr_card_image_url($id, 'large');
		echo '<meta property="og:type" content="article">' . "\n";
		echo '<meta property="og:locale" content="vi_VN">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr(wp_strip_all_tags(get_the_title($id))) . '">' . "\n";
		if ($desc) echo '<meta property="og:description" content="' . esc_attr(wp_trim_words($desc, 32, '')) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url(get_permalink($id)) . '">' . "\n";
		if ($img) echo '<meta property="og:image" content="' . esc_url(nvr_img_url($img, 1200)) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	} elseif (is_page() || is_front_page() || is_post_type_archive() || is_home()) {
		$url = is_post_type_archive() ? get_post_type_archive_link(get_query_var('post_type')) : (is_home() && !is_front_page() ? nvr_posts_page_url() : (is_front_page() ? home_url('/') : get_permalink(get_queried_object_id())));
		$img = nvr_default_og_image();
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:locale" content="vi_VN">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '">' . "\n";
		if ($desc) echo '<meta property="og:description" content="' . esc_attr(wp_trim_words($desc, 32, '')) . '">' . "\n";
		if ($url) echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
		if ($img) echo '<meta property="og:image" content="' . esc_url(nvr_img_url($img, 1200)) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}
}
add_action('wp_head', 'nvr_head_meta', 5);
