<?php
/**
 * Property listing queries: archive + search share the same filters.
 *
 * GET params: keyword|s, type, location, deal, status, price, area, beds, direction, legal, sort
 */
if (!defined('ABSPATH')) exit;

/** Price range presets. key => [label, deal, min, max] */
function nvr_price_ranges() {
	return [
		'sale:0:2000000000'            => ['Dưới 2 tỷ', 'sale'],
		'sale:2000000000:5000000000'   => ['2 – 5 tỷ', 'sale'],
		'sale:5000000000:10000000000'  => ['5 – 10 tỷ', 'sale'],
		'sale:10000000000:20000000000' => ['10 – 20 tỷ', 'sale'],
		'sale:20000000000:0'           => ['Trên 20 tỷ', 'sale'],
		'rent:0:10000000'              => ['Dưới 10 triệu/tháng', 'rent'],
		'rent:10000000:15000000'       => ['10 – 15 triệu/tháng', 'rent'],
		'rent:15000000:20000000'       => ['15 – 20 triệu/tháng', 'rent'],
		'rent:20000000:0'              => ['Trên 20 triệu/tháng', 'rent'],
	];
}

function nvr_area_ranges() {
	return ['0:30' => 'Dưới 30 m²', '30:50' => '30 – 50 m²', '50:80' => '50 – 80 m²', '80:120' => '80 – 120 m²', '120:0' => 'Trên 120 m²'];
}

function nvr_req($key, $default = '') {
	if (!isset($_GET[$key]) || is_array($_GET[$key])) return $default;
	return sanitize_text_field(wp_unslash($_GET[$key]));
}

/** Is any listing filter active in the URL? */
function nvr_has_active_filters() {
	foreach (['keyword', 's', 'type', 'location', 'deal', 'status', 'price', 'area', 'beds', 'direction', 'legal', 'sort', 'min_price', 'max_price', 'min_area', 'max_area'] as $k) {
		if (nvr_req($k) !== '') return true;
	}
	return false;
}

function nvr_is_listing_query($query) {
	if (is_admin() || !$query->is_main_query()) return false;
	if ($query->is_post_type_archive('property')) return true;
	if ($query->is_search()) {
		$pt = $query->get('post_type');
		return empty($pt) || $pt === 'property' || $pt === 'any';
	}
	return false;
}

function nvr_listing_pre_get_posts($query) {
	if (!nvr_is_listing_query($query)) return;

	$query->set('post_type', 'property');
	$query->set('posts_per_page', 9);

	$kw = nvr_req('keyword', nvr_req('s'));
	$query->set('s', '');
	if ($kw !== '') $query->set('nvr_kw', $kw);

	$tax = [];
	if (($t = nvr_req('type')) !== '') $tax[] = ['taxonomy' => 'property_type', 'field' => 'slug', 'terms' => sanitize_title($t)];
	if (($l = nvr_req('location')) !== '') $tax[] = ['taxonomy' => 'property_location', 'field' => 'slug', 'terms' => sanitize_title($l)];
	if ($tax) $query->set('tax_query', array_merge(['relation' => 'AND'], $tax));

	$meta = ['relation' => 'AND'];
	$deal = nvr_req('deal');
	$price = nvr_req('price');
	$ranges = nvr_price_ranges();
	if ($price !== '' && isset($ranges[$price])) {
		[, $pmin, $pmax] = array_pad(explode(':', $price), 3, 0);
		$deal = $ranges[$price][1];
		$meta[] = ['key' => '_nvr_price_numeric', 'value' => (float) $pmin, 'type' => 'NUMERIC', 'compare' => '>='];
		if ((float) $pmax > 0) $meta[] = ['key' => '_nvr_price_numeric', 'value' => (float) $pmax, 'type' => 'NUMERIC', 'compare' => '<='];
	}
	if (in_array($deal, ['sale', 'rent'], true)) $meta[] = ['key' => '_nvr_deal_type', 'value' => $deal];

	foreach (['min_price' => ['_nvr_price_numeric', '>='], 'max_price' => ['_nvr_price_numeric', '<='], 'min_area' => ['_nvr_area_numeric', '>='], 'max_area' => ['_nvr_area_numeric', '<=']] as $get => $def) {
		$v = nvr_req($get);
		if ($v !== '' && is_numeric($v)) $meta[] = ['key' => $def[0], 'value' => (float) $v, 'type' => 'NUMERIC', 'compare' => $def[1]];
	}
	$area = nvr_req('area');
	if ($area !== '' && isset(nvr_area_ranges()[$area])) {
		[$amin, $amax] = array_pad(explode(':', $area), 2, 0);
		$meta[] = ['key' => '_nvr_area_numeric', 'value' => (float) $amin, 'type' => 'NUMERIC', 'compare' => '>='];
		if ((float) $amax > 0) $meta[] = ['key' => '_nvr_area_numeric', 'value' => (float) $amax, 'type' => 'NUMERIC', 'compare' => '<='];
	}
	$beds = nvr_req('beds');
	if ($beds !== '' && ctype_digit($beds)) $meta[] = ['key' => '_nvr_bedrooms', 'value' => (int) $beds, 'type' => 'NUMERIC', 'compare' => (int) $beds >= 4 ? '>=' : '='];
	foreach (['status' => 'status', 'direction' => 'direction', 'legal' => 'legal'] as $get => $key) {
		if (($v = nvr_req($get)) !== '') $meta[] = ['key' => '_nvr_' . $key, 'value' => $v];
	}
	if (count($meta) > 1) $query->set('meta_query', $meta);

	switch (nvr_req('sort')) {
		case 'price_asc':  $query->set('meta_key', '_nvr_price_numeric'); $query->set('orderby', 'meta_value_num'); $query->set('order', 'ASC'); break;
		case 'price_desc': $query->set('meta_key', '_nvr_price_numeric'); $query->set('orderby', 'meta_value_num'); $query->set('order', 'DESC'); break;
		case 'area_desc':  $query->set('meta_key', '_nvr_area_numeric'); $query->set('orderby', 'meta_value_num'); $query->set('order', 'DESC'); break;
		default: $query->set('orderby', 'date'); $query->set('order', 'DESC');
	}
}
add_action('pre_get_posts', 'nvr_listing_pre_get_posts');

