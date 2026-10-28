<?php
/**
 * Helper functions: options, meta, images, icons, status, price parsing.
 */
if (!defined('ABSPATH')) exit;

/* ---------------------------------------------------------------------
 * Options (Cài đặt > Nhà Việt Realty)
 * ------------------------------------------------------------------- */
function nvr_option_defaults() {
	return [
		'company'      => 'Nhà Việt Realty',
		'hotline'      => '0900 000 000',
		'zalo'         => '',
		'facebook'     => 'https://www.facebook.com/',
		'email'        => '',
		'notify_email' => '',
		'address'      => 'TP. Hồ Chí Minh, Việt Nam',
		'google_maps_key' => '',
		'ga4_id'       => '',
		'meta_pixel_id' => '',
		'hours'        => 'Thứ 2 – Thứ 7: 8:00 – 18:00',
		'founded'      => '',
		'og_image'     => '',
	];
}

/** Parse a textarea option: one row per line, columns separated by "|". Returns array of trimmed column arrays. */
function nvr_opt_rows($key, $cols = 2) {
	$raw = (string) get_option('nvr_' . $key, '');
	$rows = [];
	foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
		$line = trim($line);
		if ($line === '') continue;
		$p = array_pad(array_map('trim', explode('|', $line)), $cols, '');
		$rows[] = $p;
	}
	return $rows;
}

/** Valid e-mail typed in settings (no fallback to the WP admin e-mail), or ''. */
function nvr_public_email() {
	$e = nvr_opt('email');
	return is_email($e) ? $e : '';
}

/** Default social-share image: setting first, then the hero demo photo. */
function nvr_default_og_image() {
	$u = nvr_opt('og_image');
	if ($u) return $u;
	$pool = nvr_pool_urls('hero', 1, 0, 1200);
	return $pool[0] ?? '';
}

function nvr_opt($key, $default = '') {
	$defaults = nvr_option_defaults();
	if ($key === 'google_maps_key' && defined('NVR_GOOGLE_MAPS_KEY') && NVR_GOOGLE_MAPS_KEY) {
		return trim((string) NVR_GOOGLE_MAPS_KEY);
	}
	$value = get_option('nvr_' . $key, null);
	if ($value === null || $value === '') {
		$value = $defaults[$key] ?? $default;
	}
	return is_string($value) ? trim($value) : $value;
}

function nvr_hotline() { return nvr_opt('hotline'); }

function nvr_hotline_tel() {
	$digits = preg_replace('/[^0-9+]/', '', nvr_hotline());
	return 'tel:' . $digits;
}

function nvr_zalo_url() {
	$z = nvr_opt('zalo');
	if ($z === '') $z = nvr_hotline();
	if (preg_match('#^https?://#i', $z)) return esc_url_raw($z);
	return 'https://zalo.me/' . preg_replace('/[^0-9]/', '', $z);
}

function nvr_facebook_url() {
	$f = nvr_opt('facebook');
	return $f ? esc_url_raw($f) : 'https://www.facebook.com/';
}

function nvr_contact_email() {
	$e = nvr_opt('email');
	return is_email($e) ? $e : get_option('admin_email');
}

function nvr_notify_email() {
	$e = nvr_opt('notify_email');
	return is_email($e) ? $e : get_option('admin_email');
}

/* ---------------------------------------------------------------------
 * Meta
 * ------------------------------------------------------------------- */
function nvr_meta($key, $id = 0, $default = '') {
	$id = $id ? $id : get_the_ID();
	$value = get_post_meta($id, '_nvr_' . $key, true);
	return ($value !== '' && $value !== null) ? $value : $default;
}
/* Backwards compatible aliases used by v2-v4 templates. */
function nvr_get($key, $id = 0, $default = '') { return nvr_meta($key, $id, $default); }
function nvr_get_meta($key, $id = 0, $default = '') { return nvr_meta($key, $id, $default); }

function nvr_is_featured($id = 0) { return nvr_meta('featured', $id) === 'yes'; }

/**
 * Convert "5.2 Tỷ", "5,2 tỷ", "15 Triệu/tháng", "1.200.000.000" to a number (VND).
 */
function nvr_parse_vnd($text) {
	$text = trim(mb_strtolower((string) $text, 'UTF-8'));
	if ($text === '') return 0;
	if (preg_match('/^\d{1,3}(\.\d{3})+$/', $text)) return (float) str_replace('.', '', $text);
	if (preg_match('/^\d+$/', $text)) return (float) $text;
	if (preg_match('/(\d+(?:[.,]\d+)?)\s*(tỷ|ty|tỉ|triệu|trieu|tr|nghìn|ngàn|k)\b/u', $text, $m)) {
		$n = (float) str_replace(',', '.', $m[1]);
		switch ($m[2]) {
			case 'tỷ': case 'ty': case 'tỉ': return $n * 1000000000;
			case 'triệu': case 'trieu': case 'tr': return $n * 1000000;
			default: return $n * 1000;
		}
	}
	return 0;
}

