<?php get_header(); while (have_posts()) : the_post(); ?>
<section class="page-hero"><div class="container inner-container">
	<?php nvr_breadcrumbs([['Yêu thích']]); ?>
	<h1><?php the_title(); ?></h1>
	<p>Danh sách được lưu ngay trên trình duyệt của bạn, không cần đăng nhập.</p>
</div></section>
<section class="section"><div class="container inner-container">
	<div class="property-grid" id="favorites-list" hidden></div>
	<div class="empty-state" id="favorites-empty">
		<h2>Bạn chưa lưu bất động sản nào</h2>
		<p>Bấm biểu tượng trái tim trên mỗi tin đăng để lưu lại và xem sau.</p>
		<a class="btn" href="<?php echo esc_url(nvr_archive_url('property')); ?>">Khám phá bất động sản</a>
	</div>
</div></section>
<?php endwhile; get_footer(); ?>
