<?php
/**
 * Template tags shared by several templates.
 */
if (!defined('ABSPATH')) exit;

/** Up to $limit taxonomy terms that have listings (falls back to all terms). */
function nvr_nav_terms($taxonomy, $limit = 8) {
	$terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true, 'number' => $limit, 'orderby' => 'count', 'order' => 'DESC']);
	if (is_wp_error($terms) || !$terms) $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false, 'number' => $limit]);
	return is_wp_error($terms) ? [] : $terms;
}

/** Mega menu panel for a deal type ('sale' | 'rent'). */
function nvr_mega_menu($deal) {
	$types = nvr_nav_terms('property_type', 6);
	$locs = nvr_nav_terms('property_location', 7);
	$projects = nvr_featured_projects(3);
	echo '<div class="mega" role="menu"><div class="mega-col"><strong>Loại bất động sản</strong>';
	foreach ($types as $t) echo '<a href="' . esc_url(nvr_archive_url('property', ['deal' => $deal, 'type' => $t->slug])) . '">' . esc_html($t->name) . ($deal === 'rent' ? ' cho thuê' : '') . '</a>';
	echo '</div><div class="mega-col"><strong>Khu vực</strong>';
	foreach ($locs as $t) echo '<a href="' . esc_url(nvr_archive_url('property', ['deal' => $deal, 'location' => $t->slug])) . '">' . esc_html($t->name) . '</a>';
	echo '</div><div class="mega-col"><strong>Dự án nổi bật</strong>';
	while ($projects->have_posts()) { $projects->the_post(); echo '<a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a>'; }
	wp_reset_postdata();
	echo '<a class="mega-more" href="' . esc_url(nvr_archive_url('project')) . '">Xem tất cả dự án ' . nvr_icon('arrow', 14) . '</a></div></div>';
}