function nvr_parse_area($text) {
	if (preg_match('/\d+(?:[.,]\d+)?/', (string) $text, $m)) return (float) str_replace(',', '.', $m[0]);
	return 0;
}

function nvr_status($id = 0) {
	$id = $id ? $id : get_the_ID();
	$status = nvr_meta('status', $id);
	if ($status === '') $status = nvr_meta('deal_type', $id) === 'rent' ? 'Đang cho thuê' : 'Đang bán';
	$map = ['Đang bán' => 'sale', 'Đã bán' => 'sold', 'Đang cho thuê' => 'rent', 'Đã cho thuê' => 'rented'];
	return [$status, $map[$status] ?? 'sale'];
}

function nvr_is_land($id = 0) {
	$id = $id ? $id : get_the_ID();
	if (has_term('dat-nen', 'property_type', $id)) return true;
	return nvr_meta('bedrooms', $id) === '' && nvr_meta('bathrooms', $id) === '';
}

/** [label, value] pairs shown as "70 m² | 2 PN | 2 WC". */
function nvr_specs($id = 0) {
	$id = $id ? $id : get_the_ID();
	$specs = [];
	$area = nvr_meta('area', $id);
	if ($area !== '') $specs[] = $area . ' m²';
	if (!nvr_is_land($id)) {
		$specs[] = nvr_meta('bedrooms', $id, '0') . ' PN';
		$specs[] = nvr_meta('bathrooms', $id, '0') . ' WC';
	}
	return $specs;
}

function nvr_price_text($id = 0) {
	return nvr_meta('price', $id ? $id : get_the_ID(), 'Đang cập nhật');
}

/* ---------------------------------------------------------------------
 * Pages / URLs
 * ------------------------------------------------------------------- */
function nvr_page_url($slug) {
	static $cache = [];
	if (!isset($cache[$slug])) {
		$page = get_page_by_path($slug);
		$cache[$slug] = $page ? get_permalink($page) : home_url('/' . $slug . '/');
	}
	return $cache[$slug];
}

function nvr_posts_page_url() {
	$id = (int) get_option('page_for_posts');
	return $id ? get_permalink($id) : nvr_page_url('tin-tuc');
}

function nvr_archive_url($post_type = 'property', $args = []) {
	$url = get_post_type_archive_link($post_type);
	if (!$url) $url = home_url('/');
	return $args ? add_query_arg($args, $url) : $url;
}

/* ---------------------------------------------------------------------
 * Icons (Lucide, ISC licence) - inline SVG, inherits currentColor
 * ------------------------------------------------------------------- */
function nvr_icon($name, $size = 20, $class = '') {
	static $paths = null;
	if ($paths === null) {
		$paths = [
			'heart'  => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
			'pin'    => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
			'phone'  => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/>',
			'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
			'menu'   => '<path d="M4 6h16M4 12h16M4 18h16"/>',
			'close'  => '<path d="M18 6 6 18M6 6l12 12"/>',
			'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
			'zalo'   => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M8 10h8l-8 5h8"/>',
			'map'    => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3Z"/><path d="M9 3v15M15 6v15"/>',
			'grid'   => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
			'list'   => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
			'chevron' => '<path d="m6 9 6 6 6-6"/>',
			'check'  => '<path d="M20 6 9 17l-5-5"/>',
			'share'  => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
			'mail'   => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
			'arrow'  => '<path d="M5 12h14M12 5l7 7-7 7"/>',
			'filter' => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
			'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
			'award'  => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/>',
			'handshake' => '<path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.9-3.9a3 3 0 0 0-4.2 0l-.9.9a1.5 1.5 0 0 1-2.1 0 1.5 1.5 0 0 1 0-2.1l2.6-2.6a5 5 0 0 1 4.1-1.4L16 5"/><path d="m21 3 1 11h-2M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/>',
			'gift'   => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7M7.5 8a2.5 2.5 0 0 1 0-5A4.8 4.8 0 0 1 12 8a4.8 4.8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/>',
			'building' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/>',
			'arrow-up' => '<path d="m5 12 7-7 7 7M12 19V5"/>',
			'success' => '<circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/>',
			'clock'  => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
			'calc'   => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h4"/>',
			'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
			'star'   => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1Z"/>',
			'quote'  => '<path d="M3 21c3 0 7-1 7-8V5a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h4M15 21c3 0 7-1 7-8V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h4"/>',
			'home'   => '<path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path d="M9 22V12h6v10"/>',
		];
	}
	if (!isset($paths[$name])) return '';
	return '<svg class="nvr-icon ' . esc_attr($class) . '" width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>';
}

