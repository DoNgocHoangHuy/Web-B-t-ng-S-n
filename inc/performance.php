<?php
/**
 * Performance: WebP output for uploads, lean <head>, resource hints, lazy-loading.
 */
if (!defined('ABSPATH')) exit;

/* Convert uploaded JPEG/PNG (and all generated sizes) to WebP (WP 6.1+ with GD/Imagick WebP support). */
add_filter('image_editor_output_format', function ($formats) {
	$formats['image/jpeg'] = 'image/webp';
	$formats['image/png']  = 'image/webp';
	return $formats;
});
add_filter('wp_editor_set_quality', function ($q, $mime) { return $mime === 'image/webp' ? 78 : $q; }, 10, 2);

/* Remove things the theme does not need. */
function nvr_slim_head() {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_shortlink_wp_head');
	remove_action('wp_head', 'wp_oembed_add_discovery_links');
	remove_action('wp_head', 'wp_oembed_add_host_js');
}
add_action('init', 'nvr_slim_head');
add_filter('wp_resource_hints', function ($urls, $relation) {
	if ($relation === 'preconnect') {
		$urls[] = ['href' => 'https://images.unsplash.com', 'crossorigin' => 'anonymous'];
	}
	return $urls;
}, 10, 2);

/* Gutenberg block styles are not used on the front end. */
add_action('wp_enqueue_scripts', function () {
	if (is_admin()) return;
	if (!is_singular('post') && !is_page()) {
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');
		wp_dequeue_style('global-styles');
	}
}, 100);

/* Larger images: do not lazy-load the first (LCP) image in the main content. */
add_filter('wp_omit_loading_attr_threshold', function () { return 2; });
