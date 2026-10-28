<?php
/**
 * Shared listing layout for the property archive and the search results page.
 * Left: filter sidebar. Right: results (grid / list / map). No results -> featured suggestions.
 */
global $wp_query;
$args = isset($args) ? $args : [];
$title = $args['title'] ?? 'Nhà đất mua bán & cho thuê';
$kw = nvr_req('keyword', nvr_req('s'));
if ($kw !== '' && empty($args['title'])) $title = 'Kết quả tìm kiếm cho “' . $kw . '”';

// Map markers for ALL filtered results (not only the current page).
$map_items = [];
if ($wp_query->found_posts) {
	$mq = new WP_Query(array_merge($wp_query->query_vars, ['posts_per_page' => 120, 'paged' => 1, 'no_found_rows' => true, 'fields' => 'ids']));
	foreach ($mq->posts as $mid) {
		$lat = nvr_meta('latitude', $mid); $lng = nvr_meta('longitude', $mid);
		if (is_numeric($lat) && is_numeric($lng)) {
			$map_items[] = ['lat' => (float) $lat, 'lng' => (float) $lng, 'title' => get_the_title($mid), 'url' => get_permalink($mid), 'price' => nvr_price_text($mid), 'address' => nvr_meta('address', $mid), 'image' => nvr_img_url(nvr_card_image_url($mid, 'thumbnail'), 240, 160)];
		}
	}
	wp_reset_postdata();
}
?>
<section class="page-hero">
	<div class="container inner-container">
		<?php nvr_breadcrumbs([[$kw !== '' ? 'Tìm kiếm' : 'Bất động sản']]); ?>
		<h1><?php echo esc_html($title); ?></h1>
		<p>Khám phá bất động sản theo khu vực, mức giá và nhu cầu của bạn.</p>
	</div>
</section>

<div class="container listing-layout">
	<button type="button" class="filter-mobile-toggle" data-filter-toggle aria-expanded="false" aria-controls="property-filter"><?php echo nvr_icon('filter', 18); ?> Bộ lọc & sắp xếp</button>
	<?php get_template_part('template-parts/property-filter'); ?>
	<div class="filter-backdrop" data-filter-toggle hidden></div>

	<div class="listing-main">
		<div class="results-top">
			<span class="results-count">Tìm thấy <strong><?php echo esc_html(number_format_i18n($wp_query->found_posts)); ?></strong> bất động sản</span>
			<div class="view-controls" role="group" aria-label="Chế độ xem">
				<button type="button" class="view-button active" data-view="grid" aria-pressed="true"><?php echo nvr_icon('grid', 16); ?> Lưới</button>
				<button type="button" class="view-button" data-view="list" aria-pressed="false"><?php echo nvr_icon('list', 16); ?> Danh sách</button>
				<button type="button" class="view-button" data-view="map" aria-pressed="false"><?php echo nvr_icon('map', 16); ?> Bản đồ</button>
			</div>
		</div>

		<?php if (have_posts()) : ?>
			<div class="property-grid archive-results" id="archive-results">
				<?php while (have_posts()) { the_post(); get_template_part('template-parts/property-card'); } ?>
			</div>
			<div class="map-wrap" id="map-wrap" hidden>
				<div id="nvr-property-map" role="region" aria-label="Bản đồ bất động sản"></div>
				<p class="map-help"><?php echo count($map_items) ? 'Bấm vào điểm đánh dấu để xem giá và mở chi tiết.' : 'Chưa có tin đăng nào gắn tọa độ.'; ?></p>
				<script type="application/json" id="nvr-map-data"><?php echo wp_json_encode($map_items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
			</div>
			<nav class="pagination" aria-label="Phân trang"><?php echo wp_kses_post(paginate_links(['type' => 'plain', 'prev_text' => '‹', 'next_text' => '›'])); ?></nav>
		<?php else : ?>
			<div class="empty-state">
				<h2>Chưa tìm thấy bất động sản phù hợp</h2>
				<p>Hãy thử bỏ bớt điều kiện lọc hoặc chọn khu vực khác. Bạn cũng có thể tham khảo các bất động sản nổi bật bên dưới.</p>
				<a class="btn" href="<?php echo esc_url(nvr_archive_url('property')); ?>">Xem tất cả bất động sản</a>
			</div>
			<h2 class="suggest-title">Bất động sản nổi bật dành cho bạn</h2>
			<div class="property-grid">
				<?php $sug = nvr_featured_properties(6); while ($sug->have_posts()) { $sug->the_post(); get_template_part('template-parts/property-card'); } wp_reset_postdata(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