/* ---------------------------------------------------------------------
 * Images: gallery, card image, Unsplash WebP helper, placeholder
 * ------------------------------------------------------------------- */
function nvr_placeholder_url() { return NVR_URI . '/assets/img/placeholder.svg'; }

/** Add WebP + sizing parameters for Unsplash URLs; leave other URLs untouched. */
function nvr_img_url($url, $w = 0, $h = 0) {
	if (!$url) return '';
	$p = wp_parse_url($url);
	if (($p['host'] ?? '') === 'images.unsplash.com') {
		$q = [];
		parse_str($p['query'] ?? '', $q);
		$q['auto'] = 'format';
		$q['fm'] = 'webp';
		$q['fit'] = 'crop';
		$q['q'] = 75;
		if ($w) $q['w'] = $w;
		if ($h) $q['h'] = $h; else unset($q['h']);
		return 'https://images.unsplash.com' . $p['path'] . '?' . http_build_query($q);
	}
	return $url;
}

/** Build an <img> with width/height (no layout shift), srcset for Unsplash, lazy by default. */
function nvr_img($url, $alt = '', $w = 800, $h = 560, $args = []) {
	$args = wp_parse_args($args, ['loading' => 'lazy', 'class' => '', 'sizes' => '(max-width: 650px) 100vw, 400px', 'priority' => false]);
	if (!$url) $url = nvr_placeholder_url();
	$p = wp_parse_url($url);
	$srcset = '';
	if (($p['host'] ?? '') === 'images.unsplash.com') {
		$parts = [];
		foreach ([480, 800, 1200] as $width) {
			$parts[] = esc_url(nvr_img_url($url, $width, $h ? (int) round($width * $h / $w) : 0)) . ' ' . $width . 'w';
		}
		$srcset = ' srcset="' . implode(', ', $parts) . '" sizes="' . esc_attr($args['sizes']) . '"';
		$src = nvr_img_url($url, min($w, 800), $h ? (int) round(min($w, 800) * $h / $w) : 0);
	} else {
		$src = $url;
	}
	$attrs = $args['priority'] ? ' fetchpriority="high" loading="eager"' : ' loading="' . esc_attr($args['loading']) . '"';
	return '<img src="' . esc_url($src) . '"' . $srcset . ' alt="' . esc_attr($alt) . '" width="' . (int) $w . '" height="' . (int) $h . '"' . $attrs . ' decoding="async"' . ($args['class'] ? ' class="' . esc_attr($args['class']) . '"' : '') . '>';
}

/** Demo image pools (Unsplash photo IDs). Replace with licensed images in production. */
function nvr_image_pool($group) {
	$pools = [
		'apartment' => ['1600607687939-ce8a6c25118c', '1600566753086-00f18fb6b3ea', '1600607687920-4e2a09cf159d', '1600607687644-c7171b42498f', '1600210492486-724fe5c67fb0', '1600566753190-17f0baa2a6c3', '1522708323590-d24dbb6b0267', '1502672260266-1c1ef2d93688', '1493809842364-78817add7ffb', '1560448204-e02f11c3d0e2'],
		'house'     => ['1600585154340-be6161a56a0c', '1600047509807-ba8f99d2cdde', '1600573472592-401b489a3cdc', '1600585154526-990dced4db0d', '1564013799919-ab600027ffc6', '1512917774080-9991f1c4c750', '1568605114967-8130f3a36994', '1613490493576-7fde63acd811'],
		'land'      => ['1500382017468-9049fed747ef', '1600585154340-be6161a56a0c', '1564013799919-ab600027ffc6', '1568605114967-8130f3a36994'],
		'office'    => ['1497366216548-37526070297c', '1497366811353-6870744d04b2', '1600607687644-c7171b42498f', '1522708323590-d24dbb6b0267'],
		'city'      => ['1545324418-cc1a3fa10c00', '1480714378408-67cf0d13bc1b', '1486406146926-c627a92ad1ab', '1600566753086-00f18fb6b3ea', '1560448204-e02f11c3d0e2'],
		'hero'      => ['1600596542815-ffad4c1539a9'],
	];
	return $pools[$group] ?? $pools['apartment'];
}

function nvr_pool_urls($group, $count = 5, $offset = 0, $width = 1400) {
	$pool = nvr_image_pool($group);
	$urls = [];
	for ($i = 0; $i < $count; $i++) {
		$urls[] = 'https://images.unsplash.com/photo-' . $pool[($offset + $i) % count($pool)] . '?auto=format&fit=crop&w=' . $width . '&q=80';
	}
	return $urls;
}

