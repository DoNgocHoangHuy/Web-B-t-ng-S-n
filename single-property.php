<?php
get_header();
while (have_posts()) : the_post();
	$id = get_the_ID();
	[$status, $status_class] = nvr_status($id);
	$specs = nvr_specs($id);
	$area_num = nvr_parse_area(nvr_meta('area', $id));
	$price_num = (float) nvr_meta('price_numeric', $id, 0);
	$sqm = ($area_num > 0 && $price_num > 0 && nvr_meta('deal_type', $id) !== 'rent') ? $price_num / $area_num : 0;
	$address = nvr_meta('address', $id, 'Địa chỉ đang cập nhật');
	$embed = nvr_map_embed_url(nvr_meta('address', $id), nvr_meta('latitude', $id), nvr_meta('longitude', $id), nvr_meta('map_url', $id));
	$gallery = nvr_gallery($id);
	$project_id = (int) nvr_meta('project_id', $id);
	$amenities = array_filter(array_map('trim', explode(',', (string) nvr_meta('amenities', $id))));
	$tour = nvr_meta('tour_url', $id);
	$phone = nvr_meta('contact_phone', $id);
?>
<div class="container">
	<?php nvr_breadcrumbs([['Bất động sản', nvr_archive_url('property')], [get_the_title()]]); ?>
	<div class="single-layout">
		<article>
			<?php get_template_part('template-parts/gallery', null, ['urls' => $gallery, 'title' => get_the_title()]); ?>

			<div class="single-summary">
				<span class="tag tag-inline tag-<?php echo esc_attr($status_class); ?>"><?php echo esc_html($status); ?></span>
				<h1><?php the_title(); ?></h1>
				<p class="location"><?php echo nvr_icon('pin', 16); ?><span><?php echo esc_html($address); ?></span></p>
				<div class="price price-xl"><?php echo esc_html(nvr_price_text($id)); ?></div>
				<div class="detail-stats">
					<div class="stat"><strong><?php echo esc_html(nvr_meta('area', $id, '—')); ?> m²</strong><span>Diện tích</span></div>
					<?php if (!nvr_is_land($id)) : ?>
						<div class="stat"><strong><?php echo esc_html(nvr_meta('bedrooms', $id, '0')); ?></strong><span>Phòng ngủ</span></div>
						<div class="stat"><strong><?php echo esc_html(nvr_meta('bathrooms', $id, '0')); ?></strong><span>Phòng tắm / WC</span></div>
					<?php endif; ?>
					<div class="stat"><strong><?php echo esc_html(nvr_meta('direction', $id, 'Đang cập nhật')); ?></strong><span>Hướng</span></div>
					<div class="stat"><strong><?php echo esc_html(nvr_meta('legal', $id, 'Đang cập nhật')); ?></strong><span>Pháp lý</span></div>
					<div class="stat"><strong><?php echo esc_html(nvr_meta('code', $id, 'NVR-' . $id)); ?></strong><span>Mã tin</span></div>
					<?php if ($sqm) : ?><div class="stat"><strong><?php echo esc_html(number_format_i18n($sqm / 1000000, 1)); ?> triệu/m²</strong><span>Đơn giá ước tính</span></div><?php endif; ?>
				</div>
				<div class="share-row">
					<button class="view-button" type="button" data-share><?php echo nvr_icon('share', 16); ?> Chia sẻ</button>
					<button class="view-button favorite-inline" type="button" data-favorite="<?php echo esc_attr($id); ?>" aria-pressed="false"><?php echo nvr_icon('heart', 16); ?> <span>Yêu thích</span></button>
					<a class="view-button" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode(get_permalink()); ?>" target="_blank" rel="noopener"><?php echo nvr_icon('facebook', 16); ?> Facebook</a>
				</div>
			</div>

			<section class="content-block"><h2>Mô tả bất động sản</h2><div class="entry-content"><?php the_content(); ?></div></section>

			<?php if ($amenities) : ?>
			<section class="content-block"><h2>Tiện ích</h2><div class="amenity-grid"><?php foreach ($amenities as $a) echo '<span>' . nvr_icon('check', 16) . esc_html($a) . '</span>'; ?></div></section>
			<?php endif; ?>

			<?php if ($project_id && get_post_status($project_id) === 'publish') : ?>
			<section class="content-block"><h2>Thuộc dự án</h2><p><a class="text-link" href="<?php echo esc_url(get_permalink($project_id)); ?>"><?php echo esc_html(get_the_title($project_id)); ?> <?php echo nvr_icon('arrow', 16); ?></a></p></section>
			<?php endif; ?>

			<section class="content-block" id="vi-tri">
				<h2>Vị trí</h2>
				<p class="location"><?php echo nvr_icon('pin', 16); ?><span><?php echo esc_html($address); ?></span></p>
				<iframe class="map-frame" src="<?php echo esc_url($embed); ?>" title="Bản đồ vị trí: <?php echo esc_attr($address); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
				<p><a class="text-link" href="<?php echo esc_url(nvr_map_directions_url($address)); ?>" target="_blank" rel="noopener">Mở trên Google Maps <?php echo nvr_icon('arrow', 16); ?></a></p>
			</section>

			<?php if ($tour && preg_match('~^https://~i', $tour)) : ?>
			<section class="content-block"><h2>Tham quan 3D</h2><iframe class="map-frame" src="<?php echo esc_url($tour); ?>" title="Tham quan 3D" loading="lazy" allowfullscreen></iframe></section>
			<?php endif; ?>
		</article>

		<?php get_template_part('template-parts/contact-box', null, ['title' => 'Quan tâm bất động sản này?', 'text' => 'Gửi thông tin để được tư vấn và đặt lịch xem nhà.', 'ref' => $id, 'source' => get_the_title(), 'phone' => $phone, 'price' => nvr_price_text($id), 'price_numeric' => nvr_meta('deal_type', $id) === 'rent' ? 0 : $price_num]); ?>
	</div>

	<?php
	$tax = [];
	$types = wp_get_post_terms($id, 'property_type', ['fields' => 'ids']);
	$locs = wp_get_post_terms($id, 'property_location', ['fields' => 'ids']);
	if ($locs) $tax[] = ['taxonomy' => 'property_location', 'terms' => $locs];
	if ($types) $tax[] = ['taxonomy' => 'property_type', 'terms' => $types];
	$related = new WP_Query(['post_type' => 'property', 'posts_per_page' => 3, 'post__not_in' => [$id], 'no_found_rows' => true, 'tax_query' => $tax ? array_merge(['relation' => 'OR'], $tax) : []]);
	if ($related->have_posts()) : ?>
	<section class="section related"><div class="section-head"><h2>Bất động sản tương tự</h2></div>
		<div class="property-grid"><?php while ($related->have_posts()) { $related->the_post(); get_template_part('template-parts/property-card'); } wp_reset_postdata(); ?></div>
	</section>
	<?php endif; ?>
</div>
<?php endwhile; get_footer(); ?>