/** Keyword searches title, content, address, listing code, type/location names. */
function nvr_keyword_where($where, $query) {
	global $wpdb;
	$kw = $query->get('nvr_kw');
	if (!$kw) return $where;
	$like = '%' . $wpdb->esc_like($kw) . '%';
	$where .= $wpdb->prepare(
		" AND ( {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s
			OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} nvr_pm WHERE nvr_pm.post_id = {$wpdb->posts}.ID AND nvr_pm.meta_key IN ('_nvr_address','_nvr_code') AND nvr_pm.meta_value LIKE %s)
			OR EXISTS (SELECT 1 FROM {$wpdb->term_relationships} nvr_tr
				INNER JOIN {$wpdb->term_taxonomy} nvr_tt ON nvr_tt.term_taxonomy_id = nvr_tr.term_taxonomy_id
				INNER JOIN {$wpdb->terms} nvr_t ON nvr_t.term_id = nvr_tt.term_id
				WHERE nvr_tr.object_id = {$wpdb->posts}.ID AND nvr_tt.taxonomy IN ('property_type','property_location') AND nvr_t.name LIKE %s) )",
		$like, $like, $like, $like
	);
	return $where;
}
add_filter('posts_where', 'nvr_keyword_where', 10, 2);

/** Featured listings used for homepage & "no result" suggestions. */
function nvr_featured_properties($limit = 12, $exclude = []) {
	$args = ['post_type' => 'property', 'posts_per_page' => $limit, 'post__not_in' => $exclude, 'no_found_rows' => true, 'meta_query' => [['key' => '_nvr_featured', 'value' => 'yes']]];
	$q = new WP_Query($args);
	if (!$q->have_posts()) {
		unset($args['meta_query']);
		$q = new WP_Query($args);
	}
	return $q;
}

function nvr_featured_projects($limit = 6) {
	$args = ['post_type' => 'project', 'posts_per_page' => $limit, 'no_found_rows' => true, 'meta_query' => [['key' => '_nvr_featured', 'value' => 'yes']]];
	$q = new WP_Query($args);
	if (!$q->have_posts()) {
		unset($args['meta_query']);
		$q = new WP_Query($args);
	}
	return $q;
}

/** noindex for filtered/search/thank-you/favorites pages to avoid thin duplicate content. */
function nvr_robots($robots) {
	$noindex = false;
	if (is_search()) $noindex = true;
	if (is_post_type_archive('property') && nvr_has_active_filters()) $noindex = true;
	if (is_page(['cam-on', 'yeu-thich'])) $noindex = true;
	if ($noindex) {
		$robots['noindex'] = true;
		$robots['follow'] = true;
		unset($robots['max-image-preview']);
	}
	return $robots;
}
add_filter('wp_robots', 'nvr_robots');
