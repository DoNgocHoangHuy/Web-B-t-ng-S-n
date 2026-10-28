<?php
/**
 * AJAX: render favourite property cards (favourites are stored in the visitor's localStorage).
 */
if (!defined('ABSPATH')) exit;

function nvr_ajax_favorites() {
	$ids = isset($_GET['ids']) ? array_slice(array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_GET['ids']))))), 0, 36) : [];
	if (!$ids) wp_send_json_success(['html' => '', 'count' => 0]);
	$q = new WP_Query(['post_type' => 'property', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => 36, 'no_found_rows' => true, 'post_status' => 'publish']);
	ob_start();
	while ($q->have_posts()) {
		$q->the_post();
		get_template_part('template-parts/property-card');
	}
	wp_reset_postdata();
	wp_send_json_success(['html' => ob_get_clean(), 'count' => $q->post_count]);
}
add_action('wp_ajax_nvr_favorites', 'nvr_ajax_favorites');
add_action('wp_ajax_nopriv_nvr_favorites', 'nvr_ajax_favorites');