/** Gallery URLs: media-library IDs first, then one-URL-per-line field, then featured image. */
function nvr_gallery($id = 0) {
	$id = $id ? $id : get_the_ID();
	$urls = [];
	if (has_post_thumbnail($id)) $urls[] = get_the_post_thumbnail_url($id, 'full');
	foreach (array_filter(array_map('absint', explode(',', (string) nvr_meta('gallery_ids', $id)))) as $aid) {
		$u = wp_get_attachment_image_url($aid, 'full');
		if ($u) $urls[] = $u;
	}
	foreach (preg_split('/\r\n|\r|\n/', (string) nvr_meta('gallery_images', $id)) as $line) {
		$line = trim($line);
		if ($line !== '' && preg_match('#^https?://#i', $line)) $urls[] = $line;
	}
	return array_values(array_unique($urls));
}

/** Main image URL for cards (featured image, else first gallery image). */
function nvr_card_image_url($id = 0, $size = 'nvr-card') {
	$id = $id ? $id : get_the_ID();
	if (has_post_thumbnail($id)) return get_the_post_thumbnail_url($id, $size);
	$g = nvr_gallery($id);
	if ($g) return $g[0];
	$t = nvr_meta('thumb_url', $id);
	return $t ? $t : '';
}

function nvr_card_image($id = 0, $w = 640, $h = 440) {
	$id = $id ? $id : get_the_ID();
	if (has_post_thumbnail($id)) {
		return get_the_post_thumbnail($id, 'nvr-card', ['loading' => 'lazy', 'decoding' => 'async', 'alt' => get_the_title($id)]);
	}
	return nvr_img(nvr_card_image_url($id), get_the_title($id), $w, $h);
}

/* ---------------------------------------------------------------------
 * Map embed URL (single pages). Works with or without Google API key.
 * ------------------------------------------------------------------- */
function nvr_map_embed_url($address, $lat = '', $lng = '', $manual = '') {
	if ($manual && preg_match('~^https://(www\.)?(google\.com/maps|maps\.google\.com|maps\.googleapis\.com)/~i', $manual)) {
		return $manual;
	}
	$address = trim((string) $address);
	if ($address !== '' && stripos($address, 'hồ chí minh') === false && stripos($address, 'hcm') === false) {
		$address .= ', Thành phố Hồ Chí Minh, Việt Nam';
	}
	$q = $address !== '' ? $address : (is_numeric($lat) && is_numeric($lng) ? $lat . ',' . $lng : 'Thành phố Hồ Chí Minh');
	$key = nvr_opt('google_maps_key');
	if ($key) {
		return 'https://www.google.com/maps/embed/v1/place?key=' . rawurlencode($key) . '&q=' . rawurlencode($q) . '&zoom=16&language=vi';
	}
	return 'https://maps.google.com/maps?q=' . rawurlencode($q) . '&z=16&hl=vi&output=embed';
}

function nvr_map_directions_url($address) {
	return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
}

/* ---------------------------------------------------------------------
 * Breadcrumbs (uses Rank Math output if available, else own + JSON-LD)
 * ------------------------------------------------------------------- */
function nvr_breadcrumbs($trail = []) {
	if (function_exists('rank_math_the_breadcrumbs')) {
		ob_start();
		rank_math_the_breadcrumbs();
		$html = trim(ob_get_clean());
		if ($html !== '') {
			echo '<nav class="breadcrumbs" aria-label="Breadcrumb">' . $html . '</nav>';
			return;
		}
	}
	$items = [['name' => 'Trang chủ', 'url' => home_url('/')]];
	foreach ($trail as $t) $items[] = ['name' => $t[0], 'url' => $t[1] ?? ''];
	echo '<nav class="breadcrumbs" aria-label="Breadcrumb"><ol>';
	$last = count($items) - 1;
	foreach ($items as $i => $it) {
		echo '<li>';
		if ($it['url'] && $i !== $last) echo '<a href="' . esc_url($it['url']) . '">' . esc_html($it['name']) . '</a>';
		else echo '<span aria-current="page">' . esc_html($it['name']) . '</span>';
		echo '</li>';
	}
	echo '</ol></nav>';
	if (!nvr_seo_plugin_active()) {
		$list = [];
		foreach ($items as $i => $it) {
			$entry = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $it['name']];
			if ($i !== $last && $it['url']) $entry['item'] = $it['url'];
			$list[] = $entry;
		}
		echo '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
	}
}

function nvr_seo_plugin_active() {
	return defined('RANK_MATH_VERSION') || defined('WPSEO_VERSION') || defined('AIOSEO_VERSION');
}

/* Fetch the related-by-text link for a developer / project etc. */
function nvr_find_post_by_title($title, $post_type) {
	global $wpdb;
	$id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type=%s AND post_title=%s AND post_status IN ('publish','draft','pending','private') ORDER BY ID ASC LIMIT 1", $post_type, $title));
	return $id ? (int) $id : 0;
}
