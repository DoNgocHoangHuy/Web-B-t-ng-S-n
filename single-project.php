<?php
get_header();
while (have_posts()) : the_post();
	$id = get_the_ID();
	$status = nvr_meta('project_status_text', $id, 'Đang mở bán');
	$soon = mb_stripos($status, 'Sắp') !== false;
	$loc = nvr_merge_address(nvr_meta('project_location', $id, ''), nvr_meta('project_area', $id, 'TP. Hồ Chí Minh'));
	$dev = nvr_meta('project_developer', $id);
	$dev_id = $dev ? nvr_find_post_by_title($dev, 'developer') : 0;
	$embed = nvr_map_embed_url($loc, nvr_meta('latitude', $id), nvr_meta('longitude', $id), nvr_meta('project_map_url', $id));
	$amen = array_filter(array_map('trim', explode(',', (string) nvr_meta('project_amenities', $id))));
?>
<div class="container">
	<?php nvr_breadcrumbs([['Dự án', nvr_archive_url('project')], [get_the_title()]]); ?>
	<div class="single-layout">
		<article>
			<div class="detail-heading">
				<div>
					<span class="tag tag-inline <?php echo $soon ? 'tag-soon' : 'tag-sale'; ?>"><?php echo esc_html($status); ?></span>
					<?php if (nvr_is_featured($id)) echo '<span class="tag tag-inline tag-featured">Nổi bật</span>'; ?>
					<h1><?php the_title(); ?></h1>
					<p class="location"><?php echo nvr_icon('pin', 16); ?><span><?php echo esc_html($loc); ?></span></p>
				</div>
				<div class="price price-xl"><?php echo esc_html(nvr_meta('project_price', $id, 'Đang cập nhật')); ?></div>
			</div>
			<?php get_template_part('template-parts/gallery', null, ['urls' => nvr_gallery($id), 'title' => get_the_title()]); ?>
			<div class="detail-stats">
				<div class="stat"><strong><?php echo esc_html($loc); ?></strong><span>Vị trí</span></div>
				<div class="stat"><strong><?php echo $dev_id ? '<a href="' . esc_url(get_permalink($dev_id)) . '">' . esc_html($dev) . '</a>' : esc_html($dev ?: 'Đang cập nhật'); ?></strong><span>Chủ đầu tư</span></div>
				<div class="stat"><strong><?php echo esc_html(nvr_meta('project_scale', $id, 'Đang cập nhật')); ?></strong><span>Quy mô</span></div>
			</div>
			<section class="content-block"><h2>Tổng quan dự án</h2><div class="entry-content"><?php the_content(); ?></div></section>
			<?php if ($amen) : ?><section class="content-block"><h2>Tiện ích nổi bật</h2><div class="amenity-grid"><?php foreach ($amen as $a) echo '<span>' . nvr_icon('check', 16) . esc_html($a) . '</span>'; ?></div></section><?php endif; ?>
			<section class="content-block"><h2>Vị trí dự án</h2>
				<iframe class="map-frame" src="<?php echo esc_url($embed); ?>" title="Bản đồ vị trí dự án" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
			</section>
		</article>
		<?php get_template_part('template-parts/contact-box', null, ['title' => 'Nhận tư vấn dự án', 'ref' => $id, 'source' => get_the_title()]); ?>
	</div>
	<?php
	$props = new WP_Query(['post_type' => 'property', 'posts_per_page' => 3, 'no_found_rows' => true, 'meta_query' => [['key' => '_nvr_project_id', 'value' => $id]]]);
	if ($props->have_posts()) : ?>
	<section class="section related"><div class="section-head"><h2>Bất động sản trong dự án</h2></div>
		<div class="property-grid"><?php while ($props->have_posts()) { $props->the_post(); get_template_part('template-parts/property-card'); } wp_reset_postdata(); ?></div>
	</section>
	<?php endif; ?>
</div>
<?php endwhile; get_footer(); ?>
