<?php
get_header();
while (have_posts()) : the_post();
	$id = get_the_ID();
	$gallery = nvr_gallery($id);
	$district = nvr_meta('neighborhood_district', $id, 'TP. Hồ Chí Minh');
	$city = nvr_meta('neighborhood_city', $id, 'TP. Hồ Chí Minh');
	$term = nvr_meta('neighborhood_term', $id);
	$embed = nvr_map_embed_url(get_the_title() . ', ' . $district, nvr_meta('latitude', $id), nvr_meta('longitude', $id));
	$sections = [['Vị trí & giao thông', 'neighborhood_location'], ['Tiện ích nội khu', 'neighborhood_amenities'], ['Phân khu', 'neighborhood_subareas'], ['Đánh giá thị trường', 'neighborhood_market']];
?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Khu dân cư', nvr_archive_url('neighborhood')], [get_the_title()]]); ?>
	<h1>Tổng quan khu dân cư <?php the_title(); ?></h1>
	<p><?php echo esc_html($district . ', ' . $city); ?></p>
</div></section>
<div class="container">
	<div class="single-layout">
		<article>
			<?php get_template_part('template-parts/gallery', null, ['urls' => $gallery, 'title' => get_the_title()]); ?>
			<section class="content-block"><h2>Giới thiệu tổng quan</h2><div class="entry-content"><?php the_content(); ?></div></section>
			<?php foreach ($sections as $i => $s) : $text = nvr_meta($s[1], $id); if (!$text) continue; ?>
				<section class="content-block"><h2><?php echo esc_html($s[0]); ?></h2><p><?php echo nl2br(esc_html($text)); ?></p></section>
			<?php endforeach; ?>
			<section class="content-block"><h2>Bản đồ khu vực</h2><iframe class="map-frame" src="<?php echo esc_url($embed); ?>" title="Bản đồ <?php echo esc_attr(get_the_title()); ?>" loading="lazy" allowfullscreen></iframe></section>
			<p class="disclaimer">Thông tin mang tính tham khảo, cần đối chiếu với nguồn chính thức trước khi dùng cho quyết định giao dịch.</p>
		</article>
		<?php get_template_part('template-parts/contact-box', null, ['title' => 'Nhận tư vấn khu vực', 'ref' => $id, 'source' => get_the_title()]); ?>
	</div>
	<?php
	$args = ['post_type' => 'property', 'posts_per_page' => 3, 'no_found_rows' => true];
	if ($term) $args['tax_query'] = [['taxonomy' => 'property_location', 'field' => 'slug', 'terms' => $term]];
	$q = new WP_Query($args);
	if ($q->have_posts()) : ?>
	<section class="section related"><div class="section-head"><h2>Bất động sản trong khu vực</h2><?php if ($term) : ?><a class="text-link" href="<?php echo esc_url(nvr_archive_url('property', ['location' => $term])); ?>">Xem tất cả <?php echo nvr_icon('arrow', 16); ?></a><?php endif; ?></div>
		<div class="property-grid"><?php while ($q->have_posts()) { $q->the_post(); get_template_part('template-parts/property-card'); } wp_reset_postdata(); ?></div>
	</section>
	<?php endif; ?>
</div>
<?php endwhile; get_footer(); ?>
