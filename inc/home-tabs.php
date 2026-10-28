<?php
/**
 * Homepage category tabs ("Tất cả | Thủ Đức | Quận 7 ..."), each with its own
 * "Xem tất cả (N)" button that opens the archive filtered to that tab only.
 *
 *  - Properties: tabs = property_location terms (filter: ?location=slug, already supported).
 *  - Projects:   tabs = first part of the "Khu vực hiển thị" meta (filter: ?area=Label, added below).
 */
if (!defined('ABSPATH')) exit;

/** "TP. Thủ Đức, TP. Hồ Chí Minh" -> "TP. Thủ Đức" */
function nvr_project_area_label($full) {
	$parts = explode(',', (string) $full);
	return trim($parts[0]);
}

/** Distinct project areas (with project counts), most projects first. */
function nvr_project_areas($limit = 6) {
	global $wpdb;
	$rows = $wpdb->get_col($wpdb->prepare(
		"SELECT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = '_nvr_project_area' AND p.post_type = 'project' AND p.post_status = 'publish' AND pm.meta_value <> '' LIMIT %d", 500
	));
	$counts = [];
	foreach ($rows as $r) {
		$l = nvr_project_area_label($r);
		if ($l !== '') $counts[$l] = ($counts[$l] ?? 0) + 1;
	}
	arsort($counts);
	return array_slice($counts, 0, $limit, true);
}

/** Archive filter for projects: ?area=Quận 7 */
function nvr_project_archive_filter($query) {
	if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('project')) return;
	$area = nvr_req('area');
	if ($area === '') return;
	$query->set('meta_query', [['key' => '_nvr_project_area', 'value' => $area, 'compare' => 'LIKE']]);
}
add_action('pre_get_posts', 'nvr_project_archive_filter');

add_filter('wp_robots', function ($robots) {
	if (is_post_type_archive('project') && nvr_req('area') !== '') {
		$robots['noindex'] = true;
		$robots['follow'] = true;
		unset($robots['max-image-preview']);
	}
	return $robots;
});

/**
 * Render a tabbed block.
 * $tabs: [ ['id','label','query'=>WP_Query,'url','count','all_label'] ... ] - first tab is "Tất cả".
 * $opts: card (template part), grid (css classes), empty (message), noun (e.g. "dự án")
 */
function nvr_render_tabbed_block($uid, $tabs, $opts) {
	if (!$tabs) return;
	echo '<div class="cat-tabs-block" data-tabs>';
	echo '<div class="cat-tabs-scroll"><div class="cat-tabs" role="tablist" aria-label="' . esc_attr($opts['label']) . '">';
	foreach ($tabs as $i => $t) {
		$tid = $uid . '-' . $i;
		printf(
			'<button type="button" class="cat-tab%s" role="tab" id="tab-%s" aria-controls="panel-%s" aria-selected="%s" tabindex="%s" data-tab>%s</button>',
			$i === 0 ? ' is-active' : '', esc_attr($tid), esc_attr($tid), $i === 0 ? 'true' : 'false', $i === 0 ? '0' : '-1', esc_html($t['label'])
		);
	}
	echo '</div></div>';
	foreach ($tabs as $i => $t) {
		$tid = $uid . '-' . $i;
		/** @var WP_Query $q */
		$q = $t['query'];
		printf('<div class="cat-panel" role="tabpanel" id="panel-%s" aria-labelledby="tab-%s"%s>', esc_attr($tid), esc_attr($tid), $i === 0 ? '' : ' hidden');
		if ($q->have_posts()) {
			echo '<div class="' . esc_attr($opts['grid']) . '">';
			while ($q->have_posts()) { $q->the_post(); get_template_part($opts['card']); }
			echo '</div>';
			wp_reset_postdata();
			echo '<div class="cat-more"><a class="cat-more-link" href="' . esc_url($t['url']) . '">Xem tất cả'
				. ($t['count'] ? ' (' . number_format_i18n((int) $t['count']) . ')' : '')
				. ' ' . nvr_icon('arrow', 16) . '</a></div>';
		} else {
			echo '<div class="empty-state">' . esc_html($opts['empty']) . '</div>';
		}
		echo '</div>';
	}
	echo '</div>';
}

/** Tabs for homepage "Dự án mới nổi bật". */
function nvr_home_project_tabs() {
	$total = (int) wp_count_posts('project')->publish;
	$tabs = [['label' => 'Tất cả', 'query' => nvr_featured_projects(6), 'url' => nvr_archive_url('project'), 'count' => $total]];
	foreach (nvr_project_areas(5) as $label => $n) {
		$tabs[] = [
			'label' => $label,
			'query' => new WP_Query(['post_type' => 'project', 'posts_per_page' => 6, 'no_found_rows' => true, 'meta_query' => [['key' => '_nvr_project_area', 'value' => $label, 'compare' => 'LIKE']]]),
			'url'   => nvr_archive_url('project', ['area' => $label]),
			'count' => $n,
		];
	}
	return $tabs;
}

/** Tabs for homepage "Bất động sản nổi bật". */
function nvr_home_property_tabs() {
	$total = (int) wp_count_posts('property')->publish;
	$tabs = [['label' => 'Tất cả', 'query' => nvr_featured_properties(12), 'url' => nvr_archive_url('property'), 'count' => $total]];
	$terms = get_terms(['taxonomy' => 'property_location', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 6]);
	if (!is_wp_error($terms)) {
		foreach ($terms as $term) {
			$tabs[] = [
				'label' => $term->name,
				'query' => new WP_Query(['post_type' => 'property', 'posts_per_page' => 12, 'no_found_rows' => true, 'tax_query' => [['taxonomy' => 'property_location', 'field' => 'term_id', 'terms' => $term->term_id]]]),
				'url'   => nvr_archive_url('property', ['location' => $term->slug]),
				'count' => $term->count,
			];
		}
	}
	return $tabs;
}
