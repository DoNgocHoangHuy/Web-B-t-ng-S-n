<?php
/** Thank-you page (slug: cam-on). Fires the conversion events (GA4 generate_lead + Pixel Lead) once per visit. */
get_header(); while (have_posts()) : the_post(); ?>
<section class="section thanks" data-track-on-load="generate_lead">
	<div class="container narrow center">
		<div class="thanks-icon"><?php echo nvr_icon('success', 64); ?></div>
		<h1><?php the_title(); ?></h1>
		<div class="entry-content"><?php the_content(); ?></div>
		<div class="thanks-actions">
			<a class="btn" href="<?php echo esc_url(nvr_archive_url('property')); ?>">Xem thêm bất động sản</a>
			<a class="btn btn-outline" href="<?php echo esc_attr(nvr_hotline_tel()); ?>" data-track="click_hotline"><?php echo nvr_icon('phone', 18); ?> Gọi <?php echo esc_html(nvr_hotline()); ?></a>
			<a class="btn btn-outline" href="<?php echo esc_url(nvr_zalo_url()); ?>" target="_blank" rel="noopener" data-track="click_zalo"><?php echo nvr_icon('zalo', 18); ?> Chat Zalo</a>
		</div>
	</div>
</section>
<section class="section soft"><div class="container">
	<div class="section-head"><h2>Có thể bạn quan tâm</h2></div>
	<div class="property-grid"><?php $q = nvr_featured_properties(3); while ($q->have_posts()) { $q->the_post(); get_template_part('template-parts/property-card'); } wp_reset_postdata(); ?></div>
</div></section>
<?php endwhile; get_footer(); ?>
