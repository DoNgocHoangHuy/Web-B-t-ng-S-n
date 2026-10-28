<?php get_header(); ?>
<section class="section"><div class="container narrow center">
	<h1>Không tìm thấy trang</h1>
	<p>Địa chỉ có thể đã thay đổi hoặc không còn tồn tại. Hãy thử tìm bất động sản bên dưới.</p>
	<form class="compact-search inline" action="<?php echo esc_url(nvr_archive_url('property')); ?>" method="get" role="search"><input type="search" name="keyword" placeholder="Từ khóa, khu vực…" aria-label="Từ khóa"><button class="btn" type="submit">Tìm kiếm</button></form>
	<p><a class="btn btn-outline" href="<?php echo esc_url(home_url('/')); ?>">Về trang chủ</a></p>
</div></section>
<section class="section soft"><div class="container"><div class="section-head"><h2>Bất động sản nổi bật</h2></div>
	<div class="property-grid"><?php $q = nvr_featured_properties(3); while ($q->have_posts()) { $q->the_post(); get_template_part('template-parts/property-card'); } wp_reset_postdata(); ?></div>
</div></section>
<?php get_footer(); ?>
